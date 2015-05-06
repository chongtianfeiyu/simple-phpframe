<?php
class XcCache{
	private $cache_type = 'mongo'; //缓存类型有 file,mongo,memcache
	private $config		= array();
	private $table		= ''; //mongo的表名
	function __construct(){
		$INS = &XcInstance::getInstance();
		$this->config     = $INS->G("XcConfig")->Config("cache");
		$this->cache_type = $this->config['default'];
		$this->table	  = $this->config['mongo']['collname'];
	}
	
	/**
	 * 还原默认类型(用于链式操作之后)
	 */
	function _clert(){
		$this->cache_type = $this->config['default'];
		$this->table	  = $this->config['mongo']['collname'];
	}
	
	/**
	 * 选择mongo表名
	 * @param $table 表名称
	 */
	function table($table){
		$this->table = $table;
		return $this;
	}
	
	/**
	 * 选择缓存类型
	 * @param $type
	 */
	function type($type){
		if(in_array($type,array('file','mongo','memcache'))){
			$this->cache_type = $type;
		}
		return $this;
	}
	
	/**
	 * 设置缓存
	 * @param	string	$key      缓存键值
	 * @param	mixed	$val      缓存内容
	 * @param	int	    $expire   缓存失效时间(0为永久缓存,单位秒)
	 * @param	string	$prefix   缓存前缀,未指定时使用统一的前缀
	 * @return	boolean
	 */
	function set($key,$val,$expire=0,$prefix=""){
		$key    = $prefix.$key;
		$obj    = $this->cache_type.'_set';
		$result = $this->$obj($key,$val,$expire);
		$this->_clert();
		return $result;
	}

	/**
	 * 获取缓存
	 * @param	string	$key      缓存键值
	 * @param	string	$prefix   缓存前缀,未指定时使用统一的前缀
	 * @return	mixed
	 */	
	function get($key, $prefix=""){
		$key    = $prefix.$key;
		$obj    = $this->cache_type.'_get';
		$result = $this->$obj($key);
		$this->_clert();
		return $result;
	}
	
	/**
	 * 获得缓存列表(不支持缓存前缀)
	 * @param $arrkey	键值数组
	 * @param $prefix	缓存前缀
	 */
	function getlist($arrkey){
		$obj    = $this->cache_type.'_getlist';
		$result = $this->$obj($arrkey);
		$this->_clert();
		return $result;
	}

	/**
	 * 删除缓存数据
	 * @param	string	$key      缓存键值
	 * @param	string	$prefix   缓存前缀,未指定时使用统一的前缀
	 * @return	boolean
	 */	
	function del($key, $prefix=""){
		$key    = $prefix.$key;
		$obj    = $this->cache_type.'_del';
		$result = $this->$obj($key);
		$this->_clert();
		return $result;
	}
	
	##########################################################
	# file缓存操作
	##########################################################
	/**
	 * 设置文件缓存
	 * @param  $key		键值
	 * @param  $val		数据
	 * @param  $expire  缓存时间,0为永久
	 */
	private function file_set($key, $val, $expire=0){
		//创建存储路径
		$dir = $this->config["file"]["dir"].$this->sub_dir($key).DIRECTORY_SEPARATOR;
		error_reporting(E_ALL);
		if(!is_dir($dir)){
    		$this->_mkdirs($dir, 0777);
    	}
    	
	    // 将数据序列化并压缩
        $cache_data = serialize($val);
        if(function_exists("gzcompress")){
            // 压缩数据
            $cache_data = gzcompress($cache_data,3);
        }
		
        // 将过期时间添加到缓存数据中，为0时表示永久保存
        $expire     = empty($expire) ? 0:time()+$expire;
        $cache_data = sprintf("%012d", $expire).$cache_data;
        
	    // 写入数据
    	if(@$fp = fopen($dir.md5($key), "w")){
    		flock($fp, LOCK_EX);    //进行排它型锁定
    		fwrite($fp, $cache_data);
    		flock($fp, LOCK_UN);   //释放锁定
    		fclose($fp);
    		return true;
    	}else{
    		return false;
    	}
	}
	/**
	 * 读取文件缓存
	 * @param  $key		键值
	 */
	private function file_get($key){            
    	$cache_file = $this->config["file"]["dir"].$this->sub_dir($key).DIRECTORY_SEPARATOR.md5($key);
		
        // 文件不存在
    	if(!file_exists($cache_file)){
    	    return FALSE;
    	}
            
        //文件是否过期
        $fp = fopen($cache_file , 'r'); 
        if(flock($fp , LOCK_SH)){// 进行共享锁定，写操作的“排它锁”需要等此锁解除后才可以加
           	$cache_data	= fread($fp,filesize($cache_file));
            flock($fp , LOCK_UN); // 释放锁定
        }
        fclose($fp);
        $expire     =  (int)substr($cache_data, 0, 12);
        if($expire != 0 && time() > $expire){
              //文件过期删除缓存文件，返回
              @unlink($cache_file);
              return FALSE;
        }
        
        //解压数据
        $cache_data = substr($cache_data, 12);
        if(function_exists('gzuncompress')){
            $cache_data = gzuncompress($cache_data); 
        }
        return unserialize($cache_data);
	}
	
