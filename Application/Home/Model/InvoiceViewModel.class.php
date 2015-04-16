<?php
namespace Home\Model;
use Think\Model\ViewModel;

class InvoiceViewModel extends ViewModel {
	
	//定义本数据表的视图关系
	protected $viewFields = array(
		'Invoice'	=>	array(
			'invoice_id',
			'invoice_number',
			'invoice_date',
			'client_id',
			'total_amount',
			'official_fee',
			'service_fee',
			'follower_id',
			'bill_id',
			'_type'=>'LEFT',
		),
		
		'Client'	=>	array(
			'client_name',
			'_type'=>'LEFT',
			'_on'	=>	'Client.client_id=Invoice.client_id',
		),
		
		'Member'	=>	array(
			'member_name',
			'_type'=>'LEFT',
			'_on'	=>	'Member.member_id=Invoice.follower_id',
			'_type'=>'LEFT'
		),	
		
		
		
		
	
	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['invoice_date']	=	'desc';
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
		$order['invoice_date']	=	'desc';		
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
}