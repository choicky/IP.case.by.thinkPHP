<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: MemberController,
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;
//因为没有数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class MemberModel extends Model {
	
	//返回本数据表的基本数据，可作为选单
	public function listBasic() {			
		$order['convert(member_name using gb2312)']	=	'asc';
		$list	=	$this->field('member_id,member_name')->order($order)->select();
		return $list;
	}
	
	//返回本数据表的所有数据
	public function listAll() {
		$order['convert(member_name using gb2312)']	=	'asc';
		$list	=	$this->order($order)->select();
		return $list;
	}
		
	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$Model	=	M('Account');
		$order['convert(member_name using gb2312)']	=	'asc';
		$list	= $this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//更新member表中主键为$member_id的记录，$data是数组
	public function update($member_id,$data){
		$Model	=	M('Member');
		$map['member_id']	=	$member_id;
		$result	=	$Model->where($map)->save($data);
		return $result;
	}
}