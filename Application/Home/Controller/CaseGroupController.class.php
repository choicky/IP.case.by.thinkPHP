<?php
namespace Home\Controller;
use Think\Controller;

class CaseGroupController extends Controller {
    
	//默认跳转到pageList，分页显示
	public function index(){
        header("Location: pageList");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function pageList(){
		$p  =   I("p",1,"int");
		$limit  =   C("RECORDS_PER_PAGE");
		$case_group_data    =   D('CaseGroup')->pageList($p,$limit);
		$this->assign('case_group_data',$case_group_data['data']);
		$this->assign('case_group_page',$case_group_data['page']);
        
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
			$this->success('新增成功', 'pageList');
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function edit(){
		if(IS_POST){
			$case_group_id	=	trim(I('post.case_group_id'));
			
			$data=array();
			$data['case_group_name']	=	trim(I('post.case_group_name'));

			$result = D('CaseGroup')->edit($case_group_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'pageList');
			}else{
				$this->error('修改失败', 'pageList');
			}
		} else{
			$case_group_id = I('get.id',0,'int');

			if(!$case_group_id){
				$this->error('未指明要编辑的客户');
			}

			$case_group_data = M('CaseGroup')->getByCaseGroupId($case_group_id);
			
			$this->assign('case_group_data',$case_group_data);

			$this->display();
		}
	}
    
	//删除
	public function delete(){
		
		//检测是否 post
		if(IS_POST){
			$case_group_id	=	I('post.case_group_id');
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'pageList');
			}
			
			if(1==$yes_btn){
				$map['case_group_id']   =   $case_group_id;
                $case_type_data =   M('CaesType')->field('case_group_id')->where($map)->find();
                if(is_array($case_type_data)){
                    $this->error('删除失败，本大类下面还有其他小类');
                }else{
                    
                }
                
                //实例化GroupModel 并调用其 deleteById 方法
				$result = D('Group')->deleteById($case_group_id);
				if($result){
					$this->success('删除成功', 'listAll');
				}
			
			}
			
		} else{							//这是针对 get 方式的
			$case_group_id	=	I('get.id');
			if(!$case_group_id){
				$this->error('未指明要删除的主键');
			}

			$case_group_data = D('CaseGroup')->getByCaseGroupId($case_group_id);
			$this->assign('case_group_data',$case_group_data);
			$this->display();
		}
	}

}