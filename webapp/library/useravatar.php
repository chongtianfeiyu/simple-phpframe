<?php
/**
 * 用户头像上传&处理类
 */
class UserAvatar{

	private $Files;
	private $Files_type;
	function setFiles($Files){
		$this->Files = $Files;
	}


	/**
	 * 上传文件
	 * @param $dir  文件夹
	 * @param $file 文件名
	 * @return true|false
	 */
	function upload($dir,$file){

		//默认存放在temp目录下
		if($dir==''){
			$dir = 'temp'.DIRECTORY_SEPARATOR;
		}

		//创建目录
		$newfilepath = UPLOAD_DIR.$dir;
		if (!file_exists($newfilepath)){
		    @mkdir($newfilepath,0755);
		}

		//上传文件
		$newfilepath .= $file;
		if(copy($this->Files['tmp_name'],$newfilepath)){
		   return true;
		}else{
		   return false;
		}
	}

	/**
	 * 将图片按比例缩放
	 * @param $srcImage  图片原路径
	 * @param $saveImage 存储图片路径
	 * @param $w  最大宽度
	 * @param $h  最大高度
	 */
	function zoomImages($srcImage,$saveImage,$w,$h){

		//初始化
		$image  = new Imagick();
		$image->readImage(UPLOAD_DIR.$srcImage);

		//获得原图片宽度高度
		$srcWH  = $image->getImageGeometry();
		$width  = $srcWH['width'];
		$height = $srcWH['height'];
		
		//宽高比 和 高宽比
		$whra  = number_format(($width/$height),1);
        $hwra  = number_format(($height/$width),1);

        //新图宽高
        $newwidth  = $width;
        $newheight = $height;
        if($width > $w || $height > $h){
        	if ($width > $height){
        	 	$newwidth  = $w;
             	$newheight = ceil($newwidth/$whra);
        	}elseif($width < $height) {
        		$newheight = $h;
             	$newwidth  = ceil($newheight/$hwra);
        	}else{
        		$newwidth  = $w;
             	$newheight = ceil($newwidth/$whra);
        	}
        }

        //保存新图
		$image->thumbnailImage( $newwidth, $newheight, true );
		$image->setImageFileName(UPLOAD_DIR.$srcImage);
		$image->writeImage();
	}

	function CutImages($srcImage,$w,$h,$x,$y){
		$image = new Imagick();
		$image->readImage(UPLOAD_DIR.$srcImage);
		$image->cropImage($w, $h, $x, $y);
		$image->setImageFileName(UPLOAD_DIR.$srcImage);
		$image->writeImage();
	}

	/**
	 * 检查上传文件合法性
	 * @param $fileSize 文件大小(默认5M)
	 */
	function Check($fileSize=5242880){

		if( empty($this->Files['size']) || 
			empty($this->Files['tmp_name']) || 
			!empty($this->Files['error'])) {
      		return array('status'=>0,'msg'=>'请检查要上传的文件.');
    	}

    	if( $this->GetFileType() === false ){
    		return array('status'=>0,'msg'=>'只允许上传.jpg .gif .png格式文件.');
    	}

    	if( $this->Files['size'] >= $fileSize ){
    		return array('status'=>0,'msg'=>'文件太大了.');
    	}

    	return array('status'=>1,'msg'=>'验证通过');
	}

	/**
	* 通过判断文件头部的前两个字节就能判断出文件的真实类型
	* @return String
	*/
	function GetFileType(){
		if(empty($this->Files['tmp_name'])){
	  		return false;
		}
	    $file     = fopen($this->Files['tmp_name'], "rb");    
	    $strInfo  = @unpack("C2chars", fread($file, 2) );// C为无符号整数
	    $typeCode = intval($strInfo['chars1'].$strInfo['chars2']); 
	    fclose($file);
	    if($typeCode == 255216){
			$this->Files_type = 'jpg';
			return 'jpg';
		}else if($typeCode == 7173){
	      	$this->Files_type = 'gif';
	      	return 'gif';
		}else if($typeCode == 13780) {
	   	  	$this->Files_type = 'png';
	   	  	return 'png';
	    }else{   
	      	return false;
	    }
	}

}