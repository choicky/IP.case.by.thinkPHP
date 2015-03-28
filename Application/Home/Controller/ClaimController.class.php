<?php
namespace Home\Controller;
use Think\Controller;

class ClaimController extends Controller {
    
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
		$claim_list = D('Claim')->listPage($p,$limit);
		$this->assign('claim_list',$claim_list['list']);
		$this->assign('claim_page',$claim_list['page']);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$member_count	=	count($member_list);
		$this->assign('member_list',$member_list);
		$this->assign('member_count',$member_count);
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$client_count	=	count($client_list);
		$this->assign('client_list',$client_list);
		$this->assign('client_count',$client_count);
		
		//取出 CostCenter 表的内容以及数量
		$cost_center_list	=	D('CostCenter')->listBasic();
		$cost_center_count	=	count($client_list);
		$this->assign('cost_center_list',$cost_center_list);
		$this->assign('cost_center_count',$cost_center_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
        $this->assign('today',$today);
	
		$this->display();
	}

	//新增
	public function add(){
		$data	=	array();
		$data['claimer_id']	=	trim(I('post.claimer_id'));
		$data['claim_date']	=	trim(I('post.claim_date'));		
		$data['claim_date']	=	strtotime($data['claim_date']);
		$data['balance_id']	=	trim(I('post.balance_id'));
		$data['total_amount']	=	trim(I('post.total_amount'));
		$data['total_amount']	=	$data['total_amount']*100;
		$data['official_fee']	=	trim(I('post.official_fee'));
		$data['official_fee']	=	$data['official_fee']*100;
		$data['service_fee']	=	trim(I('post.service_fee'));
		$data['service_fee']	=	$data['service_fee']*100;
		$data['client_id']	=	trim(I('post.client_id'));

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
	
	//更新	
	public function update(){
		if(IS_POST){
			$claim_id	=	trim(I('post.claim_id'));
			
			$data=array();
			$data['claimer_id']	=	trim(I('post.claimer_id'));
			$data['claim_date']	=	trim(I('post.claim_date'));		
			$data['claim_date']	=	strtotime($data['claim_date']);
			$data['balance_id']	=	trim(I('post.balance_id'));
			$data['total_amount']	=	trim(I('post.total_amount'));
			$data['total_amount']	=	$data['total_amount']*100;
			$data['official_fee']	=	trim(I('post.official_fee'));
			$data['official_fee']	=	$data['official_fee']*100;
			$data['service_fee']	=	trim(I('post.service_fee'));
			$data['service_fee']	=	$data['service_fee']*100;
			$data['client_id']	=	trim(I('post.client_id'));
						
			$result = D('Claim')->update($claim_id,$data);
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
			
			//取出 Member 表的内容以及数量
			$member_list	=	D('Member')->listBasic();
			$member_count	=	count($member_list);
			$this->assign('member_list',$member_list);
			$this->assign('member_count',$member_count);
			
			//取出 Client 表的内容以及数量
			$client_list	=	D('Client')->listBasic();
			$client_count	=	count($client_list);
			$this->assign('client_list',$client_list);
			$this->assign('client_count',$client_count);
			
			//取出 CostCenter 表的内容以及数量
			$cost_center_list	=	D('CostCenter')->listBasic();
			$cost_center_count	=	count($client_list);
			$this->assign('cost_center_list',$cost_center_list);
			$this->assign('cost_center_count',$cost_center_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);
			
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 claim_id
			$claim_id	=	trim(I('post.claim_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'listAll');
			}
			
			if(1==$yes_btn){
				$map['claim_id']	=	$claim_id;
				$condition	=	M('Claim')->where($map)->find();
				if(is_array($condition)){
					$this->error('本收支流水已结算到成本中心，不可删除');
				}
				
				$result = M('Claim')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listAll');
				}
			}
			
		} else{
			$claim_id = I('get.id',0,'int');

			if(!$claim_id){
				$this->error('未指明要删除的流水');
			}
			
			$claim_list = M('Claim')->getByClaimId($claim_id);
			
			$this->assign('claim_list',$claim_list);
			
			$claim_list = D('Claim')->relation(true)->field(true)->getByClaimId($claim_id);			
			$this->assign('claim_list',$claim_list);
			
			//取出 Account 表的内容以及数量
			$account_list	=	D('Account')->field(true)->listAll();
			$account_count	=	count($account_list);
			$this->assign('account_list',$account_list);
			$this->assign('account_count',$account_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);

			$this->display();
		}
	}
}