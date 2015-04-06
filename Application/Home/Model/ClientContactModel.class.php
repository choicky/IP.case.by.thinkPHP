<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: ClientContactController,
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class ClientContactModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		array('update_date','time',3,'function') , // 将 yyyy-mm-dd 转换时间戳
	);

   
   //定义本数据表的数据关联
	protected $_link = array(
		
		'Client'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Client',			//重新定义本数据关联的名称
			'class_name'		=>	'Client',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'client_id',		//外键
			'mapping_fields'	=>	'client_name',	//关联字段
			//'as_fields'			=>	'member_name'	//字段别名
		),
		
		'ClientExtend'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'ClientExtend',			//重新定义本数据关联的名称
			'class_name'		=>	'ClientExtend',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'client_id',		//外键
			'mapping_fields'	=>	'client_name_en,client_id_number,client_business_number,client_tax_number',	//关联字段
			//'as_fields'			=>	'client_name'	//字段别名
		),
		
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['update_date']	=	'asc';
		$list	=	$this->relation(true)->field(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['update_date']	=	'asc';
		$list	=	$this->relation(true)->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}

}