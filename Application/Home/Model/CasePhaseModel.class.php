<?php
namespace Home\Model;
use Think\Model;

class CasePhaseModel extends Model {		
	
	//返回本数据表的基本数据，可作为选单
	public function listBasic() {			
		$order['convert(case_phase_name using gb2312)']	=	'asc';
		$list	=	$this->field('case_phase_id,case_phase_name')->order($order)->select();
		return $list;
	}
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['convert(case_phase_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['convert(case_phase_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$case_phase_id的记录，$data是数组
	public function update($case_phase_id,$data){
		$map['case_phase_id']	=	$case_phase_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}
	
	//删除本数据表中主键为$case_phase_id的记录
	public function delete($case_phase_id){
		$map['case_phase_id']	=	$case_phase_id;	//自定义 where() 语句的参数
		$result	=	$this->where($map)->delete();	//基于 where() 删除对应的数据，
		return $result;	//返回值为删除的记录数量
	}

}