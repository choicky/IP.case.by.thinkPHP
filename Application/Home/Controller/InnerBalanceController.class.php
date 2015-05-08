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
		$inner_balance_list = D('InnerBalanceView')->listPage($p,$page_limit);
		$this->assign('inner_balance_list',$inner_balance_list['list']);
		$this->assign('inner_balance_page',$inner_balance_list['page']);
		$this->assign('inner_balance_count',$inner_balance_list['count']);
		
		//取出 CostCenter 表的内容以及数量
		$cost_center_list	=	D('cost_center')->field(true)->listBasic();
		$this->assign('cost_center_list',$cost_center_list);
		
		
		//取出其他变量
		$start_date	=	strtotime("-1 month");
		$end_date	=	time();
		$this->assign('start_date',$start_date);
		$this->assign('end_date',$end_date);
		
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['cost_center_id']	=	trim(I('post.cost_center_id'));
		$data['end_date']	=	trim(I('post.end_date'));
		$data['end_date']	=	$data['end_date']	?	strtotime($data['end_date'])	:	time();
		$data['start_date']	=	trim(I('post.start_date'));
		$data['start_date']	=	$data['start_date']	?	strtotime($data['start_date'])	:	strtotime("-1 month",$data['end_date']);
					
		
		if(!$data['cost_center_id']){
			$this->error('未选择内部结算中心');
		}

		if($data['end_date']<$data['start_date']){
			$this->error('起始时间不正确');
		}
		
		//从 Claim 表找出实际收支
		$map_claim['cost_center_id']	=	$data['cost_center_id'];
		$map_claim['claim_date']	=	array('between',$data['start_date'].','.$data['end_date']);
		$claim_list	=	M('Claim')->field(true)->where($map_claim)->select();		
		$true_income_amount	=	0;
		$true_outcome_amount	=	0;
		if(is_array($claim_list)){
			for($j=0;$j<count($claim_list);$j++){
				$true_income_amount	+=	$claim_list[$j]['income_amount'];
				$true_outcome_amount	+=	$claim_list[$j]['outcome_amount'];
			}
		}
		
		//从 Cost 表找出内部收支
		$map_cost['cost_center_id']	=	$data['cost_center_id'];
		$map_cost['cost_date']	=	array('between',$data['start_date'].','.$data['end_date']);
		$cost_list	=	M('Cost')->field(true)->where($map_cost)->select();		
		$inner_income_amount	=	0;
		$inner_outcome_amount	=	0;
		if(is_array($cost_list)){
			for($j=0;$j<count($cost_list);$j++){
				$inner_income_amount	+=	$cost_list[$j]['income_amount'];
				$inner_outcome_amount	+=	$cost_list[$j]['outcome_amount'];
			}
		}
		
		//构造其他数值
		$data['true_income_amount']	=	$true_income_amount;
		$data['true_outcome_amount']	=	$true_outcome_amount;
		$data['inner_income_amount']	=	$inner_income_amount;
		$data['inner_outcome_amount']	=	$inner_outcome_amount;
		$data['balance_amount']	=	$true_income_amount	+	$inner_income_amount	-	$true_outcome_amount	-	$inner_outcome_amount;
		

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
			$data['cost_center_id']	=	trim(I('post.cost_center_id'));
			$data['start_date']	=	strtotime(trim(I('post.start_date')));
			$data['end_date']	=	strtotime(trim(I('post.end_date')));
			$data['true_income_amount']	=	trim(I('post.true_income_amount'))*100;
			$data['true_outcome_amount']	=	trim(I('post.true_outcome_amount'))*100;
			$data['inner_income_amount']	=	trim(I('post.inner_income_amount'))*100;
			$data['inner_outcome_amount']	=	trim(I('post.inner_outcome_amount'))*100;
			$data['balance_amount']	=	$data['true_income_amount']	-	$data['true_outcome_amount']	+	$data['inner_outcome_amount']	-	$data['inner_outcome_amount'];
		
			$result = M('InnerBalance')->save($data);
			if(false !== $result){
				$this->success('修改成功', U('InnerBalance/view','inner_balance_id='.$data['inner_balance_id']));
			}else{
				$this->error('修改失败', U('InnerBalance/view','inner_balance_id='.$data['inner_balance_id']));
			}
		} else{
			$inner_balance_id = I('get.inner_balance_id',0,'int');

			if(!$inner_balance_id){
				$this->error('未指明要编辑的缴费单');
			}

			$inner_balance_list = D('InnerBalanceView')->getByInnerBalanceId($inner_balance_id);			
			$this->assign('inner_balance_list',$inner_balance_list);
			
			//取出 cost_center 表的内容以及数量
			$cost_center_list	=	D('cost_center')->field(true)->listBasic();
			$this->assign('cost_center_list',$cost_center_list);
			
			$this->display();
		}
	}
	
	//查看主键为 $inner_balance_id 的收支流水的所有 case_file
	public function view(){
		$inner_balance_id = I('get.inner_balance_id',0,'int');

		if(!$inner_balance_id){
			$this->error('未指明要查看的汇总单');
		}
		
		//取出 InnerBalance 的信息
		$inner_balance_list = D('InnerBalanceView')->field(true)->getByInnerBalanceId($inner_balance_id);		
		$this->assign('inner_balance_list',$inner_balance_list);
		
		//取出本次汇总的基础信息
		$cost_center_id	=	$inner_balance_list['cost_center_id'];
		$start_date	=	$inner_balance_list['start_date'];
		$end_date	=	$inner_balance_list['end_date'];
		
		//从 Claim 表找出实际收支
		$map_claim['cost_center_id']	=	$cost_center_id;
		$map_claim['claim_date']	=	array('between',$start_date.','.$end_date);
		$claim_list	=	D('ClaimView')->field(true)->where($map_claim)->listAll();		
		$this->assign('claim_list',$claim_list);
		$this->assign('claim_count',count($claim_list));
		
		//统计 Claim 的信息
		$true_income_amount	=	0;
		$true_outcome_amount	=	0;
		if(is_array($claim_list)){
			for($j=0;$j<count($claim_list);$j++){
				$true_income_amount	+=	$claim_list[$j]['income_amount']/100;
				$true_outcome_amount	+=	$claim_list[$j]['outcome_amount']/100;
			}
		}
		$this->assign('true_income_amount',$true_income_amount);
		$this->assign('true_outcome_amount',$true_outcome_amount);
		
		//从 Cost 表找出内部收支
		$map_cost['cost_center_id']	=	$cost_center_id;
		$map_cost['cost_date']	=	array('between',$start_date.','.$end_date);
		$cost_list	=	D('CostView')->field(true)->where($map_cost)->listAll();
		$this->assign('cost_list',$cost_list);
		$this->assign('cost_count',count($cost_list));		
		
		//统计 Cost 的信息
		$inner_income_amount	=	0;
		$inner_outcome_amount	=	0;
		if(is_array($cost_list)){
			for($j=0;$j<count($cost_list);$j++){
				$inner_income_amount	+=	$cost_list[$j]['income_amount']/100;
				$inner_outcome_amount	+=	$cost_list[$j]['outcome_amount']/100;
			}
		}
		$this->assign('inner_income_amount',$inner_income_amount);
		$this->assign('inner_outcome_amount',$inner_outcome_amount);		

		$this->display();
	}
	
	
}