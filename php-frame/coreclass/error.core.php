<?php
	/**
	 * 错误信息提示类
	 * @author  http://xiaocai.name
	 * @date	2011-4-24
	 * @version 1.9
	 */
	class XcError{
		
		function __construct(){
			
		}
		
		/**
		 * 显示错误信息
		 * @param  $message  消息内容
		 * @param  $isexit	  是否中断程序
		 */
		function ShowError($message,$isexit=false){
			$html="<div style='background-color:#FF9; border:#F90 1px solid; color:#000; font-size:12px; padding:5px; margin:10px;'>{$message}</div>";
			if(!$isexit){
				echo $html;
			}else{
				exit($html);
			}
		}
		
		static function DBerror($message,$isexit=false){
			$html="<div style='background-color:#FF9; border:#F90 1px solid; color:#000; font-size:12px; padding:5px; margin:10px;'>{$message}</div>";
			if(!$isexit){
				echo $html;
			}else{
				exit($html);
			}
		}
		
		/**
		 * 抛出一个错误
		 * @param  $message 内容
		 */
		function throwException($message){
			if($this->config['is_bug']===false){
				return;
			}
			throw new Exception($message);
		}
		
		/**
		 * 将一个错误信息写入日志文件
		 * @param  $message	 错误信息
		 */
		function LogError($message){
			
		}
		
	}
?>