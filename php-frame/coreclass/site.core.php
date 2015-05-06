<?php 
/**
 * 控制器方法
 * @author xiaocai.name
 */
class Controller{
	var $load;
	var $error;
	var $config;
	var $cache;
	var $D;
	var $M;
	var $V;
	var $C;
	var $L;
	var $I;
	var $header_include = '';
	
	function __construct(){
		$INS = &XcInstance::getInstance();
		$this->config = &$INS->G('XcConfig');
		$this->lang   = &$INS->G('XcLang');
		$this->error  = &$INS->G('XcError');
		$this->load   = &$INS->G('XcLoader');
		$this->cache  = &$INS->G('XcCache');
		$this->D      =	&$INS->G('XcDB');
		$this->V      =	&$INS->G('XcView');
		$this->C      =	&XcCON::getInstance();
		$this->M	  = &XcMOB::getInstance();
		$this->L	  = &XcLIB::getInstance();
		$this->I	  = &$this->L;
		
		$config = $this->config->system('view');
      	$this->V->template_dir 	   = &$config['view_path'];
        $this->V->force_compile    = false;
        $this->V->compile_check    = false;
        $this->V->cache 		   = false;
        $this->V->cache_lifetime   = 3600;
        $this->V->config_overwrite = false;
      	$this->V->left_delimiter   = &$config['left_delimiter'];
		$this->V->right_delimiter  = &$config['right_delimiter'];
		
		$this->V->assign('title','Test Page');
		$this->initPage();
	}
	

	/**
	 * 载入页面前初始化代码
	 */
	function initPage(){
		$this->load->model('user');
		$is = $this->M->user->InitUser();
		if($is===true){
			$this->V->assign('loginuser',$this->M->user->loginuser);
			$this->V->assign('user_json',json_encode($this->M->user->loginuser));
		}else{
			$this->V->assign('user_json','null');
		}
		$uri = isset($_GET['uri']) ? $_GET['uri'] : '/home/index/';
		unset($_GET['uri']);
		$this->V->assign('head_uri', $uri);
		$this->V->assign('head_get',json_encode($_GET));
		$this->V->assign('head_data',json_encode(array()));
		$this->V->assign('head_include','');
	}


	/**
	 * 错误页面
	 * @param  $msg	 提示信息
	 * @param  $url	 跳转的页面
	 */
	function errorPage($msg='发生异常',$url=''){
		$this->V->assign('errorMsg',$msg);
		$this->V->assign('url',$url);
		$this->V->display('module/error.html');
		exit;
	}

	/**
	 * 用于需要登录后才能访问的页面
	 */
	function islogin(){
		if(empty($this->M->user->loginuser)){
			$this->errorPage('需要登录才能访问');
		}
	}

	/**
	 * 错误页面
	 * @param  $url	 跳转页面(字符串或数组)
	 */
	function gourl($url=''){
		if(is_array($url)){
			//$url = 'index.php?uri='.$url[0].'&'.$url;
		}else{
			header("Location:{$url}");
		}
		return false;
	}

	/**
	 * 页面引用文件
	 * @param $type 类型 (css|js)
	 * @param $path 文件路径
	 */
	function Pageheaderload($type='js',$path){
		if($type=='js'){
			$this->header_include.="<script type='text/javascript' src='{$path}'></script>";
		}else if($type=='css'){
			$this->header_include.="<link rel='stylesheet' type='text/css' href='{$path}'>";
		}else{
			$this->header_include.=$path;
		}
		$this->V->assign('head_include',$this->header_include);
	}

	/**
	 * 初始化语言包
	 */
	function InitLang(){
		require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'lang.core.php';
		$INS = &XcInstance::getInstance();
		$INS->L('XcLang'  , new XcLang());
		$this->lang   = &$INS->G('XcLang');
	}
	
	/**
	 * Ajax返回结构
	 * @param  $Status	状态 >0 成功 <=0 失败
	 * @param  $msg		提示信息
	 * @param  $data	返回数据
	 */
	function AjaxReturn($Status,$msg='',$data=array()){
		exit( json_encode(array('result'=>$Status,'msg'=>$msg,'data'=>$data))  );
	}
	
