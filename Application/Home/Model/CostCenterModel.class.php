<?php
namespace Home\Model;

use Think\Model;
//因为没有数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class CostCenterModel extends Model {
	
	//获取cost_center表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listCostCenter($p,$limit) {
		$cost_center_list	= $this->order('convert(cost_center_name using gb2312) asc')->page($p.','.$limit)->select();
		
		$cost_center_count	= $this->count();
		
		$Page	= new \Think\Page($cost_center_count,$limit);
		$show	= $Page->show();
		
		return array("cost_center_list"=>$cost_center_list,"cost_center_page"=>$show);
	}
	
	//向cost_center表插入记录，$data是数组，且不包含主键
	public function addCostCenter($data){
		$result	=	$this->add($data);
		return $result;
	}
	
	//更新cost_center表中主键为$cost_center_id的记录，$data是数组
	public function editCostCenter($cost_center_id,$data){
		$map['cost_center_id']	=	$cost_center_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}