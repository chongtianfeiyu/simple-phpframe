<?php
/**
 * 控制器加载类
 * @author  http://xiaocai.name
 * @date	2011-4-20
 * @version 1.7
 */
class XcRouting{
	var $url_dir = '';
	var $controller;
	var $function;
	var $param;
	var $default_c;
	var $default_a;
	var $path;
	var $suffix;
	
	
	public function __construct($InitType='site') {
		$INS    = XcInstance::getInstance();
		$config = $INS->G('XcConfig')->Config('system');
		
		if($InitType=='service'){
			$this->default_c	= 	 $config['serviceName'];
			$this->default_a	=	 $config['serviceFun'];
			$this->path			=	 $config['service_path'];
			$this->suffix		=	 '.s.php';
		}else{
			$this->default_c	= 	 $config['controller'];
			$this->default_a	=	 $config['function'];
			$this->path			=	 $config['controller_path'];
			$this->suffix		=	 '.c.php';
		}
		
		$this->run();
	}
	
	/**
	 * 通过解析URL获得Action类名,执行Action类名的方法.
	 */
	public function run(){
		$this->parsePath();			//解析URL
		$this->getActionFile();		//加载控制器文件
		$this->getActionClass();	//实例化控制器类
	}
	
	/**
	 * 解析URL路径
	 * 获得控制器、控制器中函数、参数
	 */
	private function parsePath(){
		
		if(isset($_GET['uri'])){
			//index.php?uri=/home/index/1/2/3
			$_SERVER['REQUEST_URI'] = preg_replace('/(.*).php/','',$_GET['uri']);
		}else{
			//index.php/home/index/1/2/3
			$_SERVER['REQUEST_URI'] = preg_replace('/(.*).php/','',$_SERVER['REQUEST_URI']);
		}
		
		if(isset($_SERVER['REQUEST_URI'])){
			$tmp				= &$_SERVER['REQUEST_URI'];
			$url_param			= array_filter(explode("/",$tmp));
			$this->controller 	= (isset($url_param[1]) && $url_param[1]!='')?$url_param[1]:$this->default_c;
			$this->function		= (isset($url_param[2]) && $url_param[2]!='')?$url_param[2]:$this->default_a;
			$url_g = 1;
			foreach ($url_param as $value){
				$file 			= 	$this->path.$this->url_dir.$value.$this->suffix;
				if(file_exists($file)){
					$this->controller 	= (isset($url_param[$url_g]) && $url_param[$url_g]!='')?$url_param[$url_g]:$this->default_c;
					$this->function		= (isset($url_param[$url_g+1]) && $url_param[$url_g+1]!='')?$url_param[$url_g+1]:$this->default_a;
					$this->param		= array_splice($url_param,$url_g+1);
					break;
				}else{
					++$url_g;
					$this->url_dir	=	$this->url_dir.$value.'/';
				}
			}
		}else{
			$this->controller	=	$this->default_c;
			$this->function		=	$this->default_a;
			$this->param		=	array();
		}
	}
	
	/**
	 * 根据解析的URL获取Controller文件
	 */
	private function getActionFile(){
		
		$filename	=	$this->controller;
		$filename	=	$filename.$this->suffix;
		$file 		= 	$this->path.$this->url_dir.$filename; //站点控制器文件

		if(!file_exists($file)) {
			//$this->throwException("错误:找不到文件({$this->url_dir}{$filename})<br>");
			 header("location:index.php");
		}else{
			include_once($file);
		}

	}
	
	/**
	 * 执行Controller
	 */
	private function getActionClass(){
		
		$actionClass 	= 	$this->controller."_Action";
		if(!class_exists($actionClass)) {
			$this->throwException("错误:类不存在( class ".$actionClass." )");
		}else{
			$class 		= new $actionClass();
			if(!method_exists($class,$this->function)){
				$this->throwException("错误:控制器中缺少函数》> {$this->function}");
			}
			if($this->param==null){
				$fun = $this->function;
				$class->$fun();
			}else{
				call_user_func_array(array($class,$this->function),$this->param);
			}
		}
		
	}
	
	/**
     * 抛出一个错误信息
     *
     * @param string $message
     * @return void
     */
	private function throwException($message) {
		$INS    = XcInstance::getInstance();
		$INS->G('XcError')->ShowError($message);
		exit();
		//throw new Exception($message);
	}
	
}