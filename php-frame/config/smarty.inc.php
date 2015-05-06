<?php
/**
 * Smarty模板配置
 * @author  http://xiaocai.name
 * @date	2011-6-27
 * @version 2.1
 */
#******************************************
# 模板路径配置
#******************************************
	/* smarty在框架中的位置 */
	$config['smarty_path']				=	ABS_PATH.'vendor/smarty/';
	
	/* Smarty.class.php */
	$config['smarty_class_path']		=	ABS_PATH.'vendor/smarty/Smarty.class.php';
	
	/* 模板文件默认存放路径 */
	if(defined('SITE_ABS_PATH')){
		$config['view_tpldir']			=	SITE_ABS_PATH.'view/';
	}else{
		$config['view_tpldir']			=	ABS_PATH.'view/';
	}
	
	
	/* 编译后的模板文件存放路径 */
	$config['view_objdir']				=	ABS_PATH.'sitedata/templates/';
	//$config['view_objdir']				=	"saemc://templates";
	

#******************************************
# 缓存配置
#******************************************
	/* 是否开启缓存(本地调试时不需要) */
	$config['view_iscache']				=	false;
	
	/* 缓存更新时间(秒) */
	$config['view_cachetime']			=	360;
	
	/* 缓存文件指定的目录 */
	$config['view_cachedir']			=	ABS_PATH.'sitedata/cache/smarty/';
	
	
#******************************************
# 
#******************************************
	/* 左标签  */
	$config['view_symbol_left']			=	'<#';
	
	/* 右标签  */
	$config['view_symbol_right']		=	'#>';
	
	/* 模板文件默认后缀 */
	$config['view_suffix']				=	'.html';
	
	
	return $config;