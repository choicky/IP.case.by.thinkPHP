<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseFileTaskModel, CaseFeeTaskModel
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\ViewModel;

class CaseBasicViewModel extends ViewModel {
	
	//定义本数据表的视图关系
	protected $viewFields = array(
		'Case'	=>	array(	//定义别名，这个别名不能与 PHP 内置关键词冲突
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
			'_table'=>"__CASE__",	//定义数据表
			'_type'=>'LEFT'			//定义join类型
		),
		
		'CaseExtend'	=>	array(
			'expired_date',
			'related_our_ref',
			'remarks',
			'_on'	=>	'CaseExtend.case_id=Case.case_id',
			'_type'=>'LEFT'			//定义join类型
		),
		
		'CaseType'	=>	array(
			'case_type_name',
			'_on'	=>	'CaseType.case_type_id=Case.case_type_id',
			'_type'=>'LEFT'			//定义join类型
		),
		
		'Follower'	=>	array(
			'member_name'	=>	'follower_name',	//重新定义名称
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Follower.member_id=Case.follower_id',
			'_type'=>'LEFT'			//定义join类型
		),	
		
		'Client'	=>	array(
			'client_name',
			'_on'	=>	'Client.client_id=Case.client_id',
			'_type'=>'LEFT'			//定义join类型
		),
		
		'Applicant'	=>	array(
			'client_name'	=>	'applicant_name',	//重新定义名称
			'_table'=>"__CLIENT__",	//定义数据表
			'_on'	=>	'Applicant.applicant_id=Case.client_id',
			'_type'=>'LEFT'			//定义join类型
		),
		
		'Handler'	=>	array(
			'member_name'	=>	'handler_name',	//重新定义名称
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Handler.member_id=Case.handler_id',
			'_type'=>'LEFT'			//定义join类型
		),
		
		'TmCategory'	=>	array(
			'tm_category_number',
			'_table'=>"__TM_CATEGORY__",	//定义数据表
			'_on'	=>	'TmCategory.tm_category_id=Case.tm_category_id',
			'_type'=>'LEFT'			//定义join类型
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