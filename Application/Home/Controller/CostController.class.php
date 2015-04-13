<?php
namespace Home\Controller;
use Think\Controller;

class CostController extends Controller {
    
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
		$cost_list = D('Cost')->field(true)->listPage($p,$page_limit);
		$this->assign('cost_list',$cost_list['list']);
		$this->assign('cost_page',$cost_list['page']);
		$this->assign('cost_count',$cost_list['count']);
		
		//取出 CostCenter 表的内容以及数量
		$cost_center_list	=	D('CostCenter')->field(true)->listBasic();
		$cost_center_count	=	count($cost_center_list);
		$this->assign('cost_center_list',$cost_center_list);
		$this->assign('cost_center_count',$cost_center_count);		
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
		$this->assign('today',$today);
		
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['cost_name']	=	trim(I('post.cost_name'));
		$data['cost_date']	=	strtotime(trim(I('post.cost_date')));
		$data['cost_center_id']	=	trim(I('post.cost_center_id'));
		$data['cost_amount']	=	trim(I('post.cost_amount'))*100;
		$data['other_fee']	=	trim(I('post.other_fee'))*100;
		$data['total_amount']	=	trim(I('post.total_amount'))*100;
		
		if(!$data['cost_name']){
			$this->error('未填写结算单名称');
		} 

		$result = M('Cost')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			
			$data=array();
			$data['cost_id']	=	trim(I('post.cost_id'));
			$data['cost_name']	=	trim(I('post.cost_name'));
			$data['cost_date']	=	strtotime(trim(I('post.cost_date')));
			$data['cost_center_id']	=	trim(I('post.cost_center_id'));
			$data['cost_amount']	=	trim(I('post.cost_amount'))*100;
			$data['other_fee']	=	trim(I('post.other_fee'))*100;
			$data['total_amount']	=	trim(I('post.total_amount'))*100;

			$result = M('Cost')->save($data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败');
			}
		} else{
			$cost_id = I('get.cost_id',0,'int');

			if(!$cost_id){
				$this->error('未指明要编辑的缴费单');
			}

			$cost_list = D('Cost')->relation(true)->field(true)->getByCostId($cost_id);			
			$this->assign('cost_list',$cost_list);
			
			//取出 CostCenter 表的内容以及数量
			$cost_center_list	=	D('CostCenter')->field(true)->listBasic();
			$cost_center_count	=	count($cost_center_list);
			$this->assign('cost_center_list',$cost_center_list);
			$this->assign('cost_center_count',$cost_center_count);		
		
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);

			$this->display();
		}
	}
	
	//查看主键为 $cost_id 的收支流水的所有 case_file
	public function view(){
		$cost_id = I('get.cost_id',0,'int');

		if(!$cost_id){
			$this->error('未指明要查看的缴费单');
		}

		$cost_list = D('Cost')->relation(true)->field(true)->getByCaseId($cost_id);	
		
		$case_fee_list	=	D('CaseFeeInfoView')->field(true)->listAll();
		$case_fee_count	=	count($case_fee_list);
		
		$case_file_list	=	D('CaseFileInfoView')->field(true)->listAll();
		$case_file_count	=	count($case_file_list);
		
		$this->assign('cost_list',$cost_list);
		$this->assign('case_fee_list',$case_fee_list);
		$this->assign('case_fee_count',$case_fee_count);
		$this->assign('case_file_list',$case_file_list);
		$this->assign('case_file_count',$case_file_count);		

		$this->display();
	}
	
	
}