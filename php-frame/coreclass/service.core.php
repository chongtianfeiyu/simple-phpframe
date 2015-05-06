<?php 
/**
 * 服务端
 * @author xiaocai.name
 */
class Service{
	var $load;
	var $error;
	var $config;
	var $cache;
	var $lang;
	var $D;
	var $M;
	var $L;
	var $I;
	var $V;
	var $API_PARAM;
	function __construct(){
		$INS = &XcInstance::getInstance();
		$this->config = &$INS->G('XcConfig');
		$this->error  = &$INS->G('XcError');
		$this->load   = &$INS->G('XcLoader');
		$this->cache  = &$INS->G('XcCache');
		$this->D      =	&$INS->G('XcDB');
		$this->M	  = &XcMOB::getInstance();
		$this->L	  = &XcLIB::getInstance();
		$this->I	  = &$this->L;
		if(isset($_POST['{API}'])){
			$this->API_PARAM = $_POST['{API}'];
			unset($_POST['{API}']);
		}
	}

	/**
	 * 用于需要登录后才能访问的接口
	 */
	function islogin(){
		$this->load->model('user');
		$is = $this->M->user->InitUser();
		if($is===false){
			$this->ResultApi(0,'您还没有登录.');
			exit;
		}
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
	 * 初始化模板引擎
	 */
	function InitView(){
		require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'tpdriver'.DIRECTORY_SEPARATOR.'class.template.php';
		$INS = &XcInstance::getInstance();
		$INS->L('XcView'  , new Template_Lite());
		$config     = $this->config->system('view');
		$this->V    = &$INS->G('XcView');
		$this->V->template_dir      = $config['view_path'];
		$this->V->left_delimiter    = $config['left_delimiter'];
		$this->V->right_delimiter   = $config['right_delimiter'];
		$this->V->cache_lifetime    =  20;
        $this->V->force_compile     = false;
        $this->V->compile_check     = false;
        $this->V->config_overwrite  = false;
        $this->V->cache 	       	= false;
	}
	
	/**
	 * 是否需要验证授权
	 * @param $isSite
	 */
	function InitApi($isSite=true){
		if($isSite===false){
			return true;
		}
		
		//参数是否齐全
		if( !isset($this->API_PARAM['FROM']) || !isset($this->API_PARAM['APIKEY']) 
			|| !isset($this->API_PARAM['TIME']) || !isset($this->API_PARAM['APISEC']) ){
			$this->ResultApi('-413','请求参数异常.');
		}
		//是否被授权使用
		$config = $this->config->siteapi();
		foreach ($config as $val) {
			if($this->API_PARAM['FROM'] == $val['from']){
				if($this->API_PARAM['APIKEY']  == md5($val['from'].$val['key'].$this->API_PARAM['TIME'].'xiaocai.name') ){
					if($this->API_PARAM['APISEC'] == $val['Secret']){
						return true;
					}
				}
			}
		}
		$this->ResultApi('-414','您的应用未被授权.');
	}
	
	/**
	 * 统一的json返回结果
	 * @param  $Status	状态 >0 成功 <=0 失败
	 * @param  $msg		提示信息
	 * @param  $data	返回数据
	 */
	function ResultApi($Status,$msg='',$data=array(),$isheader=true){
		if($isheader){
			header('Content-type:application/json;charset=utf-8');
		}
		echo json_encode(array('result'=>$Status,'msg'=>$msg,'data'=>$data));
		return true;
	}
	
	/**
	 * 验证Post参数是否存在(只要有一个参数不存在就返回错误)
	 * @param  $name	字段名称,为空则验证是否有提交post(多个用逗号隔开)
	 */
	function IsPost($name=''){
		if(!isset($_POST)){
			$this->ResultApi(-1,'缺少参数:'.$name);
		}
		if($name!=''){
			$arrname = explode(',',$name);
			$msg	 = '';
			foreach ($arrname as $val){
				if(!isset($_POST[$val])){
					$msg .= " {$val}, ";
				}
			}
			if($msg!=''){
				$this->ResultApi(-1,'缺少参数:'.$msg);
			}
		}
	}
	
	
}

?>