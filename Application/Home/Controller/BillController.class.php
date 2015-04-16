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
		$data['handler_id'] = I('post.handler_id',0,'int');
		$data['bill_date']	=	trim(I('post.bill_date'));
		//转为时间戳
		$data['bill_date']	=	time($data['bill_date']);
		$data['client_id'] = I('post.client_id',0,'int');
		$data['total_amount'] = I('post.total_amount',0,'int');
		$data['official_fee'] = I('post.official_fee',0,'int');
		$data['service_fee'] = I('post.service_fee',0,'int');
		
		if(!$data['handler_id']	or	!$data['client_id']){
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
			
			if(!$data['handler_id']	or	!$data['client_id']){
				$this->error('未填写开开单人、收单人');
			} 
			
			$map['bill_id']	=	I('post.bill_id',0,'int');
			$result = M('Bill')->where($map)->save($data);
			/*
			if(false == $result){
					$this->error('修改1失败');
				}*/
			
			$Model	=	M('BillInvoice');
			for($i=0;	$i<count(I('post.invoice_id'));	$i++){
				$bill_invoice_id	=	trim(I('post.bill_invoice_id')[$i]);
				$invoice_id	=	trim(I('post.invoice_id')[$i]);
				
				var_dump($bill_invoice_id);
				var_dump($invoice_id);
				var_dump('aaaa');
				
				//如果主键不存在
				if(!$bill_invoice_id){
					//如果主键不存在，且 invoice_id 不为零，就增加
					if($invoice_id){
						$bill_invoice[$i]	=	array(
							'bill_id'			=>	$bill_id,
							'invoice_id'		=>	$invoice_id
						);
						$result	=	$Model->add($bill_invoice[$i]);
						/*if(false == $result){
							$this->error('修改2失败');
						}*/
					}else{
					//如果主键不存在，且 invoice_id 为0，不做处理；
					}
				
				//如果主键存在
				}else{
					//如果主键存在，且 invoice_id 不为零，就更新
					if($invoice_id){ 
						$map['bill_invoice_id']	=	$bill_invoice_id;
						$bill_invoice[$i]	=	array(
							'bill_id'			=>	$bill_id,
							'invoice_id'		=>	$invoice_id
						);
						$result	=	$Model->where($map)->save($bill_invoice[$i]);
						/*if(false == $result){
							$this->error('修改3失败');
						}*/
					}else{
					//如果主键存在，且invoice_id 为零，就删除
						$map['bill_invoice_id']	=	$bill_invoice_id;
						$result	=	$Model->where($map)->delete();
						/*if(false == $result){
							$this->error('修改4失败');
						}*/
					}
					
				}
				
			}
		} else{
			$bill_id = I('get.id',0,'int');

			if(!$bill_id){
				$this->error('未指明要编辑的认领单号');
			}
			
			$bill_list = D('Bill')->relation(true)->getByBillId($bill_id);
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
	public function testa(){
		$bill_list = D('Bill')->listAll();
		print_r($bill_list);
		print_r($bill_list['BillInvoice']);
		
	}
}