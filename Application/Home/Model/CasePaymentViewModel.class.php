<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CasePaymentController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\ViewModel;

class CasePaymentViewModel extends ViewModel {
	
	//定义 CaseFee 表与 Case 表的视图关系
	protected $viewFields = array(
		'CasePayment'	=>	array(
			'case_payment_id',
			'payment_name',
			'payment_date',
			'payer_id',
			'official_fee',
			'other_fee',
			'total_amount',			
			'_type'=>'LEFT'
		),
		
		'Payer'	=>	array(
			'payer_name',
			'_type'=>'LEFT',
			'_on'	=>	'Payer.payer_id=CasePayment.payer_id'
		),
		
		'Balance'	=>	array(
			'balance_id',
			'deal_date',
			'income_amount',
			'outcome_amount',
			'_on'	=>	'Balance.case_payment_id=CasePayment.case_payment_id'
		),
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['payment_date']	=	'desc';
		$list	=	$this->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['payment_date']	=	'desc';
		$list	=	$this->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
}