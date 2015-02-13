<?php
namespace Home\Model;

//因为数据表关联，必须继承RelationModel，注释Model
//use Think\Model;
use Think\Model\RelationModel;

class BalanceModel extends RelationModel {
	
	//更新本数据表中主键为$Balance_id的记录，$data是数组
	public function edit($balance_id,$data){
		$Model	=	M('Balance');
		$map['balance_id']	=	$balance_id;
		$result	=	$Model->where($map)->save($data);
		return $result;
	}
}