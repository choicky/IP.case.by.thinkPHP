<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CasePaymentController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class CasePaymentModel extends RelationModel {
	
	//定义本数据表的数据关联
	protected $_link = array(
		
		'CaseFee'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CaseFee',			//重新定义本数据关联的名称
			'class_name'		=>	'CaseFeePaymentView',			//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,	//属于关系一对一关联			
			'foreign_key'		=>	'case_payment_id',		//外键，
			'mapping_fields'	=>	'case_fee_id,case_id,official_fee,service_fee,oa_date,due_date,allow_date,payer_id,case_payment_id,bill_id,invoice_id,claim_id,cost_amount,our_ref,client_ref,tentative_title,application_date,application_number,formal_title,tm_category_id,publication_date,issue_date,case_phase_name,fee_type_name,fee_type_name_cpc,payer_name,cost_center_name,client_name,applicant_name,follower_name,handler_name,case_type_name,',	//关联字段
			//'as_fields'			=>	'account_name'	//字段别名
		),
		
		'Payer'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Payer',			//重新定义本数据关联的名称
			'class_name'		=>	'Payer',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO ,	//属于关系一对一关联			
			'foreign_key'		=>	'payer_id',		//外键，
			'mapping_fields'	=>	'payer_name',	//关联字段
			//'as_fields'			=>	'account_name'	//字段别名
		),
		
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['payment_date']	=	'desc';
		$list	=	$this->relation(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['payment_date']	=	'desc';
		$list	=	$this->relation(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
}