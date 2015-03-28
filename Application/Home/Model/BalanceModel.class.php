<?php
namespace Home\Model;
use Think\Model\RelationModel;

class BalanceModel extends RelationModel {
	
	//定义本数据表的数据关联
	protected $_link = array(
		
		'Account'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Account',			//重新定义本数据关联的名称
			'class_name'		=>	'Account',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'account_id',		//外键，
			'mapping_fields'	=>	'account_name',	//关联字段
			'as_fields'			=>	'account_name'	//字段别名
		),
		
		'Claim'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'Claim',		//重新定义本数据关联的名称
			'class_name'		=>	'ClaimView',		//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'balance_id',			//外键
			'mapping_fields'	=>	'claim_id,claimer_id,member_name,claim_date,total_amount,client_id,client_name',		//关联字段
		),
	
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['convert(deal_date)']	=	'desc';
		$list	=	$this->relation(true)->field(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['deal_date']	=	'desc';
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
	
	//删除本数据表中主键为$balance_id的记录
	public function delete($balance_id){
		$map['balance_id']	=	$balance_id;	//自定义 where() 语句的参数
		$result	=	$this->where($map)->delete();	//基于 where() 删除对应的数据，
		return $result;	//返回值为删除的记录数量
	}
}