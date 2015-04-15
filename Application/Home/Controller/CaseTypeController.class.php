<?php
namespace Home\Controller;
use Think\Controller;

class CaseTypeController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//显示全部
	public function listAll(){
		$case_type_list	=	D('CaseTypeView')->field(true)->listAll();
		$case_type_count	=	count($case_type_list);
		$this->assign('case_type_list',$case_type_list);
		$this->assign('case_type_count',$case_type_count);
		
		//取出 CaseGroup 表的内容以及数量
		$case_group_list	=	D('CaseGroup')->listBasic();
		$case_group_count	=	count($case_group_list);
		$this->assign('case_group_list',$case_group_list);
		$this->assign('case_group_count',$case_group_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$this->assign('row_limit',$row_limit);
		
		$this->display();
	}
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		header("Location: listAll");
		/*
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_type_list = D('CaseTypeView')->listPage($p,$limit);
		$this->assign('case_type_list',$case_type_list['list']);
		$this->assign('case_type_page',$case_type_list['page']);
		$this->assign('case_type_count',$case_type_list['count']);
		
		//取出 CaseGroup 表的内容以及数量
		$case_group_list	=	D('CaseGroup')->listBasic();
		$case_group_count	=	count($case_group_list);
		$this->assign('case_group_list',$case_group_list);
		$this->assign('case_group_count',$case_group_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$this->assign('row_limit',$row_limit);
        	
		$this->display();*/
	}

	//新增
	public function add(){
		$data	=	array();
		$data['case_type_name']	=	trim(I('post.case_type_name'));
		$data['case_group_id']	=	trim(I('post.case_group_id'));
		
		//检测 $data['case_type_name'] 是否空值
		if(!$data['case_type_name']){
			$this->error('未填写案件小类名称');
		}
		
		//检测 $data['case_group_id'] 是否空值
		if(!$data['case_group_id']){
			$this->error('未选择案件类型');
		}

		$result = M('CaseType')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', U('CaseType/view','case_group_id='.$data['case_group_id']));
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			$case_type_id	=	trim(I('post.case_type_id'));
			
			$data=array();
			$data['case_type_name']	=	trim(I('post.case_type_name'));
			$data['case_group_id']	=	trim(I('post.case_group_id'));	
						
			$result = D('CaseTypeView')->update($case_type_id,$data);
			if(false !== $result){
				$this->success('修改成功','view/case_group_id/'.$data['case_group_id']);
			}else{
				$this->error('修改失败');
			}
		} else{
			$case_type_id = I('get.case_type_id',0,'int');

			if(!$case_type_id){
				$this->error('未指明要编辑的案件类型编号');
			}

			$case_type_list = D('CaseTypeView')->getByCaseTypeId($case_type_id);
			$this->assign('case_type_list',$case_type_list);
			
			//取出 CaseGroup 表的内容以及数量
			$case_group_list	=	D('CaseGroup')->listBasic();
			$case_group_count	=	count($case_group_list);
			$this->assign('case_group_list',$case_group_list);
			$this->assign('case_group_count',$case_group_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);
			
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_type_id 和 case_group_id
			$case_type_id	=	trim(I('post.case_type_id'));
			$case_group_id	=	trim(I('post.case_group_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', U('CaseType/view','case_group_id='.$case_group_id));
			}
			
			if(1==$yes_btn){
				
				$map['case_type_id']	=	$case_type_id;

				$result = M('CaseType')->where($map)->delete();
				if($result){
					$this->success('删除成功', U('CaseType/view','case_group_id='.$case_group_id));
				}
			}
			
		} else{
			$case_type_id = I('get.case_type_id',0,'int');

			if(!$case_type_id){
				$this->error('未指明要删除的流水');
			}
			
			$case_type_list = D('CaseTypeView')->field(true)->getByCaseTypeId($case_type_id);			
			$this->assign('case_type_list',$case_type_list);
			
			//取出 CaseGroup 表的内容以及数量
			$case_group_list	=	D('CaseGroup')->listBasic();
			$case_group_count	=	count($case_group_list);
			$this->assign('case_group_list',$case_group_list);
			$this->assign('case_group_count',$case_group_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);

			$this->display();
		}
	}

	
	//查看主键为 $case_group_id 的收支流水的所有 case_type
	public function view(){
		$case_group_id = I('get.case_group_id',0,'int');

		if(!$case_group_id){
			$this->error('未指明要查看的案件大类');
		}

		$case_group_list = D('CaseGroup')->field(true)->getByCaseGroupId($case_group_id);			
		$this->assign('case_group_list',$case_group_list);
		
		$map['case_group_id']	=	$case_group_id;
		$case_type_list	=	M('CaseType')->where($map)->select();
		$case_type_count	=	count($case_type_list);
		$this->assign('case_type_list',$case_type_list);
		$this->assign('case_type_count',$case_type_count);
		
		$this->display();
	}
}