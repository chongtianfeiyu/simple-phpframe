<?php

/**
 * 用户相关模块
 * @author xiaocai.name
 */
class User_Model extends Model{

	public $loginuser = null;

	function __construct(){
		parent::__construct();
	}

	/**
	 * 页面初始化时载入登陆用户信息
	 *    更新cookie判断用户是否登陆(站点载入时在site.core.php自动运行)
	 * @return true/false
	 */
	function InitUser(){

		//cookie是否存在
		if(!isset($_COOKIE['userinfo']) || !isset($_COOKIE['userinfo'])){
			return false;
		}

		//取值
		$cookie   = stripslashes($_COOKIE['userinfo']);
		$cookiemd5= $_COOKIE['usermd5'];

		//判断cookie是否被改过
		if($cookiemd5!=md5($cookie.'xiaocai')){
			return false;
		}
		$cookie   = json_decode($cookie);
		if(!$cookie){
			return false;
		}

		$this->loginuser = $this->GetUser($cookie->userid);
		if(!$this->loginuser){
			return false;
		}

		return true;
	}

	/**
	 * 用户注册
	 * @param $data 表单信息
	 * @return $this->Mreturn()
	 */
	function Register($data){

		//格式验证
		if(!ereg("^([a-za-z0-9_-])+@([a-za-z0-9_-])+(\.[a-za-z0-9_-])+",$data['email'])){
			return $this->Mreturn(-1,'邮箱格式不正确.');
		}
		if($data['password']!=$data['repassword']){
			return $this->Mreturn(-1,'两次输入的密码不一致.');
		}
		if(strlen($data['password'])<=5){
			return $this->Mreturn(-1,'密码长度不能少于6位.');
		}

		//唯一性验证
		if($this->isaccount($data['email'])){
			return $this->Mreturn(-1,"邮箱'{$data['email']}'已经被注册了.");
		}
		if($this->isnickname($data['nickname'])){
			return $this->Mreturn(-1,"昵称'{$data['nickname']}'已经存在.");
		}
		
		//写入账号表
		$uid = $this->D->insert('user-account',array(
							'user_email'    => $data['email'],
							'user_password' => $data['password']
						));
		if(!$uid){
			return $this->Mreturn(-2,'注册失败');
		}

		//写入用户详情表
		$infodata = array();
		$infodata['user_id'] 	   = 	$uid;
		$infodata['user_email']    =    $data['email'];
		$infodata['user_nickname'] =    $data['nickname'];
		$infodata['user_ctime']    =    date('Y-m-d H:i:s',time());
		if(isset($data['birthday'])){
			$infodata['user_birthday']    =    $data['birthday'];
		}
		if( isset($data['sex']) && $data['sex']!=0 ){
			$infodata['user_sex']    =    $data['sex'];
		}
		if( isset($data['location_code']) && isset($data['location_name'])){
			$infodata['user_location_code']    =    $data['location_code'];
			$infodata['user_location_name']    =    $data['location_name'];
		}
		$this->D->insert('user-info',$infodata);
		return $this->Mreturn(1,'注册成功',array('uid'=>$uid));
	}

	/**
	 * 用户登陆
	 * @param $email 邮箱
	 */
	function login($account,$password){
		//格式验证
		if(!ereg("^([a-za-z0-9_-])+@([a-za-z0-9_-])+(\.[a-za-z0-9_-])+",$account)){
			return $this->Mreturn(-1,'邮箱格式不正确.');
		}

		//账号密码验证
		$info = $this->D->get('user-account')
		                 ->where('user_email',$account)
		                 ->where('user_password',$password)
		                 ->toone();
		if(!$info){
			return $this->Mreturn(-1,'用户不存在.');
		}

		//用户详情
		$this->loginuser = $this->GetUser($info['user_id']);
		if(!$this->loginuser){
			return $this->Mreturn(-1,'无此用户个人资料.');
		}

		//写Cookie,
		$json = json_encode(array(
			'userid'   => $this->loginuser['user_id'],
			'nickname' => $this->loginuser['user_nickname'],
			'account'  => $this->loginuser['user_email']
		));
		setcookie('userinfo',$json);
		setcookie('usermd5',md5($json.'xiaocai'));
		
		return $this->Mreturn(1,'登陆成功');
	}

	/**
	 * 用户登出页面
	 * @param $email 邮箱
	 */
	function loginout(){
		setcookie('userinfo','');
		setcookie('usermd5','');
		$this->loginuser = null;
	}

	/**
	 * 验证账号邮箱是否被注册
	 * @param $email 邮箱
	 */
	function isaccount($email){
		$count = $this->D->get('user-account')
		                 ->where('user_email',$email)
		                 ->tocount();
		if($count){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 验证账号邮箱是否被注册
	 * @param $nickname 用户昵称
	 */
	function isnickname($nickname){
		$count = $this->D->get('user-info')
		                 ->where('user_nickname',$nickname)
		                 ->tocount();
		if($count){
			return true;
		}else{
			return false;
		}
	}

	/**
	 *  获得用户信息
	 */
	function GetUser($uid){
		if(intval($uid)==0){
			return array();
		}
		$user =  $this->D->get('user-info')
					     ->where('user_id',$uid)
					     ->toone();
		if($user['user_isface']=='0'){
			$user['user_face'] = 'resources/themes/temp/64x64.jpg';
		}else{
			$user['user_face'] = '';
		}
		return $user;
	}

	/**
	 * 修改用户资料
	 * @param $uid   用户id
	 * @param $data  待更新数据
	 */
	function UpdateUserInfo($uid,$data){
		
		//昵称是否可用
		if( $data['old_nickname'] != $data['nickname'] ){
			if($this->isnickname($data['nickname'])){
				return $this->Mreturn(-1,"昵称'{$data['nickname']}'已经存在.");
			}
		}
		
		//写入用户详情表
		$infodata = array();
		$infodata['user_id'] 	   = 	$uid;
		$infodata['user_email']    =    $data['email'];
		$infodata['user_nickname'] =    $data['nickname'];
		if(isset($data['birthday'])){
			$infodata['user_birthday']    =    $data['birthday'];
		}
		if( isset($data['sex'])  ){
			$infodata['user_sex']    =    $data['sex'];
		}
		if( isset($data['location_code']) && isset($data['location_name'])){
			$infodata['user_location_code']    =    $data['location_code'];
			$infodata['user_location_name']    =    $data['location_name'];
		}

		//更新
		$result = $this->D->where('user_id',$uid)
						  ->update('user-info',$infodata);

		//返回
		return $this->Mreturn(1,"个人资料已修改");
	}

}