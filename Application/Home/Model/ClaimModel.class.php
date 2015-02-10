<?php
namespace Home\Model;

use Think\Model;
//因为没有数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class ClaimModel extends Model {
	
	//获取claim表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listClaim($p,$limit) {
		$claim_list	= $this->order('convert(claim_name using gb2312) asc')->page($p.','.$limit)->select();
		
		$claim_count	= $this->count();
		
		$Page	= new \Think\Page($claim_count,$limit);
		$show	= $Page->show();
		
		return array("claim_list"=>$claim_list,"page"=>$show);
	}
	
	//向claim表插入记录，$data是数组，且不包含主键
	public function addClaim($data){
		$result	=	$this->add($data);
		return $result;
	}
	
	//更新claim表中主键为$claim_id的记录，$data是数组
	public function editClaim($claim_id,$data){
		$map['claim_id']	=	$claim_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}