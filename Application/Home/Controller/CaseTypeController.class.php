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
		$p  =   I("p",1,"int");
		$limit  =   C('RECORDES_PER_PAGE');
		$case_type_data = D('CaseType')->relation(true)->field(true)->listPage($p,$limit);
		$this->assign('case_type_data',$case_type_data['data']);
		$this->assign('case_type_page',$case_type_data['page']);
        
        $case_group_data =   D('CaseGroup')->select();
        $case_group_count    =   count($case_group_data);
        $this->assign('case_group_data',$case_group_data);
        $this->assign('case_group_count',$case_group_count);
       
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['case_type_name']	=	trim(I('post.case_type_name'));
        $data['case_group_id']	=	trim(I('post.case_group_id'));
		
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
	public function update(){
		if(IS_POST){
			$case_type_id	=	I('post.case_type_id',0,'int');
			
			$data=array();
			$data['case_type_name']	=	trim(I('post.case_type_name'));
            $data['case_group_id']	=	trim(I('post.case_group_id'));

			$result = D('CaseType')->update($case_type_id,$data);
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

			$case_type_data = D('CaseType')->field(true)->relation(true)->getByCaseTypeId($case_type_id);
                        
            $case_group_data =   D('CaseGroup')->select();
            $case_group_count    =   count($case_group_data);
            $this->assign('case_type_data',$case_type_data);
            $this->assign('case_group_data',$case_group_data);
            $this->assign('case_group_count',$case_group_count);

			$this->display();
		}
	}
    
    	//删除
	public function delete(){
		
		//检测是否 post
		if(IS_POST){
			$case_type_id	=	I('post.case_type_id',0,'int');
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'listPage');
			}
			
			if(1==$yes_btn){
				$map['case_type_id']   =   $case_type_id;
                $case_type_data =   M('Case')->field('case_type_id')->where($map)->find();
                if(is_array($case_type_data)){
                    $this->error('删除失败，本类型下面有案件');
                }
                
                $result = M('CaseType')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listPage');
				}			
			}
			
		} else{							//这是针对 get 方式的
			$case_type_id	=	I('get.id',0,'int');
			if(!$case_type_id){
				$this->error('未指明要删除的主键');
			}

			$case_type_data = D('CaseType')->field(true)->relation(true)->getByCaseTypeId($case_type_id);
			$this->assign('case_type_data',$case_type_data);
			$this->display();
		}
	}

}