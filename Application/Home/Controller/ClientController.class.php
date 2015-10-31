<?php
namespace Home\Controller;
use Think\Controller;

class ClientController extends Controller {
    
	//默认跳转到 listPage 
	public function index(){
        header("Location: listAll");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		header("Location: listAll");
		/*
		$p	= I("p",1,"int");
		$limit	= C('200_PER_PAGE');
		$client_list = D('Client')->listPage($p,$limit);
		$this->assign('client_list',$client_list['list']);
		$this->assign('client_page',$client_list['page']);
		$this->assign('client_count',$client_list['count']);
		$this->display();*/
	}
	
	//显示全部
	public function listAll(){
		$client_list	=	D('Client')->field(true)->listAll();
		$client_count	=	count($client_list);
		$this->assign('client_list',$client_list);
		$this->assign('client_count',$client_count);
		$this->display();
	}
	
	//新增
	public function add(){
		if(!trim(I('post.client_name'))){
			$this->error("未填写客户中文名称");
		}

		$data=array();
		$data['client_name'] = trim(I('post.client_name'));
		$data['ClientExtend'] = array(
			'client_name_en' => trim(I('post.client_name_en')),			
		);	
		
		$result = D('Client')->relation('ClientExtend')->add($data);
		
		if(false !== $result){
			$this->success("增加成功",'listPage');
		}else{
			$this->error("增加失败");
		}
	}
	
	//编辑
	public function update(){
		if(IS_POST){
			$client_id =  trim(I('post.client_id'));
			
			$data=array();
			$data['client_name'] = trim(I('post.client_name'));
			$data['ClientExtend'] = array(
				'client_name_en' => trim(I('post.client_name_en')),
				'client_address_zh' => trim(I('post.client_address_zh')),
				'client_address_en' => trim(I('post.client_address_en')),
				'client_id_number' => trim(I('post.client_id_number')),
				'client_business_number' => trim(I('post.client_business_number')),
				'client_tax_number' => trim(I('post.client_tax_number')),
				);	
			
			$result	=	D('Client')->update($client_id,$data);
			
			if(false !== $result){
				$this->success("修改成功", 'listPage');
			}else{
				$this->error("增加失败", 'listPage');
			}
		} else{
			$client_id = I('get.client_id',0,'int');

			if(!$client_id){
				$this->error("未指明要编辑的客户");
			}
			
			$Client = D('Client');
			$client_list = $Client->relation(true)->getByClientId($client_id);
			
			$this->assign('client_list',$client_list);
			$this->display();
		}

	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 client_id
			$client_id	=	trim(I('post.client_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'listPage');
			}
			
			if(1==$yes_btn){
				$map['client_id']	=	$client_id;
				
				//判断是否有联系人信息
				$condition_client_contact	=	M('ClientContact')->where($map)->find();
				if(is_array($condition_client_contact)){
					$this->error('该客户/申请人下面有联系信息，不可删除，只能修改');
				}
				
				//判断是否有案件信息
				$condition_case	=	M('Case')->where($map)->find();
				if(is_array($condition_case)){
					$this->error('该客户/申请人已关联到案件，不可删除，只能修改');
				}
				
				//判断是否有账单信息
				$condition_bill	=	M('Bill')->where($map)->find();
				if(is_array($condition_bill)){
					$this->error('该客户/申请人已有账单信息，不可删除，只能修改');
				}
				
				$result = D('Client')->relation('ClientExtend')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listAll');
				}
			}
			
		} else{
			$client_id = I('get.client_id',0,'int');

			if(!$client_id){
				$this->error('未指明要删除的客户/申请人');
			}

			$client_list = D('Client')->relation(true)->field(true)->getByClientId($client_id);			
			$this->assign('client_list',$client_list);

			$this->display();
		}
	}
	
	
	//该函数暂时只用于搜索某个申请人名下的专利案
	public function listPatent(){
		
		//接收搜索参数
		$applicant_id	=	I('get.applicant_id','0','int');
		
		//构造 maping
		$case_type_list	=	D('CaseType')->listPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);

		if($applicant_id){
			$map['applicant_id']	=	$applicant_id;
		}else{
			$this->error('未指明要搜索的申请人');
		}		

		//取出其他参数并搜索
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_list = D('CaseView')->listPageSearch($p,$page_limit,$map);
		
		//取出该客户的名称
		$client_list = D('Client')->relation(true)->field(true)->getByClientId($applicant_id);			
		$this->assign('client_list',$client_list);
		
		$this->assign('case_list',$case_list['list']);
		$this->assign('case_page',$case_list['page']);
		$this->assign('case_count',$case_list['count']);

		$this->display();
	}
	
	//该函数暂时只用于搜索某个申请人名下的非专利案
	public function listNotPatent(){
		
		//接收搜索参数
		$applicant_id	=	I('get.applicant_id','0','int');
		
		//构造 maping
		$case_type_list	=	D('CaseType')->listNotPatentCaseTypeId();
		$map['case_type_id']  = array('in',$case_type_list);

		if($applicant_id){
			$map['applicant_id']	=	$applicant_id;
		}else{
			$this->error('未指明要搜索的申请人');
		}		

		//取出其他参数并搜索
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_list = D('CaseView')->listPageSearch($p,$page_limit,$map);
		
		//取出该客户的名称
		$client_list = D('Client')->relation(true)->field(true)->getByClientId($applicant_id);			
		$this->assign('client_list',$client_list);
		
		$this->assign('case_list',$case_list['list']);
		$this->assign('case_page',$case_list['page']);
		$this->assign('case_count',$case_list['count']);

		$this->display();
	}
}