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
if($_SERVER['SERVER_NAME']=='xxxxxx.com'){
	/* 站点访问路径  */
	$config['http']					=   'http://xxxxxx.com/';
	
	/* 文件上传路径 */
    $config['upload_dir']			=  'saemc://upload/';

	/* 上传文件访问路径 */
	$config['upload_url']			=  'http://images.icosplay.cc/cosplaycc/';//'http://kuman-upload.stor.sinaapp.com/';

	define('IS_SAE' , true);

}else{
	
	$config['http']					=   'http://kuman.loc/';
	
	/* 文件上传路径 */
	$config['upload_dir']			=   SITE_DIR.'resources'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR;

	/* 上传文件访问路径 */
	$config['upload_url']			=  'http://www.xiaocai.cc/cosplay_v2/resources/upload/';

	define('IS_SAE' , false);
}
	/* 是否将文件存储到云存储中 */
	define('IS_OSS' , true);

#******************************************
# 缓存相关配置
#******************************************
if($_SERVER['SERVER_NAME']=='xxxxxxxx.com'){
	/* 缓存文件 */
	$config['view']['view_cahce']   =   "saemc://templates";
}else{
	$config['view']['view_cahce']   =   "saemc://templates";
}
	/* 模板文件路径  */
	$config['view']['view_path']    =    APPS_DIR.'view';
        

	/* 是否开启缓存(本地调试时不需要) */
	$config['view']['iscache']		=    false;
	
	/* 缓存过期时间 */
	$config['view']['cachetime']	=    360;

	/*  */
	$config['view']['left_delimiter']	=	'{%';
	$config['view']['right_delimiter']	=	'%}';


#******************************************
# 常量定义
#******************************************

	define('SITE_URL' ,$config['http']);
	define('ADMIN_URL',$config['http'].'index.php/admin/');
	define('UPLOAD_URL',$config['upload_url']);
	define('UPLOAD_DIR',$config['upload_dir']);
	
	return $config;
	
