<?php
namespace Home\Model;
use Think\Model;
use Think\Model\RelationModel;

class CaseTypeModel extends RelationModel {
	public function listCaseType($p,$limit) {
		//$p为当前页数，$limit为每页显示的记录条数
		$data	= $this->order('convert(case_type_name using gb2312) asc')->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$data,"page"=>$show);
	}
	
	public function addCaseType($data){
		//$data是数组，且不包含主键
		$result	=	$this->add($data);
		return $result;
	}
	
	public function editCaseType($case_type_id,$data){
		//$case_type_id为主键，$data是数组，且不包含主键
		$map['case_type_id']	=	$case_type_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}