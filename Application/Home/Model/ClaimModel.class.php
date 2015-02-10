<?php
namespace Home\Model;

//因为数据表关联，必须继承RelationModel，注释Model
//use Think\Model;
use Think\Model\RelationModel;

class ClaimModel extends RelationModel {
	
	//定义claim表与member表、Client表的关联性
	protected $_link = array(
		'Member'	=>	array(
			'mapping_type'		=>	self::BELONGS_TO,
			'class_name'		=>	'Member',
			'foreign_key'		=>	'member_id',
			'mapping_fields'	=>	'member_name',
			'as_fields'			=>	'member_name',
		),	
		
		'Client'	=>	array(
			'mapping_type'		=>	self::BELONGS_TO,
			'class_name'		=>	'Client',
			'foreign_key'		=>	'client_id',
			'mapping_fields'	=>	'client_name',
			'as_fields'			=>	'client_name',
		),	
	);
	
	//获取claim表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listClaim($p,$limit) {
		$claim_list	= $this->relation(true)->order('claim_date asc')->page($p.','.$limit)->select();
		
		$claim_count	= $this->count();
		
		$Page	= new \Think\Page($claim_count,$limit);
		$show	= $Page->show();
		
		return array("claim_list"=>$claim_list,"claim_page"=>$show);
	}
	
	//向claim表插入记录，$data是数组，且不包含主键
	public function addClaim($data){
		$result	=	$this->relation(true)->add($data);
		return $result;
	}
	
	//更新claim表中主键为$claim_id的记录，$data是数组
	public function editClaim($claim_id,$data){
		$map['claim_id']	=	$claim_id;
		$result	=	$this->relation(true)->where($map)->save($data);
		return $result;
	}

}