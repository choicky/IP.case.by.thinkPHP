<?php
namespace Home\Controller;
use Think\Controller;

class CaseFileController extends Controller {
    
	//新增
	public function add(){
	
		$case_id	=	trim(I('post.case_id'));
		$map_case['case_id']	=	$case_id;
		$condition_case	=	M('Case')->where($map_case)->find();
		if(!is_array($condition_case)){
			$this->error('案件编号不正确');
		}
		
		$Model	=	D('CaseFile');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			$this->success('新增成功', U('CaseFile/view','case_id='.$case_id));
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			$case_file_data['case_file_id']	=	trim(I('post.case_file_id'));
			$case_file_data['case_id']	=	trim(I('post.case_id'));
			$case_file_data['file_type_id']	=	trim(I('post.file_type_id'));
			$case_file_data['oa_date']	=	strtotime(trim(I('post.oa_date')));			
			$case_file_data['due_date']	=	strtotime(trim(I('post.due_date')));
			$case_file_data['completion_date']	=	strtotime(trim(I('post.completion_date')));
			$case_file_data['service_fee']	=	100*trim(I('post.service_fee'));
			$case_file_data['bill_id']	=	trim(I('post.bill_id'));
			$case_file_data['invoice_id']	=	trim(I('post.invoice_id'));
			$case_file_data['claim_id']	=	trim(I('post.claim_id'));
			$case_file_data['cost_id']	=	trim(I('post.cost_id'));
			$case_file_data['cost_amount']	=	100*trim(I('post.cost_amount'));
			
			$result	=	M('CaseFile')->save($case_file_data);
			
			if(false !== $result){
				$this->success('修改成功', U('Case/view','case_id='.$case_file_data['case_id']));
			}else{
				$this->error('修改失败');
			}
			
		} else{
			$case_file_id = I('get.case_file_id',0,'int');

			if(!$case_file_id){
				$this->error('未指明要编辑的文件编号');
			}

			$case_file_list = M('CaseFile')->getByCaseFileId($case_file_id);
			$this->assign('case_file_list',$case_file_list);
			
			//获取本条费用的案子的 $case_type_name
			$case_type_name	=	D('CaseFile')->returnCaseTypeName($case_file_id);
			//获取本案子的 $case_type_name
			$case_type_name	=	$case_list['case_type_name'];
			
			//根据 $case_type_name 是否包含“专利”来构造对应的检索条件
			if(false	!==	strpos($case_type_name,'专利')){
				$map_file_type['file_type_name']	=	array('like','%专利%');
			}else{
				$map_file_type['file_type_name']	=	array('notlike','%专利%');
			}
				
			//取出 FileType 表的内容以及数量
			$file_type_list	=	D('FileType')->where($map_file_type)->listBasic();
			$file_type_count	=	count($file_type_list);
			$this->assign('file_type_list',$file_type_list);
			$this->assign('file_type_count',$file_type_count);
			
			//取出 FileType 表的内容以及数量
			$file_type_list	=	D('FileType')->field(true)->listAll();
			$file_type_count	=	count($file_type_list);
			$this->assign('file_type_list',$file_type_list);
			$this->assign('file_type_count',$file_type_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);
			
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_file_id 和 case_id
			$case_file_id	=	trim(I('post.case_file_id'));
			$case_id	=	trim(I('post.case_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', U('Case/view','case_id='.$case_id));
			}
			
			if(1==$yes_btn){
				
				$map['case_file_id']	=	$case_file_id;

				$result = M('CaseFile')->where($map)->delete();
				if($result){
					$this->success('删除成功', U('Case/view','case_id='.$case_id));
				}
			}
			
		} else{
			$case_file_id = I('get.case_file_id',0,'int');

			if(!$case_file_id){
				$this->error('未指明要删除的流水');
			}
			
			$case_file_list = D('CaseFileView')->field(true)->getByCaseFileId($case_file_id);			
			$this->assign('case_file_list',$case_file_list);			

			$this->display();
		}
	}
	
	//查看主键为 $case_id 的收支流水的所有 case_file
	public function view(){
		$case_id = I('get.case_id',0,'int');

		if(!$case_id){
			$this->error('未指明要查看的案件');
		}

		//取出案件的基本信息
		$case_list = D('CaseView')->field(true)->getByCaseId($case_id);
		
		//定义查询
		$map['case_id']	=	$case_id;
		
		//取出文件信息
		$case_file_list	=	D('CaseFileView')->where($map)->listAll();
		$case_file_count	=	count($case_file_list);
		
		$this->assign('case_list',$case_list);
		$this->assign('case_file_list',$case_file_list);
		$this->assign('case_file_count',$case_file_count);
		
		//获取本案子的 $case_type_name
		$case_type_name	=	$case_list['case_type_name'];
		
		//根据 $case_type_name 是否包含“专利”来构造对应的检索条件
		if(false	!==	strpos($case_type_name,'专利')){
			$map_file_type['file_type_name']	=	array('like','%专利%');
		}else{
			$map_file_type['file_type_name']	=	array('notlike','%专利%');
		}
			
		//取出 FileType 表的内容以及数量
		$file_type_list	=	D('FileType')->where($map_file_type)->listBasic();
		$file_type_count	=	count($file_type_list);
		$this->assign('file_type_list',$file_type_list);
		$this->assign('file_type_count',$file_type_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
		$this->assign('today',$today);

		$this->display();
	}
	
	//搜索专利文件
	public function searchPatentFile(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//默认查询未来1个月期限
		$start_due_date	=	"";
		$end_due_date	=	strtotime('+1 month');
		$this->assign('start_due_date',$start_due_date);
		$this->assign('end_due_date',$end_due_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$is_filed	=	I('post.is_filed','0','int');
			
			$start_due_date	=	trim(I('post.start_due_date'));
			$start_due_date	=	$start_due_date ? strtotime($start_due_date) : "";			
			$end_due_date	=	trim(I('post.end_due_date'));
			$end_due_date	=	$end_due_date ? strtotime($end_due_date) : strtotime('+1 month');
			
			//构造 maping
			if($client_id){
				$map_case_file['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_file['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_file['follower_id']	=	$follower_id;
			}	
			
			if(1==$is_filed){
				$map_case_file['completion_date']	=	array('LT',1);
			}
			if(2==$is_filed){
				$map_case_file['completion_date']	=	array('GT',1);
			}
			
			$map_case_file['due_date']	=	array('between',$start_due_date.','.$end_due_date);
			
			//获取专利的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
			$map_case_file['case_type_id']  = array('in',$case_type_list);
			
			
			//分页显示搜索结果
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_file_list = D('CaseFileView')->listPageSearch($p,$page_limit,$map_case_file);
			$this->assign('case_file_list',$case_file_list['list']);
			$this->assign('case_file_page',$case_file_list['page']);
			$this->assign('case_file_count',$case_file_list['count']);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_filed',$is_filed);
			$this->assign('start_due_date',$start_due_date);
			$this->assign('end_due_date',$end_due_date);
		
		} 
	
	$this->display();
	}
	
	//搜索非专利文件
	public function searchNotPatentFile(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//默认查询未来1个月期限
		$start_due_date	=	"";
		$end_due_date	=	strtotime('+1 month');
		$this->assign('start_due_date',$start_due_date);
		$this->assign('end_due_date',$end_due_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$is_filed	=	I('post.is_filed','0','int');
			
			$start_due_date	=	trim(I('post.start_due_date'));
			$start_due_date	=	$start_due_date ? strtotime($start_due_date) : "";			
			$end_due_date	=	trim(I('post.end_due_date'));
			$end_due_date	=	$end_due_date ? strtotime($end_due_date) : strtotime('+1 month');
			
			//构造 maping
			if($client_id){
				$map_case_file['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_file['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_file['follower_id']	=	$follower_id;
			}	
			
			if(1==$is_filed){
				$map_case_file['completion_date']	=	array('LT',1);
			}
			if(2==$is_filed){
				$map_case_file['completion_date']	=	array('GT',1);
			}
			
			$map_case_file['due_date']	=	array('between',$start_due_date.','.$end_due_date);
			
			//获取专利的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
			$map_case_file['case_type_id']  = array('in',$case_type_list);
			
			
			//分页显示搜索结果
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_file_list = D('CaseFileView')->listPageSearch($p,$page_limit,$map_case_file);
			$this->assign('case_file_list',$case_file_list['list']);
			$this->assign('case_file_page',$case_file_list['page']);
			$this->assign('case_file_count',$case_file_list['count']);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_filed',$is_filed);
			$this->assign('start_due_date',$start_due_date);
			$this->assign('end_due_date',$end_due_date);
		
		} 
	
	$this->display();
	}
	
	//搜索
	public function searchPatentFileList(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//默认查询最近1个月
		$start_completion_date	=	strtotime('-1 month');
		$end_completion_date	=	time();
		$this->assign('start_completion_date',$start_completion_date);
		$this->assign('end_completion_date',$end_completion_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$is_filed	=	I('post.is_filed','0','int');
			
			$start_completion_date	=	trim(I('post.start_completion_date'));
			$start_completion_date	=	$start_completion_date ? strtotime($start_completion_date) : time();			
			$end_completion_date	=	trim(I('post.end_completion_date'));
			$end_completion_date	=	$end_completion_date ? strtotime($end_completion_date) : strtotime('+1 month');
			
			//构造 maping
			if($client_id){
				$map_case_file['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_file['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_file['follower_id']	=	$follower_id;
			}	
			
			$map_case_file['completion_date']	=	array('between',$start_completion_date.','.$end_completion_date);
			
			//获取专利的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
			$map_case_file['case_type_id']  = array('in',$case_type_list);
			
			
			//分页显示搜索结果
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_file_list = D('CaseFileView')->listPageSearch($p,$page_limit,$map_case_file);
			$this->assign('case_file_list',$case_file_list['list']);
			$this->assign('case_file_page',$case_file_list['page']);
			$this->assign('case_file_count',$case_file_list['count']);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_filed',$is_filed);
			$this->assign('start_completion_date',$start_completion_date);
			$this->assign('end_completion_date',$end_completion_date);
		
		} 
	
	$this->display();
	}
	
	//搜索
	public function searchNotPatentFileList(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//默认查询最近1个月
		$start_completion_date	=	strtotime('-1 month');
		$end_completion_date	=	time();
		$this->assign('start_completion_date',$start_completion_date);
		$this->assign('end_completion_date',$end_completion_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$is_filed	=	I('post.is_filed','0','int');
			
			$start_completion_date	=	trim(I('post.start_completion_date'));
			$start_completion_date	=	$start_completion_date ? strtotime($start_completion_date) : time();			
			$end_completion_date	=	trim(I('post.end_completion_date'));
			$end_completion_date	=	$end_completion_date ? strtotime($end_completion_date) : strtotime('+1 month');
			
			//构造 maping
			if($client_id){
				$map_case_file['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_file['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_file['follower_id']	=	$follower_id;
			}	
			
			$map_case_file['completion_date']	=	array('between',$start_completion_date.','.$end_completion_date);
			
			//获取专利的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
			$map_case_file['case_type_id']  = array('in',$case_type_list);
			
			
			//分页显示搜索结果
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_file_list = D('CaseFileView')->listPageSearch($p,$page_limit,$map_case_file);
			$this->assign('case_file_list',$case_file_list['list']);
			$this->assign('case_file_page',$case_file_list['page']);
			$this->assign('case_file_count',$case_file_list['count']);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_filed',$is_filed);
			$this->assign('start_completion_date',$start_completion_date);
			$this->assign('end_completion_date',$end_completion_date);
		
		} 
	
	$this->display();
	}
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPagePatentFile(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		
		//获取专利的 case_type_id 集合
		$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
		$map_case_file['case_type_id']  = array('in',$case_type_list);
		
		$case_file_list = D('CaseFileView')->listPageSearch($p,$limit,$map_case_file);
		$this->assign('case_file_list',$case_file_list['list']);
		$this->assign('case_file_page',$case_file_list['page']);
		$this->assign('case_file_count',$case_file_list['count']);
		
		$this->display();
	}
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPageNotPatentFile(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		
		//获取专利的 case_type_id 集合
		$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
		$map_case_file['case_type_id']  = array('in',$case_type_list);
		
		$case_file_list = D('CaseFileView')->listPageSearch($p,$limit,$map_case_file);
		$this->assign('case_file_list',$case_file_list['list']);
		$this->assign('case_file_page',$case_file_list['page']);
		$this->assign('case_file_count',$case_file_list['count']);
		
		$this->display();
	}
	
}