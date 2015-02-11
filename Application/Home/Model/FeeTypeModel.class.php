<?php
namespace Home\Model;

//因启用了模型视图，所以禁用了关联定义
use Think\Model;
//use Think\Model\RelationModel;

class FeeTypeModel extends Model {
	
	/* 启用了模型视图，所以禁用了关联定义
	//定义fee_type表与fee_phase表的关联性
	protected $_link = array(
		'FeePhase'	=>	array(
			'mapping_type'		=>	self::BELONGS_TO,
			'class_name'		=>	'FeePhase',
			'foreign_key'		=>	'fee_phase_id',
			'mapping_fields'	=>	'fee_phase_name',
			'as_fields'			=>	'fee_phase_name',
		),		
	);
	*/
	
	//向fee_type表插入记录，$data是数组，且不包含主键
	public function addFeeType($data){
		$result	=	$this->add($data);
		return $result;
	}
	
	//更新fee_type表中主键为$fee_type_id的记录，$data是数组
	public function editFeeType($fee_type_id,$data){
		$map['fee_type_id']	=	$fee_type_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}