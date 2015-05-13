<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | It's recommended to use more ViewModel and less RelationModel
// +----------------------------------------------------------------------
// | This file is required by: CaseController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class CaseModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		array('create_date','strtotime',3,'function,ignore') , // 将 yyyy-mm-dd 转换时间戳
		array('application_date','strtotime',3,'function,ignore') , // 将 yyyy-mm-dd 转换时间戳
		array('issue_date','strtotime',3,'function,ignore') , // 将 yyyy-mm-dd 转换时间戳
		array('expired_date','strtotime',3,'function,ignore') , // 将 yyyy-mm-dd 转换时间戳
	);
	
	//定义本数据表的自动验证
	protected $_validate = array(
		 array('our_ref','require','必须指明我方案号',1), //必须验证非空
		 array('case_type_id','require','必须指明案件类型',1), //必须验证非空
		// array('follower_id','require','必须指明跟案人/开案人',1), //必须验证非空
		// array('client_id','require','必须指明客户',1), //必须验证非空

   );
   
   //基于案号 $our_ref 返回对应的 $case_id 
	public function returnCaseId($our_ref){
		$map['our_ref']	=	$our_ref;
		$case_id_list	=	$this->field('case_id')->where($map)->find();
		$case_id	=	$case_id_list['case_id'];
		return $case_id;
	}
	
	/* ====为了简化，暂时省掉
	//定义本数据表的数据关联
	protected $_link = array(
		
		'CaseType'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CaseType',			//重新定义本数据关联的名称
			'class_name'		=>	'CaseType',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'case_type_id',		//外键，
			'mapping_fields'	=>	'case_type_name',	//关联字段
			//'as_fields'			=>	'case_type_name'	//字段别名
		),
		
		'Follower'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Follower',			//重新定义本数据关联的名称
			'class_name'		=>	'Member',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'follower_id',		//外键，
			'mapping_fields'	=>	'member_name',	//关联字段
			//'as_fields'			=>	'member_name'	//字段别名
		),
		
		'Client'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Client',			//重新定义本数据关联的名称
			'class_name'		=>	'Client',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'client_id',		//外键，
			'mapping_fields'	=>	'client_name',	//关联字段
			//'as_fields'			=>	'client_name'	//字段别名
		),
		
		'Applicant'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Applicant',			//重新定义本数据关联的名称
			'class_name'		=>	'Client',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'applicant_id',		//外键，
			'mapping_fields'	=>	'client_name',	//关联字段
			//'as_fields'			=>	'applicant_name'	//字段别名
		),
		
		'Handler'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Handler',			//重新定义本数据关联的名称
			'class_name'		=>	'Member',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'handler_id',		//外键，
			'mapping_fields'	=>	'member_name',	//关联字段
			//'as_fields'			=>	'handler_name'	//字段别名
		),
		
		'CaseExtend'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CaseExtend',		//重新定义本数据关联的名称
			'class_name'		=>	'CaseExtend',		//被关联的数据表
			'mapping_type'		=>	self::HAS_ONE,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'case_extend_id,expired_date,related_our_ref,remarks',		//关联字段
			'as_fields'	=>	'case_extend_id,expired_date,related_our_ref,remarks',		//字段别名
		),
		
		
		'CasePriority'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CasePriority',		//重新定义本数据关联的名称
			'class_name'		=>	'CasePriorityView',		//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'case_priority_id,priority_number,priority_date,priority_country_id,country_name',		//关联字段
			'mapping_order' => 'priority_date asc',		//排序
		),
		
		'CaseFile'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CaseFile',		//重新定义本数据关联的名称
			'class_name'		=>	'CaseFileView',		//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'case_file_id,file_type_name,oa_date,due_date,completion_date',		//关联字段
			'mapping_order' => 'completion_date asc',		//排序
		),
		
		'CaseFee'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CaseFee',		//重新定义本数据关联的名称
			'class_name'		=>	'CaseFeeView',		//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'case_fee_id,case_phase_name,fee_type_name,official_fee,service_fee,oa_date,due_date,allow_date,payer_name,case_payment_id,payment_date,bill_id,invoice_id,claim_id,Cost_name,cost_amount',		//关联字段
			'mapping_order' => 'due_date asc',		//排序
		),
		
	);
	======暂时省掉 */	
	
}