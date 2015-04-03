<?php
namespace Home\Model;
use Think\Model\ViewModel;

class CaseTypeViewModel extends ViewModel {
	
	//定义本数据表的视图关系
	protected $viewFields = array(
		'CaseType'	=>	array(
			'case_type_id',
			'case_type_name',
			'case_group_id',
			'_type'=>'LEFT'
		),
		
		'CaseGroup'	=>	array(
			'case_group_name',
			'_on'	=>	'CaseType.case_group_id=CaseGroup.case_group_id'
		),	
	
	);
	
	//返回本数据表中与专利有关的数据
	public function listAllPatent() {
		$map['case_group_name']	=	array('like','%专利%');
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表中与专利有关的 case_type_id
	public function listPatentCaseTypeId() {
		$map['case_group_name']	=	array('like','%专利%');
		$order['case_type_id']	=	'asc';
		$list	=	$this->field('case_type_id')->where($map)->order($order)->select();
		for($i=0;$i<count($list);$i++){
			$case_type_list[$i]	=	$list[$i]['case_type_id'];
		}
		return $case_type_list;
	}
	
	//根据 $case_group_id 返回本数据表中与专利有关的 case_type_id
	public function listCaseTypeId($case_group_id) {
		$map['case_group_id']	=	$case_group_id;
		$order['case_type_id']	=	'asc';
		$list	=	$this->field('case_type_id,case_group_id')->where($map)->order($order)->select();
		for($i=0;$i<count($list);$i++){
			$case_type_list[$i]	=	$list[$i]['case_type_id'];
		}
		return $case_type_list;
	}
	
	//返回本数据表中与商标有关的数据
	public function listAllTrademark() {
		$map['case_group_name']	=	array('like','%商标%');
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表中与商标有关的 case_type_id
	public function listTrademarkCaseTypeId() {
		$map['case_group_name']	=	array('like','%商标%');
		$order['case_type_id']	=	'asc';
		$list	=	$this->field('case_type_id')->where($map)->order($order)->select();
		for($i=0;$i<count($list);$i++){
			$case_type_list[$i]	=	$list[$i]['case_type_id'];
		}
		return $case_type_list;
	}
	
	//返回本数据表中与版权有关的数据
	public function listAllCopyright() {
		$map['case_group_name']	=	array('like','%版权%');
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表中与版权有关的 case_type_id
	public function listCopyrightCaseTypeId() {
		$map['case_group_name']	=	array('like','%版权%');
		$order['case_type_id']	=	'asc';
		$list	=	$this->field('case_type_id')->where($map)->order($order)->select();
		for($i=0;$i<count($list);$i++){
			$case_type_list[$i]	=	$list[$i]['case_type_id'];
		}
		return $case_type_list;
	}
	
	//返回本数据视图的基本数据
	public function listBasic() {
		$order['case_type_name']	=	'desc';
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$this->field('case_type_id,case_type_name')->order($order)->select();
		return $list;
	}
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['case_type_name']	=	'desc';
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->select();
		return $list;
	}

	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['case_type_name']	=	'desc';		
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$case_type_id的记录，$data是数组
	public function update($case_type_id,$data){
		$map['case_type_id']	=	$case_type_id;
		$Model	=	M('CaseType');
		$result	=	$Model->where($map)->save($data);
		return $result;
	}

}