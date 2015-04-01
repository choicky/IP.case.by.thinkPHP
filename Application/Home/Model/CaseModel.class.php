<?php
namespace Home\Model;
use Think\Model\RelationModel;

class CaseModel extends RelationModel {
	
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
		
		'TmCategory'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'TmCategory',			//重新定义本数据关联的名称
			'class_name'		=>	'TmCategory',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'tm_category_id',		//外键，
			'mapping_fields'	=>	'tm_category_number',	//关联字段
			//'as_fields'			=>	'tm_category_number'	//字段别名
		),
		
		'CaseExtend'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CaseExtend',		//重新定义本数据关联的名称
			'class_name'		=>	'CaseExtend',		//被关联的数据表
			'mapping_type'		=>	self::HAS_ONE,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'expired_date,related_our_ref,remarks',		//关联字段
			'as_fields'	=>	'expired_date,related_our_ref,remarks',		//字段别名
		),
		
		'CasePriority'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CasePriority',		//重新定义本数据关联的名称
			'class_name'		=>	'CasePriority',		//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'priority_number,priority_date,priority_country_id',		//关联字段
			'mapping_order' => 'priority_date asc',		//排序
		),
		/*
		'CaseFee'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CaseFee',		//重新定义本数据关联的名称
			'class_name'		=>	'CaseFeeView',		//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'due_date',		//关联字段
			'mapping_order' => 'due_date asc',		//排序
		),
		
		'CaseFile'	=>	array(						//本数据关联的名称
			'mapping_name'		=>	'CaseFile',		//重新定义本数据关联的名称
			'class_name'		=>	'CaseFileView',		//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,		//主从关系的一对多关联
			'foreign_key'		=>	'case_id',			//外键
			'mapping_fields'	=>	'due_date',		//关联字段
			'mapping_order' => 'due_date asc',		//排序
		),
		*/

	
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['our_ref']	=	'desc';
		$list	=	$this->relation(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['our_ref']	=	'desc';
		$list	=	$this->relation(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$case_id的记录，$data是数组
	public function update($case_id,$data){
		$map['case_id']	=	$case_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}
	
	//返回本数据视图的所有专利数据
	public function listAllPatent() {
		$case_type_list	=	D('CaseTypeView')->listPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['our_ref']	=	'desc';
		$list	=	$this->relation(true)->where($map)->order($order)->select();

		return $list;
	}
	//分页返回本数据视图的所有专利数据
	public function listPagePatent($p,$limit) {
		$case_type_list	=	D('CaseTypeView')->listPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['our_ref']	=	'desc';
		$list	=	$this->relation(true)->where($map)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();	//待确定
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//返回本数据视图的所有商标数据
	public function listAllTrademark() {
		$case_type_list	=	D('CaseTypeView')->listTrademarkCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['our_ref']	=	'desc';
		$list	=	$this->relation(true)->where($map)->order($order)->select();

		return $list;
	}
		//分页返回本数据视图的所有商标数据
	public function listPageTrademark($p,$limit) {
		$case_type_list	=	D('CaseTypeView')->listTrademarkCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['our_ref']	=	'desc';
		$list	=	$this->relation(true)->where($map)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();	//待确定
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//返回本数据视图的所有商标数据
	public function listAllCopyright() {
		$case_type_list	=	D('CaseTypeView')->listCopyrightCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['our_ref']	=	'desc';
		$list	=	$this->relation(true)->where($map)->order($order)->select();

		return $list;
	}	
	//分页返回本数据视图的所有商标数据
	public function listPageCopyright($p,$limit) {
		$case_type_list	=	D('CaseTypeView')->listCopyrightCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['our_ref']	=	'desc';
		$list	=	$this->relation(true)->where($map)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();	//待确定
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}

}