<?php
/**
 * API接口授权配置
 * @author  http://xiaocai.name
 */

	$config['my.mmosite'] = array(
		'from'		=>	'MY.MMOSITE',
		'key'		=> 	'e77f6e5b9712c6ab1e4e921c6f45e09c',
		'Timediff'	=>	0	//服务器与客户端的时差(秒)
	);
	
	$config['test'] = array(
		'from'	=>	'test',
		'key'	=> 	'e77f6e5b9712c6ab1e4e921c6f45e09c',
		'Timediff'	=>	0
	);
	
	
	return $config;