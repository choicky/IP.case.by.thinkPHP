<?php
namespace Home\Model;

//因为启动数据表视图模型，必须继承ViewModel，注释Model
//use Think\Model;
use Think\Model\ViewModel;

class BillViewModel extends ViewModel {
	
	//定义Bill表与fee_phase表的视图关系
	protected $viewFields = array(
		'Bill'	=>	array(
			'bill_id',
			'handler_id',
			'bill_date',
			'client_id',
			'total_amount',
			'official_fee',
			'service_fee',
			'invoice_id',
			'_type'=>'LEFT'
		),
		
		'Member'	=>	array(
			'member_name',
			'_on'	=>	'Bill.handler_id=Member.member_id'
		),	
		
		'Client'	=>	array(
			'client_name',
			'_on'	=>	'Bill.client_id=Client.client_id'
		),
	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['bill_date']	=	'desc';
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
		$order['bill_date']	=	'desc';	
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
}