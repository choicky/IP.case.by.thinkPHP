<?php
namespace Home\Model;

//use Think\Model;
//因为有数据表关联
use Think\Model\RelationModel;
class BillModel extends RelationModel {
	
	//定义本数据表的数据关联
	protected $_link = array(
		
		'BillInvoice'	=>	array(						//被关联的名称
			'mapping_name'		=>	'BillInvoice',		//重新定义被关联的名称
			'mapping_type'		=>	self::HAS_MANY,		//一对多关联
			'class_name'		=>	'BillInvoice',		//被关联的数据表
			'foreign_key'		=>	'bill_id',			//外键
			'mapping_fields'	=>	'bill_invoice_id,invoice_id',		//关联字段
			'as_fields'			=>	'bill_invoice_id,invoice_id'		//字段别名
		),
		
		'Member'	=>	array(							//被关联的名称
			'mapping_name'		=>	'Member',			//重新定义被关联的名称
			'mapping_type'		=>	self::BELONGS_TO,	//一对多关联
			'class_name'		=>	'Member',			//被关联的数据表
			'foreign_key'		=>	'handler_id',		//外键，
			'mapping_fields'	=>	'member_name',		//关联字段
			'as_fields'			=>	'member_name'		//字段别名
		),
		
		'Client'	=>	array(							//被关联的名称
			'mapping_name'		=>	'Client',			//重新定义被关联的名称
			'mapping_type'		=>	self::BELONGS_TO,	//一对多关联
			'class_name'		=>	'Client',			//被关联的数据表
			'foreign_key'		=>	'client_id',		//外键
			'mapping_fields'	=>	'client_name',		//关联字段
			'as_fields'			=>	'client_name'		//字段别名
		),		
	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['bill_date']	=	'desc';
		$list	=	$this->relation(true)->field(true)->order($order)->select();
		return $list;
	}
		
	//返回本数据视图的基本数据
	public function listBasic() {
		$list	=	$this->listAll();
		return $list;
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function pageList($p,$limit) {
		$order['bill_date']	=	'desc';	
		$list	=	$this->relation(true)->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$Bill_id的记录，$data是数组
	public function edit($bill_id,$data){
		$map['bill_id']	=	$bill_id;
		$result	=	$this->relation(true)->where($map)->save($data);
		return $result;
	}
}