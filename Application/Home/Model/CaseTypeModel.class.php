<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | It's recommended to use more ViewModel and less RelationModel
// +----------------------------------------------------------------------
// | This file is required by: CaseController,
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class CaseTypeModel extends RelationModel {
	
	//返回本数据视图的基本数据，可作为Options
	public function listBasic() {
		$order['case_type_name']	=	'asc';
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$this->field('case_type_id,case_type_name')->order($order)->select();
		return $list;
	}
	
	//返回本数据表中与专利有关的数据
	public function listAllPatent() {
		$map['case_type_name']	=	array('like','%专利%');
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表中与专利有关的数据
	public function listAllNotPatent() {
		$map['case_type_name']	=	array('notlike','%专利%');
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//根据 $case_group_id 返回本数据表中与专利有关的 case_type_id
	public function listCaseTypeId($case_group_id) {
		$map['case_group_id']	=	$case_group_id;
		$order['case_type_id']	=	'asc';
		$list	=	$this->field('case_type_id')->where($map)->order($order)->select();
		for($i=0;$i<count($list);$i++){
			$case_type_list[$i]	=	$list[$i]['case_type_id'];
		}
		return $case_type_list;
	}
	
	
	//返回本数据表中与专利有关的 case_type_id
	public function listPatentCaseTypeId() {
		$map['case_type_name']	=	array('like','%专利%');
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$this->field('case_type_id')->where($map)->order($order)->select();
		for($i=0;$i<count($list);$i++){
			$case_type_list[$i]	=	$list[$i]['case_type_id'];
		}
		return $case_type_list;
	}
	
	//返回本数据表中与非专利有关的 case_type_id
	public function listNotPatentCaseTypeId() {
		$map['case_type_name']	=	array('notlike','%专利%');
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$this->field('case_type_id')->where($map)->order($order)->select();
		for($i=0;$i<count($list);$i++){
			$case_type_list[$i]	=	$list[$i]['case_type_id'];
		}
		return $case_type_list;
	}
	
}