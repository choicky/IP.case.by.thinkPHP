<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: InnerBalanceController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\ViewModel;

class InnerBalanceViewModel extends ViewModel {
	
	//定义 CaseFee 表与 Case 表的视图关系
	protected $viewFields = array(
		'InnerBalance'	=>	array(
			'inner_balance_id',
			'cost_center_id',
			'start_date',
			'end_date',
			'true_income_amount',
			'true_outcome_amount',
			'inner_income_amount',
			'inner_outcome_amount',
			'balance_amount',			
			'_type'=>'LEFT'
		),
		
		'CostCenter'	=>	array(
			'cost_center_name',
			'_on'	=>	'CostCenter.cost_center_id=InnerBalance.cost_center_id'
		),
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['start_date']	=	'desc';
		$list	=	$this->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['start_date']	=	'desc';
		$list	=	$this->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
}