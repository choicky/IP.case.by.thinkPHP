<?php
namespace Home\Controller;
use Think\Controller;

class CaseFileController extends Controller {
    
	//新增
	public function add(){
	
		$case_id	=	trim(I('post.case_id'));
		$map_case['case_id']	=	$case_id;
		$condition_case	=	M('Case')->where($map_case)->find();
		if(!is_array($condition_case)){
			$this->error('案件编号不正确');
		}
		
		$Model	=	D('CaseFile');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			$this->success('新增成功', U('CaseFile/view','case_id='.$case_id));
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			$case_file_data['case_file_id']	=	trim(I('post.case_file_id'));
			$case_file_data['case_id']	=	trim(I('post.case_id'));
			$case_file_data['file_type_id']	=	trim(I('post.file_type_id'));
			$case_file_data['oa_date']	=	strtotime(trim(I('post.oa_date')));			
			$case_file_data['due_date']	=	strtotime(trim(I('post.due_date')));
			$case_file_data['completion_date']	=	strtotime(trim(I('post.completion_date')));
			
			$result	=	M('CaseFile')->save($case_file_data);
			
			if(false !== $result){
				$this->success('修改成功', U('Case/view','case_id='.$case_file_data['case_id']));
			}else{
				$this->error('修改失败');
			}
			
		} else{
			$case_file_id = I('get.case_file_id',0,'int');

			if(!$case_file_id){
				$this->error('未指明要编辑的文件编号');
			}

			$case_file_list = M('CaseFile')->getByCaseFileId($case_file_id);
			$this->assign('case_file_list',$case_file_list);
			
			//取出 FileType 表的内容以及数量
			$file_type_list	=	D('FileType')->field(true)->listAll();
			$file_type_count	=	count($file_type_list);
			$this->assign('file_type_list',$file_type_list);
			$this->assign('file_type_count',$file_type_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);
			
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_file_id 和 case_id
			$case_file_id	=	trim(I('post.case_file_id'));
			$case_id	=	trim(I('post.case_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', U('Case/view','case_id='.$case_id));
			}
			
			if(1==$yes_btn){
				
				$map['case_file_id']	=	$case_file_id;

				$result = M('CaseFile')->where($map)->delete();
				if($result){
					$this->success('删除成功', U('Case/view','case_id='.$case_id));
				}
			}
			
		} else{
			$case_file_id = I('get.case_file_id',0,'int');

			if(!$case_file_id){
				$this->error('未指明要删除的流水');
			}
			
			$case_file_list = D('CaseFileView')->field(true)->getByCaseFileId($case_file_id);			
			$this->assign('case_file_list',$case_file_list);			

			$this->display();
		}
	}
	
	//查看主键为 $case_id 的收支流水的所有 case_file
	public function view(){
		$case_id = I('get.case_id',0,'int');

		if(!$case_id){
			$this->error('未指明要查看的案件');
		}

		$case_list = D('Case')->relation(true)->field(true)->getByCaseId($case_id);	
		$case_file_count	=	count($case_list['CaseFile']);
		$this->assign('case_list',$case_list);
		$this->assign('case_file_count',$case_file_count);
		
		//取出 FileType 表的内容以及数量
		$file_type_list	=	D('FileType')->field(true)->listAll();
		$file_type_count	=	count($file_type_list);
		$this->assign('file_type_list',$file_type_list);
		$this->assign('file_type_count',$file_type_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
		$this->assign('today',$today);

		$this->display();
	}
	
}