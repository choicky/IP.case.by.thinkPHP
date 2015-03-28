<?php
namespace Home\Controller;
use Think\Controller;

class AccountController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//默认跳转到listPage，分页显示
	public function listAll(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit  =   C("RECORDS_PER_PAGE");
		$account_list = D('Account')->listPage($p,$limit);
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
			$this->success('新增成功', 'listAll');
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			
			$account_id	=	trim(I('post.account_id'));
			
			$data=array();
			$data['account_name']	=	trim(I('post.account_name'));
			$data['account_number']	=	trim(I('post.account_number'));
			$data['bank_name']	=	trim(I('post.bank_name'));

			$result = D('Account')->update($account_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
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
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 account_id
			$account_id	=	trim(I('post.account_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			
			if(1==$no_btn){
				$this->success('取消删除', 'listAll');
			}
			
			if(1==$yes_btn){
				$map['account_id']	=	$account_id;
				$condition	=	M('Balance')->where($map)->find();
				if(is_array($condition)){
					$this->error('本账户下面有收支流水，不可删除');
				}
				
				$result = M('Account')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listAll');
				}
			
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