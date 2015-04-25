<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//登陆页面
	public function login(){
        $this->display();
    }
	
	//检测登陆是否正确
	public function checkLogin(){
        $user_name	=	trim(I('post.user_name'));
		$user_password	=	trim(I('post.user_password'));
		$list	=	D('User')->checkLogin($user_name,$user_password);
		if(1==$list['result']){			
			cookie('id',$list['id']);
			cookie('name',$list['name']);
			$this->success('登陆成功',U('Index/index'));
		}else{
			$this->error('用户名与密码不匹配');
		}
		
		//$this->display();
    }
	
	//注销
	public function logout(){
        cookie('id',null);
		cookie('name',null);
		if(!cookie('id') and !cookie('name')){
			$this->success('注销成功',U('User/login'));
		}
    }
	
	//修改密码的界面
	public function changePassword(){
        $this->display();
    }
	
	//检测是否能够修改密码
	public function checkPassword(){
        $user_id	=	cookie('id');
		if(!$user_id){
			$this->error('尚未登陆',U('User/login'));
		}
		
		$current_password	=	trim(I('post.current_password'));
		if(!$current_password){
			$this->error('未填写当前密码');
		}
		
		$new_password	=	trim(I('post.new_password'));
		$confirm_password	=	trim(I('post.confirm_password'));
		if(!($new_password	==	$confirm_password)){
			$this->error('新密码不匹配');
		}
		
		$result	=	D('User')->checkPassword($user_id,$current_password);
		if(!$result){
			$this->error('当前密码验证失败');
		}
		
		$map_user['user_id']	=	$user_id;
		$data_user['user_password']	=	md5($new_password);
		
		$result	=	D('User')->where($map_user)->save($data_user);
		if(false	!==	$result){
			$this->success('密码修改成功');
		}
		
		//$this->display();
    }
}