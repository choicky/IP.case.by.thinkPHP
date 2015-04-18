<?php
namespace Home\Model;
use Think\Model\RelationModel;

class ClaimModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		array('claim_date','strtotime',3,'function') , // 将 yyyy-mm-dd 转换时间戳
		array('income_amount','multiplyByHundred',3,'function') , // 将金额乘以100
		array('outcome_amount','multiplyByHundred',3,'function') , // 将金额乘以100
		array('official_fee','multiplyByHundred',3,'function') , // 将金额乘以100
		array('service_fee','multiplyByHundred',3,'function') , // 将金额乘以100	
	);
	
	//定义本数据表的自动验证
	protected $_validate = array(
		 array('claimer_id','require','必须指明认领人',1), //必须验证非空
		 array('cost_center_id','require','必须指明结算账户',1), //必须验证非空
		 array('claim_date','require','必须指明认领日期',1), //必须验证非空
		 array('balance_id','require','必须指明收支流水编号',1), //必须验证非空
		 //array('income_amount','require','未填写收入金额',0), //有字段时验证非空
		 //array('outcome_amount','require','未填写支出金额',0), //必须验证非空
		 //array('client_id','require','必须指明客户',1), //必须验证非空     
   );
   
   //定义本数据表的数据关联
	protected $_link = array(
		
		'Member'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Member',			//重新定义本数据关联的名称
			'class_name'		=>	'Member',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'claimer_id',		//外键
			'mapping_fields'	=>	'member_name',	//关联字段
			//'as_fields'			=>	'member_name'	//字段别名
		),
		
		'CostCenter'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CostCenter',			//重新定义本数据关联的名称
			'class_name'		=>	'CostCenter',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'cost_center_id',		//外键
			'mapping_fields'	=>	'cost_center_name',	//关联字段
			//'as_fields'			=>	'cost_center_name'	//字段别名
		),
		
		'Balance'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'Balance',		//重新定义本数据关联的名称
			'class_name'		=>	'Balance',		//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,		//主从关系的一对多关联
			'foreign_key'		=>	'balance_id',			//外键
			'mapping_fields'	=>	'deal_date,income_amount,outcome_amount',		//关联字段
			//'as_fields'			=>	'deal_date,income_amount,outcome_amount'	//字段别名
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
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}

}