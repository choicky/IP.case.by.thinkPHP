<?php
namespace Home\Controller;
use Think\Controller;

class CheckController extends Controller {

	//找出没有登记发证日的专利案子	
	public function listNotIssuedPatent(){
		
		//找到专利的 case_type_id
		$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
		
		//构造 mapping
		$map['case_type_id']  = array('in',$case_type_list);
		$map['issue_date']	=	array('LT',1);
		$map['expired_date']	=	array('LT',1);
		
		//获取案件列表
		$order['our_ref']	=	'asc';
		$case_list	=	D('CaseView')->where($map)->order($order)->select();		
		
		$case_count	=	count($case_list);
		
		$this->assign('case_list',$case_list);
		$this->assign('case_count',$case_count);

		$this->display();
	
  }
    
		//找出没有有发证日但没有续展提醒的商标	
	public function listNotRenewalNotPatent(){
		
		//找到非专利的 case_type_id
		$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
		
		//构造 mapping
		$map['case_type_id']  = array('in',$case_type_list);
		$map['issue_date']	=	array('GT',0);
		
		//获取案件列表
		$order['our_ref']	=	'asc';
		$case_list_tmp	=	D('CaseView')->where($map)->order($order)->select();		
		
		$case_count	=	count($case_list_tmp);
    
    for($i=0;$i<$case_count;$i++){
      $case_id  = $case_list_tmp[$i]['case_id'];
      
      //获取文件任务
      $map['case_id']	=	$case_id;
      $case_file_list	=	M('CaseFile')->where($map)->select();	
      
      if(count($case_file_list) < 4){
        $case_list[$i]  = $case_list_tmp[$i];
      }
    }
		
    $case_count	=	count($case_list);
    $title	=	"有发证日但没有续展任务监控的商标案件清单";
    
    
		$this->assign('case_list',$case_list);
		$this->assign('case_count',$case_count);
    $this->assign('title',$title);

		$this->display();
	
  }
	
	//找出没有费用任务的专利案子
	public function listNoFeePatent(){
		
		//找到专利的 case_type_id
		$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
		
		//构造 mapping
		$map['case_type_id']  = array('in',$case_type_list);
		$map['expired_date']	=	array('LT',1);
		
		//获取案件列表
		$order['our_ref']	=	'asc';
		$case_tmp_list	=	D('CaseView')->where($map)->order($order)->select();		
		
		for($j=0;$j<count($case_tmp_list);$j++){
			
			//提取 case_id 和 our_ref
			$case_id[$j]=$case_tmp_list[$j]['case_id'];
			$our_ref[$j]=$case_tmp_list[$j]['our_ref'];
			
			
			//构造 mapping
			$map_case_fee['case_id']	=	$case_id[$j];
			
			//获取费用列表
			$case_fee_list	=	M('CaseFee')->where($map_case_fee)->find();
			
			if(!is_array($case_fee_list)){
				$case_list[$j]	=	$case_tmp_list[$j];
			}			
		}
		
		$case_count	=	count($case_list);
		
		$this->assign('case_list',$case_list);
		$this->assign('case_count',$case_count);
		$this->display();	
    }
	
	//找出没有登记发证日的商标案子	
	public function listNotIssuedNotPatent(){
		
		//找到商标的 case_type_id
		$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
		
		//构造 mapping
		$map['case_type_id']  = array('in',$case_type_list);
		$map['issue_date']	=	array('LT',1);
		$map['expired_date']	=	array('LT',1);
		
		//获取案件列表
		$order['our_ref']	=	'asc';
		$case_list	=	D('CaseView')->where($map)->order($order)->select();		
		
		$case_count	=	count($case_list);
		
		$this->assign('case_list',$case_list);
		$this->assign('case_count',$case_count);

		$this->display();	
    }
	
	//找出没有费用任务的商标案子
	public function listNoFeeNotPatent(){
		
		//找到商标的 case_type_id
		$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
		
		//构造 mapping
		$map['case_type_id']  = array('in',$case_type_list);
		$map['expired_date']	=	array('LT',1);
		
		//获取案件列表
		$order['our_ref']	=	'asc';
		$case_tmp_list	=	D('CaseView')->where($map)->order($order)->select();		
		
		for($j=0;$j<count($case_tmp_list);$j++){
			
			//提取 case_id 和 our_ref
			$case_id[$j]=$case_tmp_list[$j]['case_id'];
			$our_ref[$j]=$case_tmp_list[$j]['our_ref'];
			
			
			//构造 mapping
			$map_case_fee['case_id']	=	$case_id[$j];
			
			//获取费用列表
			$case_fee_list	=	M('CaseFee')->where($map_case_fee)->find();
			
			if(!is_array($case_fee_list)){
				$case_list[$j]	=	$case_tmp_list[$j];
			}			
		}
		
		$case_count	=	count($case_list);
		
		$this->assign('case_list',$case_list);
		$this->assign('case_count',$case_count);
		$this->display();	
    }
	
	//找出没有填写申请号或分类号的商标案子
	public function listNoApplicationNumberNotPatent(){
		
		//找到商标的 case_type_id
		$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
		
		//构造 mapping
		$map['case_type_id']  = array('in',$case_type_list);
		$map['expired_date']	=	array('LT',1);
		
		//获取案件列表
		$order['our_ref']	=	'asc';
		$case_tmp_list	=	D('CaseView')->where($map)->order($order)->select();		
		
		for($j=0;$j<count($case_tmp_list);$j++){
			
			if(FALSE==$case_tmp_list[$j]['application_number'] or FALSE==$case_tmp_list[$j]['formal_title']){
				$case_list[$j]	=	$case_tmp_list[$j];
			}			
		}
		
		$case_count	=	count($case_list);
		
		$this->assign('case_list',$case_list);
		$this->assign('case_count',$case_count);
		$this->display();	
    }
	
	
}