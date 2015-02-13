<?php
namespace Home\Controller;
use Think\Controller;

class ClaimController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= 2;
		$claim_list = D('ClaimView')->listPage($p,$limit);
		 
		$this->assign('claim_list',$claim_list['list']);
		$this->assign('claim_page',$claim_list['page']);
	
		$member_list	=	D('Member')->listBasic();
		$member_count	=	count($member_list);
		$this->assign('member_list',$member_list);
		$this->assign('member_count',$member_count);
		
		$client_list	=	D('Client')->listBasic();
		$client_count	=	count($client_list);
		$this->assign('client_list',$client_list);
		$this->assign('client_count',$client_count);
		
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['claimer_id']	=	trim(I('post.claimer_id'));
		$data['client_id']	=	trim(I('post.client_id'));
		$data['claim_date']	=	trim(I('post.claim_date'));
		//转为时间戳
		$data['claim_date']	=	time($data['claim_date']);
		$data['balance_id']	=	trim(I('post.balance_id'));
		$data['total_amount']	=	trim(I('post.total_amount'));
		$data['official_fee']	=	trim(I('post.official_fee'));
		$data['service_fee']	=	trim(I('post.service_fee'));

		if(!$data['claimer_id']){
			$this->error('未选择认领人');
		} 

		$result = M('Claim')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function edit(){
		if(IS_POST){
			$claim_id	=	trim(I('post.claim_id'));
			
			$data=array();
			$data['claimer_id']	=	trim(I('post.claimer_id'));
			$data['client_id']	=	trim(I('post.client_id'));
			$data['claim_date']	=	trim(I('post.claim_date'));
			//转为时间戳
			$data['claim_date']	=	time($data['claim_date']);
			$data['balance_id']	=	trim(I('post.balance_id'));
			$data['total_amount']	=	trim(I('post.total_amount'));
			$data['official_fee']	=	trim(I('post.official_fee'));
			$data['service_fee']	=	trim(I('post.service_fee'));
						
			$result = D('Claim')->edit($claim_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$claim_id = I('get.id',0,'int');

			if(!$claim_id){
				$this->error('未指明要编辑的认领单号');
			}

			$claim_list = M('Claim')->getByClaimId($claim_id);
			$this->assign('claim_list',$claim_list);
			
			$member_list	=	D('Member')->listBasic();
			$this->assign('member_list',$member_list);
			
			$client_list	=	D('Client')->listBasic();
			$this->assign('client_list',$client_list);
			
			$this->display();
		}
	}
}