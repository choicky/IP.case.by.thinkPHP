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
		$case_list = D('CaseView')->listPage($p,$page_limit);
		$case_count	=	count($case_list);
		$this->assign('case_list',$case_list['list']);
		$this->assign('case_page',$case_list['page']);
		$this->assign('case_count',$case_list['count']);

		$this->display();
	}
	
	//分页显示所有专利案件，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPagePatent(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_list = D('CaseView')->listPagePatent($p,$page_limit);
		$case_count	=	count($case_list);
		$this->assign('case_list',$case_list['list']);
		$this->assign('case_page',$case_list['page']);
		$this->assign('case_count',$case_list['count']);
		
		$this->display();
	}
	
	//分页显示所有非专利案件，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPageNotPatent(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_list = D('CaseView')->listPageNotPatent($p,$page_limit);
		$case_count	=	count($case_list);
		$this->assign('case_list',$case_list['list']);
		$this->assign('case_page',$case_list['page']);
		$this->assign('case_count',$case_list['count']);
		
		$this->display();
	}
	

	//新增
	public function add(){
	
		$our_ref	=	trim(I('post.our_ref'));
		$extend_info['related_our_ref']	=	trim(I('post.related_our_ref'));
		$extend_info['remarks']	=	trim(I('post.remarks'));
		
		$map['our_ref']	=	$our_ref;
		$condition	=	M('Case')->where($map)->find();
		if(is_array($condition)){
			$this->error('重复案号，请返回修改');
		}
		
		$Model	=	D('Case');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			$case_id	=	D('Case')->returnCaseId($our_ref);
			
			//一对一关联数据表不会更新，这个很麻烦
			$extend_info['case_id']	=	$case_id;
			
			M('CaseExtend')->add($extend_info);	
			$this->success('新增成功', U('Case/view','case_id='.$case_id));
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			$case_id	=	trim(I('post.case_id'));
			
			$extend_info['case_extend_id']	=	trim(I('post.case_extend_id'));			
			$extend_info['related_our_ref']	=	trim(I('post.related_our_ref'));
			$extend_info['remarks']	=	trim(I('post.remarks'));			
			
			$Model	=	D('Case');
			if (!$Model->relation(true)->create()){ // 创建数据对象
				 // 如果创建失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 //exit($Model->getError());
			}else{
				 // 验证通过 写入新增数据
				 $result_case	=	$Model->save();	
				
				//写入关联表数据
				if(!$extend_info['case_extend_id']){
					$map_case_extend['case_id']	=	$case_id;
					$result_extend	=	M('CaseExtend')->where($map_case_extend)->find();
					if(!is_array($result_extend)){
						$result_data	=	M('CaseExtend')->add($map_case_extend);
					}
					$extend_info['case_extend_id']	=	D('CaseExtend')->returnCaseExtendId($case_id);
				}
				$result_extend	=	M('CaseExtend')->save($extend_info);
									
				 
			}
			if((false !== $result_case)&&(false !== $result_extend)){
				$this->success('修改成功', U('Case/view','case_id='.$case_id));
			}else{
				$this->error('修改失败');
			}
			
		} else{
			$case_id = I('get.case_id',0,'int');

			if(!$case_id){
				$this->error('未指明要编辑的案号');
			}

			$case_list = D('CaseView')->getByCaseId($case_id);			
			
			$this->assign('case_list',$case_list);
			
			
			//判断本案是专利、还是非专利
			if(strpos($case_list['case_type_name'],'专利') !== false){
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
			$case_type_list	=	D('CaseType')->where($map)->listBasic();
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
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$today	=	time();
			$this->assign('row_limit',$row_limit);
			$this->assign('today',$today);

			$this->display();
		}
	}
		
	//搜索专利案件
	public function searchPatent(){
		//取出年份列表，作为 options
		$year_list	=	yearOption();
		$this->assign('year_list',$year_list);
		
		//取出 CaseGroup 表的专利数据，作为 options
		$case_group_list	=	D('CaseGroup')->field(true)->listAllPatent();
		$this->assign('case_group_list',$case_group_list);
		
		//取出 CaseType 表的专利数据，作为 options
		$case_type_list	=	D('CaseType')->field('case_type_id,case_type_name')->listAllPatent();
		$this->assign('case_type_list',$case_type_list);
		
		//取出 Member 表的基本内容，作为 options
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出Client 表的基本内容，作为 options
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//默认查询最近一年
		$end_time	=	time();
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
			$start_time	=	$start_time ? strtotime($start_time) : 0;
			
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	$end_time ? strtotime($end_time) : time();
			
			$formal_title	=	trim(I('post.formal_title'));			
			
			//构造 maping
			$map['issue_date']  = array('between',array($start_time,$end_time));
			if($case_year){
				$map['our_ref']	=	array('like',"%".$case_year."%");
			}
			if($case_group_id){
				$case_type_list	=	D('CaseType')->listCaseTypeId($case_group_id);
				$map['case_type_id']  = array('in',$case_type_list);
			}else{
				$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
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
			$case_list = D('CaseView')->listPageSearch($p,$page_limit,$map);
			
			$this->assign('case_list',$case_list['list']);
			$this->assign('case_page',$case_list['page']);
			$this->assign('case_count',$case_list['count']);
			
			//返回搜索参数
			$this->assign('case_year',$case_year);
			$this->assign('case_group_id',$case_group_id);
			$this->assign('case_type_id',$case_type_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('handler_id',$handler_id);
			$this->assign('client_id',$client_id);
			$this->assign('start_time',$start_time);
			$this->assign('end_time',$end_time);
			$this->assign('formal_title',$formal_title);		
		
		} 
	
	$this->display();
	}
	
	//搜索非专利案件
	public function searchNotPatent(){
		//取出年份列表，作为 options
		$year_list	=	yearOption();
		$this->assign('year_list',$year_list);
		
		//取出 CaseGroup 表的专利数据，作为 options
		$case_group_list	=	D('CaseGroup')->field(true)->listAllNotPatent();
		$this->assign('case_group_list',$case_group_list);
		
		//取出 CaseType 表的专利数据，作为 options
		$case_type_list	=	D('CaseType')->field('case_type_id,case_type_name')->listAllNotPatent();
		$this->assign('case_type_list',$case_type_list);
		
		//取出 Member 表的基本内容，作为 options
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出Client 表的基本内容，作为 options
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//默认查询最近一年
		$end_time	=	time();
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
			$start_time	=	$start_time ? strtotime($start_time) : 0;
			
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	$end_time ? strtotime($end_time) : time();
			
			$formal_title	=	trim(I('post.formal_title'));			
			
			//构造 maping
			$map['issue_date']  = array('between',array($start_time,$end_time));
			if($case_year){
				$map['our_ref']	=	array('like',"%".$case_year."%");
			}
			if($case_group_id){
				$case_type_list	=	D('CaseType')->listCaseTypeId($case_group_id);
				$map['case_type_id']  = array('in',$case_type_list);
			}else{
				$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
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
			$case_list = D('CaseView')->listPageSearch($p,$page_limit,$map);
			
			$this->assign('case_list',$case_list['list']);
			$this->assign('case_page',$case_list['page']);
			$this->assign('case_count',$case_list['count']);
			
			//返回搜索参数
			$this->assign('case_year',$case_year);
			$this->assign('case_group_id',$case_group_id);
			$this->assign('case_type_id',$case_type_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('handler_id',$handler_id);
			$this->assign('client_id',$client_id);
			$this->assign('start_time',$start_time);
			$this->assign('end_time',$end_time);
			$this->assign('formal_title',$formal_title);		
		
		} 
	
	$this->display();
	}
	
	//搜索专利
	public function searchForNewPatent(){
		//取出年份列表，作为 options
		$year_list	=	yearOption();
		$this->assign('year_list',$year_list);
		
		//取出 CaseGroup 表的专利数据，作为 options
		$case_group_list	=	D('CaseGroup')->field(true)->listAllPatent();
		$this->assign('case_group_list',$case_group_list);
		
		//取出数量，作为 options
		$number_list	=	numberOption(5);
		$this->assign('number_list',$number_list);

		//取出 CaseType 表的专利数据，作为 options
		$case_type_list	=	D('CaseType')->field('case_type_id,case_type_name')->listAllPatent();
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

		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
		$this->assign('today',$today);
				
		if(IS_POST){
			
			//接收搜索参数
			$case_year	=	I('post.case_year','0','int');
			$case_group_id	=	I('post.case_group_id','0','int');
			$limit_number	=	I('post.limit_number','0','int');

					
			//构造 maping			
			if($case_year){
				$map['our_ref']	=	array('like',"%".$case_year."%");
			}
			if($case_group_id){				
				$case_type_id_list	=	D('CaseType')->listCaseTypeId($case_group_id);				
				$map['case_type_id']  = array('in',$case_type_id_list);
			}else{
				$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
				$map['case_type_id']  = array('in',$case_type_list);
			}
						
			//取出搜索结果
			$order['our_ref']	=	'desc';
			$case_list_temp	=	D('CaseView')->where($map)->limit($limit_number)->order($order)->select();
			$case_list	=	array_reverse($case_list_temp);
			$case_count	=	count($case_list);
			$case_count	=	($case_count<$limit_number) ? $case_count : $limit_number;
			$this->assign('case_list',$case_list);			
			$this->assign('case_count',$case_count);

			//返回检索参数
			$this->assign('case_year',$case_year);
			$this->assign('case_group_id',$case_group_id);			
		} 
	
	$this->display();
	}
	
	//搜索非专利
	public function searchForNewNotPatent(){
		//取出年份列表，作为 options
		$year_list	=	yearOption();
		$this->assign('year_list',$year_list);
		
		//取出 CaseGroup 表的非专利数据，作为 options
		$case_group_list	=	D('CaseGroup')->field(true)->listAllNotPatent();
		$this->assign('case_group_list',$case_group_list);
		
		//取出数量，作为 options
		$number_list	=	numberOption(5);
		$this->assign('number_list',$number_list);

		//取出 CaseType 表的非专利数据，作为 options
		$case_type_list	=	D('CaseType')->field('case_type_id,case_type_name')->listAllNotPatent();
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

		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
		$this->assign('today',$today);
				
		if(IS_POST){
			
			//接收搜索参数
			$case_year	=	I('post.case_year','0','int');
			$case_group_id	=	I('post.case_group_id','0','int');
			$limit_number	=	I('post.limit_number','0','int');

					
			//构造 maping			
			if($case_year){
				$map['our_ref']	=	array('like',"%".$case_year."%");
			}
			if($case_group_id){				
				$case_type_id_list	=	D('CaseType')->listCaseTypeId($case_group_id);				
				$map['case_type_id']  = array('in',$case_type_id_list);
			}else{
				$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
				$map['case_type_id']  = array('in',$case_type_list);
			}
						
			//取出搜索结果
			$order['our_ref']	=	'desc';
			$case_list_temp	=	D('CaseView')->where($map)->limit($limit_number)->order($order)->select();
			$case_list	=	array_reverse($case_list_temp);
			$case_count	=	count($case_list);
			$case_count	=	($case_count<$limit_number) ? $case_count : $limit_number;
			$this->assign('case_list',$case_list);			
			$this->assign('case_count',$case_count);

			//返回检索参数
			$this->assign('case_year',$case_year);
			$this->assign('case_group_id',$case_group_id);			
		} 
	
	$this->display();
	}
	
	//搜索
	public function searchAll(){

		//接收搜索参数
		$key_word	=	trim(I('get.key_word'));
		
		if(!$key_word){
			$this->error('要填写申请号、我方案号、或对方案号');
		}else{
			$case_list	=	D('CaseView')->searchAll($key_word);
		}
				
		$this->assign('case_our_ref_list',$case_list['case_our_ref_list']);
		$this->assign('case_our_ref_count',$case_list['case_our_ref_count']);
		$this->assign('case_client_ref_list',$case_list['case_client_ref_list']);
		$this->assign('case_client_ref_count',$case_list['case_client_ref_count']);
		$this->assign('case_application_number_list',$case_list['case_application_number_list']);
		$this->assign('case_application_number_count',$case_list['case_application_number_count']);
		

	$this->display();
	}
	
	//查看 $case_id 的详情	
	public function view(){

		$case_id = I('get.case_id',0,'int');

		if(!$case_id){
			$this->error('未指明要编辑的账户');
		}
		
		//取出案件的基本信息
		$case_list = D('CaseView')->field(true)->getByCaseId($case_id);
		
		//定义查询
		$map['case_id']	=	$case_id;
		
		//取出优先权信息
		$case_priority_list	=	D('CasePriorityView')->where($map)->listAll();		
		$case_priority_count	=	count($case_priority_list);
		
		//取出文件信息
		$case_file_list	=	D('CaseFileView')->where($map)->listAll();
		$case_file_count	=	count($case_file_list);
		
		//取出费用信息
		$case_fee_list	=	D('CaseFeeView')->where($map)->listAll();
		$case_fee_count	=	count($case_fee_list);
		
		$this->assign('case_list',$case_list);
		$this->assign('case_priority_list',$case_priority_list);
		$this->assign('case_priority_count',$case_priority_count);
		$this->assign('case_file_list',$case_file_list);
		$this->assign('case_file_count',$case_file_count);
		$this->assign('case_fee_list',$case_fee_list);
		$this->assign('case_fee_count',$case_fee_count);

		$this->display();

	}
}