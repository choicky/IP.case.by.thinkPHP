<?php
namespace Home\Controller;
use Think\Controller;

class CaseTypeGroupController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$case_type_group_list = D('CaseTypeGroup')->listPage($p,$limit);
		$this->assign('case_type_group_list',$case_type_group_list['data']);
		$this->assign('case_type_group_page',$case_type_group_list['page']);
        
        dump(case_type_group_list);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['case_type_group_name']	=	trim(I('post.case_type_group_name'));
		
		if(!$data['case_type_group_name']){
			$this->error('未填写费用名称');
		} 

		$result = M('CaseTypeGroup')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function edit(){
		if(IS_POST){
			$case_type_group_id	=	trim(I('post.case_type_group_id'));
			
			$data=array();
			$data['case_type_group_name']	=	trim(I('post.case_type_group_name'));

			$result = D('CaseTypeGroup')->edit($case_type_group_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$case_type_group_id = I('get.id',0,'int');

			if(!$case_type_group_id){
				$this->error('未指明要编辑的客户');
			}

			$case_type_group_list = M('CaseTypeGroup')->getByCaseTypeGroupId($case_type_group_id);
			
			$this->assign('case_type_group_list',$case_type_group_list);

			$this->display();
		}
	}
}