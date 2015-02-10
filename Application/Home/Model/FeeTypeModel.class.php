<?php
namespace Home\Model;

//因为数据表关联，必须继承RelationModel，注释Model
//use Think\Model;
use Think\Model\RelationModel;

class FeeTypeModel extends RelationModel {
	
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
	
	//获取fee_type表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listFeeType($p,$limit) {
		$fee_type_list	= $this->relation(true)->order('convert(fee_name using gb2312) asc')->page($p.','.$limit)->select();
		
		$fee_type_count	= $this->count();
		
		$Page	= new \Think\Page($fee_type_count,$limit);
		$show	= $Page->show();
		
		return array("fee_type_list"=>$fee_type_list,"page"=>$show);
	}
	
	//向fee_type表插入记录，$data是数组，且不包含主键
	public function addFeeType($data){
		$result	=	$this->relation(true)->add($data);
		return $result;
	}
	
	//更新fee_type表中主键为$fee_type_id的记录，$data是数组
	public function editFeeType($fee_type_id,$data){
		$map['fee_type_id']	=	$fee_type_id;
		$result	=	$this->relation(true)->where($map)->save($data);
		return $result;
	}

}