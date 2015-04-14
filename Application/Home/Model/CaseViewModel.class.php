<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\ViewModel;

class CaseViewModel extends ViewModel {
	
	//定义本数据表的视图关系
	protected $viewFields = array(
		'CaseInfo'	=>	array(	//定义别名，这个别名不能与 PHP 内置关键词冲突
			'case_id',
			'our_ref',
			'case_type_id',
			'follower_id',
			'create_date',
			'client_id',
			'client_ref',
			'applicant_id',
			'tentative_title',
			'handler_id',
			'application_date',
			'application_number',
			'formal_title',
			'issue_date',
			'expired_date',
			'_table'=>"__CASE__",	//定义数据表
			'_type'=>'LEFT'			//定义join类型
		),
		
		'CaseExtend'	=>	array(
			'related_our_ref',
			'remarks',
			'_on'	=>	'CaseExtend.case_id=CaseInfo.case_id',
			'_type'=>'LEFT'			//定义join类型
		),
		
		'CaseType'	=>	array(
			'case_type_name',
			'_on'	=>	'CaseType.case_type_id=CaseInfo.case_type_id',
			'_type'=>'LEFT'			//定义join类型
		),		
		
		'Follower'	=>	array(
			'member_name'	=>	'follower_name',	//重新定义名称
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Follower.member_id=CaseInfo.follower_id',
			'_type'=>'LEFT'			//定义join类型
		),	
		
		'Client'	=>	array(
			'client_name',
			'_on'	=>	'Client.client_id=CaseInfo.client_id',
			'_type'=>'LEFT'			//定义join类型
		),
		
		'Applicant'	=>	array(
			'client_name'	=>	'applicant_name',	//重新定义名称
			'_table'=>"__CLIENT__",	//定义数据表
			'_on'	=>	'Applicant.client_id=CaseInfo.applicant_id',
			'_type'=>'LEFT'			//定义join类型
		),
		
		'Handler'	=>	array(
			'member_name'	=>	'handler_name',	//重新定义名称
			'_table'=>"__MEMBER__",	//定义数据表
			'_on'	=>	'Handler.member_id=CaseInfo.follower_id',
			'_type'=>'LEFT'			//定义join类型
		),
		
	);
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['create_date']	=	'desc';
		$order['our_ref']	=	'desc';
		$list	=	$this->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['create_date']	=	'desc';
		$order['our_ref']	=	'desc';
		$list	=	$this->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
	
	//分页返回本数据表的搜索数据，$p为当前页数，$limit为每页显示的记录条数,$map为搜索参数
	public function listPageSearch($p,$limit,$map) {
		$order['create_date']	=	'desc';
		$order['our_ref']	=	'desc';
		$list	=	$this->where($map)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}	
	
	//返回本数据视图的所有专利数据
	public function listAllPatent() {
		$case_type_list	=	D('CaseTypeView')->listPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['create_date']	=	'desc';
		$order['our_ref']	=	'desc';
		$list	=	$this->where($map)->order($order)->select();

		return $list;
	}
	
	//返回本数据视图的所有非专利数据
	public function listAllNotPatent() {
		$case_type_list	=	D('CaseTypeView')->listNotPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['create_date']	=	'desc';
		$order['our_ref']	=	'desc';
		$list	=	$this->where($map)->order($order)->select();

		return $list;
	}
	
	//分页返回本数据视图的所有专利数据
	public function listPagePatent($p,$limit) {
		$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['create_date']	=	'desc';
		$order['our_ref']	=	'desc';
		$list	=	$this->where($map)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();	//待确定
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//分页返回本数据视图的所有非专利数据
	public function listPageNotPatent($p,$limit) {
		$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['create_date']	=	'desc';
		$order['our_ref']	=	'desc';
		$list	=	$this->where($map)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();	//待确定
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//以 $key_word	为关键词，模糊查询 our_ref、 client_ref、 application_number
	public function searchAll($key_word) {
		$order['our_ref']	=	'asc';
		//在 our_ref 中查询		
		$map_our_ref['our_ref']  = array('like',"%".$key_word."%");
		$case_our_ref_list	=	$this->field(true)->where($map_our_ref)->order($order)->select();
		$case_our_ref_count	=	count($case_our_ref_list);
		
		//在 client_ref 中查询
		$map_client_ref['client_ref']  = array('like',"%".$key_word."%");
		$case_client_ref_list	=	$this->field(true)->where($map_client_ref)->order($order)->select();
		$case_client_ref_count	=	count($case_client_ref_list);
		
		//在 application_number 中查询
		$map_application_number['application_number']  = array('like',"%".$key_word."%");
		$case_application_number_list	=	$this->field(true)->where($map_application_number)->order($order)->select();
		$case_application_number_count	=	count($case_application_number_list);
				
		return array(
			"case_our_ref_list"=>$case_our_ref_list,
			"case_our_ref_count"=>$case_our_ref_count,
			"case_client_ref_list"=>$case_client_ref_list,
			"case_client_ref_count"=>$case_client_ref_count,
			"case_application_number_list"=>$case_application_number_list,
			"case_application_number_count"=>$case_application_number_count,
		);
	}
}