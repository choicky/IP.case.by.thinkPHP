<?php
namespace Home\Controller;
use Think\Controller;

class CaseTypeController extends Controller {
    public function index(){
        header("Location: listCaseType");
    }
	
	public function listCaseType(){
		$p	= I("p",1,"int");
		$limit	= 10;		
		$list = D('CaseType')->listCaseType($p,$limit);
		//var_dump($list);
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		$data	=	array();
		$data['case_type_name']	=	trim(I('post.case_type_name'));
		
		if(!$data['case_type_name']){
			$this->error('未填写案件类型名称');
		} 
		
		$result = D('CaseType')->addCaseType($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listCaseType');
		}else{
			$this->error('增加失败');
		}
	}
	
	
	
	public function edit(){
		if(IS_POST){
			
			$case_type_id	=	trim(I('post.case_type_id'));
			
			$data=array();
			$data['case_type_name'] = trim(I('case_type_name'));
						
			//var_dump($data);
			$result = D('CaseType')->editCaseType($case_type_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listCaseType');
				//header("Location: listCaseType");
			}else{
				$this->error('修改失败');
			}
		} else{
			$case_type_id = I('get.id',0,'int');

			if(!$case_type_id){
				$this->error('未指明要编辑的费用');
			}

			$case_info = D('CaseType')->getByCaseTypeId($case_type_id);
			
			$this->assign('case_info',$case_info);
			$this->display();
		}
	}
}