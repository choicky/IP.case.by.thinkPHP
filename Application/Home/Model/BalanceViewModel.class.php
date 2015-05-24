<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: BalanceController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\ViewModel;

class BalanceViewModel extends ViewModel {
	
	//定义本数据表的视图关系
	protected $viewFields = array(
		'Balance'	=>	array(
			'balance_id',
			'account_id',
			'deal_date',
			'income_amount',
			'outcome_amount',
			'summary',
			'other_party',
			'follower_id',
			'bill_id',
			'case_payment_id',
			'_type'=>'LEFT'
		),
		
		'Account'	=>	array(
			'account_name',
			'_type'=>'LEFT',
			'_on'	=>	'Account.account_id=Balance.account_id'
		),
		
		'Member'	=>	array(
			'member_name',
			'_type'=>'LEFT',
			'_on'	=>	'Member.member_id=Balance.follower_id'
		),
		
		'Bill'	=>	array(
			'bill_date',
			'total_amount'	=>	'bill_amount',
			'_type'=>'LEFT',
			'_on'	=>	'Bill.bill_id=Balance.bill_id'
		),
		
		'CasePayment'	=>	array(
			'payment_date',
			'total_amount'	=>	'payment_amount',
			'_type'=>'LEFT',
			'_on'	=>	'CasePayment.case_payment_id=Balance.case_payment_id'
		),
		
	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['deal_date']	=	'desc';
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
		$order['deal_date']	=	'desc';	
		$list	=	$this->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
}