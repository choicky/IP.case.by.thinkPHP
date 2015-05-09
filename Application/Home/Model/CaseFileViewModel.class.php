<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | It's recommended to use more ViewModel and less RelationModel
// +----------------------------------------------------------------------
// | This file is required by: CaseController,
// +----------------------------------------------------------------------

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
			'service_fee',
			'bill_id',
			'invoice_id',
			'claim_id',
			'inner_balance_id',
			'cost_amount',
			'_type'=>'LEFT'
		),
		
		//定义本表与 FileType 表的视图关系
		'FileType'	=>	array(
			'file_type_name',
			'_on'	=>	'FileType.file_type_id=CaseFile.file_type_id'
		),		
		
		//定义本表与 Case 表的视图关系
		'CaseInfo'	=>	array(
			'our_ref',
			'case_type_id',
			'follower_id',
			'client_id',
			'client_ref',
			'applicant_id',
			'tentative_title',
			'handler_id',
			'application_date',
			'application_number',
			'formal_title',
			'issue_date',
			'expired_date',
			'_type'=>'LEFT',
			'_table'=>"__CASE__",	//定义数据表
			'_on'	=>	'CaseInfo.case_id=CaseFile.case_id'
		),
		
		'Client'	=>	array(
			'client_name',
			'_type'=>'LEFT',
			'_on'	=>	'Client.client_id=CaseInfo.client_id'
		),
		
		'Applicant'	=>	array(
			'client_name'=>'applicant_name',
			'_type'=>'LEFT',
			'_table'=>"__CLIENT__",	//定义数据表
			'_on'	=>	'Applicant.client_id=CaseInfo.applicant_id'
		),
		
		'Follower'	=>	array(
			'member_name'=>'follower_name',
			'_type'=>'LEFT',
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Follower.member_id=CaseInfo.follower_id'
		),
		
		'Hanlder'	=>	array(
			'member_name'=>'handler_name',
			'_type'=>'LEFT',
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Hanlder.member_id=CaseInfo.handler_id'
		),
		
		'CaseType'	=>	array(
			'case_type_name',
			'_type'=>'LEFT',
			'_on'	=>	'CaseType.case_type_id=CaseInfo.case_type_id'
		),

	);	
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['due_date']	=	'asc';
		$order['our_ref']	=	'asc';
		$list	=	$this->order($order)->select();
		return $list;
	}
		
	//返回本数据视图的基本数据
	public function listBasic() {
		$list	=	$this->listAll();
		return $list;
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['due_date']	=	'asc';
		$order['our_ref']	=	'asc';	
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPageSearch($p,$limit,$map) {
		$order['due_date']	=	'asc';
		$order['our_ref']	=	'asc';	
		$list	=	$this->field(true)->order($order)->where($map)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
}