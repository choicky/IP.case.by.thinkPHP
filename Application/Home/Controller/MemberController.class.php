<?php
namespace Home\Controller;
use Think\Controller;

class MemberController extends Controller {
    public function index(){
        header("Location: listMember");
    }
	
	public function listMember(){
		$p	= I("p",1,"int");
		$limit	= 10;		
		$list = D('Member')->listMember($p,$limit);
		//var_dump($list);
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		$data	=	array();
		$data['member_name']	=	trim(I('post.member_name'));
		$data['member_email']	=	trim(I('post.member_email'));
		$data['member_phone']	=	trim(I('post.member_phone'));
		
		if(!$data['member_name']){
			$this->error('未填写成员姓名');
		} 
				
		$result = D('Member')->addMember($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listMember');
		}else{
			$this->error('增加失败');
		}
	}
		
	public function edit(){
		if(IS_POST){
			
			$member_id	=	trim(I('post.member_id'));
			
			$data=array();
			$data['member_name']	=	trim(I('post.member_name'));
			$data['member_email']	=	trim(I('post.member_email'));
			$data['member_phone']	=	trim(I('post.member_phone'));
						
			//var_dump($data);
			$result = D('Member')->editMember($member_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listMember');
				//header("Location: listCaseType");
			}else{
				$this->error('修改成功');
			}
		} else{
			$member_id = I('get.id',0,'int');

			if(!$member_id){
				$this->error('未指明要编辑的客户');
			}

			$member_info = D('Member')->getByMemberId($member_id);
			
			$this->assign('member_info',$member_info);
			$this->display();
		}
	}
}