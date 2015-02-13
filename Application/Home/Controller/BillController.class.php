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
		$limit	= 10;
		$bill_list = D('BillView')->listPage($p,$limit);
		$this->assign('bill_list',$bill_list['list']);
		$this->assign('bill_page',$bill_list['page']);
		
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
		$data['handler_id'] = I('post.handler_id',0,'int');
		$data['bill_date']	=	trim(I('post.bill_date'));
		//转为时间戳
		$data['bill_date']	=	time($data['bill_date']);
		$data['client_id'] = I('post.client_id',0,'int');
		$data['total_amount'] = I('post.total_amount',0,'int');
		$data['official_fee'] = I('post.official_fee',0,'int');
		$data['service_fee'] = I('post.service_fee',0,'int');
		
		if(!$data['handler_id']	or	!data['client_id']){
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
	public function edit(){
		if(IS_POST){
			$bill_id = I('post.bill_id',0,'int');
			
			$data	=	array();
			$data['handler_id'] = I('post.handler_id',0,'int');
			$data['bill_date']	=	trim(I('post.bill_date'));
			//转为时间戳
			$data['bill_date']	=	time($data['bill_date']);
			$data['client_id'] = I('post.client_id',0,'int');
			$data['total_amount'] = I('post.total_amount',0,'int');
			$data['official_fee'] = I('post.official_fee',0,'int');
			$data['service_fee'] = I('post.service_fee',0,'int');
			
			if(!$data['handler_id']	or	!data['client_id']){
				$this->error('未填写开开单人、收单人');
			} 
			
			$result = D('Bill')->edit($bill_id,$data);
			
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$bill_id = I('get.id',0,'int');

			if(!$bill_id){
				$this->error('未指明要编辑的认领单号');
			}

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
}