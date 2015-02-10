<?php
namespace Home\Model;

use Think\Model;
//因为没有数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class MemberModel extends Model {
	
	//获取member表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listMember($p,$limit) {
		$member_list	= $this->order('convert(member_name using gb2312) asc')->page($p.','.$limit)->select();
		
		$member_count	= $this->count();
		
		$Page	= new \Think\Page($member_count,$limit);
		$show	= $Page->show();
		
		return array("member_list"=>$member_list,"member_page"=>$show);
	}
	
	//向member表插入记录，$data是数组，且不包含主键
	public function addMember($data){
		$result	=	$this->add($data);
		return $result;
	}
	
	//更新member表中主键为$member_id的记录，$data是数组
	public function editMember($member_id,$data){
		$map['member_id']	=	$member_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}