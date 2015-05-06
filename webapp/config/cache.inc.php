<?php
/**
 * 框架配置文件
 * @author  http://xiaocai.name
 * @date	2011-11-22
 * @version 2.0
 */

#******************************************
# 站点缓存配置
#******************************************
	/* 默认使用的缓存类型(即不使用->type()方法时默认指定的缓存类型) */
	$config['default']	 = 'file';  //缓存类型有 file,mongo,memcache (推荐mongo),kvdb(sae上代替mongo)
        

	/* 文件缓存  */
	$config['file']				=   array();
	$config['file']['dir']		=   SITE_DIR.'resources'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR;
	

	/* mongo缓存 */
	$config['mongo']			=	array();
	$config['mongo']['host']	=	'192.168.34.76:27027';	
	$config['mongo']['dbname']	=	'tmmo';				//库名
	$config['mongo']['collname']=	'cache';			//表名
	$config['mongo']['user']	=	'';
	$config['mongo']['pwd']		=	'';
	
	/* mencache缓存 */
	$config['memcache']				=	array();
	$config['memcache']['host'] 	= 	'127.0.0.1';
	$config['memcache']['port'] 	=   '11211';
	
	
	//自动判断
	if($_SERVER['SERVER_NAME']=='kuman.sinaapp.com' || $_SERVER['SERVER_NAME']=='kuman.cc'){
        $config['default']	 =   'kvdb';
        $config['file']		 =   'saemc://cache'; 
    }
        
	return $config;