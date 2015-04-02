<?php
namespace Home\Model;
use Think\Model\RelationModel;

class CasePriorityModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		array('priority_date','strtotime',3,'function') , // 将 yyyy-mm-dd 转换时间戳
	);
	
	//定义本数据表的自动验证
	protected $_validate = array(
		 array('case_id','require','必须指明案号',1), //必须验证非空
		 array('priority_number','require','必须填写优先权号码',1), //必须验证非空
		 array('priority_date','require','必须填写优先权日期',1), //必须验证非空
		 array('priority_country_id','require','必须选择优先权国家/地区',1), //必须验证非空   
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
		
		'Country'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'Country',			//重新定义本数据关联的名称
			'class_name'		=>	'Country',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'priority_country_id',		//外键
			'mapping_fields'	=>	'country_name',	//关联字段
			//'as_fields'			=>	'country_name'	//字段别名
		),
		
	
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['priority_date']	=	'asc';
		$list	=	$this->relation(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['priority_date']	=	'asc';
		$list	=	$this->relation(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$case_priority_id 的记录，$data是数组
	public function update($case_priority_id,$data){
		$map['case_priority_id']	=	$case_priority_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}