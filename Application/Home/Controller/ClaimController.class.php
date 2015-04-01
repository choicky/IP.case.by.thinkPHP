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
		$cost_center_count	=	count($cost_center_list);
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
		$data['cost_center_id']	=	trim(I('post.cost_center_id'));
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

		$map['balance_id']	=	$data['balance_id'];
		$condition	=	M('Balance')->where($map)->find();
		if(!is_array($condition)){
			$this->error('收支流水编号不正确');
		}

		$result = M('Claim')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'view/balance_id/'.$data['balance_id']);
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
			$data['cost_center_id']	=	trim(I('post.cost_center_id'));
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
				$this->success('修改成功','view/balance_id/'.$data['balance_id']);
			}else{
				$this->error('修改失败');
			}
		} else{
			$claim_id = I('get.claim_id',0,'int');

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
			$cost_center_count	=	count($cost_center_list);
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
			
			//通过 I 方法获取 post 过来的 claim_id 和 balance_id
			$claim_id	=	trim(I('post.claim_id'));
			$balance_id	=	trim(I('post.balance_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', 'view/balance_id/'.$balance_id);
			}
			
			if(1==$yes_btn){
				
				$map['claim_id']	=	$claim_id;

				$result = M('Claim')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'view/balance_id/'.$balance_id);
				}
			}
			
		} else{
			$claim_id = I('get.claim_id',0,'int');

			if(!$claim_id){
				$this->error('未指明要删除的流水');
			}
			
			$claim_list = D('Claim')->relation(true)->field(true)->getByClaimId($claim_id);			
			$this->assign('claim_list',$claim_list);
			

			$this->display();
		}
	}
	//搜索
	public function search(){
		
		//取出 Member 表的基本内容，作为 options
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出 CostCenter 表的基本内容，作为 options
		$cost_center_list	=	D('CostCenter')->listBasic();
		$this->assign('cost_center_list',$cost_center_list);
				
		//默认查询 0 元 至 20000 元
		$start_amount	=	0;
		$end_amount	=	20000;
		$this->assign('start_amount',$start_amount);
		$this->assign('end_amount',$end_amount);
				
		//取出 Client 表的基本内容，作为 options
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		if(IS_POST){
			
			//接收搜索参数
			$claimer_id	=	I('post.claimer_id','0','int');
			$cost_center_id	=	I('post.cost_center_id','0','int');
			$start_amount	=	trim(I('post.start_amount'))*100;			
			$end_amount	=	trim(I('post.end_amount'))*100;			
			$client_id	=	I('post.client_id','0','int');
			
			//构造 maping
			$map['total_amount']	=	array('EGT',$start_amount);
			$map['deal_date']	=	array('ELT',$end_amount);			
			if($claimer_id){
				$map['claimer_id']	=	$claimer_id;
			}
			if($cost_center_id){
				$map['cost_center_id']	=	$cost_center_id;
			}
			if($client_id){
				$map['client_id']	=	$client_id;
			}	
			
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$claim_list = D('Claim')->where($map)->listPage($p,$page_limit);
			$this->assign('claim_list',$claim_list['list']);
			$this->assign('claim_page',$claim_list['page']);
		
		} 
	
	$this->display();
	}
	
	//查看主键为 $balance_id 的收支流水的所有 claim
	public function view(){
		$balance_id = I('get.balance_id',0,'int');

		if(!$balance_id){
			$this->error('未指明要查看的收支流水');
		}

		$balance_list = D('Balance')->relation(true)->field(true)->getByBalanceId($balance_id);			
		$this->assign('balance_list',$balance_list);
		
		$map['balance_id']	=	$balance_id;
		$claim_list	=	D('Claim')->where($map)->listAll();
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
		$cost_center_count	=	count($cost_center_list);
		$this->assign('cost_center_list',$cost_center_list);
		$this->assign('cost_center_count',$cost_center_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
        $this->assign('today',$today);


		$this->display();
	}
}