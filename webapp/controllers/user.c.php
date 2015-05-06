<?php
/**
 * 用户相关页面
 * @author xiaocai.name
 */
class User_Action extends Controller{

	function __construct(){
		parent::__construct();
		//$this->load->model('user');
	}

	/**
	 * 个人信息修改页面
	 */
	function info(){
		$this->islogin();
		$userid = $this->M->user->loginuser['user_id'];
		if(isset($_POST['nickname'])){
			$postdata = $this->GetPost('nickname,sex,birthday,location_code,location_name,old_nickname');
			$resutl   = $this->M->user->UpdateUserInfo($userid,$postdata);
		}
		$user = $this->M->user->GetUser($userid);
		$this->V->assign('user',$user);
		$this->V->display('user/info.html');
	}

	/**
	 * 个人头像修改页
	 */
	function avatar(){
		$this->islogin();

		$this->Pageheaderload('js','./resources/public/javascripts/ajaxfileupload.js');
		$this->Pageheaderload('js','./resources/public/javascripts/jquery.jcrop.min.js');
		$this->Pageheaderload('css','./resources/public/css/jquery.Jcrop.min.css');
		$this->V->assign('user',$this->M->user->loginuser);
		$this->V->display('user/avatar.html');
	}

	/**
	 * 用户注册页面
	 */
	function register(){
		if(isset($_POST['email'])){
			$postdata = $this->GetPost('email,nickname,password,repassword,sex,
										,birthday,location_code,location_name');
			$result = $this->M->user->Register($postdata);
			$this->gourl('index.php?uri=/user/info/');
		}
		$this->V->display('user/register.html');
	}

	/**
	 * 用户登陆页面
	 */
	function login(){
		if(isset($_POST['email'])){
			$result = $this->M->user->login($_POST['email'],$_POST['password']);
			$this->gourl('index.php?uri=/user/info/');
		}
		$this->V->display('user/login.html');
	}

	/**
	 * 用户登出页面
	 */
	function loginout(){
		$this->M->user->loginout();
		$this->V->assign('loginuser',array());
		$this->V->display('user/loginout.html');
	}


}