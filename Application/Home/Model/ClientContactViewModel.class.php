<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: ,
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\ViewModel;

class ClientContactViewModel extends ViewModel {
	
	//定义本数据表的视图关系
	protected $viewFields = array(
		'ClientContact'	=>	array(
			'client_contact_id',
			'client_id',
			'contact_person',
			'contact_address',
			'contact_address_en',
			'update_date',			
			'_type'=>'LEFT'
		),
		
		'Client'	=>	array(
			'client_name',
			'_on'	=>	'Client.client_id=ClientContact.client_id',
			'_type'=>'LEFT'
		),	
		
		'ClientExtend'	=>	array(
			'client_name_en',
			'client_id_number',
			'client_business_number',
			'client_tax_number',
			'_on'	=>	'ClientExtend.client_id=ClientContact.client_id',
			'_type'=>'LEFT'
		),
	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['update_date']	=	'asc';
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
		$order['update_date']	=	'asc';		
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
}