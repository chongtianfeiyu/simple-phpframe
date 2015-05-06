<?php
 class XcLoader{

 	/**
	 * 引用文件
	 * +----------------------
	 * @param $path	文件地址
	 * @param $is	文件不存在时是否终止
	 * +----------------------
	 */
	function loader_file($path,$is=false){
		if(file_exists($path)){
			include_once $path;
			return true;
		}else{
			if($is===true){
				exit('File does not exist:'.$path);
			}else{
				return false;
			}
		}
	}
	
 	/**
	 * 载入系统核心文件
	 * +----------------------
	 * @param $FileList	 文件列表(数组或与,隔开的字符串)
	 * +----------------------
	 */
	function LoadCore($FileList){
		if(!is_array($FileList)){
			$FileList = explode(',',$FileList);
		}
		foreach ($FileList as $file){
			$this->LoadClass('coreclass',$file);
		}
	}
 	
	/**
	 * 载入配置信息
	 * +--------------------------------------
	 * @param string $path		配置文件路径
	 * +--------------------------------------
	 */
	function loadConfig($path){
		if(is_file($path)){
		    return include $path;
		}else{
			return false;
		}
	}
	
	/**
	 * 兼容修版本
	 */
	function config(){
		return true;
	}
	
	/**
	 * 载入Model文件并注册至变量中
	 * +--------------------------------------
	 * @param $model model名称(包含路径)
	 * +--------------------------------------
	 */
	function Model($model){
		return $this->loadObj('Model',$model,null);
	}
	
	/**
	 * 载入Library文件并注册至变量中
	 * +--------------------------------------
	 * @param $library   Library名称(包含路径)
	 * @param $ClassName 自定义类名称
	 * +--------------------------------------
	 */
	function Library($library,$ClassName=null){
		return $this->loadObj('Library',$library,$ClassName);
	}
	
	/**
	 * 载入Controller文件并注册至变量中
	 * +--------------------------------------
	 * @param $Controller   控制器名称(包含路径)
	 * +--------------------------------------
	 */
	function Controller($Controller){
		return $this->loadObj('Controller',$Controller,null);
	}
	
	/**
	 * 载入interface文件并注册至变量中
	 * +--------------------------------------
	 * @param $interface   接口名称名称(包含路径)
	 * +--------------------------------------
	 */
	function Iport($interface,$ClassName=null){
		return $this->loadObj('Iport',$interface,$ClassName);
	}
	
	/**
	 * 载入对象至Instance中
	 * @param  $ObjType		载入对象类型
	 * @param  $ObjName		载入对象名称
	 * @param  $ClassName	自定义类名称
	 */
 	private function loadObj($ObjType,$ObjName,$ClassName=null){
 		
 		switch ($ObjType){
 			case 'Model':
 				$ClassSuffix = '_Model';
 				$FileSuffix  = '.m.php';
 				$FilePath	 = 'model_path';
 				break;
 			case 'Library':
 				$ClassSuffix = '';
 				$FileSuffix  = '.php';
 				$FilePath	 = 'library_path';
 				break;
 			case 'Controller':
 				$ClassSuffix = '_Action';
 				$FileSuffix  = '.c.php';
 				$FilePath	 = 'controller_path';
 				break;
 			case 'Iport':
 				$ClassSuffix = '';
 				$FileSuffix  = '.php';
 				$FilePath	 = 'interface_path';
 				break;
 			default:
 				return false;
 				;
 		}
 		$INS 	= &XcInstance::getInstance();
		$ObjName	= str_replace('/' ,DIRECTORY_SEPARATOR,$ObjName);
		$ObjName	= str_replace('\\',DIRECTORY_SEPARATOR,$ObjName);
 		if($ClassName==null){
			$actionClass  = substr(strrchr('http://xiaocai.name'.DIRECTORY_SEPARATOR.$ObjName,DIRECTORY_SEPARATOR),1);
			$actionClass .= $ClassSuffix;
		}else{
			$actionClass  = $ClassName;
		}
		
 		if($ObjType=='Model'){
			$is = $INS->Model($actionClass)!==null;
		}else{
			$is = $INS->G($actionClass)!==null;
		}
		
		if($is){
			return true;
		}else{
			$path = $INS->G('XcConfig')->Config('system',$FilePath);
			if($this->loader_file($path.strtolower($ObjName).$FileSuffix)){
				if(!class_exists($actionClass)){
					$INS->G('XcError')->ShowError('错误 :class '.$actionClass.'{} 不存在.');
					return false;
				}
				if($ObjType=='Model'){
					$INS->Model($actionClass , new $actionClass());
				}else{
					$INS->L($actionClass , new $actionClass());
				}
			}else{
				$INS->G('XcError')->ShowError('错误 :没有找到'.$ObjType.'文件. $this->load->'.$ObjType.'("'.strtolower($ObjType).'");');
				return false;
			}
		}
	}
 }
?>