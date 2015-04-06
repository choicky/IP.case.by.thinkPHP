<?php
namespace Home\Controller;
use Think\Controller;

class CasePhaseController extends Controller {
    
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
		$limit  =   C("RECORDS_PER_PAGE");
		$case_phase_list = D('CasePhase')->listPage($p,$limit);
		$this->assign('case_phase_list',$case_phase_list['list']);
		$this->assign('case_phase_page',$case_phase_list['page']);
		$this->assign('case_phase_page',$case_phase_list['page']);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['case_phase_name']	=	trim(I('post.case_phase_name'));
		
		if(!$data['case_phase_name']){
			$this->error('未填写账户名称');
		} 

		$result = M('CasePhase')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listAll');
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			
			$case_phase_id	=	trim(I('post.case_phase_id'));
			
			$data=array();
			$data['case_phase_name']	=	trim(I('post.case_phase_name'));

			$result = D('CasePhase')->update($case_phase_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$case_phase_id = I('get.case_phase_id',0,'int');

			if(!$case_phase_id){
				$this->error('未指明要编辑的账户');
			}

			$case_phase_list = M('CasePhase')->getByCasePhaseId($case_phase_id);
			
			$this->assign('case_phase_list',$case_phase_list);

			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_phase_id
			$case_phase_id	=	trim(I('post.case_phase_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			
			if(1==$no_btn){
				$this->success('取消删除', 'listAll');
			}
			
			if(1==$yes_btn){
				$map['case_phase_id']	=	$case_phase_id;
				$condition_file	=	M('CaseFile')->where($map)->find();				
				if(is_array($condition_file)){
					$this->error('存在此阶段的文件任务，只可编辑，不可删除');
				}
				
				$condition_fee	=	M('CaseFee')->where($map)->find();
				if(is_array($condition_fee)){
					$this->error('存在此阶段的缴费任务，只可编辑，不可删除');
				}
				
				$result = M('CasePhase')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listAll');
				}
			
			}
			
		} else{
			$case_phase_id = I('get.case_phase_id',0,'int');

			if(!$case_phase_id){
				$this->error('未指明要编辑的账户');
			}
			
			$case_phase_list = M('CasePhase')->getByCasePhaseId($case_phase_id);
			
			$this->assign('case_phase_list',$case_phase_list);

			$this->display();
		}
	}
}