<?php
/**
 * 引用框架核心文件
 * @author xiaocai.name
 */
class XcCore{
	function __construct($InitType='site'){
		$INS = XcInstance::getInstance();
		require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'loader.core.php';
		require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'config.core.php';
		require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'routing.core.php';
		require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'cache.core.php';
		require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'error.core.php';
		require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'dbdriver'.DIRECTORY_SEPARATOR.'mysql.driver.db.php';
		require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'dbdriver'.DIRECTORY_SEPARATOR.'mysql.driver.php';
		$INS->L('XcLoader' , new XcLoader());
		$INS->L('XcConfig' , new XcConfig());
		$INS->L('XcError'  , new XcError());
		$INS->L('XcCache'  , new XcCache());
		if($InitType=='service'){
			require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'service.core.php';
		}else if($InitType=='site'){
			require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'site.core.php';
			require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'tpdriver'.DIRECTORY_SEPARATOR.'class.template.php';
			$INS->L('XcView'  , new Template_Lite());
		}else{
			require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'site.core.php';
			require_once SYS_DIR.'coreclass'.DIRECTORY_SEPARATOR.'tpdriver'.DIRECTORY_SEPARATOR.'class.template.php';
			$INS->L('XcView'  , new Template_Lite());
		}
		$config			=	$INS->G('XcConfig')->Config('database');
		$INS->L("XcDB",new xc_mysql_driver($config));
	}
}

/**
 * 模型
 * @author xiaocai.name
 */
class Model{
	var $load;
	var $error;
	var $config;
	var $cache;
	var $lang;
	var $D;
	var $M;
	var $L;
	var $I;
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
	 * Model返回数据
	 * @param  $Status	状态 >0 成功 <=0 失败
	 * @param  $msg		提示信息
	 * @param  $data	返回数据
	 */
	function Mreturn($Status,$msg='',$data=array()){
		return array('result'=>$Status,'msg'=>$msg,'data'=>$data);
	}
	
//	function __call($name,$key=null){echo $name;}
//	
//	function __get($name){echo $name;}
}
class XcMOB{
	private static $instance=null;
    public static function &getInstance(){
      if (self::$instance==null){
        self::$instance = new XcMOB();
      }
      return self::$instance;
    }
	function __get($name){
		$INS = &XcInstance::getInstance();
		return $INS->Model($name.'_Model');
	}
}
class XcLIB{
	private static $instance=null;
    public  static function &getInstance(){
      if (self::$instance==null){
        self::$instance = new XcLIB();
      }
      return self::$instance;
    }
	function __get($name){
		$INS = &XcInstance::getInstance();
		return $INS->G($name);
	}
}


class XcInstance{
  private static $instance=null;
  private static $class = array();
  private static $model = array();
  private static $config= array();
  private static $view  = array();
  private static $lang  = array();
  private function __construct() {}
  public static function &getInstance(){if (self::$instance==null){self::$instance = new XcInstance();} return self::$instance;}
  public function L($name,$obj){if (!isset(self::$class[$name])){self::$class[$name] = $obj;}}
  public function &G($name){return self::$class[$name];}
  public function &Config($name,$set=null){if($set!=null){if (!isset(self::$config[$name])){self::$config[$name] = $set;}}return self::$config[$name];}
  public function &Lang($name,$set=null){if($set!=null){if (!isset(self::$lang[$name])){self::$lang[$name] = $set;}}return self::$lang[$name];}
  public function &Model($name,$set=null){if($set!=null){if (!isset(self::$model[$name])){self::$model[$name] = $set;}}return self::$model[$name];}
}

?>