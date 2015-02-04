<?php
namespace Home\Controller;
use Think\Controller;

class ClientController extends Controller {
    public function index(){
        $this->display();
    }
	
	public function listClient(){
		$client = D('Client');
		$clientList = $client->relation(true)->select();
		//var_dump($clientList);
		$this->assign('clientList',$clientList);
		$this->display();
	}
	
	public function editClient(){
		$arr=I('post.client_name');
		var_dump ($arr);
	}
	
	public function addClient(){
		if(!trim(I('post.name_zh'))){
			$this->error("未填写客户中文名称");
		} 
		
		$client = D('Client');
		
		$data=array();
		
		$data['name_zh'] = trim(I('post.name_zh'));
		$data['ClientExtend'] = array(
			'address_zh' => trim(I('post.address_zh')),
			'address_en' => trim(I('post.address_en')),
			'id_number' => trim(I('post.id_number')),
			'business_number' => trim(I('post.business_number')),
			'tax_number' => trim(I('post.tax_number')),
			);	
		
		$result = $client->relation(true)->add($data);
		
		if($result == true){
			$this->success("增加成功");
		}else{
			$this->error("增加失败");
		}
	}
}