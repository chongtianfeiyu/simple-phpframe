<?php
/**
 * 文章的作品类型
 */
class WrType_Model extends Controller{
	function __construct(){
		parent::__construct();
	}

	/**
	 * 新增类型文章关联
	 */
	function add($aid,$typearr){
		foreach ($typearr as $key => $value) {
			$this->D->insert('km_works_type_article',array(
				'type_id' => $value,
				'ArId'	  => $aid
			));
		}
	}

	/**
	 * 删除类型文章关联
	 */
	function del($aid){
		$this->D->where('ArId',$aid)
				->delete('km_works_type_article');
	}

	/**
	 * 获得文章对应的类型
	 */
	function get($aid){
		if(intval($aid)==0){
			return false;
		}
		return $this->D->get('km_works_type_article')
					   ->join('km_cartoontype','`km_cartoontype`.`type_id`=`km_works_type_article`.`type_id`')
		               ->where('ArId',$aid)
		               ->toarray();
	}

}