	/**
	 * 获得缓存列表
	 */
	private function file_getlist($arrkey){
		$result = array();
		foreach ($arrkey as $key){
			$result[$val] = $this->file_get($key);
		}
		return $result;
	}
	
	/**
	 * 删除文件缓存
	 * @param  $key  键值
	 */
	private function file_del($key){
	    $cache_file = $this->config["file"]["dir"].$this->sub_dir($key).DIRECTORY_SEPARATOR.md5($key);  	
    	if(file_exists($cache_file)){
            return @unlink($cache_file);
        }
        return true;
	}
	
        
    ##########################################################
	# sae kvdb缓存操作
	##########################################################
        /**
	 * 设置磁盘件缓存
	 * @param  $key		键值
	 * @param  $val		数据
	 * @param  $expire  缓存时间,0为永久
	 */
	private function kvdb_set($key, $val, $expire=0){
              $kv  = new SaeKV();
              if($kv->init()==false){
          	 var_dump( $kv->errmsg() );
                 return false;
              }
              
              $setarr['{KEY}'] 	  = $key;
              $setarr['{VAL}']    = serialize($val);
              $setarr['{CTime}']  = time();
              $setarr['{ETime}']  = empty($expire) ? 0 : time() + $expire;
              
              $ret = $kv->get($key);
              if($ret==false){
          	  $ret = $kv->add($key,$setarr);
              }else{
              	  $ret = $kv->set($key,$setarr);
              }
              
              return $ret;
        }
        
        /**
	 * 读取磁盘缓存
	 * @param  $key		键值
	 */
	private function kvdb_get($key){  
              $kv  = new SaeKV();
              if($kv->init()==false){
          	 var_dump( $kv->errmsg() );
                 return false;
              }
              
              $info = $kv->get($key);
              if($info['{ETime}'] !=0 && $info['{ETime}'] <= time()){
              	   $kv->delete($key);
		   return false;
	      }
	      return unserialize($info['{VAL}']);
        }
        
    /**
	 * 获得多个磁盘缓存
	 */
	private function kvdb_getlist($arrkey){
        $kv  = new SaeKV();
        if($kv->init()==false){
          	var_dump( $kv->errmsg() );
            return false;
         }

        $result = $kv->mget($arrkey);
        $data   = array();
        if($result){
            foreach($arrkey as $key){

            	if( isset($result[$key]) ){
            		$val = $result[$key];
					if($val['{ETime}'] !=0 && $val['{ETime}'] <= time()){
	                    $kv->delete($val['{KEY}']);
	                    $data[$key] = false;
		      		}else{
	                    $data[$key] = unserialize($val['{VAL}']);
	                }
            	}else{
            		$data[$key] = false;
            	}

            }
            return $data;
         }else{
            return array();
        }
    }
        
    /**
	 * 删除磁盘缓存
	 * @param  $key  键值
	 */
    private function kvdb_del($key){
      $kv  = new SaeKV();
      if($kv->init()==false){
  	 	var_dump( $kv->errmsg() );
        return false;
      }
      return $kv->delete($key);
    }
        
        
        
