<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseFeeController,
// +----------------------------------------------------------------------

namespace Home\Model;

//因为启动数据表视图模型，必须继承 ViewModel ，注释 Model
//use Think\Model;
use Think\Model\ViewModel;

class CaseFeeTaskViewModel extends ViewModel {
	
	//定义 CaseFee 表与 Case 表的视图关系
	protected $viewFields = array(
		'CaseFee'	=>	array(
			'case_fee_id',
			'case_id',
			'case_phase_id',
			'fee_type_id',
			'official_fee',
			'service_fee',
			'oa_date',
			'due_date',
			'allow_date',
			'payer_id',
			'case_payment_id',
			'bill_id',
			'invoice_id',
			'claim_id',
			'cost_id',
			'cost_amount',
			'_type'=>'LEFT'
		),
		
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
			'publication_date',
			'issue_date',
			'_type'=>'LEFT',
			'_table'=>"__CASE__",	//定义数据表
			'_on'	=>	'CaseInfo.case_id=CaseFee.case_id'
		),
		
		'CasePhase'	=>	array(
			'case_phase_name',
			'_type'=>'LEFT',
			'_on'	=>	'CasePhase.case_phase_id=CaseFee.case_phase_id'
		),
				
		'FeeType'	=>	array(
			'fee_type_name',
			'fee_type_name_cpc',
			'_type'=>'LEFT',
			'_on'	=>	'FeeType.fee_type_id=CaseFee.fee_type_id'
		),	
		
		'Payer'	=>	array(
			'payer_name',
			'_type'=>'LEFT',
			'_on'	=>	'Payer.payer_id=CaseFee.payer_id'
		),
		
		'CasePayment'	=>	array(
			'payment_date',
			'_type'=>'LEFT',
			'_on'	=>	'CasePayment.case_payment_id=CaseFee.case_payment_id'
		),
		
		'Cost'	=>	array(
			'cost_name',
			'_type'=>'LEFT',
			'_on'	=>	'Cost.cost_id=CaseFee.cost_id'
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
		$order['our_ref']	=	'asc';
		$order['due_date']	=	'asc';
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
		$order['our_ref']	=	'asc';
		$order['due_date']	=	'asc';	
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPageSearch($p,$limit,$map) {
		$order['our_ref']	=	'asc';
		$order['due_date']	=	'asc';	
		$list	=	$this->field(true)->order($order)->where($map)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
}