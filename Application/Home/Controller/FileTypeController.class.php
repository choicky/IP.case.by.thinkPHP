<?php
namespace Home\Controller;
use Think\Controller;

class FileTypeController extends Controller {
    
	//默认跳转到pageList，分页显示
	public function index(){
        header("Location: pageList");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function pageList(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$file_type_list = D('FileType')->pageList($p,$limit);
		$this->assign('file_type_list',$file_type_list['list']);
		$this->assign('file_type_page',$file_type_list['page']);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['file_name']	=	trim(I('post.file_name'));
		
		if(!$data['file_name']){
			$this->error('未填写费用名称');
		} 

		$result = M('FileType')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'pageList');
		}else{
			$this->error('增加失败', 'pageList');
		}
	}
		
	public function edit(){
		if(IS_POST){
			$file_type_id	=	trim(I('post.file_type_id'));
			
			$data=array();
			$data['file_name']	=	trim(I('post.file_name'));

			$result = D('FileType')->edit($file_type_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'pageList');
			}else{
				$this->error('修改失败', 'pageList');
			}
		} else{
			$file_type_id = I('get.id',0,'int');

			if(!$file_type_id){
				$this->error('未指明要编辑的客户');
			}

			$file_type_list = M('FileType')->getByFileTypeId($file_type_id);
			
			$this->assign('file_type_list',$file_type_list);

			$this->display();
		}
	}
}