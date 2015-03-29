<?php
namespace Home\Model;
use Think\Model\RelationModel;

class ClaimModel extends RelationModel {
	
	//定义本数据表的数据关联
	protected $_link = array(
		
		'Member'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Member',			//重新定义本数据关联的名称
			'class_name'		=>	'Member',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'claimer_id',		//外键
			'mapping_fields'	=>	'member_name',	//关联字段
			'as_fields'			=>	'member_name'	//字段别名
		),
		
		'Client'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Client',			//重新定义本数据关联的名称
			'class_name'		=>	'Client',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'client_id',		//外键
			'mapping_fields'	=>	'client_name',	//关联字段
			'as_fields'			=>	'client_name'	//字段别名
		),
		
		'CostCenter'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CostCenter',			//重新定义本数据关联的名称
			'class_name'		=>	'CostCenter',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'cost_center_id',		//外键
			'mapping_fields'	=>	'cost_center_name',	//关联字段
			'as_fields'			=>	'cost_center_name'	//字段别名
		),
		
		'Balance'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'Balance',		//重新定义本数据关联的名称
			'class_name'		=>	'Balance',		//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,		//主从关系的一对多关联
			'foreign_key'		=>	'balance_id',			//外键
			'mapping_fields'	=>	'deal_date,income_amount,outcome_amount',		//关联字段
			'as_fields'			=>	'deal_date,income_amount,outcome_amount'	//字段别名
		),
	
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['claim_date']	=	'desc';
		$list	=	$this->relation(true)->field(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['claim_date']	=	'desc';
		$list	=	$this->relation(true)->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$claim_id 的记录，$data是数组
	public function update($claim_id,$data){
		$map['claim_id']	=	$claim_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}