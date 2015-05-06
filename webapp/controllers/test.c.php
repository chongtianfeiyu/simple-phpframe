<?php 
/**
 * 示例文件
 * @author xiaocai.name
 */
class Test_Action extends controller{


	function oss(){
		$this->load->Iport('aliyunoss');
		$result = $this->I->aliyunoss->list_file();
		//$result = $this->I->aliyunoss->upload_content('article/test.txt',time().'-');
		//$result = $this->I->aliyunoss->upload_file('article/test.jpg','E:\php\apache\htdocs\project-me\kuman\test.jpg');
		//$result = $this->I->aliyunoss->delete_file(array('123.txt','ew.txt'));
		//$result = $this->I->aliyunoss->is_file('t1.jpg');
		//$result = $this->I->aliyunoss->upload_dir('E:\php\apache\htdocs\project-me\kuman\dir\\');
		var_dump($result);
	}


	//控制器测试
	function index($p1='默认值1',$p2='默认值2'){
		echo time();
		var_dump($_GET);
		var_dump($_POST);
		var_dump($p1);
		var_dump($p2);
	}

	//模板测试
	function view(){

		$data[] = array('name'=>'xiaocai','age'=>15,'Date'=>time());
		$data[] = array('name'=>'xiaobai','age'=>20,'Date'=>time());
		$data[] = array('name'=>'diudiui','age'=>15,'Date'=>time());

		$this->V->Smarty->assign('data',$data);
		$this->V->Smarty->assign('name','STR STR STR');
		$this->V->Smarty->display('test-view.html');
	}

	//模型测试
	function model(){
		//模型
		$this->load->model('test');
		$result = $this->M->test->Fun1( rand(11,99) );
		var_dump($result);
	}

	//类库&接口测试
	function lib(){
		//加载类
		$this->load->library('test');
		$result = $this->L->test->GetEncoding();
		var_dump($result);

		//加载自定义类
		$this->load->library('test','EditClass');
		$result = $this->L->EditClass->GetEncoding();
		var_dump($result);

		//静态
		var_dump(EditClass::SGetEncoding());

		echo '<hr>';

		//加载接口类(实际上和library是一样的)
		$this->load->Iport('itest');
		$result = $this->I->itest->GetEncoding();
		var_dump($result);

	}

	//缓存测试
	function cache(){
		//读取单个缓存
		$this->cache->set('Test', time(), 0);
		$result = $this->cache->get('Test');
		var_dump($result);
		$this->cache->del('Test');
		$result = $this->cache->get('Test');
		var_dump($result);

		//读取缓存列表(本地文件存储不支持)
		$cachetype = $this->config->cache('default');
		$timeOut   = 60;
		if($cachetype!='file'){
			for ($i=0; $i <= 2 ; $i++) { 
				$this->cache->set('Test'.$i, rand(11,99), $timeOut);
			}
			$result = $this->cache->getlist(array('Test0','Test1','TestX','Test2'));
			var_dump($result);
		}

	}

	//配置测试
	function config(){
		//加载单项配置
		$cachetype = $this->config->cache('default');
		var_dump($cachetype);

		//加载整个配置
		$config	   = $this->config->cache();
		var_dump($config);
	}

	//语言包
	function lang(){
		$this->InitLang();

		//加载单项
		$lang = $this->lang->test('caption');
		var_dump($lang);

		//加载整个语言包
		$lang = $this->lang->test();
		var_dump($lang);

		//将配置项写入模板中
		$this->lang->assign('test','caption');
		$this->lang->assign('test');

	}

	//加载其他控制器里的方法
	function con(){
		$this->load->Controller('blog');
		$this->C->blog->index();
	}

	//数据库测试
	function database(){
		$sql = $this->D->get('user')->limit(1,10)->tosql();
		var_dump($sql);

		$this->D->OPEN_BUG();
		
		$reuslt = $this->D->get('user')->toarray();
		
		$reuslt = $this->D
		               ->where('User','root')
		               ->where(array('Select_priv','Y'))
		               ->where('age=17')
		               ->where_or('age=18')
					   ->where_or('age','20')
		               ->get('user')
		               ->toarray();
		
		$newid = $this->D->insert('user',array('name'=>'HUI'));
		
		
		$this->D->where("id",123)->update("user",array('name'=>date('H:i:s')));
		
		$this->D->where(array('id'=>123))->delete('user');
		
		$reuslt = $this->D->get('user')->join('role','user.r=role.id')->toarray();

		$this->D->CLOSE_BUG();

	}

	function upthum(){
		$list = $this->D->get('km_works_class')->toarray();
		foreach ($list as $key => $value) {
			$value['wc_thum_A'] = str_replace('cl/thumb/', 'thumb/cl/', $value['wc_thum_A']);
			$this->D->where('wc_id',$value['wc_id'])->update('km_works_class',array('wc_thum_A'=>$value['wc_thum_A']));
			echo $value['wc_thum_A'].'<br>';
		}
	}
}