<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CostController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class CostModel extends RelationModel {
	
	//定义本数据表的数据关联
	protected $_link = array(
		
		'CostCenter'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CostCenter',			//重新定义本数据关联的名称
			'class_name'		=>	'CostCenter',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO ,	//属于关系一对一关联			
			'foreign_key'		=>	'cost_center_id',		//外键，
			'mapping_fields'	=>	'cost_center_name',	//关联字段
			//'as_fields'			=>	'account_name'	//字段别名
		),
		
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['cost_date']	=	'desc';
		$list	=	$this->relation(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['cost_date']	=	'desc';
		$list	=	$this->relation(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
}