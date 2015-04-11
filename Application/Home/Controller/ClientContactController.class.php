<?php
namespace Home\Controller;
use Think\Controller;

class ClientContactController extends Controller {
    
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
		$page_limit  =   C("RECORDS_PER_PAGE");
		$client_contact_list = D('ClientContact')->listPage($p,$limit);
		$this->assign('client_contact_list',$client_contact_list['list']);
		$this->assign('client_contact_page',$client_contact_list['page']);
		$this->assign('client_contact_count',$client_contact_list['count']);
		
		$this->display();
	}

	//新增	
	public function add(){
		$client_id	=	trim(I('post.client_id'));
		
		$Model	=	D('ClientContact');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			$this->success('新增成功', U('ClientContact/view','client_id='.$client_id));
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			$client_id	=	trim(I('post.client_id'));
			$client_contact_id	=	trim(I('post.client_contact_id'));
			
			$Model	=	D('ClientContact');
			if (!$Model->create()){ // 创建数据对象
				 // 如果创建失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 //exit($Model->getError());
			}else{
				 // 验证通过 写入新增数据
				 $result	=	$Model->save();		 
			}
			if(false !== $result){
				$this->success('修改成功', U('ClientContact/view','client_id='.$client_id));
			}else{
				$this->error('修改失败');
			}
		} else{
			$client_contact_id = I('get.client_contact_id',0,'int');

			if(!$client_contact_id){
				$this->error('未指明要编辑的认领单号');
			}

			$client_contact_list = D('ClientContact')->getByClientContactId($client_contact_id);
			$this->assign('client_contact_list',$client_contact_list);
			
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 client_contact_id 和 client_id
			$client_contact_id	=	trim(I('post.client_contact_id'));
			$client_id	=	trim(I('post.client_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', U('ClientContact/view','client_id='.$client_id));
			}
			
			if(1==$yes_btn){
				
				$map['client_contact_id']	=	$client_contact_id;

				$result = M('ClientContact')->where($map)->delete();
				if(false	!==	$result){
					$this->success('删除成功', U('ClientContact/view','client_id='.$client_id));
				}else{
					$this->error('删除失败', U('ClientContact/view','client_id='.$client_id));
				}
			}
			
		} else{
			$client_contact_id = I('get.client_contact_id',0,'int');

			if(!$client_contact_id){
				$this->error('未指明要删除的流水');
			}
			
			$client_contact_list = D('ClientContact')->relation(true)->field(true)->getByClientContactId($client_contact_id);			
			$this->assign('client_contact_list',$client_contact_list);
			

			$this->display();
		}
	}
	
	
	//查看主键为 $client_id 的收支流水的所有 client_contact
	public function view(){
		$client_id = I('get.client_id',0,'int');

		if(!$client_id){
			$this->error('未指明要查看的收支流水');
		}

		$client_list = D('Client')->relation('ClientExtend')->field(true)->getByClientId($client_id);			
		$this->assign('client_list',$client_list);
		
		$map['client_id']	=	$client_id;
		$client_contact_list	=	D('ClientContact')->where($map)->listAll();
		$client_contact_count	=	count($client_contact_list);
		$this->assign('client_contact_list',$client_contact_list);
		$this->assign('client_contact_count',$client_contact_count);
		
		//取出其他变量
		$today	=	time();
		$this->assign('today',$today);

		$this->display();
	}
}