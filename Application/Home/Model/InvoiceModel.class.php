<?php
namespace Home\Model;
use Think\Model\RelationModel;

class InvoiceModel extends RelationModel {
	
	//定义本数据表的数据关联
	protected $_link = array(
		
		'Client'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Client',			//重新定义本数据关联的名称
			'class_name'		=>	'Client',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'client_id',		//外键，
			'mapping_fields'	=>	'client_name',	//关联字段
			'as_fields'			=>	'client_name'	//字段别名
		),
		
		'Follower'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Follower',			//重新定义本数据关联的名称
			'class_name'		=>	'Member',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'follower_id',		//外键，
			'mapping_fields'	=>	'member_name',	//关联字段
			'as_fields'			=>	'member_name'	//字段别名
		),

	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['invoice_date']	=	'desc';
		$list	=	$this->relation(true)->field(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['invoice_date']	=	'desc';
		$list	=	$this->relation(true)->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$balance_id的记录，$data是数组
	public function update($balance_id,$data){
		$map['balance_id']	=	$balance_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}