<?php
namespace Home\Controller;
use Think\Controller;

class FeeTypeController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listAll");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		 header("Location: listAll");
		/*
		$p	= I("p",1,"int");
		$limit	= C('RECORDS_PER_PAGE');
		$fee_type_list = D('FeeType')->listPage($p,$limit);

		$this->assign('fee_type_list',$fee_type_list['list']);
		$this->assign('fee_type_page',$fee_type_list['page']);
		$this->assign('fee_type_count',$fee_type_list['count']);		
		
		$this->display();*/
	}
	
	//显示全部
	public function listAll(){
		$fee_type_list	=	D('FeeType')->field(true)->listAll();
		$fee_type_count	=	count($fee_type_list);
		$this->assign('fee_type_list',$fee_type_list);
		$this->assign('fee_type_count',$fee_type_count);
		$this->display();
	}
	
	//新增	
	public function add(){
		$Model	=	D('FeeType');
		if (!$Model->create()){ 
			
			 // 如果创建数据对象失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());

		}else{
			 
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			
			// 写入新增数据成功，返回案件信息页面
			$this->success('新增成功', 'listPage#addNew');
			
		}else{
			$this->error('增加失败');
		}
	}
	
	//新增
	public function update(){
		//针对 POST 的处理方式
		if(IS_POST){
			$fee_type_id	=	trim(I('post.fee_type_id'));
			
			$Model	=	D('FeeType');
			if (!$Model->create()){ 
				 
				 // 如果创建数据对象失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 
			}else{
				 
				 // 验证通过 修改数据
				 $result	=	$Model->save();		 
			}
			if(false !== $result){
				
				// 修改数据成功，返回案件信息页面
				$this->success('修改成功', 'listPage#addNew');
				
			}else{
				$this->error('修改失败');
			}
			
		//针对 GET 的处理方式
		}else{
			$fee_type_id = I('get.fee_type_id',0,'int');

			if(!$fee_type_id){
				$this->error('未指明要编辑的客户');
			}

			$fee_type_list = M('FeeType')->getByFeeTypeId($fee_type_id);
			
			$this->assign('fee_type_list',$fee_type_list);
			$this->display();
		}
	}
}