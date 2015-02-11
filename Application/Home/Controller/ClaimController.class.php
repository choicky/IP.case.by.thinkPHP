<?php
namespace Home\Controller;
use Think\Controller;

class ClaimController extends Controller {
    
	//默认跳转到listClaim，显示claim表列表
	public function index(){
        header("Location: listClaim");
    }
	
	//列表，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listClaim(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$claim_list = D('ClaimView')->listClaim($p,$limit);
		 
		$this->assign('claim_list',$claim_list['claim_list']);
		$this->assign('claim_page',$claim_list['claim_page']);
		//var_dump($claim_list['claim_list']);
		
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		//var_dump($client_list['client_list']);
		
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
		//var_dump($data);	
		if(!$data['claimer_id']){
			$this->error('未选择认领人');
		} 

		$result = D('Claim')->addClaim($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listClaim');
		}else{
			$this->error('增加失败');
		}
	}
		
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
						
			//var_dump($data);
			$result = D('Claim')->editClaim($claim_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listClaim');
				//header("Location: listClaim");
			}else{
				$this->error('修改失败');
			}
		} else{
			$claim_id = I('get.id',0,'int');

			if(!$claim_id){
				$this->error('未指明要编辑的认领单号');
			}

			$claim_list = D('Claim')->getByClaimId($claim_id);
			$this->assign('claim_list',$claim_list);
			
			$member_list	=	D('Member')->listBasic();
			$this->assign('member_list',$member_list);
			
			$client_list	=	D('Client')->listBasic();
			$this->assign('client_list',$client_list);
			
			$this->display();
		}
	}
}