<?php
/**
 * 配置信息加载类
 * @author  	http://xiaocai.name
 * @date		2011-11
 * @version 	2.0
 */
class XcConfig{

	function __construct(){
	}
	
	/**
	 * 载入配置信息
	 *  先载入系统中的配置文件,接着载入站点中的配置文件之后将与系统配置重复的配置项覆盖
	 * +--------------------------------------
	 * @param string $name		配置文件名称
	 * @param string $key		键值
	 * +--------------------------------------
	 */
	function Config($name,$key=null){
		$inc    = XcInstance::getInstance();
		$config = $inc->Config($name);
		if($config){
			if($key==null){
				return $config;
			}else{
				if(isset($config[$key])){
					return $config[$key];
				}else{
					return null;
				}
			}
		}
		$path   = 'config'.DIRECTORY_SEPARATOR;
		
		if (is_dir(SYS_DIR.$path)) {
			$config = $inc->G('XcLoader')->loadConfig(SYS_DIR.$path.$name.'.inc.php');
		}
		
		if(!defined('APPS_DIR')){
			exit('define(APPS_DIR)');
		}
		
		if (is_dir(APPS_DIR.$path)) {
			if($config==null){
				  $config = $inc->G('XcLoader')->loadConfig(APPS_DIR.$path.$name.'.inc.php');
			}else{
				  $temp   = $inc->G('XcLoader')->loadConfig(APPS_DIR.$path.$name.'.inc.php');
				  $config = array_merge($config,$temp);
			}
		}
		$inc->Config($name,$config);
		if($key==null){
			return $config;
		}else{
			if(isset($config[$key])){
				return $config[$key];
			}else{
				return null;
			}
		}
	}
	
	/**
	 * 提供给controller 与  model 的调用函数 
	 *  $this->config->system('controller');  相当于  $this->Config('system','controller');
	 * +--------------------------------------
	 * @param string $fun		配置文件名称
	 * @param string $ages		配置键值
	 * +--------------------------------------
	 */
	function __call($name,$key=null){
		if($key==null){
			return $this->Config($name,null);
		}else{
			return $this->Config($name,$key[0]);
		}
	}
	
}

