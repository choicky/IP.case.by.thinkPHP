<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {

	public function index(){
		if(!cookie('member_id') and !cookie('user_group_id')){
			//$this->error('尚未登陆',U('User/login'));
			header('Location: '.U('User/login'));
		}else{/*
			//监控未来一周的交文任务
			$file_due_date	=	strtotime('+1 week');
			
			//构造共用的 mapping 
			$map_case_file['completion_date']	=	array('LT',1);
			$map_case_file['due_date']	=	array('LT',$file_due_date);
			
			//构造专利的 mapping
			$patent_file_type_list	=	D('CaseType')->listPatentCaseTypeId();
			$map_case_file['case_type_id']  = array('in',$patent_file_type_list);
			
			//搜索出专利交文任务
			$patent_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			$patent_file_count	=	count($patent_file_list);
			$this->assign('patent_file_list',$patent_file_list);
			$this->assign('patent_file_count',$patent_file_count);
			/*
			//构造非专利的 mapping
			$map_case_file['case_type_id']  = array('notin',$patent_file_type_list);
			
			//搜索出非专利交文任务
			$not_patent_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			$not_patent_file_count	=	count($not_patent_file_list);
			$this->assign('not_patent_file_list',$not_patent_file_list);
			$this->assign('not_patent_file_count',$not_patent_file_count);
			
			//监控未来两周的缴费任务
			$fee_due_date	=	strtotime('+1 week');
			
			//构造共用的 mapping 
			$map_case_fee['case_payment_id']	=	array('LT',1);
			$map_case_fee['due_date']	=	array('LT',$fee_due_date);
			
			//构造专利的 mapping
			$patent_fee_type_list	=	D('CaseType')->listPatentCaseTypeId();
			$map_case_fee['case_type_id']  = array('in',$patent_fee_type_list);
			
			//搜索出专利缴费任务
			$patent_fee_list = D('CaseFeeView')->field(true)->where($map_case_fee)->listAll();
			$patent_fee_count	=	count($patent_fee_list);
			$this->assign('patent_fee_list',$patent_fee_list);
			$this->assign('patent_fee_count',$patent_fee_count);
			/*
			//构造非专利的 mapping
			$map_case_fee['case_type_id']  = array('notin',$patent_fee_type_list);
			
			//搜索出非专利缴费任务
			$not_patent_fee_list = D('CaseFeeView')->field(true)->where($map_case_fee)->listAll();
			$not_patent_fee_count	=	count($not_patent_fee_list);
			$this->assign('not_patent_fee_list',$not_patent_fee_list);
			$this->assign('not_patent_fee_count',$not_patent_fee_count);
			*/
		} 
	
	$this->display();
	
    }
	
}