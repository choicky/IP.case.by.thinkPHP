<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {
    
	//默认跳转到 Index/index  
	public function index(){
        header('Location: '.U('Index/index'));
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
			cookie('member_id',$list['member_id']);
			cookie('user_group_id',$list['user_group_id']);
			$this->success('登陆成功',U('Index/index'));
		}else{
			$this->error('用户名与密码不匹配');
		}
		
		//$this->display();
    }
	
	//注销
	public function logout(){
        cookie('member_id',null);
		cookie('user_group_id',null);
		if(!cookie('member_id') and !cookie('user_group_id')){
			$this->success('注销成功',U('User/login'));
		}
    }
	
	//修改密码的界面
	public function changePassword(){
        $this->display();
    }
	
	//检测是否能够修改密码
	public function checkPassword(){
        $member_id	=	cookie('member_id');
		if(!$member_id){
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
		
		$result	=	D('User')->checkPassword($member_id,$current_password);
		if(!$result){
			$this->error('当前密码验证失败');
		}
		
		$map_user['member_id']	=	$member_id;
		$data_user['user_password']	=	md5($new_password);
		
		$result	=	D('User')->where($map_user)->save($data_user);
		if(false	!==	$result){
			$this->success('密码修改成功');
		}
		
		//$this->display();
    }
}