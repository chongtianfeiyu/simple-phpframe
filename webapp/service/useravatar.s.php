<?php

/**
* 用户头像相关接口
*/
class useravatar_Action extends Service{
	
	function index(){
		$this->ResultApi(
			1,
			'接口测试成功',
			array()
		);
	}

	/**
	 * 上传图片文件
	 */
	function UpImagesfile(){
		//登陆验证
		$this->islogin();
		$userid = $this->M->user->loginuser['user_id'];
		//$dir    = 'avatar'.DIRECTORY_SEPARATOR.substr($userid, -2).DIRECTORY_SEPARATOR;
		$dir      = 'temp'.DIRECTORY_SEPARATOR.'avatar_'.$userid;

		//接受参数
		$filename = isset($_GET['fname'])?$_GET['fname']:'upfileinput';
		if(!isset($_FILES[$filename])){
			$this->ResultApi(0,'文件不存在',array(),false);
			return;
		}

		//文件验证
		$this->load->library('useravatar');
		$this->L->useravatar->setFiles($_FILES[$filename]);
		$result = $this->L->useravatar->Check(1024000);
		if( $result['status'] <= 0 ){
			$this->ResultApi(0,$result['msg'],array(),false);
			return;
		}

		//上传
		$filename = $userid.'.gif';
		$result   = $this->L->useravatar->upload($dir,$filename);
		if(!$result){
			$this->ResultApi(0,'上传失败',array(),false);
		}

		//等比缩放图片
		$this->L->useravatar->zoomImages( $dir.$filename, $dir.$filename, 300,300 );

		//返回
		$this->ResultApi(1,'上传成功',array(
			'src' => UPLOAD_URL.$dir.$filename.'?rand='.rand(1,99)
		),false);

	}

	/**
	 * 根据预览图裁剪头像
	 */
	function CutAvatar(){
		//登陆验证
		$this->islogin();
		$userid   = $this->M->user->loginuser['user_id'];
		$dir      = 'temp'.DIRECTORY_SEPARATOR.'avatar_'.$userid;
		$filename = $userid.'.gif';

		$this->IsPost('img_w,img_h,img_x,img_y,img_src');

		$this->load->library('useravatar');
		$this->L->useravatar->CutImages(
			$dir.$filename,
			$_POST['img_w'],
			$_POST['img_h'],
			$_POST['img_x'],
			$_POST['img_y']
		);

		$this->ResultApi(1,'裁剪成功',$_POST);
	}




}
