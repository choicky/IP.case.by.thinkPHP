<?php
namespace Home\Controller;
use Think\Controller;

class CheckController extends Controller {

	public function listNotIssued(){
		
		$this->show('<h2>尚未登记授权日的专利案件列表</h2>');
		
		
		//获取专利案列表
		$case_list	=	$this->listAllPatent2();
		
		for($j=0;$j<count($case_list);$j++){
			
			//提取 case_id 和 our_ref
			$case_id[$j]=$case_list[$j]['case_id'];
			$our_ref[$j]=$case_list[$j]['our_ref'];
			
			$this->show('<a href="'.U('Case/view','case_id='.$case_id[$j]).'">'.$our_ref[$j].'</a><br>');
			
			
		}
		
	
    }
	
	public function listNoFee(){
		
		$this->show('<h2>尚未登记有费用的专利案件列表</h2>');
		
		//获取专利案列表
		$case_list	=	$this->listAllPatent();
		
		for($j=0;$j<count($case_list);$j++){
			
			//提取 case_id 和 our_ref
			$case_id[$j]=$case_list[$j]['case_id'];
			$our_ref[$j]=$case_list[$j]['our_ref'];
			
			/*
			//获取“授权后”对应的ID
			$map_case_phase['case_phase_name']	=	array('like','%授权后%');
			$case_phase_list	=	M('CasePhase')->where($map_case_phase)->find();
			$case_phase_id	=	$case_phase_list['case_phase_id'];
			*/
			
			//构造 mapping
			$map_case_fee['case_id']	=	$case_id[$j];
			
			//获取费用列表
			$case_fee_list	=	M('CaseFee')->where($map_case_fee)->find();
			
			if(!is_array($case_fee_list)){
				$this->show('<a href="'.U('Case/view','case_id='.$case_id[$j]).'">'.$our_ref[$j].'</a><br>');
			}			
			
			
		}
		
	
    }
	
	//返回本数据视图的所有专利数据
	public function listAllPatent() {
		$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		
		$order['our_ref']	=	'desc';
		$list	=	D('CaseView')->where($map)->order($order)->select();

		return $list;
	}
	
	//返回本数据视图的所有专利数据
	public function listAllPatent2() {
		$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);
		$map['issue_date']	=	array('LT',1);
		
		$order['our_ref']	=	'desc';
		$list	=	D('CaseView')->where($map)->order($order)->select();

		return $list;
	}
	
}