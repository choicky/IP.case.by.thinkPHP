<?php
namespace Home\Controller;
use Think\Controller;

class AccountController extends Controller {
    
	//默认跳转到listAccount，显示account表列表
	public function index(){
        header("Location: listAccount");
    }
	
	//列表，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listAccount(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$account_list = D('Account')->listAccount($p,$limit);
		$this->assign('account_list',$account_list['account_list']);
		$this->assign('account_page',$account_list['page']);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['account_name']	=	trim(I('post.account_name'));
		$data['account_number']	=	trim(I('post.account_number'));
		$data['bank_name']	=	trim(I('post.bank_name'));
		
		if(!$data['account_name']){
			$this->error('未填写费用名称');
		} 

		$result = D('Account')->addAccount($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listAccount');
		}else{
			$this->error('增加失败');
		}
	}
		
	public function edit(){
		if(IS_POST){
			
			$account_id	=	trim(I('post.account_id'));
			
			$data=array();
			$data['account_name']	=	trim(I('post.account_name'));
			$data['account_number']	=	trim(I('post.account_number'));
			$data['bank_name']	=	trim(I('post.bank_name'));

			$result = D('Account')->editAccount($account_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listAccount');
				//header("Location: listAccount");
			}else{
				$this->error('修改失败');
			}
		} else{
			$account_id = I('get.id',0,'int');

			if(!$account_id){
				$this->error('未指明要编辑的客户');
			}

			$account_list = D('Account')->getByAccountId($account_id);
			
			$this->assign('account_list',$account_list);

			$this->display();
		}
	}
}