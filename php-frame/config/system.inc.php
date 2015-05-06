<?php
/**
 * 框架配置文件
 * @author  http://xiaocai.name
 * @date	2011-4-19
 * @version 1.7
 */

#******************************************
# MVC相关配置
#******************************************
	/* 在URL无参数的情况下指定的默认控制器 */
	$config['controller']				=	'home';
	
	/* 在没有指定方法时的默认方法 */
	$config['function']					=	'index';
	
	/* MVC访问控制器下方法的前缀,为空则所有方法可被访问 */
	$config['prefix_function']  		=	'';
	
	/* 控制器文件的默认路径 */
	$config['controller_path']			=	APPS_DIR.'controllers'.DIRECTORY_SEPARATOR;
	
	/* 模型文件的默认路径 */
	$config['model_path']				=	APPS_DIR.'model'.DIRECTORY_SEPARATOR;
	
	/* 类库文件路径  */
	$config['library_path']				=	APPS_DIR.'library'.DIRECTORY_SEPARATOR;

	/* 接口文件路径 */
	$config['interface_path']			=	APPS_DIR.'interface'.DIRECTORY_SEPARATOR;
#******************************************
# Api service相关配置
#******************************************
	/* 在没有指定服务名称时的默认服务 */
	$config['serviceName']				=	'default';
	
	/* 在没有指定服务名称时的默认服务 */
	$config['serviceFun']				=	'index';
	
	/* 控制器文件的默认路径 */
	$config['service_path']				=	APPS_DIR.'service'.DIRECTORY_SEPARATOR;
	
#******************************************
# 视图模板相关配置
#******************************************
	/* 是否启用Simple模板引擎  */
	$config['view']['simple_is'] =  true;
	
	/* 是否启用smarty模板引擎  */
	$config['view']['smarty_is'] =  true;
	
	/* 模板文件路径  */
	$config['view']['view_path']    =	APPS_DIR.'view';
	
	/* 模板文件缓存路径  */
	$config['view']['view_cahce']	=	APPS_DIR.'resources'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'templates';
	//$config['view']['view_cahce']   =   "saemc://templates";
	
	/* 是否开启缓存(本地调试时不需要) */
	$config['view']['iscache']		=	false;
	
	/* 缓存过期时间 */
	$config['view']['cachetime']	=	360;
	
	
	/* 边界符 */
	$config['view']['left_delimiter']	=	'{%';
	$config['view']['right_delimiter']	=	'%}';
#******************************************
# 其它相关配置
#******************************************
	/* 站点访问路径  */
	$config['http']						=   'http://xiaocai.name';

	/* 框架版本  */
	$config['version']					=	'2.2';
	
	
	return $config;