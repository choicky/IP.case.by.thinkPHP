<?php
namespace Home\Controller;
use Think\Controller;

class BillController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= C('RECORDS_PER_PAGE');
		$bill_list = D('BillView')->field(true)->listPage($p,$limit);
		$this->assign('bill_list',$bill_list['list']);
		$this->assign('bill_page',$bill_list['page']);
		$this->assign('bill_count',$bill_list['count']);
		
		//取出 Member 的数据
		$member_list	=	D('Member')->field(true)->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出 Client 的数据
		$client_list	=	D('Client')->field(true)->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出其他的数据
		$today	=	date("Y-m-d",time());
		$this->assign('today',$today);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['follower_id'] = I('post.follower_id',0,'int');
		$data['bill_date']	=	trim(I('post.bill_date'));
		//转为时间戳
		$data['bill_date']	=	strtotime($data['bill_date']);
		$data['client_id'] = I('post.client_id',0,'int');
		$data['total_amount'] = I('post.total_amount',0,'int')*100;
		$data['official_fee'] = I('post.official_fee',0,'int')*100;
		$data['service_fee'] = I('post.service_fee',0,'int')*100;
		$data['other_fee'] = I('post.other_fee',0,'int')*100;
		
		if(!$data['follower_id']	or	!$data['client_id']){
			$this->error('未填写开开单人、收单人');
		} 

		$result = M('Bill')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function update(){
		if(IS_POST){
			
			$data	=	array();
			$data['bill_id'] = I('post.bill_id',0,'int');
			$data['follower_id'] = I('post.follower_id',0,'int');
			$data['bill_date']	=	trim(I('post.bill_date'));
			//转为时间戳
			$data['bill_date']	=	strtotime($data['bill_date']);
			$data['client_id'] = I('post.client_id',0,'int');
			$data['total_amount'] = I('post.total_amount',0,'int')*100;
			$data['official_fee'] = I('post.official_fee',0,'int')*100;
			$data['service_fee'] = I('post.service_fee',0,'int')*100;
			$data['other_fee'] = I('post.other_fee',0,'int')*100;
			
			if(!$data['invoice_id']	or	!$data['follower_id']){
				$this->error('未填写开开单人或跟案人');
			} 
			
			$result = M('Bill')->save($data);
			
			if(false == $result){
				$this->error('修改失败');
			}else{
				
			}$this->success('修改成功',U('Invoice/listPage'));
			
			
		} else{
			$bill_id = I('get.bill_id',0,'int');

			if(!$bill_id){
				$this->error('未指明要编辑的认领单号');
			}
			
			$bill_list = D('BillView')->getByBillId($bill_id);
			$this->assign('bill_list',$bill_list);

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
	}
	
	//查看主键为 $bill_id 对应的开票信息、到账信息
	public function view(){
		
		//接收对应的 $bill_id
		$bill_id = I('get.bill_id',0,'int');
		if(!$bill_id){
			$this->error('未指明要查看的账单');
		}
		
		//取出案件的基本信息
		$bill_list = D('BillView')->field(true)->getByBillId($bill_id);
		$this->assign('bill_list',$bill_list);
		
		//定义查询
		$map['bill_id']	=	$bill_id;

		/*
		//取出到账信息
		$balance_list	=	D('BalanceView')->where($map)->listAll();
		$balance_count	=	count($balance_list);		
		$this->assign('balance_list',$balance_list);
		$this->assign('balance_count',$balance_count);
		*/
				
		//取出到账认领信息
		$claim_list	=	D('ClaimView')->where($map)->listAll();
		$claim_count	=	count($claim_list);		
		$this->assign('claim_list',$claim_list);
		$this->assign('claim_count',$claim_count);
		
		//取出发票信息
		$invoice_list	=	D('InvoiceView')->where($map)->listAll();
		$invoice_count	=	count($invoice_list);		
		$this->assign('invoice_list',$invoice_list);
		$this->assign('invoice_count',$invoice_count);		

		$this->display();
	}
	
	//查看主键为 $bill_id 对应的开票信息、到账信息
	public function detail(){
		
		//接收对应的 $bill_id
		$bill_id = I('get.bill_id',0,'int');
		if(!$bill_id){
			$this->error('未指明要查看的账单');
		}
		
		//取出案件的基本信息
		$bill_list = D('BillView')->field(true)->getByBillId($bill_id);
		$this->assign('bill_list',$bill_list);
		
		//定义查询
		$map['bill_id']	=	$bill_id;
		
		/*
		//取出交文产生的信息
		$case_file_list	=	D('CaseFileView')->where($map)->listAll();
		$case_file_count	=	count($case_file_list);		
		$this->assign('case_file_list',$case_file_list);
		$this->assign('case_file_count',$case_file_count);
		*/
		
		//取出交费的信息
		$case_fee_list	=	D('CaseFeeView')->where($map)->listAll();
		$case_fee_count	=	count($case_fee_list);		
		$this->assign('case_fee_list',$case_fee_list);
		$this->assign('case_fee_count',$case_fee_count);	

		$this->display();
	}
	
}