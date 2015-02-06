<?php
namespace Home\Controller;
use Think\Controller;

class ClientController extends Controller {
    public function index(){
        header("Location: listClient");
    }
	
	public function listClient(){
		$client = D('Client');
		$clientList = $client->relation(true)->order('convert(name_zh using gb2312) asc')->select();
		//var_dump($clientList);
		$this->assign('clientList',$clientList);
		$this->display();
	}
	
	public function addClient(){
		if(!trim(I('post.name_zh'))){
			$this->error("未填写客户中文名称");
		} 
		
		$client = D('Client');
		
		$data=array();
		
		$data['name_zh'] = trim(I('post.name_zh'));
		$data['ClientExtend'] = array(
			'name_en' => trim(I('post.name_en')),
			'address_zh' => trim(I('post.address_zh')),
			'address_en' => trim(I('post.address_en')),
			'id_number' => trim(I('post.id_number')),
			'business_number' => trim(I('post.business_number')),
			'tax_number' => trim(I('post.tax_number')),
			);	
		
		$result = $client->relation(true)->add($data);
		
		if($result == true){
			$this->success("客户".$data['name_zh']."增加成功");
		}else{
			$this->error("增加失败");
		}
	}
	
	public function editClient(){
		if(IS_POST){
			
			$data=array();
			$data['name_zh'] = trim(I('post.name_zh'));
			$data['ClientExtend'] = array(
				'name_en' => trim(I('post.name_en')),
				'address_zh' => trim(I('post.address_zh')),
				'address_en' => trim(I('post.address_en')),
				'id_number' => trim(I('post.id_number')),
				'business_number' => trim(I('post.business_number')),
				'tax_number' => trim(I('post.tax_number')),
				);
			
			$id =  trim(I('post.client_id'));
			
			$client_extend = M('ClientExtend');
			$result = $client_extend->where(array('client_id'=>$id))->find();
			if(!is_array($result)){
				$extend['client_id']=$id;
				$client_extend->add($extend);
			}
			
			$client = D('Client');
					
			$result = $client->relation(true)->where(array('client_id'=>$id))->save($data);
			/*
			if($result == true){
				$this->success("客户".$data['name_zh']."修改成功", "listClient");
			}else{
				$this->error("增加失败", "listClient"	);
			}*/
			header("Location: listClient");
		} else{
			//$id = intval($id);
			$id = I('get.id',0,'int');

			if(!$id){
				$this->error("未指明要编辑的客户");
			}
			
			$client = D('Client');
			$client_info = $client->relation(true)->getByClientId($id);
			
			$this->assign('client_info',$client_info);
			$this->display();
		}

	}

}