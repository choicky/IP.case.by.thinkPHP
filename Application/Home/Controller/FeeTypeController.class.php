<?php
namespace Home\Controller;
use Think\Controller;

class FeeTypeController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listFeeType");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= C('RECORDS_PER_PAGE');
		$fee_type_list = D('FeeType')->listPage($p,$limit);

		$this->assign('fee_type_list',$fee_type_list['list']);
		$this->assign('fee_type_page',$fee_type_list['page']);
		$this->assign('fee_type_count',$fee_type_list['count']);		
		
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['fee_type_name']	=	trim(I('post.fee_type_name'));
		$data['fee_type_name_cpc']	=	trim(I('post.fee_type_name_cpc'));
		$data['fee_default_amount']	=	trim(I('post.fee_default_amount'));
		
		if(!$data['fee_type_name']){
			$this->error('未填写费用名称');
		} 
				
		$result = M('FeeType')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败', 'listPage');
		}
	}
	
	//新增
	public function update(){
		if(IS_POST){
			
			$data=array();
			$data['fee_type_id']	=	trim(I('post.fee_type_id'));
			$data['fee_type_name']	=	trim(I('post.fee_type_name'));
			$data['fee_type_name_cpc']	=	trim(I('post.fee_type_name_cpc'));
			$data['fee_default_amount']	=	trim(I('post.fee_default_amount'));
						
			$result = D('FeeType')->save($data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
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