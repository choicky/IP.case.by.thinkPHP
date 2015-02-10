<?php
namespace Home\Controller;
use Think\Controller;

class ClientController extends Controller {
    
	//默认跳转到listClient，显示client表列表
	public function index(){
        header("Location: listClient");
    }
	
	//列表，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listClient(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$client_list = D('Client')->listClient($p,$limit);
		$this->assign('client_list',$client_list['client_list']);
		$this->assign('client_page',$client_list['client_page']);
		
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
			'client_address_zh' => trim(I('post.client_address_zh')),
			'client_address_en' => trim(I('post.client_address_en')),
			'client_id_number' => trim(I('post.client_id_number')),
			'client_business_number' => trim(I('post.client_business_number')),
			'client_tax_number' => trim(I('post.client_tax_number')),
			);	
		
		$result = D('Client')->addClient($data);
		
		if(false !== $result){
			$this->success("增加成功",'listClient');
		}else{
			$this->error("增加失败");
		}
	}
		
	public function edit(){
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
			
			$result	=	D('Client')->editClient($client_id,$data);
			
			if(false !== $result){
				$this->success("客户".$data['client_name']."修改成功", 'listClient');
			}else{
				$this->error("增加失败", 'listClient');
			}
			//header("Location: listClient");
		} else{
			//$id = intval($id);
			$client_id = I('get.id',0,'int');

			if(!$client_id){
				$this->error("未指明要编辑的客户");
			}
			
			$client = D('Client');
			$client_list = $client->relation(true)->getByClientId($client_id);
			
			$this->assign('client_list',$client_list);
			$this->display();
		}

	}
}