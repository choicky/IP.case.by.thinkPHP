<?php
namespace Home\Model;

//因为启动数据表视图模型，必须继承ViewModel，注释Model
//use Think\Model;
use Think\Model\ViewModel;

class ClaimViewModel extends ViewModel {
	
	//定义claim表与fee_phase表的关联性
	protected $viewFields = array(
		'Claim'	=>	array(
			'claim_id',
			'claimer_id',
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
	);
	
	//获取claim表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listClaim($p,$limit) {
		$claim_list	=	$this->order(array('claim_date'=>'asc'))->page($p.','.$limit)->select();
		
		$claim_count	= $this->count();
		
		$Page	= new \Think\Page($claim_count,$limit);
		$show	= $Page->show();
		
		return array("claim_list"=>$claim_list,"claim_page"=>$show);
	}
}