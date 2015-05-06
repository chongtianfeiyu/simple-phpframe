<?php
/**
 * 数据库配置文件
 * @author  http://xiaocai.name
 * @date	2011-4-24
 * @version 1.9
 */

#******************************************
# 默认使用哪个数据库配置
#******************************************
	$db['db_group']	='default';
	$db['isbug']   	= true;
	
#******************************************
# default默认数据库配置
#******************************************

if($_SERVER['SERVER_NAME']=='xxxxxxxxx'){

	/* 数据库的位置 */
	$db['default']['hostname'] 	= SAE_MYSQL_HOST_M.':'. SAE_MYSQL_PORT;
	/* 账户 */
	$db['default']['username'] 	= SAE_MYSQL_USER;
	/* 密码 */
	$db['default']['password'] 	= SAE_MYSQL_PASS;
	/* 数据库的名字 */
	$db['default']['database'] 	= SAE_MYSQL_DB;

}else{

	/* 数据库的位置 */
	$db['default']['hostname'] 	= '192.168.1.136';
	/* 账户 */
	$db['default']['username'] 	= 'root';
	/* 密码 */
	$db['default']['password'] 	= 'xiaocai';
	/* 数据库的名字 */
	$db['default']['database'] 	= 'cosplay_new';

}

	/* 你正在使用的数据库的类型,可选  MySQL、MySQLi、MsSQL */
	$db['default']['dbdriver'] 	= "mysql";
	/* 数据库表前缀 */
	$db['default']['dbprefix'] 	= "";
	/* 字符集 */
	$db['default']['char_set'] 	= "utf8";
	/* 字符集 */
	$db['default']['dbcollat'] 	= "utf8_general_ci";


#******************************************
# 如果有多个数据库可以按照以下规范填写
#******************************************
	$db['kuman.cc']['hostname'] 	= 	"";
	$db['kuman.cc']['username'] 	= 	"";
	$db['kuman.cc']['password'] 	= 	"";
	$db['kuman.cc']['database'] 	= 	"";
	$db['kuman.cc']['dbdriver'] 	= 	"";
	$db['kuman.cc']['dbprefix'] 	= 	"";
	$db['kuman.cc']['char_set'] 	= 	"";
	$db['kuman.cc']['dbcollat'] 	= 	"";
	

return $db;

?>