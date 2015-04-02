<?php
namespace Home\Controller;
use Think\Controller;

class CasePriorityController extends Controller {
    
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
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_priority_list = D('CasePriorityView')->listPage($p,$limit);
		$this->assign('case_priority_list',$case_priority_list['list']);
		$this->assign('case_priority_page',$case_priority_list['page']);
		$this->assign('case_priority_count',$case_priority_list['count']);
		
		$this->display();
	}

	//新增
	public function add(){
	
		$case_id	=	trim(I('post.case_id'));
		$map['case_id']	=	$case_id;
		$condition	=	M('Case')->where($map)->find();
		if(!is_array($condition)){
			$this->error('案件编号不正确');
		}
		
		$Model	=	D('CasePriority');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			$this->success('新增成功', 'view/case_id/'.$case_id);
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			$case_id	=	trim(I('post.case_id'));
			$case_priority_id	=	trim(I('post.case_priority_id'));
			
			$Model	=	D('CasePriority');
			if (!$Model->create()){ // 创建数据对象
				 // 如果创建失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 //exit($Model->getError());
			}else{
				 // 验证通过 写入新增数据
				 $result	=	$Model->save();		 
			}
			if(false !== $result){
				$this->success('修改成功', 'view/case_id/'.$case_id);
			}else{
				$this->error('修改失败');
			}			
			
		} else{
			$case_priority_id = I('get.case_priority_id',0,'int');

			if(!$case_priority_id){
				$this->error('未指明要编辑的优先权编号');
			}

			$case_priority_list = M('CasePriority')->getByCasePriorityId($case_priority_id);
			$this->assign('case_priority_list',$case_priority_list);
			
			//取出 Country 表的内容以及数量
			$country_list	=	D('Country')->field(true)->listAll();
			$country_count	=	count($country_list);
			$this->assign('country_list',$country_list);
			$this->assign('country_count',$country_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);
			
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_priority_id 和 case_id
			$case_priority_id	=	trim(I('post.case_priority_id'));
			$case_id	=	trim(I('post.case_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', 'view/case_id/'.$case_id);
			}
			
			if(1==$yes_btn){
				
				$map['case_priority_id']	=	$case_priority_id;

				$result = M('CasePriority')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'view/case_id/'.$case_id);
				}
			}
			
		} else{
			$case_priority_id = I('get.case_priority_id',0,'int');

			if(!$case_priority_id){
				$this->error('未指明要删除的流水');
			}
			
			$case_priority_list = D('CasePriorityView')->field(true)->getByCasePriorityId($case_priority_id);			
			$this->assign('case_priority_list',$case_priority_list);			

			$this->display();
		}
	}
	//搜索
	public function search(){
		
		//取出 Member 表的基本内容，作为 options
		$country_list	=	D('Country')->listBasic();
		$this->assign('country_list',$country_list);		
		
		if(IS_POST){
			
			//接收搜索参数
			$priority_country_id	=	I('post.priority_country_id','0','int');
			$priority_number	=	trim(I('post.priority_number'));			
						
			//构造 maping					
			if($priority_country_id){
				$map['priority_country_id']	=	$priority_country_id;
			}
			if($priority_number){
				$map['priority_number']	=	array('like','%'.$priority_number.'%');
			}
					
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_priority_list = D('CasePriorityView')->where($map)->field(true)->listPage($p,$page_limit);
			$case_priority_count	=	D('CasePriorityView')->where($map)->count();
			$this->assign('case_priority_list',$case_priority_list['list']);
			$this->assign('case_priority_page',$case_priority_list['page']);
			$this->assign('case_priority_count',$case_priority_count);
		} 
	
	$this->display();
	}
	
	//查看主键为 $case_id 的收支流水的所有 case_priority
	public function view(){
		$case_id = I('get.case_id',0,'int');

		if(!$case_id){
			$this->error('未指明要查看的案件');
		}

		$case_list = D('Case')->relation(true)->field(true)->getByCaseId($case_id);			
		$this->assign('case_list',$case_list);
		
		$map['case_id']	=	$case_id;
		$case_priority_list	=	D('CasePriorityView')->where($map)->field(true)->listAll();
		$case_priority_count	=	D('CasePriorityView')->where($map)->count();
		$this->assign('case_priority_list',$case_priority_list);
		$this->assign('case_priority_count',$case_priority_count);
		
		//取出 Country 表的内容以及数量
		$country_list	=	D('Country')->field(true)->listAll();
		$country_count	=	count($country_list);
		$this->assign('country_list',$country_list);
		$this->assign('country_count',$country_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
		$this->assign('today',$today);

		$this->display();
	}
}