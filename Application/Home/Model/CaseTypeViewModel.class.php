<?php
namespace Home\Model;
use Think\Model\ViewModel;

class CaseTypeViewModel extends ViewModel {
	
	//定义本数据表的视图关系
	protected $viewFields = array(
		'CaseType'	=>	array(
			'case_type_id',
			'case_type_name',
			'case_group_id',
			'_type'=>'LEFT'
		),
		
		'CaseGroup'	=>	array(
			'case_group_name',
			'_on'	=>	'CaseType.case_group_id=CaseGroup.case_group_id'
		),	
	
	);
	
	//返回本数据视图的基本数据
	public function listBasic() {
		$order['case_type_name']	=	'desc';
		$list	=	$this->field('case_type_id,case_type_name')->order($order)->select();
		return $list;
	}
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['case_type_name']	=	'desc';
		$list	=	$this->field(true)->order($order)->select();
		return $list;
	}

	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['case_type_name']	=	'desc';		
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
}