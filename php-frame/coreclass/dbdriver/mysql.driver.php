<?php
	/**
	 * MySql数据库类
	 * @author  http://xiaocai.name
	 * @date	2011-4-25
	 * @version 1.9
	 */
	class xc_mysql_driver extends xc_mysql_driver_db{
		protected $sql_type				='';
		protected $sql_select_field		='';
		protected $sql_table			='';
		public $sql_limit			='';
		protected $sql_where			='';
		protected $sql_insert_field_key ='';
		protected $sql_insert_field_val ='';
		protected $sql_update_field		='';
		protected $sql_order			='';
		protected $sql_join				='';
		protected $sql_script			='';
		protected $sql_escape_char 		='`';
		protected $sql_bug				=false;
		
		function __construct(&$config){
			parent::__construct($config);
		}
		
		/**
		 * [内部方法]构造SQL语句
		 * @return sql语句
		 */
		function _sql($isclear=true){
			if($this->sql_type==''){
				$this->sql_type = 'SELECT';
			}
			$sql='';
			switch (strtoupper($this->sql_type)){
				//查询语句
	    		case "SELECT":
	    			if($this->sql_select_field=="")$this->sql_select_field="*";
	    			
	    			$sql.="SELECT ";						#SELECT
	    			$sql.=$this->sql_select_field;			#FIELD
	    			$sql.=" FROM ";							#FROM
	    			$sql.=$this->sql_escape_char.$this->sql_table.$this->sql_escape_char;					#TABLE
	    			$sql.=$this->sql_join;					#JOIN
	    			if($this->sql_where!=""){				#WHERE
	    				$sql.=" WHERE ";					#WHERE
	    				$sql.=$this->sql_where;				#WHERE
	    			}										#WHERE
	    			if($this->sql_order!=""){				#ORDER
	    				$sql.=" ORDER BY ";					#ORDER
	    				$sql.=$this->sql_order;				#ORDER
	    			}										#ORDER
	    			if($this->sql_limit!=""){				#LIMIT
		    			$sql.=" LIMIT ";					#LIMIT
		    			$sql.=$this->sql_limit;				#LIMIT
	    			}										#LIMIT
	    				  
	    			break;
	    		//插入语句
	    		case "INSERT":
	    			$sql.="INSERT INTO ";							#INSERT
	    			$sql.=$this->sql_escape_char.$this->sql_table.$this->sql_escape_char;							#TABLE
	    			$sql.=" (".$this->sql_insert_field_key.") "; 	#FIELD_NAME
	    			$sql.=" VALUES ";								#VALUES
	    			$sql.=" (".$this->sql_insert_field_val.") "; 	#FIELD_VALUE
	    			break;
	    		
	    		//删除语句
	    		case "DELETE":
	    			$sql.="DELETE FROM ";					#DELETE
	    			$sql.=$this->sql_escape_char.$this->sql_table.$this->sql_escape_char." ";				#TABLE
	    			if($this->sql_where!=""){				#WHERE
	    				$sql.=" WHERE ";					#WHERE
	    				$sql.=$this->sql_where;				#WHERE
	    			}										#WHERE
	    			if($this->sql_limit!=""){				#LIMIT
		    			$sql.=" LIMIT ";					#LIMIT
		    			$sql.=$this->sql_limit;				#LIMIT
	    			}										#LIMIT
	    			break;
	    		case "UPDATE":
	    			$sql.="UPDATE ";						#UPDATE
	    			$sql.=$this->sql_escape_char.$this->sql_table.$this->sql_escape_char." ";				#TABLE
	    			$sql.="SET ";							#SET
	    			$sql.=$this->sql_update_field;			#FIELD
	    			if($this->sql_where!=""){				#WHERE
	    				$sql.=" WHERE ";					#WHERE
	    				$sql.=$this->sql_where;				#WHERE
	    			}										#WHERE
	    			if($this->sql_limit!=""){				#LIMIT
		    			$sql.=" LIMIT ";					#LIMIT
		    			$sql.=$this->sql_limit;				#LIMIT
	    			}										#LIMIT
	    			
	    			break;
	    		case "SQL":
	    			$sql=$this->sql_script;
	    			break;
			}
			if($isclear===true){
				$this->_clear();
			}
			return $sql;
		}
		
		/**
		 * [内部方法]清除SQL记录
		 */
		function _clear(){
			$this->sql_type				="";
			$this->sql_select_field		="";
			$this->sql_table			="";
			$this->sql_limit			="";
			$this->sql_where			="";
			$this->sql_insert_field_key ="";
			$this->sql_insert_field_val ="";
			$this->sql_update_field		="";
			$this->sql_order			="";
			$this->sql_order_desc		="";
			$this->sql_join				="";
			//$this->sql_bug				=false;
		}
		
		/**
		 * 直接写SQL语句
		 * @param $sql	sql命令
		 */
		function sql($sql=''){
			if($sql==''){
				XcError::DBerror("错误:sql()方法中需要传入待执行的SQl命令.");
				return false;
			}
			$this->sql_type		=	"SQL";
			$this->sql_script	=	$sql;
			return $this;
		}
		
		/**
		 * 表名设置
		 * @param $tablename	设置表名
		 * @param $limit 		设置limit,可为空.
		 */
		function get($tablename,$limit=""){
			$this->sql_type		= "SELECT";
			$this->sql_table	= $tablename;
			if($this->sql_limit==''){
				if(is_array($limit)){
					$this->sql_limit=implode(",",$limit);
				}else{
					$this->sql_limit=$limit;
				}
			}
			return $this;
		}
		
		/**
		 * 关联查询
		 * @param string 关联表名名称
		 * @param string 关联条件
		 * @param string 关联类型
		 */
		function join($tablename,$where,$type=""){
			$where = $this->escape_str($where);
			$this->sql_join.=" ". $type ." JOIN `". $tablename ."` ON(". $where .") ";
			return $this;
		}
		
		/**
		 * 排序字段
		 * ->order('title desc, name asc');
		 * @param string $parameter 排序字段
		 */
		function order($parameter){
			$parameter = $this->escape_str($parameter);
			if($this->sql_order=="")
				$this->sql_order.=$parameter;
			else
				$this->sql_order.=" , ".$parameter;
			return $this;
		}
		
		/**
		 * 字段选择
		 * ->select('a1,a2,a3')
		 * ->select(array('a1','a2','a3'))
		 * @param $parameter 字段
		 */
		function select($parameter){
			$parameter = $this->escape_str($parameter);
			if($this->sql_select_field!="")
				$this->sql_select_field.=",";
			if(is_array($parameter)){
				$this->sql_select_field.=implode(",",$parameter);
			}else{
				$this->sql_select_field.=$parameter;
			}
			return $this;
		}
		
		/**
		 * [内部方法]查询条件设置
		 * @param $field	字段(字符串/数组)
		 * @param $value	值
		 * @param $operator 运算符,默认"AND"
		 * @example _where(字段,值) | _where(array('字段'=>'值')) | _where("字符串")
		 */
		function _where($field,$value="",$operator="AND"){
			if($value!=""){
				//格式:(字段,值)
				$field = $this->escape_str($field);
				$value = $this->escape_str($value);
				if($this->sql_where==""){
					$this->sql_where.=" `$field` = '$value' ";
				}else{
					$this->sql_where.="$operator `$field` = '$value' ";
				}
			}else if($value=="" && is_array($field)){
				//格式:(array('字段'=>'值'))
				$field = $this->escape_str($field);
				foreach ($field as $key=>$val){
					if($this->sql_where==""){
						$this->sql_where.=" `$key` = '$val' ";
					}else{
						$this->sql_where.="$operator `$key` = '$val' ";
					}
				}
			}else if($value=="" && !is_array($field)){
				//格式:("字符串")
				if($this->sql_where==""){
					$this->sql_where.=" $field ";
				}else{
					$this->sql_where.="$operator $field ";
				}
			}
			return $this;
		}
		
		/**
		 * 查询条件AND
		 * @param $field	字段(字符串/数组)
		 * @param $value	值,默认为空.为空时第一个参数可以是数组或WHERE语句.
		 */
		function where($field,$value=""){
			$this->_where($field,$value,"AND");
			return $this;
		}
		
		/**
		 * 查询条件OR
		 * @param $field	字段(字符串/数组)
		 * @param $value	值,默认为空.为空时第一个参数可以是数组或WHERE语句
		 */
		function where_or($field,$value=""){
			$this->_where($field,$value,"OR");
			return $this;
		}
		
		/**
		 * LIMIT 设置
		 * @param $value1	字段(字符串/数组)
		 * @param $value2	值,默认为空.为空时第一个参数可以是数组或LIMIT语句
		 */
		function limit($value1,$value2=""){
			$value1 = $this->escape_str($value1);
			$value2 = $this->escape_str($value2);
			if($value2!=""){
				$this->sql_limit=$value1 .",". $value2;
			}else if($value2=="" && !is_array($value1)){
				$this->sql_limit=$value1;
			}else if($value2=="" && is_array($value1)){
				$this->sql_limit=implode(",",$value1);
			}
			return $this;
		}
		
		/**
		 * 插入数据
		 * @param $tablename	表名称
		 * @param $data			数据,格式:$data['字段名']="值";
		 */
		function insert($tablename,$data,$tosql=false){
			
			$data = $this->escape_str($data);
			$this->sql_type="INSERT";
	    	$this->sql_table=$tablename;
			$this->sql_insert_field_key="";
			$this->sql_insert_field_val="";
	    	foreach ($data as $key=>$value){
	    		$this->sql_insert_field_key.="`".$key."`,";
	    		$this->sql_insert_field_val.="'".$value."',";
	    	}
	    	$this->sql_insert_field_key = substr($this->sql_insert_field_key,0,strlen($this->sql_insert_field_key)-1);
	    	$this->sql_insert_field_val = substr($this->sql_insert_field_val,0,strlen($this->sql_insert_field_val)-1);
	    	
			if($this->ERROR_SHOW()){return false;}
			if($tosql===true){return $this->_sql();}
	    	$this->Get_result($this->_sql());
	    	return $this->insert_id();
		}

		/**
		 * 更新数据(需要配合->where()指定条件,否则将更新整张表的数据.)
		 * @param $tablename	表名称
		 * @param $data			数据,格式:$data['字段名']="值";
		 * @return 返回受影响的行数,返回0时记录为修改
		 */
		function update($tablename,$data,$tosql=false){
			
			$data = $this->escape_str($data);
	    	$this->sql_type="UPDATE";
	    	$this->sql_table=$tablename;
	    	$this->sql_update_field="";
	    	
	    	foreach ($data as $key=>$value){
	    		$this->sql_update_field.="`".$key."` = '".$value."',";
	    	}
	    	$this->sql_update_field = substr($this->sql_update_field,0,strlen($this->sql_update_field)-1);
	    	
			if($this->ERROR_SHOW()){return false;}
			if($tosql===true){return $this->_sql();}
	    	$this->Get_result($this->_sql());
	    	return $this->torows();
		}
		
		/**
		 * 删除数据(需要配合->where()指定条件,否则将删除整张表的数据.)
		 * @param $tablename	表名
		 */
		function delete($tablename,$tosql=false){
			$this->sql_type ="DELETE";
			$this->sql_table=$tablename;
			if($this->ERROR_SHOW()){return false;}
			if($tosql===true){return $this->_sql();}
			$this->Get_result($this->_sql());
			return $this->torows();
		}

		
		/**
		 * 不存在某个方法时
		 * @param $fun	函数名
		 * @param $ages 参数
		 */
		function __call($fun,$ages){
			XcError::DBerror("错误:数据库里没有'{$fun}()'这个操作(>_<)  [xc.mysql.1112]");
		}
		
		/**
		 * 输出sql
		 * @return string 输出sql
		 */
		function getsql(){
			return $this->_sql(false);
		}
		
		/**
		 * 获得当前查询条件的count(*)语句
		 */
		function getcountsql(){
			$temp1 = $this->sql_select_field;
			$temp2 = $this->sql_limit;
			$temp3 = $this->sql_order;
			$this->sql_select_field = ' count(*) as count ';
			$this->sql_limit		= '';
			$this->sql_order		= '';
			$sql =  $this->_sql(false);
			$this->sql_select_field = $temp1;
			$this->sql_limit        = $temp2;
			$this->sql_order        = $temp3;
			return $sql;
		}
		
		/**
		 * 返回sql的执行结果
		 */
		function toresult(){
			if($this->ERROR_SHOW()){return false;}
			return $this->Get_result($this->_sql());
		}
		
		/**
		 * 将sql执行结果转换成数据并返回
		 */
		function toarray(){
			if($this->ERROR_SHOW()){return false;}
			$result = $this->Get_result($this->_sql());
			$result = $this->fetch_array($result);
			if(function_exists('stripslashes_deep')){
				$result = stripslashes_deep($result);
			}
			return $result;
		}
		
		/**
		 * 返回记录总数
		 */
		function tocount(){
			if($this->ERROR_SHOW()){return false;}
			$this->sql_select_field = ' count(*) as count ';
			$array = $this->toarray();
			return intval($array[0]['count']);
		}
		
		/**
		 * 返回上一次操作受影响的行数(主要用于更新、删除等操作)
		 */
		function torows(){
			if($this->ERROR_SHOW()){return false;}
			return $this->affected_rows();
		}
		
		/**
		 * 返回结果集行数
		 */
		function tonum(){
			if($this->ERROR_SHOW()){return false;}
			$result = $this->Get_result($this->_sql());
			return $this->num_rows($result);
		}
		
		/**
		 * 返回json格式数据
		 */
		function tojson(){
			if($this->ERROR_SHOW()){return false;}
			$array = $this->toarray();
			if(function_exists('json_encode')){
				return json_encode($array);
			}else{
				//[待实现]若PHP中不支持json若没有开启json功能则使用libraries中的json函数
				XcError::DBerror("错误:抱歉当前php不支持json_encode().");
				return false;
			}
			
		}
		
		/**
		 * 返回单条记录
		 */
		function toone(){
			$this->limit(1);
			if($this->ERROR_SHOW()){return false;}
			$array = $this->toarray();
			if(empty($array)){
				return array();
			}else{
				return $array[0];
			}
		}
		
		/**
		 * 返回xml数据
		 */
		function toxml(){
			if($this->ERROR_SHOW()){return false;}
			XcError::DBerror("错误:抱歉,toxml()方法尚未实现.");
			return false;
		}
		
		/**
		 * 输出sql
		 * @return unknown
		 */
		function tosql(){
			return $this->_sql();
		}
		
		/**
		 * 返回一笔记录某字段的值
		 * @param $ield 待返回的字段名
		 */
		function tostring($ield=null){
			$this->limit(1);
			if($this->ERROR_SHOW()){return false;}
			if($ield==null){
				XcError::DBerror("错误:tostring() 你必须传入'待返回的字段名'参数");
				return false;
			}
			$array = $this->toarray();
			if(!isset($array[0][$ield])){
				return false;
			}
			return $array[0][$ield];
		}
		
		/**
		 * 以int格式返回一笔记录某字段的值
		 * @param $ield 待返回的字段名
		 */
		function toint($ield=null){
			if($this->ERROR_SHOW()){return false;}
			if($ield==null){
				XcError::DBerror("错误:toint() 你必须传入'待返回的字段名'参数");
				return false;
			}
			
			return intval($this->tostring($ield));
		}

		function OPEN_BUG(){
			$this->sql_bug = true;
		}
		
		function CLOSE_BUG(){
			$this->sql_bug = false;
		}
		
		function ERROR_SHOW(){
			if($this->sql_bug){
				XcError::DBerror($this->_sql());
				return true;
			}
			return false;
		}
}

//对数组使用 stripslashes()
if(!function_exists('stripslashes_deep')){
	function stripslashes_deep($value){
	    $value = is_array($value) ?
	                array_map('stripslashes_deep', $value) :
	                stripslashes($value);
	    return $value;
	}
}