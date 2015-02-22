<?php
namespace Home\Controller;
use Think\Controller;

class ClientController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$client_list = D('Client')->listPage($p,$limit);
		$this->assign('client_list',$client_list['list']);
		$this->assign('client_page',$client_list['page']);
		
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
		
		$result = D('Client')->relation(true)->add($data);
		
		if(false !== $result){
			$this->success("增加成功",'listPage');
		}else{
			$this->error("增加失败");
		}
	}
	
	//编辑
	public function update(){
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
			
			$result	=	D('Client')->update($client_id,$data);
			
			if(false !== $result){
				$this->success("修改成功", 'listPage');
			}else{
				$this->error("增加失败", 'listPage');
			}
		} else{
			$client_id = I('get.id',0,'int');

			if(!$client_id){
				$this->error("未指明要编辑的客户");
			}
			
			$Client = D('Client');
			$client_list = $Client->relation(true)->getByClientId($client_id);
			
			$this->assign('client_list',$client_list);
			$this->display();
		}

	}
}