	##########################################################
	# mongo缓存操作
	##########################################################
	/**
	 * 写入缓存
	 * @param  $key		键值
	 * @param  $val		数据
	 * @param  $expire  缓存时间,0为永久
	 */
	private function mongo_set($key, $val, $expire=0){
		//查询条件
	    $find['{KEY}']      = $key;
        $setarr['{KEY}'] 	= $key;
        $setarr['{VAL}']    = serialize($val);
        $setarr['{CTime}'] 	= time();
        $setarr['{ETime}']  = empty($expire) ? 0 : time() + $expire;
		$mongo = $this->mongo_init();
		
		//是否存在
	 	$isset = $mongo->findOne($find);
        if ( empty( $isset ) ) {
        	//插入
            $result = $mongo->insert($setarr);
        } else {
        	//更新
        	$result = $mongo->update($find, array('$set' => $setarr));
        }
        return $result;
	}
	
	/**
	 * 获得缓存
	 * @param  $key	键值
	 */
	private function mongo_get($key){
		$mongo = $this->mongo_init();
		$info  = $mongo->findOne(array('{KEY}'=>$key));
		if($info['{ETime}'] !=0 && $info['{ETime}'] <= time()){
			return false;
		}
		return unserialize($info['{VAL}']);
	}
	
	/**
	 * 获得缓存列表
	 * @param  $arrkey  数组格式的缓存键名
	 */
	private function mongo_getlist($arrkey){//return array();
		$mongo = $this->mongo_init();
		$cursor  = $mongo->find( array(
			'{KEY}'=>array(
				'$in'=>$arrkey
			)
		) );
		//$where = array('$or'=>array(array('{KEY}'=>'a'),array('{KEY}'=>'b')));
		$result   = array();
		foreach ($cursor as $id => $value) {
		   if($value['{ETime}'] !=0 && $value['{ETime}'] <= time()){
			   $result[$value['{KEY}']] = false;
		   }else{
		   	   $result[$value['{KEY}']] = unserialize($value['{VAL}']);
		   }
		}
		return $result;
	}
	
	/**
	 * 删除缓存
	 * @param  $key 键值
	 */
	private function mongo_del($key){
		$mongo = $this->mongo_init();
		return $mongo->remove(array('{KEY}'=>$key));
	}
	
	/**
	 * 连接mongoDb
	 * @param $table 表名
	 */
	public function &mongo_init(){
		$links = 'mongodb://';
		if( $this->config['mongo']['user'] != ''  &&  $this->config['mongo']['pwd'] != '' ){
			$links = $links.$this->config['mongo']['user']. ':' .$this->config['mongo']['pwd']. '@';
		}
		$links = $links.$this->config['mongo']['host'].'/'.$this->config['mongo']['dbname'];
		$conn  = new Mongo($links);
		$db    = $conn->selectDB($this->config['mongo']['dbname']);
		if($this->table==''){
			$this->table = $this->config['mongo']['collname'];
		}
		$collection = $db->selectCollection($this->table);
		return $collection;
	}
	
        
	##########################################################
	# memcache缓存操作
	##########################################################
	private function memcache_set(){
		echo 'memcache_set';
	}
	private function memcache_get(){
		echo 'memcache_set';
	}
	private function memcache_del(){
		
	}
	
	##########################################################
	# 辅助函数
	##########################################################
	/**
	 * md5字符串取得三级的子目录名
	 */
	private function sub_dir($str = "",$level=3){
		if($str == ""){
			return false;
		}else{
			$md5_str = md5($str);
			$sub_dir = "";
			for($i=0;$i<$level;$i++){
				$sub_dir .= ($i==0)?substr($md5_str,$i*2,2):DIRECTORY_SEPARATOR.substr($md5_str,$i*2,2); 
			}
			return $sub_dir;
		}
	}
	
	/**
	 * 建立多级目录
	 */
	private function _mkdirs($dir,$mode=0777){
		if(!is_dir($dir)){
			$this->_mkdirs(dirname($dir), $mode);
			mkdir($dir,$mode);
		}
		return ;
	}
	
}
?>