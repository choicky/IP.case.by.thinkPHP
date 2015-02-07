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
		if(!trim(I('post.name_zh'))){
			$this->error("未填写客户中文名称");
		} 

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
		
		$result = D('Client')->relation(true)->addClient($data);
		
		if(false !== $result){
			$this->success("客户".$data['name_zh']."增加成功",'listClient');
		}else{
			$this->error("增加失败");
		}
	}
	
	public function edit(){
		if(IS_POST){
			$client_id =  trim(I('post.client_id'));
			
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
			
			$result	=	D('Client')->editClient($client_id,$data);
			
			if(false !== $result){
				$this->success("客户".$data['name_zh']."修改成功", 'listClient');
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