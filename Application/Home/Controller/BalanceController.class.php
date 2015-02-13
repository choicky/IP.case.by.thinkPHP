<?php
namespace Home\Controller;
use Think\Controller;

class BalanceController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$balance_list = D('BalanceView')->listPage($p,$limit);
		 
		$this->assign('balance_list',$balance_list['list']);
		$this->assign('balance_page',$balance_list['page']);
		
		$account_list	=	D('Account')->listBasic();
		$account_count	=	count($account_list);
		$this->assign('account_list',$account_list);
		$this->assign('account_count',$account_count);
		
		$client_list	=	D('Client')->listBasic();
		$client_count	=	count($client_list);
		$this->assign('client_list',$client_list);
		$this->assign('client_count',$client_count);
	
		$member_list	=	D('Member')->listBasic();
		$member_count	=	count($member_list);
		$this->assign('member_list',$member_list);
		$this->assign('member_count',$member_count);
		
		$cost_center_list	=	D('CostCenter')->listBasic();
		$cost_center_count	=	count($cost_center_list);
		$this->assign('cost_center_list',$cost_center_list);
		$this->assign('cost_center_count',$cost_center_count);
				
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['account_id'] = I('post.account_id',0,'int');
		$data['deal_date']	=	trim(I('post.deal_date'));
		//转为时间戳
		$data['deal_date']	=	time($data['deal_date']);
		$data['income_amount'] = I('post.income_amount',0,'int');
		$data['outcome_amount'] = I('post.outcome_amount',0,'int');
		$data['summary']	=	trim(I('post.summary','','string'));
		$data['other_party_id'] = I('post.other_party_id',0,'int');
		$data['claimer_id'] = I('post.claimer_id',0,'int');
		$data['cost_center_id'] = I('post.cost_center_id',0,'int');
		$data['invoice_number']	=	trim(I('post.invoice_number','','string'));

		if(!$data['0==$data['income_amount']'] && 0==$data['outcome_amount']){
			$this->error('未填写金额');
		} 

		$result = M('Balance')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function edit(){
		if(IS_POST){
			$balance_id = I('post.balance_id',0,'int');
			
			$data=array();
			$data['account_id'] = I('post.account_id',0,'int');
			$data['deal_date']	=	trim(I('post.deal_date'));
			//转为时间戳
			$data['deal_date']	=	time($data['deal_date']);
			$data['income_amount'] = I('post.income_amount',0,'int');
			$data['outcome_amount'] = I('post.outcome_amount',0,'int');
			$data['summary']	=	trim(I('post.summary','','string'));
			$data['other_party_id'] = I('post.other_party_id',0,'int');
			$data['claimer_id'] = I('post.claimer_id',0,'int');
			$data['cost_center_id'] = I('post.cost_center_id',0,'int');
			$data['invoice_number']	=	trim(I('post.invoice_number','','string'));
			
			if(!$data['0==$data['income_amount']'] && 0==$data['outcome_amount']){
				$this->error('未填写金额');
			} 
			
			$result = D('Balance')->edit($balance_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$balance_id = I('get.id',0,'int');

			if(!$balance_id){
				$this->error('未指明要编辑的认领单号');
			}

			$balance_list = M('Balance')->getByBalanceId($balance_id);
			$this->assign('balance_list',$balance_list);
			
			$account_list	=	D('Account')->listBasic();
			$this->assign('account_list',$account_list);
			
			$client_list	=	D('Client')->listBasic();
			$this->assign('client_list',$client_list);
			
			$member_list	=	D('Member')->listBasic();
			$this->assign('member_list',$member_list);
			
			$cost_center_list	=	D('CostCenter')->listBasic();
			$this->assign('cost_center_list',$cost_center_list);
			
			$this->display();
		}
	}
}