<?php
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
			'_type'=>'LEFT'
		),
		
		'Account'	=>	array(
			'account_name',
			'_on'	=>	'Balance.account_id=Account.account_id'
		),
		
		'Claim'	=>	array(
			'claim_id',
			'_on'	=>	'Balance.account_id=Account.account_id'
		),
		
		'Client'	=>	array(
			'client_name',
			'_on'	=>	'Balance.other_party_id=Client.client_id'
		),
		
		'Member'	=>	array(
			'member_name',
			'_on'	=>	'Balance.claimer_id=Member.member_id'
		),	
		
		'CostCenter'	=>	array(
			'cost_center_name',
			'_on'	=>	'Balance.cost_center_id=CostCenter.cost_center_name'
		),	
	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['deal_date']	=	'desc';
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
		$order['deal_date']	=	'desc';	
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
}