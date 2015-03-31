<?php
namespace Home\Model;
use Think\Model\ViewModel;

class CaseViewModel extends ViewModel {
	
	//定义本数据表的视图关系
	protected $viewFields = array(
		'Patent'	=>	array(	//定义别名，这个别名不能与 PHP 内置关键词冲突
			'case_id',
			'our_ref',
			'case_type_id',
			'follower_id',
			'create_date',
			'form_date',
			'client_id',
			'client_ref',
			'applicant_id',
			'tentative_title',
			'handler_id',
			'application_number',
			'formal_title',
			'tm_category_id',
			'publication_date',
			'issue_date',
			'registration_date',
			'_table'=>"__CASE__",	//定义数据表
			'_type'=>'LEFT'
		),
		
		'CaseExtend'	=>	array(
			'expired_date',
			'related_our_ref',
			'remarks',
			'_on'	=>	'Patent.case_id=CaseExtend.case_id'
		),
		
		'CaseType'	=>	array(
			'case_type_name',
			'case_group_id',
			'_on'	=>	'Patent.case_type_id=CaseType.case_type_id'
		),
		
		'CaseGroup'	=>	array(
			'case_group_name',
			'_on'	=>	'CaseType.case_group_id=CaseGroup.case_group_id'
		),
		
		'Follower'	=>	array(
			'member_name'	=>	'follower_name',	//重新定义名称
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Patent.follower_id=Follower.member_id'
		),	
		
		'Client'	=>	array(
			'client_name',
			'_on'	=>	'Patent.client_id=Client.client_id'
		),
		
		'Applicant'	=>	array(
			'client_name'	=>	'applicant_name',	//重新定义名称
			'_table'=>"__CLIENT__",	//定义数据表
			'_on'	=>	'Patent.applicant_id=Applicant.client_id'
		),
		
		'Handler'	=>	array(
			'member_name'	=>	'handler_name',	//重新定义名称
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Patent.follower_id=Handler.member_id'
		),
		
		'TmCategory'	=>	array(
			'tm_category_number',
			'tm_category_name',
			'_table'=>"__TM_CATEGORY__",	//定义数据表
			'_on'	=>	'Patent.tm_category_id=TmCategory.tm_category_id'
		),


	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['our_ref']	=	'desc';
		$list	=	$this->field(true)->order($order)->select();
		return $list;
	}
		
	//返回本数据视图的基本数据
	public function listBasic() {
		$list	=	$this->listAll();
		return $list;
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['our_ref']	=	'desc';		
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
}