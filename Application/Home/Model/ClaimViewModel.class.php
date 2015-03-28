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
			'total_amount',
			'official_fee',
			'service_fee',
			'client_id',
			'_type'=>'LEFT'
		),
		
		'Member'	=>	array(
			'member_name',
			'_on'	=>	'Claim.claimer_id=Member.member_id'
		),	
		
		'Client'	=>	array(
			'client_name',
			'_on'	=>	'Claim.client_id=Client.client_id'
		),
		
		'CostCenter'	=>	array(
			'cost_center_name',
			'_on'	=>	'Claim.cost_center_id=CostCenter.cost_center_id'
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
		
		return array("list"=>$list,"page"=>$show);
	}
}