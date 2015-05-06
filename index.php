<?php
//
Session_start();
header("Content-Type:text/html;charset=utf-8");

if(Extension_Loaded('zlib')){
	Ob_Start('ob_gzhandler'); 
}

define('SITE_DIR',dirname(__FILE__).DIRECTORY_SEPARATOR);
define('APPS_DIR',SITE_DIR.'webapp'.DIRECTORY_SEPARATOR);

require 'php-frame/index.php';