<?php
namespace Home\Controller;
use Think\Controller;

class InnerBalanceController extends Controller {
    
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
		$inner_balance_list = D('InnerBalanceView')->field(true)->listPage($p,$page_limit);
		$this->assign('inner_balance_list',$inner_balance_list['list']);
		$this->assign('inner_balance_page',$inner_balance_list['page']);
		$this->assign('inner_balance_count',$inner_balance_list['count']);
		
		//取出 CostCenter 表的内容以及数量
		$cost_center_list	=	D('CostCenter')->field(true)->listBasic();
		$cost_center_count	=	count($cost_center_list);
		$this->assign('cost_center_list',$cost_center_list);
		$this->assign('cost_center_count',$cost_center_count);		
		
		//取出其他变量
		$today	=	time();
		$this->assign('today',$today);
		
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['inner_balance_name']	=	trim(I('post.inner_balance_name'));
		$data['inner_balance_date']	=	strtotime(trim(I('post.inner_balance_date')));
		$data['cost_center_id']	=	trim(I('post.cost_center_id'));
		$data['income_amount']	=	trim(I('post.income_amount'))*100;
		$data['outcome_amount']	=	trim(I('post.outcome_amount'))*100;
		
		if(!$data['inner_balance_name']){
			$this->error('未填写结算单名称');
		} 

		$result = M('InnerBalance')->add($data);
		
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
			$data['inner_balance_id']	=	trim(I('post.inner_balance_id'));
			$data['inner_balance_name']	=	trim(I('post.inner_balance_name'));
			$data['inner_balance_date']	=	strtotime(trim(I('post.inner_balance_date')));
			$data['cost_center_id']	=	trim(I('post.cost_center_id'));
			$data['income_amount']	=	trim(I('post.income_amount'))*100;
			$data['outcome_amount']	=	trim(I('post.outcome_amount'))*100;

			$result = M('InnerBalance')->save($data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败');
			}
		} else{
			$inner_balance_id = I('get.inner_balance_id',0,'int');

			if(!$inner_balance_id){
				$this->error('未指明要编辑的缴费单');
			}

			$inner_balance_list = D('InnerBalanceView')->field(true)->getByInnerBalanceId($inner_balance_id);			
			$this->assign('inner_balance_list',$inner_balance_list);
			
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
	
	//查看主键为 $inner_balance_id 的收支流水的所有 case_file
	public function view(){
		$inner_balance_id = I('get.inner_balance_id',0,'int');

		if(!$inner_balance_id){
			$this->error('未指明要查看的缴费单');
		}
		
		//取出 InnerBalance 信息
		$inner_balance_list = D('InnerBalanceView')->field(true)->getByInnerBalanceId($inner_balance_id);	
		
		//取出 CaseFile 信息
		$map_case_file['inner_balance_id']	=	$inner_balance_id;
		
		$case_file_list	=	D('CaseFileView')->field(true)->where($map_case_file)->listAll();
		$case_file_count	=	count($case_file_list);
		
		//统计信息
		$file_cost_total	=	0;
		for($j=0;$j<$case_file_count;$j++){
			$file_cost_total	+=	$case_file_list[$j]['cost_amount'];
			}
		$file_cost_total	=	$file_cost_total/100;
		
		$this->assign('inner_balance_list',$inner_balance_list);
		$this->assign('case_file_list',$case_file_list);
		$this->assign('case_file_count',$case_file_count);
		$this->assign('file_cost_total',$file_cost_total);		

		$this->display();
	}
	
	//快捷更新	
	public function adjust(){
		if(IS_POST){
			
			$data=array();
			$data['inner_balance_id']	=	trim(I('post.inner_balance_id'));
			$data['income_amount']	=	100*trim(I('post.income_amount'));			
			$data['outcome_amount']	=	100*trim(I('post.outcome_amount'));

			$result = M('InnerBalance')->save($data);
			if(false !== $result){
				$this->success('修改成功', U('InnerBalance/view','inner_balance_id='.$data['inner_balance_id']));
			}else{
				$this->error('修改失败');
			}
		} 
	}
	
	
}