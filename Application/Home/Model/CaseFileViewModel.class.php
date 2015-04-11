<?php
namespace Home\Model;

//因为启动数据表视图模型，必须继承ViewModel，注释Model
//use Think\Model;
use Think\Model\ViewModel;

class CaseFileViewModel extends ViewModel {
	
	//定义本表与其他数据表的视图关系
	protected $viewFields = array(
		
		//定义本表与 CaseFile 表的视图关系
		'CaseFile'	=>	array(
			'case_file_id',
			'case_id',
			'file_type_id',
			'oa_date',
			'due_date',
			'completion_date',
			'_type'=>'LEFT'
		),
		
		//定义本表与 Country 表的视图关系
		'FileType'	=>	array(
			'file_type_name',
			'_on'	=>	'FileType.file_type_id=CaseFile.file_type_id'
		),

	);
	
			
	//返回本数据视图的基本数据
	public function listBasic() {
		$list	=	$this->listAll();
		return $list;
	}
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['priority_date']	=	'asc';
		$list	=	$this->order($order)->select();
		return $list;
	}

	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['priority_date']	=	'asc';	
		$list	=	$this->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show, "count"=>$count);
	}
}