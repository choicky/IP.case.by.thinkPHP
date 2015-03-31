<?php
namespace Home\Controller;
use Think\Controller;

class CaseGroupController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p  =   I("p",1,"int");
		$limit  =   C("RECORDS_PER_PAGE");
		$case_group_list    =   D('CaseGroup')->listPage($p,$limit);
		$this->assign('case_group_list',$case_group_list['data']);
		$this->assign('case_group_page',$case_group_list['page']);
        
		$this->display();
	}
	
	//新增
	public function add(){
		$data   =   array();
		$data['case_group_name']    =   trim(I('post.case_group_name'));
		
		if(!$data['case_group_name']){
			$this->error('未填写费用名称');
		} 

		$result = M('CaseGroup')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function update(){
		if(IS_POST){
			$case_group_id	=	I('post.case_group_id',0,'int');
			
			$data=array();
			$data['case_group_name']	=	trim(I('post.case_group_name'));

			$result = D('CaseGroup')->update($case_group_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$case_group_id = I('get.case_group_id',0,'int');

			if(!$case_group_id){
				$this->error('未指明要编辑的客户');
			}

			$case_group_list = M('CaseGroup')->getByCaseGroupId($case_group_id);
			
			$this->assign('case_group_list',$case_group_list);

			$this->display();
		}
	}
    
	//删除
	public function delete(){
		
		//检测是否 post
		if(IS_POST){
			$case_group_id	=	I('post.case_group_id',0,'int');
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'listPage');
			}
			
			if(1==$yes_btn){
				$map['case_group_id']   =   $case_group_id;
                $case_type_list =   M('CaseType')->field('case_group_id')->where($map)->find();
                if(is_array($case_type_list)){
                    $this->error('删除失败，本大类下面还有其他小类');
                }
                
                $result = M('CaseGroup')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listPage');
				}
			
			}
			
		} else{							//这是针对 get 方式的
			$case_group_id	=	I('get.case_group_id',0,'int');
			if(!$case_group_id){
				$this->error('未指明要删除的主键');
			}

			$case_group_list = M('CaseGroup')->getByCaseGroupId($case_group_id);
			$this->assign('case_group_list',$case_group_list);
			$this->display();
		}
	}

}