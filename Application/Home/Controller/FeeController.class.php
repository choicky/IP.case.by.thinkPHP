<?php
namespace Home\Controller;
use Think\Controller;

class FeeController extends Controller {
    public function index(){
        header("Location: listFee");
    }
	
	public function listFee(){
		$p	= I("p",1,"int");
		$limit	= 10;		
		$list = D('Fee')->listFee($p,$limit);
		//var_dump($list);
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		$data	=	array();
		$data['name']	=	trim(I('post.name'));
		$data['email']	=	trim(I('post.email'));
		$data['phone']	=	trim(I('post.phone'));
		
		if(!$data['name']){
			$this->error('未填写成员姓名');
		} 
				
		$result = D('Fee')->addFee($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listFee');
		}else{
			$this->error('增加失败');
		}
	}
		
	public function edit(){
		if(IS_POST){
			
			$fee_id	=	trim(I('post.fee_id'));
			
			$data=array();
			$data['name']	=	trim(I('post.name'));
			$data['email']	=	trim(I('post.email'));
			$data['phone']	=	trim(I('post.phone'));
						
			//var_dump($data);
			$result = D('Fee')->editFee($fee_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listFee');
				//header("Location: listCaseType");
			}else{
				$this->error('修改失败');
			}
		} else{
			$fee_id = I('get.id',0,'int');

			if(!$fee_id){
				$this->error('未指明要编辑的客户');
			}

			$fee_info = D('Fee')->getByFeeId($fee_id);
			
			$this->assign('fee_info',$fee_info);
			$this->display();
		}
	}
}