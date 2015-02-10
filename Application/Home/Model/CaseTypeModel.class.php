<?php
namespace Home\Model;

use Think\Model;
//因为没有数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class CaseTypeModel extends Model {
	
	//获取case_type表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listCaseType($p,$limit) {
		$case_type_list	= $this->order('convert(case_type_name using gb2312) asc')->page($p.','.$limit)->select();
		
		$case_type_count	= $this->count();
		
		$Page	= new \Think\Page($case_type_count,$limit);
		$show	= $Page->show();
		
		return array("case_type_list"=>$case_type_list,"case_type_page"=>$show);
	}
	
	//向case_type表插入记录，$data是数组，且不包含主键
	public function addCaseType($data){
		$result	=	$this->add($data);
		return $result;
	}
	
	//更新case_type表中主键为$case_type_id的记录，$data是数组
	public function editCaseType($case_type_id,$data){
		$map['case_type_id']	=	$case_type_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}