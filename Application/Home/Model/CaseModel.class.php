<?php
namespace Home\Model;

//use Think\Model;
//因为有数据表关联
use Think\Model\RelationModel;
class CaseModel extends RelationModel {
	
	//定义本数据表的数据关联
	protected $_link = array(
		
		'CaseExtend'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CaseExtend',		//重新定义本数据关联的名称
			'class_name'		=>	'CaseExtend',		//被关联的数据表
			'mapping_type'		=>	self::HAS_ONE,		//主从关系的一对一关联			
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'tm_category_id,publication_date,registration_date,related_our_ref,remarks',		//关联字段
			'as_fields'			=>	'tm_category_id,publication_date,registration_date,related_our_ref,remarks'		//字段别名
		),
		
		'CaseType'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CaseType',			//重新定义本数据关联的名称
			'class_name'		=>	'CaseType',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'case_type_id',		//外键，
			'mapping_fields'	=>	'case_type_name',	//关联字段
			'as_fields'			=>	'case_type_name'	//字段别名
		),
		
		'CaseGroup'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CaseGroup',			//重新定义本数据关联的名称
			'class_name'		=>	'CaseGroup',			//被关联的数据表
			'mapping_type'		=>	self::MANY_TO_MANY,		//主从关系的一对多关联
			'relation_table'    =>  'tp_case_type', //此处应显式定义中间表名称，且不能使用C函数读取表前缀
			'foreign_key'		=>	'case_type_id',			//中间表与本表的外键
			'relation_foreign_key'  =>  'case_group_id', //中间表与目的表的外键
		),
		
		'Follower'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Follower',			//重新定义本数据关联的名称
			'class_name'		=>	'Member',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系的一对一关联
			'foreign_key'		=>	'follower_id',		//外键，
			'mapping_fields'	=>	'member_name',		//关联字段
			'as_fields'			=>	'follower_name'		//字段别名
		),
		
		'Handler'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Handler',			//重新定义本数据关联的名称
			'class_name'		=>	'Member',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系的一对一关联
			'foreign_key'		=>	'handler_id',		//外键，
			'mapping_fields'	=>	'member_name',		//关联字段
			'as_fields'			=>	'handler_name'		//字段别名
		),
		
		'Client'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Client',			//重新定义本数据关联的名称
			'class_name'		=>	'Client',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系的一对一关联
			'foreign_key'		=>	'client_id',		//外键
			'mapping_fields'	=>	'client_name',		//关联字段
			'as_fields'			=>	'client_name'		//字段别名
		),
		
		'Applicant'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Client',			//重新定义本数据关联的名称
			'class_name'		=>	'Client',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系的一对一关联
			'foreign_key'		=>	'applicant_id',		//外键
			'mapping_fields'	=>	'client_name',		//关联字段
			'as_fields'			=>	'applicant_name'	//字段别名
		),
		
		'CasePriority'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CasePriority',		//重新定义本数据关联的名称
			'class_name'		=>	'CasePriority',		//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'case_priority_id,priority_number,priority_country_id,priority_date',		//关联字段
			//'as_fields'			=>	'case_priority_id,priority_number,priority_country_id,priority_date'		//字段别名在一对多情况下无效
		),
		
		'Country'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Country',			//重新定义本数据关联的名称
			'class_name'		=>	'Country',			//被关联的数据表
			'mapping_type'		=>	self::MANY_TO_MANY,		//主从关系的一对多关联
			'relation_table'    =>  'tp_case_priority', //此处应显式定义中间表名称，且不能使用C函数读取表前缀
			'foreign_key'		=>	'case_id',			//中间表与本表的外键
			'relation_foreign_key'  =>  'priority_country_id', //中间表与目的表的外键
		),

		
		'CaseFee'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CaseFee',			//重新定义本数据关联的名称
			'class_name'		=>	'CaseFee',			//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'case_fee_id,fee_type_id,official_fee,service_fee,oa_date,due_date,allow_date,completion_date,payer_id,bill_id,invoice_id,claim_id',		//关联字段
			//'as_fields'			=>	'case_fee_id,fee_type_id,official_fee,service_fee,fee_oa_date,fee_due_date,fee_allow_date,fee_completion_date,payer_id,bill_id,invoice_id,claim_id'		//字段别名在一对多情况下无效
		),
		
		'FeeType'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'FeeType',			//重新定义本数据关联的名称
			'class_name'		=>	'FeeType',			//被关联的数据表
			'mapping_type'		=>	self::MANY_TO_MANY,		//主从关系的一对多关联
			'relation_table'    =>  'tp_case_fee', //此处应显式定义中间表名称，且不能使用C函数读取表前缀
			'foreign_key'		=>	'case_id',			//中间表与本表的外键
			'relation_foreign_key'  =>  'fee_type_id', //中间表与目的表的外键
		),
		
		'CaseFile'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CaseFile',		//重新定义本数据关联的名称
			'class_name'		=>	'CaseFile',		//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'case_file_id,file_type_id,oa_date,due_date,completion_date',		//关联字段
			//'as_fields'			=>	'case_file_id,file_type_id,file_oa_date,file_due_date,file_completion_date'		//字段别名字段别名在一对多情况下无效
		),
		
		
	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['convert(our_ref using gb2312)']	=	'desc';
		$list	=	$this->order($order)->select();
		return $list;
	}
		
	//返回本数据视图的基本数据
	public function listBasic() {
		$list	=	$this->listAll();
		return $list;
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function pageList($p,$limit) {
		$order['convert(our_ref using gb2312)']	=	'desc';	
		$list	=	$this->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$case_id的记录，$data是数组
	public function edit($case_id,$data){
		$map['case_id']	=	$case_id;
		$result	=	$this->relation(true)->where($map)->save($data);
		return $result;
	}	
	
	//返回本数据表中主键为$case_id的记录，$data是数组
	public function getByID($case_id){
		$map['case_id']	=	$case_id;
		$result	=	$this->relation(true)->field(true)->where($map)->select();
		return $result;
	}	
}