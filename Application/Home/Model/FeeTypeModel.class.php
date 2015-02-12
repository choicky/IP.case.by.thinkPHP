<?php
namespace Home\Model;

//因启用了模型视图，所以禁用了关联定义
use Think\Model;
//use Think\Model\RelationModel;

class FeeTypeModel extends Model {
	
	//更新本数据表表中主键为$fee_type_id的记录，$data是数组
	public function edit($fee_type_id,$data){
		$Model	=	M('FeeType');
		$map['fee_type_id']	=	$fee_type_id;
		$result	=	$Model->where($map)->save($data);
		return $result;
	}
}