<?php
namespace Home\Model;
use Think\Model\RelationModel;

class TmCategoryModel extends RelationModel {
	
	
	//返回本数据表的基本数据，可作为选单
	public function listBasic() {			
		$order['tm_category_number']	=	'asc';
		$list	=	$this->field('tm_category_id,tm_category_number')->order($order)->select();
		return $list;
	}
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['tm_category_number']	=	'asc';
		$list	=	$this->relation(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['tm_category_number']	=	'asc';
		$list	=	$this->relation(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//更新本数据表中主键为$balance_id的记录，$data是数组
	public function update($balance_id,$data){
		$map['balance_id']	=	$balance_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}