<?php
namespace Home\Controller;
use Think\Controller;

class FileTypeController extends Controller {
    public function index(){
        header("Location: listFileType");
    }
	
	public function listFileType(){
		$p	= I("p",1,"int");
		$limit	= 10;		
		$list = D('FileType')->listFileType($p,$limit);
		//var_dump($list);
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		$data	=	array();
		$data['file_type_name']	=	trim(I('post.file_type_name'));
		
		if(!$data['file_type_name']){
			$this->error('未填写案件类型名称');
		} 
		
		$result = D('FileType')->addFileType($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listFileType');
		}else{
			$this->error('增加失败');
		}
	}
	
	
	
	public function edit(){
		if(IS_POST){
			
			$file_type_id	=	trim(I('post.file_type_id'));
			
			$data=array();
			$data['file_type_name'] = trim(I('file_type_name'));
						
			//var_dump($data);
			$result = D('FileType')->editFileType($file_type_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listFileType');
				//header("Location: listFileType");
			}else{
				$this->error('修改失败');
			}
		} else{
			$file_type_id = I('get.id',0,'int');

			if(!$file_type_id){
				$this->error('未指明要编辑的费用');
			}

			$file_info = D('FileType')->getByCaseTypeId($file_type_id);
			
			$this->assign('file_info',$file_info);
			$this->display();
		}
	}
}