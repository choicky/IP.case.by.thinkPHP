<?php
namespace Home\Model;
use Think\Model;

class FeePhaseModel extends Model {
	
	//返回本数据表的所有数据
	public function listAll() {
		$Model	=	M('FeePhase');
		$order['convert(fee_phase_name using gb2312)']	=	'asc';
		$list	=	$Model->field(true)->order($order)->select();
		return $list;
	}
		
	//返回本数据表的基本数据
	public function listBasic() {
		$list	=	$this->listAll();
		return $list;
	}
	
	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function pageList($p,$limit) {
		$Model	=	M('FeePhase');
		$order['convert(fee_phase_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$fee_phase_id的记录，$data是数组
	public function edit($fee_phase_id,$data){
		$Model	=	M('FeePhase');
		$map['fee_phase_id']	=	$fee_phase_id;
		$result	=	$Model->where($map)->save($data);
		return $result;
	}
}