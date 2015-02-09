<?php
namespace Home\Model;
use Think\Model;
use Think\Model\RelationModel;

class FeeTypeModel extends RelationModel {
	protected $_link = array(
		'FeePhase'	=>	array(
			'mapping_type'		=>	self::BELONGS_TO,
			'class_name'		=>	'FeePhase',
			'foreign_key'		=>	'fee_phase_id',
			'mapping_fields'	=>	'fee_phase_name',
			'as_fields'			=>	'fee_phase_name',
		),
		
	);
	public function listFeeType($p,$limit) {
		//$p为当前页数，$limit为每页显示的记录条数
		$data	= $this->relation(true)->order('convert(fee_type_name using gb2312) asc')->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$data,"page"=>$show);
	}
	
	public function addFeeType($data){
		//$data是数组，且不包含主键
		$result	=	$this->relation(true)->add($data);
		return $result;
	}
	
	public function editFeeType($fee_type_id,$data){
		//$case_type_id为主键，$data是数组，且不包含主键
		$map['fee_type_id']	=	$fee_type_id;
		$result	=	$this->relation(true)->where($map)->save($data);
		return $result;
	}

}