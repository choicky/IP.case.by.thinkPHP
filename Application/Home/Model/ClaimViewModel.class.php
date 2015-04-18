<?php
namespace Home\Model;
use Think\Model\ViewModel;

class ClaimViewModel extends ViewModel {
	
	//定义本数据表的视图关系
	protected $viewFields = array(
		'Claim'	=>	array(
			'claim_id',
			'claimer_id',
			'cost_center_id',
			'claim_date',
			'balance_id',
			'income_amount',
			'outcome_amount',
			'official_fee',
			'service_fee',
			'summary',
			'bill_id',
			'_type'=>'LEFT',
		),
		
		'CostCenter'	=>	array(
			'cost_center_name',
			'_type'=>'LEFT',
			'_on'	=>	'CostCenter.cost_center_id=Claim.cost_center_id'
		),
		
		'Member'	=>	array(
			'member_name',
			'_type'=>'LEFT',
			'_on'	=>	'Member.member_id=Claim.claimer_id',
			'_type'=>'LEFT'
		),			
	
	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['claim_date']	=	'desc';
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
		$order['claim_date']	=	'desc';		
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
}