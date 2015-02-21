<?php
namespace Home\Controller;
use Think\Controller;

class AccountController extends Controller {
    
	//默认跳转到pageList，分页显示
	public function index(){
        header("Location: pageList");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function pageList(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$account_list = D('Account')->pageList($p,$limit);
		$this->assign('account_list',$account_list['list']);
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
			$this->error('未填写账户名称');
		} 

		$result = M('Account')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'pageList');
		}else{
			$this->error('增加失败');
		}
	}
	
	//新增	
	public function edit(){
		if(IS_POST){
			
			$account_id	=	trim(I('post.account_id'));
			
			$data=array();
			$data['account_name']	=	trim(I('post.account_name'));
			$data['account_number']	=	trim(I('post.account_number'));
			$data['bank_name']	=	trim(I('post.bank_name'));

			$result = D('Account')->edit($account_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'pageList');
			}else{
				$this->error('修改失败', 'pageList');
			}
		} else{
			$account_id = I('get.id',0,'int');

			if(!$account_id){
				$this->error('未指明要编辑的账户');
			}

			$account_list = M('Account')->getByAccountId($account_id);
			
			$this->assign('account_list',$account_list);

			$this->display();
		}
	}
}