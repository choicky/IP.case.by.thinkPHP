<?php
namespace Home\Model;

use Think\Model;
//因为没有数据表关联，注释掉RelationModel
//use Think\Model\RelationModel;

class BillModel extends Model {
	
	//更新本数据表中主键为$Bill_id的记录，$data是数组
	public function edit($bill_id,$data){
		$Model	=	M('Bill');
		$map['bill_id']	=	$bill_id;
		$result	=	$Model->where($map)->save($data);
		return $result;
	}
}