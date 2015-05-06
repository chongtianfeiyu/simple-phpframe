<?php
/**
 * 语言包加载类
 * @author  	xiaocai.name
 * @date		2012-03
 * @version 	1.0
 */
class XcLang{

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
	function Lang($name,$key=null){
		$inc    = XcInstance::getInstance();
		$lang = $inc->Lang($name);
		if($lang){
			if($key==null){
				return $lang;
			}else{
				if(isset($lang[$key])){
					return $lang[$key];
				}else{
					return null;
				}
			}
		}
		$path   = 'lang'.DIRECTORY_SEPARATOR;
		
		if (is_dir(APPS_DIR.$path)) {
			$lang = $inc->G('XcLoader')->loadConfig(APPS_DIR.$path.$name.'.php');
		}
		
		if(!defined('SITE_DIR')){
			exit('define(SITE_DIR)');
		}
		
		if (is_dir(APPS_DIR.$path)) {
			if($lang==null){
				  $lang = $inc->G('XcLoader')->loadConfig(APPS_DIR.$path.$name.'.php');
			}else{
				  $temp   = $inc->G('XcLoader')->loadConfig(APPS_DIR.$path.$name.'.php');
				  $lang = array_merge($lang,$temp);
			}
		}
		$inc->Config($name,$lang);
		if($key==null){
			return $lang;
		}else{
			if(isset($lang[$key])){
				return $lang[$key];
			}else{
				return null;
			}
		}
	}
	
	/**
	 * 将语言包赋值到模板中
	 * @param $lang		语言包名称
	 * @param $key		值(多个用,隔开)
	 */
	function assign($lang,$key=null){
		$this->V      =	&XcVIE::getInstance();
		if($key==null){
			$this->V->Smarty->assign('lang', $this->Lang($lang) );
		}else{
			$key    = explode(',',$key);
			$lang   = $this->Lang($lang);
			$result = array();
			foreach ($key as $val){
				if(!empty($val)){
					$result[$val]  = $lang[$val];
				}
			}
		    $this->V->Smarty->assign('lang', $result );
		}
	}
	
	/**
	 * 提供给controller 与  model 的调用函数 
	 *  $this->lang->system('controller');  相当于  $this->Lang('system','controller');
	 * +--------------------------------------
	 * @param string $fun		配置文件名称
	 * @param string $ages		配置键值
	 * +--------------------------------------
	 */
	function __call($name,$key=null){
		if($key==null){
			return $this->Lang($name,null);
		}else{
			return $this->Lang($name,$key[0]);
		}
	}
	
}