<?php
namespace Home\Controller;
use Think\Controller;

class InvoiceController extends Controller {
    
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
		$invoice_list = D('InvoiceView')->listPage($p,$page_limit);
		$this->assign('invoice_list',$invoice_list['list']);
		$this->assign('invoice_page',$invoice_list['page']);
		$this->assign('invoice_count',$invoice_list['count']);
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->field(true)->listAll();
		$client_count	=	count($client_list);
		$this->assign('client_list',$client_list);
		$this->assign('client_count',$client_count);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->field(true)->listAll();
		$member_count	=	count($member_list);
		$this->assign('member_list',$member_list);
		$this->assign('member_count',$member_count);
		
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
		$data['invoice_number']	=	trim(I('post.invoice_number'));
		$data['invoice_date']	=	trim(I('post.invoice_date'));
		$data['invoice_date']	=	strtotime($data['invoice_date']);
		$data['client_id']	=	trim(I('post.client_id'));
		$data['total_amount']	=	trim(I('post.total_amount'));
		$data['total_amount']	=	$data['total_amount']*100;
		$data['official_fee']	=	trim(I('post.official_fee'));
		$data['official_fee']	=	$data['official_fee']*100;
		$data['service_fee']	=	trim(I('post.service_fee'));
		$data['service_fee']	=	$data['service_fee']*100;
		$data['follower_id']	=	trim(I('post.follower_id'));
		$data['bill_id']	=	trim(I('post.bill_id'));

		if(!$data['invoice_number']){
			$this->error('未填写账户名称');
		} 

		$result = M('Invoice')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listAll');
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			
			
			
			$data	=	array();
			$data['invoice_id']	=	trim(I('post.invoice_id'));
			$data['invoice_number']	=	trim(I('post.invoice_number'));
			$data['invoice_date']	=	trim(I('post.invoice_date'));
			$data['invoice_date']	=	strtotime($data['invoice_date']);
			$data['client_id']	=	trim(I('post.client_id'));
			$data['total_amount']	=	trim(I('post.total_amount'));
			$data['total_amount']	=	$data['total_amount']*100;
			$data['official_fee']	=	trim(I('post.official_fee'));
			$data['official_fee']	=	$data['official_fee']*100;
			$data['service_fee']	=	trim(I('post.service_fee'));
			$data['service_fee']	=	$data['service_fee']*100;
			$data['follower_id']	=	trim(I('post.follower_id'));
			$data['bill_id']	=	trim(I('post.bill_id'));

			$result = M('Invoice')->save($data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$invoice_id = I('get.invoice_id',0,'int');

			if(!$invoice_id){
				$this->error('未指明要编辑的发票');
			}

			$invoice_list = M('Invoice')->getByInvoiceId($invoice_id);			
			$this->assign('invoice_list',$invoice_list);
			
			//取出 Client 表的内容以及数量
			$client_list	=	D('Client')->field(true)->listAll();
			$client_count	=	count($client_list);
			$this->assign('client_list',$client_list);
			$this->assign('client_count',$client_count);
			
			//取出 Member 表的内容以及数量
			$member_list	=	D('Member')->field(true)->listAll();
			$member_count	=	count($member_list);
			$this->assign('member_list',$member_list);
			$this->assign('member_count',$member_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);

			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 invoice_id
			$invoice_id	=	trim(I('post.invoice_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'listAll');
			}
			
			if(1==$yes_btn){
				$map['invoice_id']	=	$invoice_id;
				$condition	=	M('CaseFee')->where($map)->find();
				if(is_array($condition)){
					$this->error('本发票已关联到案件费用，不可删除，只能修改');
				}
				
				$result = M('Invoice')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listAll');
				}
			}
			
		} else{
			$invoice_id = I('get.invoice_id',0,'int');

			if(!$invoice_id){
				$this->error('未指明要删除的发票');
			}

			$invoice_list = D('InvoiceView')->relation(true)->field(true)->getByInvoiceId($invoice_id);			
			$this->assign('invoice_list',$invoice_list);

			$this->display();
		}
	}
	
	//搜索
	public function search(){
		//取出 Client 表的基本内容，作为 options
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//默认查询最近一个月
		$start_time	=	strtotime("-1 month");
		$end_time	=	time();
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		
		//默认查询 0 元 至 20000 元
		$start_amount	=	0;
		$end_amount	=	2000000;
		$this->assign('start_amount',$start_amount);
		$this->assign('end_amount',$end_amount);
		
		//取出 Member 表的基本内容，作为 options
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		if(IS_POST){
			
			//接收搜索参数
			$client_id	=	I('post.client_id','0','int');
			$start_time	=	trim(I('post.start_time'));
			$start_time	=	$start_time	?	strtotime($start_time)	:	strtotime('-1 month');
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	$end_time	?	strtotime($end_time)	:	time();
			$start_amount	=	trim(I('post.start_amount'))*100;			
			$end_amount	=	trim(I('post.end_amount'))*100;	
			$follower_id	=	I('post.follower_id','0','int');
			
			//构造 maping
			$map['invoice_date']	=	array('between',$start_time.','.$end_time);
			$map['total_amount']	=	array('between',$start_amount.','.$end_amount);
			if($client_id){
				$map['client_id']	=	$client_id;
			}
			if($member_id){
				$map['follower_id']	=	$follower_id;
			}	
			
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$invoice_list = D('InvoiceView')->where($map)->listPage($p,$page_limit);
			$this->assign('invoice_list',$invoice_list['list']);
			$this->assign('invoice_page',$invoice_list['page']);
			$this->assign('invoice_count',$invoice_list['count']);
			
			//返回搜索参数
			$this->assign('client_id',$client_id);
			$this->assign('start_time',$start_time);
			$this->assign('end_time',$end_time);
			$this->assign('start_amount',$start_amount);
			$this->assign('end_amount',$end_amount);
			$this->assign('follower_id',$follower_id);			
		} 
	
	$this->display();
	}
}