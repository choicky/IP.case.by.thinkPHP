<?php
namespace Home\Model;

use Think\Model;
//因为不需要数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class CaseTypeGroupModel extends Model {
	
	//返回本数据表的所有数据
	public function listAllPatent() {
		$map['case_type_group_name']	=	array('like','P%');
		$order['convert(case_type_group_name using gb2312)']	=	'asc';
		$data	=	$this->where($map)->order($order)->select();
		return $data;
	}
		
	//返回本数据表的基本数据
	public function listBasic() {
		$data	=	$this->listAll();
		return $data;
	}
	
	//分页返回本数据表的所有数据，$current_page 为当前页数，$recodes_per_page 为每页显示的记录条数
	public function listPage($current_page,$recodes_per_page) {
		$order['convert(case_type_group_name using gb2312)']	=	'asc';
		$data	= $this->order($order)->page($current_page.','.$recodes_per_page)->select();
		
		$count	= $this->count();
		$Page	= new \Think\Page($count,$recodes_per_page);
		$show	= $Page->show();
		
		return array("data"=>$data,"page"=>$show);
	}
	
	//更新本数据表中主键为$case_type_group_id的记录，$data是数组
	public function edit($case_type_group_id,$data){
		$map['case_type_group_id']	=	$case_type_group_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}