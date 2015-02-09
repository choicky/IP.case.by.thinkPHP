<?php
namespace Home\Controller;
use Think\Controller;

class ClientController extends Controller {
    public function index(){
        header("Location: listClient");
    }
	
	public function listClient(){
		$p	= I("p",1,"int");
		$limit	= 10;		
		$list = D('Client')->listClient($p,$limit);
		//var_dump($list);
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		if(!trim(I('post.client_name_zh'))){
			$this->error("未填写客户中文名称");
		} 

		$data=array();
		$data['client_name_zh'] = trim(I('post.client_name_zh'));
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
			$this->success("客户".$data['client_name_zh']."增加成功",'listClient');
		}else{
			$this->error("增加失败");
		}
	}
	
	public function edit(){
		if(IS_POST){
			$client_id =  trim(I('post.client_id'));
			
			$data=array();
			$data['client_name_zh'] = trim(I('post.client_name_zh'));
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
				$this->success("客户".$data['client_name_zh']."修改成功", 'listClient');
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
			$client_info = $client->relation(true)->getByClientId($client_id);
			
			$this->assign('client_info',$client_info);
			$this->display();
		}

	}

}