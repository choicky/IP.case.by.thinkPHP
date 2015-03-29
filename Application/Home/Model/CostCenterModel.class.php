<?php
namespace Home\Model;

use Think\Model;
//因为没有数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class CostCenterModel extends Model {
	
	//返回本数据表的基本数据，可作为选单
	public function listBasic() {			
		$order['convert(cost_center_name using gb2312)']	=	'asc';
		$list	=	$this->field('cost_center_id,cost_center_name')->order($order)->select();
		return $list;
	}
	
	//返回本数据表的所有数据
	public function listAll() {
		$order['convert(cost_center_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->select();
		return $list;
	}
	
	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$Model	=	M('CostCenter');
		$order['convert(cost_center_name using gb2312)']	=	'asc';
		$list	= $Model->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新cost_center表中主键为$cost_center_id的记录，$data是数组
	public function update($cost_center_id,$data){
		$Model	=	M('CostCenter');
		$map['cost_center_id']	=	$cost_center_id;
		$result	=	$Model->where($map)->save($data);
		return $result;
	}

}