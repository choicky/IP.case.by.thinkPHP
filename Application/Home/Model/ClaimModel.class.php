<?php
namespace Home\Model;

//因为数据表关联，必须继承RelationModel，注释Model
//use Think\Model;
use Think\Model\RelationModel;

class ClaimModel extends RelationModel {
	
	//更新本数据表中主键为$claim_id的记录，$data是数组
	public function edit($claim_id,$data){
		$Model	=	M('Claim');
		$map['claim_id']	=	$claim_id;
		$result	=	$Model->where($map)->save($data);
		return $result;
	}
}