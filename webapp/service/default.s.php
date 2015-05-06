<?php

/**
* 
*/
class default_Action extends Service{
	
	function index(){
		$this->ResultApi(
			1,
			'接口测试成功',
			array('1.统一的入口','2.授权管理','3.针对接口的优化')
		);
	}

	function fun1(){
		echo '3';
	}
}
