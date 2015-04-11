<?php
namespace Home\Controller;
use Think\Controller;

class TmCategoryController extends Controller {
    
	//默认跳转到listAll，分页显示
	public function index(){
        header("Location: listAll");
    }
	
	//返回本数据表的所有数据
	public function listAll() {
		$tm_category_list = D('TmCategory')->listAll();
		$this->assign('tm_category_list',$tm_category_list['list']);
		$this->assign('tm_category_count',$tm_category_list['count']);

					
		$this->display();
	}
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$tm_category_list = D('TmCategory')->listPage($p,$page_limit);
		$this->assign('tm_category_list',$tm_category_list['list']);
		$this->assign('tm_category_page',$tm_category_list['page']);
		$this->assign('tm_category_count',$tm_category_list['count']);
					
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['tm_category_number']	=	trim(I('post.tm_category_number'));
		$data['tm_category_name']	=	trim(I('post.tm_category_name'));
		
		if(!$data['tm_category_number']){
			$this->error('未填写商标类别编号');
		} 

		$result = M('TmCategory')->add($data);
		
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
			$data['tm_category_id']	=	trim(I('post.tm_category_id'));
			$data['tm_category_number']	=	trim(I('post.tm_category_number'));
			$data['tm_category_name']	=	trim(I('post.tm_category_name'));

			$result = D('TmCategory')->save($data);
			if(false !== $result){
				$this->success('修改成功', 'listAll');
			}else{
				$this->error('修改失败', 'listAll');
			}
		} else{
			$tm_category_id = I('get.tm_category_id',0,'int');

			if(!$tm_category_id){
				$this->error('未指明要编辑的商标类别');
			}

			$tm_category_list = M('TmCategory')->getByTmCategoryId($tm_category_id);			
			$this->assign('tm_category_list',$tm_category_list);
			
			$this->display();
		}
	}
	
}