<?php
	/**
	 * MySql数据库基类
	 * @author  http://xiaocai.name
	 * @date	2011-4-25
	 * @version 1.9
	 */
	class xc_mysql_driver_db{
		
		protected $_mysql_conn = '';
		protected $config	   = '';
		protected $group	   = '';
		
		/**
		 * 构造
		 * @param $config	数据库配置
		 * @param $group	数据集
		 */
		function __construct(&$config){
			$this->config  = $config;
			$this->group   = $config['db_group'];
		}
		
		/**
		 * 执行SQL返回query结果
		 * @param  string $sql	SQL语句
		 * @return query
		 */
		public function Get_result($sql){
			//连接
			$this->db_connect();
			if(!$this->_mysql_conn){
				if($this->config['isbug']===true){
					XcError::DBerror('错误:未能连接上数据库集['.$this->group.'],请检查数据库配置是否正确.');
				}
				return false;
			}
			//选择数据库
			if(!$this->db_select($this->config[$this->group]['database'])){
				if($this->config['isbug']===true){
					XcError::DBerror('错误:数据库集['.$this->group.']没有找到名为['.$this->config[$this->group]['database'].']的数据库,请检查数据库配置是否正确.');
				}
				return false;
			}
			$this->db_set_charset();
			//执行
			return  $this->query(trim($sql));
		}
		
		/**
		 * 连接数据库
		 * @param  $hostname	地址:端口
		 * @param  $username	账号
		 * @param  $password	密码
		 * @param  $persistent  是否持久连接
		 */
		public function db_connect($persistent = false){
			if($persistent === true){
				$this->_mysql_conn = @mysql_pconnect($this->config[$this->group]['hostname'], $this->config[$this->group]['username'], $this->config[$this->group]['password']);
				return $this->_mysql_conn;
			}else{
				$this->_mysql_conn = @mysql_connect($this->config[$this->group]['hostname'], $this->config[$this->group]['username'], $this->config[$this->group]['password']);
				return $this->_mysql_conn;
			}
		}
		
		/**
		 * 选择数据库集
		 * @param $group	集合名称
		 */
		function selectDB($group){
			if(!isset($this->config[$group])){
				XcError::DBerror("配置文件中没有找到'{$group}'数据库集");
				return false;
			}
			$this->group   =   $group;
			return true;
		}
		
		/**
		 * 重新连接
		 */
		function reconnect(){
			if (mysql_ping($this->_mysql_conn) === FALSE){
				$this->_mysql_conn = FALSE;
			}
		}
		
		/**
		 * 选择数据库
		 * @param $database 数据库
		 */
		function db_select($database){
			return @mysql_select_db($database,$this->_mysql_conn);
		}
		
		/**
		 * 设置字符集
		 * @param  $charset 字符集
		 */
		function db_set_charset($charset = 'utf8'){
			return @mysql_query("SET NAMES '".$this->escape_str($charset)."'", $this->_mysql_conn);
			//return @mysql_query("SET NAMES '".$this->escape_str($charset)."' COLLATE '".$this->escape_str($collation)."'", $this->_mysql_conn);
		}
		
		/**
		 * 返回上次插入的id
		 */
		function insert_id(){
			return @mysql_insert_id($this->_mysql_conn);
		}
		
		/**
		 * 返回结果集中行的数目
		 * @param $result 结果集
		 */
		public function num_rows($result){
			return @mysql_num_rows($result);
		}
		
		/**
		 * 返回前一次 MySQL 操作所影响的记录行数
		 */
		function affected_rows(){
			$num = @mysql_affected_rows($this->_mysql_conn);
			if($num  == -1){
				return false;
			}else{
				return $num;
			}
		}
		
		/**
		 * 关闭数据库连接
		 */
		public function close(){
			@mysql_close($this->_mysql_conn);
		}
		
		/**
		 * 执行SQL
		 * @param $sql
		 */
		public function query($sql){
			$result = @mysql_query($sql,$this->_mysql_conn);
			if(!$result){
				if($this->config['isbug']===true){
					XcError::DBerror(mysql_error());
				}
			}
			return $result;
		}
		
		/**
		 * 将结果集转换成数组
		 * @param  $result
		 * @param  $result_type
		 */
		public function fetch_array(&$result, $result_type = MYSQL_ASSOC){
			$array = array();
			if(!$result){
				return false;
			}
			while($row=@mysql_fetch_array($result,$result_type)) {
	               $array[] = $row;
	         }
			return $array;
		}
		
		/**
		 * SQL语句字段转义
		 * @param $str
		 * @param $like
		 */
		function escape_str($str, $like = FALSE){
			if (is_array($str)){
				foreach ($str as $key => $val){
					$str[$key] = $this->escape_str($val, $like);
		   		}
		   		return $str;
		   	}
	
			if (function_exists('mysql_real_escape_string') AND is_resource($this->_mysql_conn)){
				$str = mysql_real_escape_string($str, $this->_mysql_conn);
			}elseif (function_exists('mysql_escape_string')){
				$str = mysql_escape_string($str);
			}else{
				$str = addslashes($str);
			}
	
			if ($like === TRUE){
				$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
			}
			return $str;
		}
		
		
		
	}