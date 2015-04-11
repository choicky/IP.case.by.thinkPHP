<?php
namespace Home\Controller;
use Think\Controller;

class PayerController extends Controller {
    
	//默认跳转到listAll，分页显示
	public function index(){
        header("Location: listAll");
    }
	
	//返回本数据表的所有数据
	public function listAll() {
		$payer_list = D('Payer')->listAll();
		$this->assign('payer_list',$payer_list['list']);
		$this->assign('payer_count',$payer_list['count']);

					
		$this->display();
	}
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$payer_list = D('Payer')->listPage($p,$page_limit);
		$this->assign('payer_list',$payer_list['list']);
		$this->assign('payer_page',$payer_list['page']);
		$this->assign('payer_count',$payer_list['count']);
					
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['payer_name']	=	trim(I('post.payer_name'));
		
		if(!$data['payer_number']){
			$this->error('未填写缴费人');
		} 

		$result = M('Payer')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listAll');
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			
			$data=array();
			$data['payer_id']	=	trim(I('post.payer_id'));
			$data['payer_name']	=	trim(I('post.payer_name'));

			$result = D('Payer')->save($data);
			if(false !== $result){
				$this->success('修改成功', 'listAll');
			}else{
				$this->error('修改失败', 'listAll');
			}
		} else{
			$payer_id = I('get.payer_id',0,'int');

			if(!$payer_id){
				$this->error('未指明要编辑的缴费人');
			}

			$payer_list = M('Payer')->getByPayerId($payer_id);			
			$this->assign('payer_list',$payer_list);
			
			$this->display();
		}
	}
	
}