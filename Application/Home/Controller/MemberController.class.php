<?php
namespace Home\Controller;
use Think\Controller;

class MemberController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//列表，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$member_list = D('Member')->listPage($p,$limit);
		$this->assign('member_list',$member_list['list']);
		$this->assign('member_page',$member_list['page']);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['member_name']	=	trim(I('post.member_name'));
		$data['member_email']	=	trim(I('post.member_email'));
		$data['member_phone']	=	trim(I('post.member_phone'));
		
		if(!$data['member_name']){
			$this->error('未填写姓名');
		} 

		$result = M('Member')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败', 'listPage');
		}
	}
		
	public function update(){
		if(IS_POST){
			
			$member_id	=	trim(I('post.member_id'));
			
			$data=array();
			$data['member_name']	=	trim(I('post.member_name'));
			$data['member_email']	=	trim(I('post.member_email'));
			$data['member_phone']	=	trim(I('post.member_phone'));

			$result = D('Member')->updateMember($member_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败','listPage');
			}
		} else{
			$member_id = I('get.id',0,'int');

			if(!$member_id){
				$this->error('未指明要编辑的成员');
			}

			$member_list = M('Member')->getByMemberId($member_id);
			
			$this->assign('member_list',$member_list);

			$this->display();
		}
	}
}