<?php
namespace Home\Model;
use Think\Model\ViewModel;

class PatentViewModel extends ViewModel {
	
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
			'publication_date',
			'issue_date',			
			'_table'=>"__CASE__",	//定义数据表
			'_type'=>'LEFT'	//左连接 必须
		),
		
		'CaseExtend'	=>	array(
			'expired_date',
			'related_our_ref',
			'remarks',
			'_on'	=>	'Patent.case_id=CaseExtend.case_id',
			'_type'=>'LEFT'	//左连接 必须
		),
		
		'CaseType'	=>	array(
			'case_type_name',
			'case_group_id',
			'_on'	=>	'Patent.case_type_id=CaseType.case_type_id',
			'_type'=>'LEFT'	//左连接 必须,
		),
		
		'CaseGroup'	=>	array(
			'case_group_name',
			'_on'	=>	'CaseType.case_group_id=CaseGroup.case_group_id',
			'_type'=>'LEFT'	//左连接 必须
		),

		
		'Follower'	=>	array(
			'member_name'	=>	'follower_name',	//重新定义名称
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Patent.follower_id=Follower.member_id',
			'_type'=>'LEFT'	//左连接 必须
		),	
		
		'Client'	=>	array(
			'client_name',
			'_on'	=>	'Patent.client_id=Client.client_id',
			'_type'=>'LEFT'	//左连接 必须
		),
		
		'Applicant'	=>	array(
			'client_name'	=>	'applicant_name',	//重新定义名称
			'_table'=>"__CLIENT__",	//定义数据表
			'_on'	=>	'Patent.applicant_id=Applicant.client_id',
			'_type'=>'LEFT'	//左连接 必须
		),
		
		'Handler'	=>	array(
			'member_name'	=>	'handler_name',	//重新定义名称
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Patent.follower_id=Handler.member_id'
		),	

	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$case_type_list	=	D('CaseTypeView')->listPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['case_type_id']	=	'desc';
		$list	=	$this->where($map)->order($order)->select();

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