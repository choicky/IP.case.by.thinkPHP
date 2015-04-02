<?php
namespace Home\Controller;
use Think\Controller;

class CaseController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//默认跳转到listPage，分页显示
	public function listAll(){
        header("Location: listPage");
    }
	
	//分页显示所有案件，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_list = D('Case')->listPage($p,$page_limit);
		$case_count	=	count($case_list);
		$this->assign('case_list',$case_list['list']);
		$this->assign('case_page',$case_list['page']);
		$this->assign('case_count',$case_list['count']);

		/*
		//取出 Account 表的内容以及数量
		$account_list	=	D('Account')->field(true)->listAll();
		$account_count	=	count($account_list);
		$this->assign('account_list',$account_list);
		$this->assign('account_count',$account_count);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->field(true)->listAll();
		$member_count	=	count($member_list);
		$this->assign('member_list',$member_list);
		$this->assign('member_count',$member_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
        $this->assign('today',$today);*/
	
		$this->display();
	}
	
	//分页显示所有专利案件，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPagePatent(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_list = D('Case')->listPagePatent($p,$page_limit);
		$case_count	=	count($case_list);
		$this->assign('case_list',$case_list['list']);
		$this->assign('case_page',$case_list['page']);
		$this->assign('case_count',$case_list['count']);

		/*
		//取出 Account 表的内容以及数量
		$account_list	=	D('Account')->field(true)->listAll();
		$account_count	=	count($account_list);
		$this->assign('account_list',$account_list);
		$this->assign('account_count',$account_count);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->field(true)->listAll();
		$member_count	=	count($member_list);
		$this->assign('member_list',$member_list);
		$this->assign('member_count',$member_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
        $this->assign('today',$today);*/
	
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['account_id']	=	trim(I('post.account_id'));
		$data['deal_date']	=	trim(I('post.deal_date'));
		$data['deal_date']	=	strtotime($data['deal_date']);
		$data['income_amount']	=	trim(I('post.income_amount'));
		$data['income_amount']	=	$data['income_amount']*100;
		$data['outcome_amount']	=	trim(I('post.outcome_amount'));
		$data['outcome_amount']	=	$data['outcome_amount']*100;
		$data['summary']	=	trim(I('post.summary'));
		$data['other_party']	=	trim(I('post.other_party'));

		if(!$data['account_id']){
			$this->error('未填写账户名称');
		} 

		$result = M('Patent')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listAll');
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			$case_id	=	trim(I('post.case_id'));
			
			$Model	=	D('Case');
			if (!$Model->relation(true)->create()){ // 创建数据对象
				 // 如果创建失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 //exit($Model->getError());
			}else{
				 // 验证通过 写入新增数据
				 $result	=	$Model->save();		 
			}
			if(false !== $result){
				$this->success('修改成功', 'view/case_id/'.$case_id);
			}else{
				$this->error('修改失败');
			}
			/*
			$case_id	=	trim(I('post.case_id'));
			
			$data=array();
			$data['our_ref']	=	trim(I('post.our_ref'));
			$data['case_type_id']	=	trim(I('post.case_type_id'));
			$data['follower_id']	=	trim(I('post.follower_id'));
			$data['create_date']	=	trim(I('post.create_date'));
			$data['create_date']	=	strtotime($data['create_date']);
			$data['form_date']	=	trim(I('post.form_date'));
			$data['form_date']	=	strtotime($data['form_date']);
			$data['client_id']	=	trim(I('post.client_id'));
			$data['client_ref']	=	trim(I('post.client_ref'));
			$data['applicant_id']	=	trim(I('post.applicant_id'));
			$data['tentative_title']	=	trim(I('post.tentative_title'));
			$data['handler_id']	=	trim(I('post.handler_id'));
			$data['application_date']	=	trim(I('post.application_date'));
			$data['application_date']	=	strtotime($data['application_date']);
			$data['application_number']	=	trim(I('post.application_number'));
			
			$data['formal_title']	=	trim(I('post.formal_title'));
			$data['tm_category_id']	=	trim(I('post.tm_category_id'));
			$data['publication_date']	=	trim(I('post.publication_date'));
			$data['publication_date']	=	strtotime($data['publication_date']);
			$data['issue_date']	=	trim(I('post.issue_date'));
			$data['issue_date']	=	strtotime($data['issue_date']);
			
			for($i=0;	$i<count(I('post.case_priority_id'));	$i++){
				$case_priority_id	=	I('post.case_priority_id')[$i];
				$priority_country_id	=	I('post.priority_country_id')[$i];
				$priority_number	=	I('post.priority_number')[$i];
				$priority_date	=	I('post.priority_date')[$i];
				$priority_date	=	strtotime($priority_date);
				$data['CasePriority'][$i]	=	array(
					'case_priority_id'	=>	$priority_country_id,
					'priority_country_id'	=>	$priority_country_id,
					'priority_number'	=>	$priority_number,
					'priority_date'	=>	$priority_date
				);
			}
			
			print_r($data);
			
			$result = D('Case')->update($case_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}*/
		} else{
			$case_id = I('get.case_id',0,'int');

			if(!$case_id){
				$this->error('未指明要编辑的案号');
			}

			$case_list = D('Case')->relation(true)->getByCaseId($case_id);			
			$this->assign('case_list',$case_list);
			
			
			//判断本案是专利、还是非专利
			if(strpos($case_list['CaseType']['case_type_name'],'专利') !== false){
				$is_patent	=	1;
			}else{
				$is_patent	=	0;
			}
			
			//取出 CaseType 表的内容以及数量
			if($is_patent){
				$map['case_type_name']	=	array('like','%专利%');
			}else{
				$map['case_type_name']	=	array('notlike','%专利%');
			}
			$case_type_list	=	D('CaseTypeView')->where($map)->listBasic();
			$case_type_count	=	count($case_type_list);
			$this->assign('case_type_list',$case_type_list);
			$this->assign('case_type_count',$case_type_count);
			
			//取出 Member 表的内容以及数量
			$member_list	=	D('Member')->field(true)->listBasic();
			$member_count	=	count($member_list);
			$this->assign('member_list',$member_list);
			$this->assign('member_count',$member_count);
			
			//取出 Client 表的内容以及数量
			$client_list	=	D('Client')->field(true)->listBasic();
			$client_count	=	count($client_list);
			$this->assign('client_list',$client_list);
			$this->assign('client_count',$client_count);
			
			//取出 TmCategory 表的内容以及数量
			$tm_category_list	=	D('TmCategory')->field(true)->listBasic();
			$tm_category_count	=	count($tm_category_list);
			$this->assign('tm_category_list',$tm_category_list);
			$this->assign('tm_category_count',$tm_category_count);
			
			//取出 Country 表的内容以及数量
			$country_list	=	D('Country')->field(true)->listAll();
			$country_count	=	count($country_list);
			$this->assign('country_list',$country_list);
			$this->assign('country_count',$country_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$today	=	time();
			$this->assign('row_limit',$row_limit);
			$this->assign('today',$today);

			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_id
			$case_id	=	trim(I('post.case_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'listAll');
			}
			
			if(1==$yes_btn){
				$map['case_id']	=	$case_id;
				$condition	=	M('Claim')->where($map)->find();
				if(is_array($condition)){
					$this->error('本收支流水已结算，不可删除，只能修改');
				}
				
				$result = M('Patent')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listAll');
				}
			}
			
		} else{
			$case_id = I('get.case_id',0,'int');

			if(!$case_id){
				$this->error('未指明要删除的流水');
			}

			$case_list = D('Patent')->relation(true)->field(true)->getByPatentId($case_id);			
			$this->assign('case_list',$case_list);

			$this->display();
		}
	}
	
	//搜索
	public function searchPatent(){
		//取出年份列表，作为 options
		$year_list	=	yearOption();
		$this->assign('year_list',$year_list);
		
		//取出 CaseGroup 表的专利数据，作为 options
		$case_group_list	=	D('CaseGroup')->field(true)->listAllPatent();
		$this->assign('case_group_list',$case_group_list);
		
		//取出 CaseType 表的专利数据，作为 options
		$case_type_list	=	D('CaseTypeView')->field('case_type_id,case_type_name')->listAllPatent();
		$this->assign('case_type_list',$case_type_list);
		
		//取出 Member 表的基本内容，作为 options
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出Client 表的基本内容，作为 options
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//默认查询最近一年
		$start_time	=	strtotime("-1 year");
		$end_time	=	time();
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
				
		if(IS_POST){
			
			//接收搜索参数
			$case_year	=	I('post.case_year','0','int');
			$case_group_id	=	I('post.case_group_id','0','int');
			$case_type_id	=	I('post.case_type_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$handler_id	=	I('post.handler_id','0','int');
			$client_id	=	I('post.client_id','0','int');
			
			$start_time	=	trim(I('post.start_time'));
			$start_time	=	strtotime($start_time);
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	strtotime($end_time);
			
			$formal_title	=	trim(I('post.formal_title'));			
			
			//构造 maping
			$map['issue_date']	=	array('EGT',$start_time);
			$map['issue_date']	=	array('ELT',$end_time);
			if($case_year){
				
				$map['our_ref']	=	array('like',"%".$case_year."%");
			}
			if($case_group_id){
				$case_type_list	=	D('CaseTypeView')->listCaseTypeId($case_group_id);
				$map['case_type_id']  = array('in',$case_type_list);
			}
			if($case_type_id){
				$map['case_type_id']	=	$case_type_id;
			}
			if($follower_id){
				$map['follower_id']	=	$follower_id;
			}
			if($applicant_id){
				$map['applicant_id']	=	$applicant_id;
			}
			if($handler_id){
				$map['handler_id']	=	$handler_id;
			}
			if($client_id){
				$map['client_id']	=	$client_id;
			}
			if($formal_title){
				$map['formal_title']	=	array('like','%'.$formal_title.'%');
			}	
			
			//取出其他参数
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_list = D('Case')->where($map)->listPage($p,$page_limit);
			$case_count = D('Case')->where($map)->count();
			$this->assign('case_list',$case_list['list']);
			$this->assign('case_page',$case_list['page']);
			$this->assign('case_count',$case_count);
		
		} 
	
	$this->display();
	}
	
	//查看 $case_id 的详情	
	public function view($case_id){

		$case_id = I('get.case_id',0,'int');

		if(!$case_id){
			$this->error('未指明要编辑的账户');
		}
		
		//取出案件的基本信息
		$case_list = D('Case')->relation(true)->getByCaseId($case_id);			
		$this->assign('case_list',$case_list);
		
		//取出案件的优先权信息		
		$map['case_id']	=	$case_id;
		$case_priority_list	=	D('CasePriority')->where($map)->listAll();
		$case_priority_count	=	count($case_priority_list);
		$this->assign('case_priority_list',$case_priority_list);
		$this->assign('case_priority_count',$case_priority_count);
		
		//取出 Country 表的内容以及数量
		$country_list	=	D('Country')->listAll();
		$country_count	=	count($country_list);
		$this->assign('country_list',$country_list);
		$this->assign('country_count',$country_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
        $this->assign('today',$today);


		$this->display();

	}
}