	/**
	 * 验证Post参数是否存在(只要有一个参数不存在就返回错误)
	 * @param  $name	字段名称,为空则验证是否有提交post(多个用逗号隔开)
	 */
	function IsPost($name=''){
		if(!isset($_POST)){
			$this->AjaxReturn(-1,'缺少参数:$_POST[]');
		}
		if($name!=''){
			$arrname = explode(',',$name);
			$msg	 = '';
			foreach ($arrname as $val){
				if(!isset($_POST[$val])){
					$msg .= ' $_POST['.$val.'] ';
				}
			}
			if($msg!=''){
				$this->AjaxReturn(-1,'缺少参数:'.$msg);
			}
		}
	}
	
	/**
	 * 判断后台管理员是否登录
	 */
	function &IsAdminLogin(){
		$time = time();
		
		if(!isset($_SESSION['admin']) && isset($_GET['adminu'])){
			$key   = $_GET['adminu'];
			$admin = $this->cache->type('xAdmin')->get($key);
			$this->cache->type('xAdmin')->del($key);
			if($admin!==false){
				$_SESSION['admin'] = $admin;
			}
		}
		
		//是否登录
		if( !isset($_SESSION['admin']) ){
			if(empty($_SESSION['admin'])){
				exit('当前处于未登录状态,<a href="http://www.mmoshare.tmc/index.php/admin/home/login_com/">点击这里</a>登录.');
				//exit('当前处于未登录状态,<a href="'.SITE_URL.'index.php/admin/home/login/">点击这里</a>登录.');
			}
		}
		
		if(!isset($_SESSION['admin']['last_access'])){
			$_SESSION['admin']['last_access'] = $time;
		}
		
		//是否登录超时
		if( $time - $_SESSION['admin']['last_access'] > 300 ){
			unset($_SESSION['admin']);
			exit('抱歉,您十分钟内没有操作,请<a href="http://www.mmoshare.tmc/index.php/admin/home/login_com/">点击这里</a>重新登录.');
		}else{
			$_SESSION['admin']['last_access'] = $time;
		}
		
		return $_SESSION['admin'];
	}
	
	/**
	 * 参数存在并且不等于某值(符合条件的参数将被返回)
	 * @param $name		字段名称(多个用逗号隔开)
	 * @param $notval	这些参数不能等于此值
	 */
	function GetPost($name,$notval=''){
		if(!isset($_POST)){
			return array();
		}
		if($name!=''){
			$arrname = explode(',',$name);
			$array   = array();
			foreach ($arrname as $val){
				if( isset($_POST[$val]) && $_POST[$val]!=$notval){
					$array[$val] = $_POST[$val];
				}
			}
			return $array;
		}
		return array();
	}
	
	/**
	 * 参数存在并且不等于某值(符合条件的参数将被返回)
	 * @param $name		字段名称(多个用逗号隔开)
	 * @param $type	参数类型 默认0为字符型，1为int
	 * @param $default	默认值
	 */
	function GetParamPost($name,$type=0,$default='') {
		if(!isset($_POST)){
			return array();
		}
		if($name!=''){
			$arrname = explode(',',$name);
			$array   = array();
			foreach ($arrname as $val){
				if( isset($_POST[$val])){
					if($type == 1){
						$array[$val] = isset($_POST[$val]) ? intval($_POST[$val]) : $default;
					}else{
						$array[$val] = empty($_POST[$val]) ? $default : htmlspecialchars(trim($_POST[$val]));
					}
				}
			}
			return $array;
		}
		return array();
	}
	
	/**
	 * 参数存在并且不等于某值(符合条件的参数将被返回)
	 * @param $name		字段名称(多个用逗号隔开)
	 * @param $type	参数类型 默认0为字符型，1为int
	 * @param $default	默认值
	 */
	function GetOneParamPost($name,$type=0,$default='') {
		
		if(!isset($_POST)){
			return $default;
		}
		if($name!=''){
			if( isset($_POST[$name])){
				if($type == 1){
					$result = isset($_POST[$name]) ? intval($_POST[$name]) : $default;
				}else{
					$result = empty($_POST[$name]) ? $default : htmlspecialchars(trim($_POST[$name]));
				}
				return $result;
			}else{
				return $default;
			}
		}else{
			return $default;
		}
	}

	//	function __call($name,$key=null){echo $name;}
	//	
	//	function __get($name){echo $name;}
	
}


class XcCON{
	private static $instance=null;
    public  static function &getInstance(){if (self::$instance==null){self::$instance = new XcCON();}return self::$instance;}
	function __get($name){$INS = &XcInstance::getInstance();return $INS->G($name.'_Action');}
}


?>