<?php
namespace Home\Controller;
use Think\Controller;

class CaseFeeController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//默认跳转到listPage，分页显示
	public function listAll(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_fee_list = D('CaseFee')->listPage($p,$limit);
		$this->assign('case_fee_list',$case_fee_list['list']);
		$this->assign('case_fee_page',$case_fee_list['page']);
		$this->assign('case_fee_count',$case_fee_list['count']);
		
		$this->display();
	}
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPagePatentFee(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		
		//获取专利的 case_type_id 集合
		$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
		$map_case_fee['case_type_id']  = array('in',$case_type_list);
		
		$case_fee_list = D('CaseFeeView')->listPage($p,$limit,$map_case_fee);
		$this->assign('case_fee_list',$case_fee_list['list']);
		$this->assign('case_fee_page',$case_fee_list['page']);
		$this->assign('case_fee_count',$case_fee_list['count']);
		
		$this->display();
	}
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPageNotPatentFee(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		
		//获取专利的 case_type_id 集合
		$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
		$map_case_fee['case_type_id']  = array('in',$case_type_list);
		
		$case_list	=	M('Case')->where($map_case_fee)->select();
		
		$case_fee_list = D('CaseFeeView')->listPage($p,$limit,$map_case_fee);
		$this->assign('case_fee_list',$case_fee_list['list']);
		$this->assign('case_fee_page',$case_fee_list['page']);
		$this->assign('case_fee_count',$case_fee_list['count']);
		print_r($case_fee_list['list']);
		$this->display();
	}

	//新增	
	public function add(){
		$case_id	=	trim(I('post.case_id'));
		$map_case['case_id']	=	$case_id;
		$condition_case	=	M('Case')->where($map_case)->find();
		if(!is_array($condition_case)){
			$this->error('案件编号不正确');
		}
		
		$Model	=	D('CaseFee');
		if (!$Model->create()){ 
			
			 // 如果创建数据对象失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());

		}else{
			 
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			
			// 写入新增数据成功，返回案件信息页面
			$this->success('新增成功', U('CaseFee/view','case_id='.$case_id));
			
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新
	public function update(){
		
		//针对 POST 的处理方式
		if(IS_POST){
			$case_fee_data['case_fee_id']	=	trim(I('post.case_fee_id'));
			$case_fee_data['case_id']	=	trim(I('post.case_id'));
			$case_fee_data['case_phase_id']	=	trim(I('post.case_phase_id'));
			$case_fee_data['fee_type_id']	=	trim(I('post.fee_type_id'));
			$case_fee_data['official_fee']	=	100*trim(I('post.official_fee'));
			$case_fee_data['service_fee']	=	100*trim(I('post.service_fee'));			
			$case_fee_data['oa_date']	=	strtotime(trim(I('post.oa_date')));			
			$case_fee_data['due_date']	=	strtotime(trim(I('post.due_date')));
			$case_fee_data['allow_date']	=	strtotime(trim(I('post.allow_date')));
			$case_fee_data['payer_id']	=	trim(I('post.payer_id'));
			$case_fee_data['case_payment_id']	=	trim(I('post.case_payment_id'));
			$case_fee_data['bill_id']	=	trim(I('post.bill_id'));
			$case_fee_data['invoice_id']	=	trim(I('post.invoice_id'));
			$case_fee_data['claim_id']	=	trim(I('post.claim_id'));
			$case_fee_data['cost_center_id']	=	trim(I('post.cost_center_id'));
			$case_fee_data['cost_amount']	=	100*trim(I('post.cost_amount'));
			
			$result	=	M('CaseFee')->save($case_fee_data);
			
			if(false !== $result){
				$this->success('修改成功', U('CaseFee/view','case_id='.$case_fee_data['case_id']));
			}else{
				$this->error('修改失败');
			}
			
		//针对 GET 的处理方式
		} else{
			
			//接收要编辑的 $case_fee_id
			$case_fee_id = I('get.case_fee_id',0,'int');
			
			if(!$case_fee_id){
				$this->error('未指明要编辑的费用编号');
			}
			
			//取出相应的信息
			$case_fee_list = M('CaseFee')->getByCaseFeeId($case_fee_id);
			$this->assign('case_fee_list',$case_fee_list);
			
			//取出 CasePhase 表的内容以及数量
			$case_phase_list	=	D('CasePhase')->listBasic();
			$case_phase_count	=	count($case_phase_list);
			$this->assign('case_phase_list',$case_phase_list);
			$this->assign('case_phase_count',$case_phase_count);
			
			//获取本条费用的案子的 $case_type_name
			$case_type_name	=	D('CaseFee')->returnCaseTypeName($case_fee_id);

			//根据 $case_type_name 是否包含“专利”来构造对应的检索条件
			if(false	!==	strpos($case_type_name,'专利')){
				$map_fee_type['fee_type_name']	=	array('like','%专利%');
			}else{
				$map_fee_type['fee_type_name']	=	array('notlike','%专利%');
			}
			$fee_type_list	=	D('FeeType')->where($map_fee_type)->listBasic();
			$fee_type_count	=	count($fee_type_list);
			$this->assign('fee_type_list',$fee_type_list);
			$this->assign('fee_type_count',$fee_type_count);
			
			//取出 Payer 表的内容以及数量
			$payer_list	=	D('Payer')->listBasic();
			$payer_count	=	count($payer_list);
			$this->assign('payer_list',$payer_list);
			$this->assign('payer_count',$payer_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);
			
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_fee_id 和 case_id
			$case_fee_id	=	trim(I('post.case_fee_id'));
			$case_id	=	trim(I('post.case_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', U('Case/view','case_id='.$case_id));
			}
			
			if(1==$yes_btn){
				
				$map['case_fee_id']	=	$case_fee_id;

				$result = M('CaseFee')->where($map)->delete();
				if($result){
					$this->success('删除成功', U('Case/view','case_id='.$case_id));
				}
			}
			
		} else{
			$case_fee_id = I('get.case_fee_id',0,'int');

			if(!$case_fee_id){
				$this->error('未指明要删除的费用记录');
			}
			
			$case_fee_list = D('CaseFeeTaskView')->field(true)->getByCaseFeeId($case_fee_id);		
			$this->assign('case_fee_list',$case_fee_list);
			
			$this->display();
		}
	}
	
	//搜索专利费用
	public function searchPatentFee(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出 CasePhase 表的内容以及数量
		$case_phase_list	=	D('CasePhase')->listBasic();
		$this->assign('case_phase_list',$case_phase_list);
		
		//取出 CostCenter 表的内容以及数量
		$cost_center_list	=	D('CostCenter')->listBasic();
		$this->assign('cost_center_list',$cost_center_list);
		
		//默认查询未来3个月期限
		$start_due_date	=	time();
		$end_due_date	=	strtotime('+1 month');
		$this->assign('start_due_date',$start_due_date);
		$this->assign('end_due_date',$end_due_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$cost_center_id	=	I('post.cost_center_id','0','int');	
			$allow_to_pay	=	I('post.allow_to_pay','0','int');
			$is_paid	=	I('post.is_paid','0','int');
			$case_phase_id	=	I('post.case_phase_id','0','int');
			
			$start_due_date	=	trim(I('post.start_due_date'));
			$start_due_date	=	$start_due_date ? strtotime($start_due_date) : time();			
			$end_due_date	=	trim(I('post.end_due_date'));
			$end_due_date	=	$end_due_date ? strtotime($end_due_date) : strtotime('+1 month');
			
			//构造 maping
			if($client_id){
				$map_case_fee['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_fee['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_fee['follower_id']	=	$follower_id;
			}	
			
			if($cost_center_id){
				$map_case_fee['cost_center_id']	=	$cost_center_id;
			}
			if($allow_to_pay){
				$map_case_fee['allow_date']	=	array('GT',1);
			}
			if(1==$is_paid){
				$map_case_fee['case_payment_id']	=	array('LT',1);
			}
			if(2==$is_paid){
				$map_case_fee['case_payment_id']	=	array('GT',1);
			}
			if($case_phase_id){
				$map_case_fee['case_phase_id']	=	$case_phase_id;
			}
			$map_case_fee['due_date']	=	array('between',$start_due_date.','.$end_due_date);
			
			//获取专利的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
			$map_case_fee['case_type_id']  = array('in',$case_type_list);
			
			
			//分页显示搜索结果
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_fee_list = D('CaseFeeView')->listPageSearch($p,$page_limit,$map_case_fee);
			$this->assign('case_fee_list',$case_fee_list['list']);
			$this->assign('case_fee_page',$case_fee_list['page']);
			$this->assign('case_fee_count',$case_fee_list['count']);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('cost_center_id',$cost_center_id);
			$this->assign('allow_to_pay',$allow_to_pay);
			$this->assign('is_paid',$is_paid);
			$this->assign('case_phase_id',$case_phase_id);
			$this->assign('start_due_date',$start_due_date);
			$this->assign('end_due_date',$end_due_date);
		
		} 
	
	$this->display();
	}
	
	//搜索非专利费用
	public function searchNotPatentFee(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出 CasePhase 表的内容以及数量
		$case_phase_list	=	D('CasePhase')->listBasic();
		$this->assign('case_phase_list',$case_phase_list);
		
		//取出 CostCenter 表的内容以及数量
		$cost_center_list	=	D('CostCenter')->listBasic();
		$this->assign('cost_center_list',$cost_center_list);
		
		//默认查询未来3个月期限
		$start_due_date	=	time();
		$end_due_date	=	strtotime('+1 month');
		$this->assign('start_due_date',$start_due_date);
		$this->assign('end_due_date',$end_due_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$cost_center_id	=	I('post.cost_center_id','0','int');	
			$allow_to_pay	=	I('post.allow_to_pay','0','int');
			$is_paid	=	I('post.is_paid','0','int');
			$case_phase_id	=	I('post.case_phase_id','0','int');
			
			$start_due_date	=	trim(I('post.start_due_date'));
			$start_due_date	=	$start_due_date ? strtotime($start_due_date) : time();			
			$end_due_date	=	trim(I('post.end_due_date'));
			$end_due_date	=	$end_due_date ? strtotime($end_due_date) : strtotime('+1 month');
			
			//构造 maping
			if($client_id){
				$map_case_fee['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_fee['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_fee['follower_id']	=	$follower_id;
			}	
			
			if($cost_center_id){
				$map_case_fee['cost_center_id']	=	$cost_center_id;
			}
			if($allow_to_pay){
				$map_case_fee['allow_date']	=	array('GT',1);
			}
			if(1==$is_paid){
				$map_case_fee['case_payment_id']	=	array('LT',1);
			}
			if(2==$is_paid){
				$map_case_fee['case_payment_id']	=	array('GT',1);
			}
			if($case_phase_id){
				$map_case_fee['case_phase_id']	=	$case_phase_id;
			}
			$map_case_fee['due_date']	=	array('between',$start_due_date.','.$end_due_date);
			
			//获取专利的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
			$map_case_fee['case_type_id']  = array('in',$case_type_list);
			
			
			//分页显示搜索结果
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_fee_list = D('CaseFeeView')->listPageSearch($p,$page_limit,$map_case_fee);
			$this->assign('case_fee_list',$case_fee_list['list']);
			$this->assign('case_fee_page',$case_fee_list['page']);
			$this->assign('case_fee_count',$case_fee_list['count']);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('cost_center_id',$cost_center_id);
			$this->assign('allow_to_pay',$allow_to_pay);
			$this->assign('is_paid',$is_paid);
			$this->assign('case_phase_id',$case_phase_id);
			$this->assign('start_due_date',$start_due_date);
			$this->assign('end_due_date',$end_due_date);
		
		} 
	
	$this->display();
	}
	
	//搜索
	public function searchPatentFeeList(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出 CasePhase 表的内容以及数量
		$case_phase_list	=	D('CasePhase')->listBasic();
		$this->assign('case_phase_list',$case_phase_list);
		
		//取出 CostCenter 表的内容以及数量
		$cost_center_list	=	D('CostCenter')->listBasic();
		$this->assign('cost_center_list',$cost_center_list);
		
		//默认查询未来3个月期限
		$start_payment_date	=	time();
		$end_payment_date	=	strtotime('+1 month');
		$this->assign('start_payment_date',$start_payment_date);
		$this->assign('end_payment_date',$end_payment_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$cost_center_id	=	I('post.cost_center_id','0','int');	
			$case_phase_id	=	I('post.case_phase_id','0','int');
			
			$start_payment_date	=	trim(I('post.start_payment_date'));
			$start_payment_date	=	$start_payment_date ? strtotime($start_payment_date) : time();			
			$end_payment_date	=	trim(I('post.end_payment_date'));
			$end_payment_date	=	$end_payment_date ? strtotime($end_payment_date) : strtotime('+1 month');
			
			//构造 maping
			if($client_id){
				$map_case_fee['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_fee['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_fee['follower_id']	=	$follower_id;
			}	
			
			if($cost_center_id){
				$map_case_fee['cost_center_id']	=	$cost_center_id;
			}			
			if($case_phase_id){
				$map_case_fee['case_phase_id']	=	$case_phase_id;
			}
			$map_case_fee['payment_date']	=	array('between',$start_payment_date.','.$end_payment_date);
			
			//获取专利的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
			$map_case_fee['case_type_id']  = array('in',$case_type_list);
			
			
			//分页显示搜索结果
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_fee_list = D('CaseFeeView')->listPageSearch($p,$page_limit,$map_case_fee);
			$this->assign('case_fee_list',$case_fee_list['list']);
			$this->assign('case_fee_page',$case_fee_list['page']);
			$this->assign('case_fee_count',$case_fee_list['count']);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('cost_center_id',$cost_center_id);
			$this->assign('case_phase_id',$case_phase_id);
			$this->assign('start_payment_date',$start_payment_date);
			$this->assign('end_payment_date',$end_payment_date);
		
		} 
	
	$this->display();
	}
	
	//查看主键为 $case_id 的收支流水的所有 case_fee
	public function view(){
		
		//接收对应的 $case_id
		$case_id = I('get.case_id',0,'int');
		if(!$case_id){
			$this->error('未指明要查看的收支流水');
		}
		
		//取出案件的基本信息
		$case_list = D('CaseView')->field(true)->getByCaseId($case_id);
		
		//定义查询
		$map['case_id']	=	$case_id;

		//取出费用信息
		$case_fee_list	=	D('CaseFeeView')->where($map)->listAll();
		$case_fee_count	=	count($case_fee_list);
		
		$this->assign('case_list',$case_list);
		$this->assign('case_fee_list',$case_fee_list);
		$this->assign('case_fee_count',$case_fee_count);
				
		//取出 CasePhase 表的内容以及数量
		$case_phase_list	=	D('CasePhase')->listBasic();
		$case_phase_count	=	count($case_phase_list);
		$this->assign('case_phase_list',$case_phase_list);
		$this->assign('case_phase_count',$case_phase_count);
		
		//获取本案子的 $case_type_name
		$case_type_name	=	$case_list['CaseType']['case_type_name'];
		
		//根据 $case_type_name 是否包含“专利”来构造对应的检索条件
		if(false	!==	strpos($case_type_name,'专利')){
			$map_fee_type['fee_type_name']	=	array('like','%专利%');
		}else{
			$map_fee_type['fee_type_name']	=	array('notlike','%专利%');
		}
		$fee_type_list	=	D('FeeType')->where($map_fee_type)->listBasic();
		$fee_type_count	=	count($fee_type_list);
		$this->assign('fee_type_list',$fee_type_list);
		$this->assign('fee_type_count',$fee_type_count);
		
		//取出 Payer 表的内容以及数量
		$payer_list	=	D('Payer')->listBasic();
		$payer_count	=	count($payer_list);
		$this->assign('payer_list',$payer_list);
		$this->assign('payer_count',$payer_count);
		
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
        $this->assign('today',$today);

		$this->display();
	}
}