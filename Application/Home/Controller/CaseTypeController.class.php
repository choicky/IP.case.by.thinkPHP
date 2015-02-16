<?php
namespace Home\Controller;
use Think\Controller;

class CaseTypeController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$case_type_list = D('CaseType')->relation(true)->field(true)->listPage($p,$limit);
		$this->assign('case_type_list',$case_type_list['data']);
		$this->assign('case_type_page',$case_type_list['page']);
        
       
        
        var_dump(case_type_list);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['case_type_name']	=	trim(I('post.case_type_name'));
        $data['case_type_group_id']	=	trim(I('post.case_type_group_id'));
		
		if(!$data['case_type_name']){
			$this->error('未填写费用名称');
		} 

		$result = M('CaseType')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function edit(){
		if(IS_POST){
			$case_type_id	=	trim(I('post.case_type_id'));
			
			$data=array();
			$data['case_type_name']	=	trim(I('post.case_type_name'));
            $data['case_type_group_id']	=	trim(I('post.case_type_group_id'));

			$result = D('CaseType')->edit($case_type_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$case_type_id = I('get.id',0,'int');

			if(!$case_type_id){
				$this->error('未指明要编辑的客户');
			}

			$case_type_list = M('CaseType')->getByCaseTypeId($case_type_id);
            
            $group_data =   D('CaseTypeGroup')->select();
           // var_dump($group_data);
            $group_count    =   count($group_data);
            $this->assign('group_data',$group_data);
            $this->assign('group_count',$group_count);

			
			$this->assign('case_type_list',$case_type_list);

			$this->display();
		}
	}
}