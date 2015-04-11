<?php
namespace Home\Controller;
use Think\Controller;

class MemberController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//默认跳转到listPage，分页显示
	public function listAll(){
        header("Location: listPage");
    }
	
	//列表，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= C('RECORDS_PER_PAGE');
		$member_list = D('Member')->field(true)->listPage($p,$limit);
		$this->assign('member_list',$member_list['list']);
		$this->assign('member_page',$member_list['page']);
		$this->assign('member_count',$member_list['count']);

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
			
			$data=array();
			$data['member_id']	=	trim(I('post.member_id'));
			$data['member_name']	=	trim(I('post.member_name'));
			$data['member_email']	=	trim(I('post.member_email'));
			$data['member_phone']	=	trim(I('post.member_phone'));

			$result = M('Member')->save($data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败','listPage');
			}
		} else{
			$member_id = I('get.member_id',0,'int');

			if(!$member_id){
				$this->error('未指明要编辑的成员');
			}

			$member_list = M('Member')->getByMemberId($member_id);
			
			$this->assign('member_list',$member_list);

			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 member_id
			$member_id	=	trim(I('post.member_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'listPage');
			}
			
			if(1==$yes_btn){
				
				
				//判断是否有结算信息
				$map_claim['claimer_id']	=	$member_id;
				$condition_claim	=	M('Claim')->where($map_claim)->find();
				if(is_array($condition_claim)){
					$this->error('该客户/申请人下面有结算信息，不可删除，只能修改');
				}
				
				
				//判断是否有案件信息
				$map_case['follower_id']	=	$member_id;
				$condition_case	=	M('Case')->where($map_case)->find();
				if(is_array($condition_case)){
					$this->error('该人员已关联到案件，不可删除，只能修改');
				}
				
				//判断是否有付款信息
				$map_balance['follower_id']	=	$member_id;
				$condition_balance	=	M('Balance')->where($map_balance)->find();
				if(is_array($condition_balance)){
					$this->error('该人员已有付款信息，不可删除，只能修改');
				}
				
				//判断是否有账单信息
				$map_bill['follower_id']	=	$member_id;
				$condition_bill	=	M('Bill')->where($map_bill)->find();
				if(is_array($condition_bill)){
					$this->error('该人员已有账单信息，不可删除，只能修改');
				}
				
				$map_member['member_id']	=	$member_id;
				$result = M('Member')->where($map_member)->delete();
				if($result){
					$this->success('删除成功', 'listPage');
				}
			}
			
		} else{
			$member_id = I('get.member_id',0,'int');

			if(!$member_id){
				$this->error('未指明要删除的客户/申请人');
			}

			$member_list = M('Member')->field(true)->getByMemberId($member_id);			
			$this->assign('member_list',$member_list);

			$this->display();
		}
	}
}