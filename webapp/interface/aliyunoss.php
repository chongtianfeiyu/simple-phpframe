<?php
require_once 'aliyun_oss/sdk.class.php';
/**
 * 阿里云存储接口类
 * @author xiaocai.name
 */
class aliyunoss{
	private $oss;
	private $bucket;
	function __construct() {
       $this->oss = new ALIOSS();
       //curl调试模式
       $this->oss->set_debug_mode(FALSE);
       //开启三级域名
       //$this->oss->set_enable_domain_style(TRUE);
       $this->bucket = 'cosplaycc';
    }

    /**
     * 设置bucket
     * @param $name 文件名称
     */
    function set_bucket($name){
    	$this->bucket = $name;
    }

    /* ---------------------------------------------------------------------------- */

    /**
     *  获得bucket列表
     */
    function list_bucket(){
    	$result = $this->oss->list_bucket();
    	$result->body = $this->xml_to_array($result->body);
    	return array(
    		'status' => $result->status,
    		'result' => $result->body['ListAllMyBucketsResult']['Buckets']['Bucket']
    	);
    }

    /**
     * 获得某个目录下的文件列表
     * @param $dir 路径(根目录则为空)
     * @param $max 最大文件数
     */
    function list_file($dir='',$max=25){
		$result = $this->oss->list_object($this->bucket,array(
			'delimiter' => '/',
			'prefix'    => $dir,
			'max-keys'  => $max,
			//'marker' => 'thum/',
		));
		$result->body = $this->xml_to_array($result->body);
		return array(
    		'status' => $result->status,
    		'result' => $result->body['ListBucketResult']['Contents']
    	);
    }

    /**
     * 以内容的方式上传文件
     * @param $file    文件存储路径
     * @param $content 内容
     * @param 文件长度
     */
    function upload_content($file,$content,$length=false){
        $file   = str_replace(DIRECTORY_SEPARATOR, '/', $file);
        $option = array(
            'content'   => $content
        );

        if($length!==false){
            $option['length'] = $length;
        }

		$result = $this->oss->upload_file_by_content($this->bucket,$file,$option);
		return array(
    		'status' => $result->status,
    		'result' => array(
    			'path'       => $file,
    			'oss_url'    => $result->header['_info']['url'],
    			'upload_url' => UPLOAD_URL.$file
    		)
    	);
    }

    /**
     * 以内容的方式上传文件
     * @param $loc_path 本地路径
     * @param $net_path 存储路径
     */
    function upload_file($net_path,$loc_path){
        $net_path = str_replace(DIRECTORY_SEPARATOR, '/', $net_path);
		$result   = $this->oss->upload_file_by_file($this->bucket,$net_path,$loc_path);
		return array(
    		'status' => $result->status,
    		'result' => array(
    			'path'       => $loc_path,
    			'oss_url'    => $result->header['_info']['url'],
    			'upload_url' => UPLOAD_URL.$loc_path
    		)
    	);
    }

    /**
     * 删除文件
     * @param $files string || array
     */
    function delete_file($files){
    	if( is_array($files) ){
			$result = $this->oss->delete_objects($this->bucket,$files,array(
				'quiet' => false
			));
    	}else{
			$result = $this->oss->delete_object($this->bucket,$files);
    	}
    	return array(
	    	'status' => $result->status,
	    	'result' => $result->isOk()
	    );
    }

    /**
     * 判断文件是否存在
     * @param $files 文件路径
     */
	function is_file($file){					
		$result = $this->oss->is_object_exist($this->bucket,$file);
		return array(
	    	'status' => $result->status
	    );
	}


	/**
     * 上传目录下的文件
     * @param $files      本地文件夹路径
     * @param $recursive  是否遍历文件夹
     * @param $exclude    排除的文件
     * ps: 文件名必须是英文否则会报错,上传后是传到根目录下。
     */
	function upload_dir($dir,$recursive = false,$exclude = ".|..|.svn"){
		$result = $this->oss->create_mtu_object_by_dir($this->bucket,$dir,$recursive,$exclude);
		return $result;
	}

	/**
	 * 上传大文件
	 * @param $loc_path  本地文件路径
	 * @param $save_path 存储目标路径
	 */
	function upload_multi_part($loc_path,$save_path){
		$options = array(
			ALIOSS::OSS_FILE_UPLOAD => $loc_path,
			'partSize' => 5242880,
		);
		$result = $this->oss->create_mpu_object($this->bucket, $save_path,$options);
		return $result;
	}


    /**
     * 生成私有bucket签名url
     */
	function get_sign_url($file,$timeout = 3600){
		$response = $$this->oss->get_sign_url($this->bucket,$file,$timeout);
		return $response;
	}

    /* ---------------------------------------------------------------------------- */

    /**
     * 将xml内容转换为array
     */
    private function xml_to_array( $xml ){
	    $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
	    if(preg_match_all($reg, $xml, $matches)){
	        $count = count($matches[0]);
	        $arr   = array();
	        for($i = 0; $i < $count; $i++){
	            $key = $matches[1][$i];
	            $val = $this->xml_to_array( $matches[2][$i] );  // 递归
	            if(array_key_exists($key, $arr)){
	                if(is_array($arr[$key])){
	                    if(!array_key_exists(0,$arr[$key])){
	                        $arr[$key] = array($arr[$key]);
	                    }
	                }else{
	                    $arr[$key] = array($arr[$key]);
	                }
	                $arr[$key][] = $val;
	            }else{
	                $arr[$key] = $val;
	            }
	        }
	        return $arr;
	    }else{
	        return $xml;
	    }
	}

}

