<?php
namespace Home\Model;
use Think\Model\RelationModel;

class CaseFileModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		// 将 yyyy-mm-dd 转换时间戳
		array('oa_date','strtotime',3,'function') , 
		array('due_date','strtotime',3,'function') , 
		array('completion_date','strtotime',3,'function') , 
	);
	
	//定义本数据表的自动验证
	protected $_validate = array(
		 array('case_id','require','必须指明案号',1), //必须验证非空
		 array('file_type_id','require','必须选择文件名称',1), //必须验证非空
		 array('due_date','require','必须填写期限日',1), //必须验证非空
   );
   
   //定义本数据表的数据关联
	protected $_link = array(
		/*
		'Case'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Case',			//重新定义本数据关联的名称
			'class_name'		=>	'CaseView',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'case_id',		//外键
			'mapping_fields'	=>	'our_ref,client_name,client_ref,applicant_name',	//关联字段
			//'as_fields'			=>	'member_name'	//字段别名
		),*/
		
		
		'Case'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Case',			//重新定义本数据关联的名称
			'class_name'		=>	'Case',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'case_id',		//外键
			'mapping_fields'	=>	'our_ref,client_ref',	//关联字段
			//'as_fields'			=>	'member_name'	//字段别名
		),
		
		'FileType'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'FileType',			//重新定义本数据关联的名称
			'class_name'		=>	'FileType',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'file_type_id',		//外键
			'mapping_fields'	=>	'file_type_name',	//关联字段
			//'as_fields'			=>	'country_name'	//字段别名
		),
		
	
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['due_date']	=	'asc';
		$list	=	$this->relation(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['due_date']	=	'asc';
		$list	=	$this->relation(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
}