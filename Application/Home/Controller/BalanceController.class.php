<?php
namespace Home\Controller;
use Think\Controller;

class BalanceController extends Controller {
    
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
		$balance_list = D('BalanceView')->listPage($p,$page_limit);
		
		for($j=0;$j<count($balance_list['list']);$j++){
			//取出 Claim 信息
			$balance_id	=	$balance_list['list'][$j]['balance_id'];
			$map_claim['balance_id']	=	$balance_id;
			$claim_list	=	D('ClaimView')->field(true)->where($map_claim)->listAll();
			
			//将 Claim 结果加入到 $balance_list
			$balance_list['list'][$j]['Claim']	=	$claim_list;
			
			//判断是否完全分摊、以及统计总金额
			$balance_income_amount	=	$balance_list['list'][$j]['income_amount'];
			$balance_outcome_amount	=	$balance_list['list'][$j]['outcome_amount'];
			
			$claim_income_amount	=	0;
			$claim_outcome_amount	=	0;
			for($k=0;$k<count($claim_list);$k++){
				$claim_income_amount	+=	$claim_list[$k]['income_amount'];
				$claim_outcome_amount	+=	$claim_list[$k]['outcome_amount'];
			}
			
			//将统计结果加入到 $balance_list			
			if($balance_income_amount	==	$claim_income_amount	and	$balance_outcome_amount	==	$claim_outcome_amount){
				$balance_list['list'][$j]['is_claimed']	=	1;
			}else{
				$balance_list['list'][$j]['is_claimed']	=	0;
			}
		}

		$this->assign('balance_list',$balance_list['list']);
		$this->assign('balance_page',$balance_list['page']);
		$this->assign('balance_count',$balance_list['count']);
		
		//取出 Account 表的内容以及数量
		$account_list	=	D('Account')->listBasic();
		$account_count	=	count($account_list);
		$this->assign('account_list',$account_list);
		$this->assign('account_count',$account_count);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$member_count	=	count($member_list);
		$this->assign('member_list',$member_list);
		$this->assign('member_count',$member_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
        $this->assign('today',$today);
	
		$this->display();
	}
	
	//新增
	public function add(){
		$income_amount	=	I('post.income_amount');
		$outcome_amount	=	I('post.outcome_amount');
		
		if(!$income_amount and !$outcome_amount){
			$this->error('未填写金额');
		}

		$Model	=	D('Balance');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}

		if(false !== $result){
			$this->success('新增成功', U('Balance/listPage'));
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			
			$balance_id	=	I('post.balance_id',0,'int');
			
			$income_amount	=	I('post.income_amount');
			$outcome_amount	=	I('post.outcome_amount');
			
			if(!$balance_id){
				$this->error('未指明要编辑的收支流水编号');
			}			
			
			if(!$income_amount and !$outcome_amount){
				$this->error('未填写金额');
			}

			$Model	=	D('Balance');
			if (!$Model->create()){ // 创建数据对象
				 // 如果创建失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 //exit($Model->getError());
			}else{
				 // 验证通过 写入新增数据
				 $result	=	$Model->save();		 
			}

			if(false !== $result){
				$this->success('修改成功', U('Claim/view','balance_id='.$balance_id));
			}else{
				$this->error('修改失败', U('Claim/view','balance_id='.$balance_id));
			}
			
		} else{
			$balance_id = I('get.balance_id',0,'int');

			if(!$balance_id){
				$this->error('未指明要编辑的收支流水编号');
			}

			$balance_list = M('Balance')->getByBalanceId($balance_id);			
			$this->assign('balance_list',$balance_list);
			
			//取出 Account 表的内容以及数量
			$account_list	=	D('Account')->listBasic();
			$account_count	=	count($account_list);
			$this->assign('account_list',$account_list);
			$this->assign('account_count',$account_count);
			
			//取出 Member 表的内容以及数量
			$member_list	=	D('Member')->listBasic();
			$member_count	=	count($member_list);
			$this->assign('member_list',$member_list);
			$this->assign('member_count',$member_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);

			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 balance_id
			$balance_id	=	trim(I('post.balance_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', U('Claim/view','balance_id='.$balance_id));
			}
			
			if(1==$yes_btn){
				$balance_list	=	M('Balance')->field(true)->getByBalanceId($balance_id);
				
				//判断关联性
				if($balance_list['bill_id']	>	0){
					$this->error('本收支流水已关联到账单，解除关联后才能删除');
				}
				if($balance_list['case_payment_id']	>	0){
					$this->error('本收支流水已关联缴费单，解除关联后才能删除');
				}
				
				$map['balance_id']	=	$balance_id;				
				$condition	=	M('Claim')->where($map)->find();
				if(is_array($condition)){
					$this->error('本收支流水已结算，不可删除，只能修改');
				}
				
				$result = M('Balance')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listAll');
				}
			}
			
		} else{
			$balance_id = I('get.balance_id',0,'int');

			if(!$balance_id){
				$this->error('未指明要删除的流水');
			}

			$balance_list = D('BalanceView')->field(true)->getByBalanceId($balance_id);

			//取出 Claim 信息
			$balance_id	=	$balance_list['balance_id'];
			$map_claim['balance_id']	=	$balance_id;
			$claim_list	=	D('ClaimView')->field(true)->where($map_claim)->listAll();
			
			//将 Claim 结果加入到 $balance_list
			$balance_list['Claim']	=	$claim_list;
			
			//判断是否完全分摊、以及统计总金额
			$balance_income_amount	=	$balance_list['income_amount'];
			$balance_outcome_amount	=	$balance_list['outcome_amount'];
			
			$claim_income_amount	=	0;
			$claim_outcome_amount	=	0;
			for($k=0;$k<count($claim_list);$k++){
				$claim_income_amount	+=	$claim_list[$k]['income_amount'];
				$claim_outcome_amount	+=	$claim_list[$k]['outcome_amount'];
			}
			
			//将统计结果加入到 $balance_list			
			if($balance_income_amount	==	$claim_income_amount	and	$balance_outcome_amount	==	$claim_outcome_amount){
				$balance_list['is_claimed']	=	1;
			}else{
				$balance_list['is_claimed']	=	0;
			}
			
			$this->assign('balance_list',$balance_list);

			$this->display();
		}
	}
	
	//搜索
	public function search(){
		//取出 Account 表的基本内容，作为 options
		$account_list	=	D('Account')->listBasic();
		$this->assign('account_list',$account_list);
		
		//默认查询最近一个月
		$start_time	=	strtotime("-1 month");
		$end_time	=	time();
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		
		//取出 Member 表的基本内容，作为 options
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		if(IS_POST){
			
			//接收搜索参数
			$account_id	=	I('post.account_id','0','int');
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	$end_time	?	strtotime($end_time)	:	time();
			$start_time	=	trim(I('post.start_time'));
			$start_time	=	$start_time	?	strtotime($start_time)	:	strtotime("-1 month",$end_time);			
			$follower_id	=	I('post.follower_id','0','int');
			
			//构造 maping
			$map['deal_date']	=	array('between',$start_time.','.$end_time);
			if($account_id){
				$map['account_id']	=	$account_id;
			}
			if($follower_id){
				$map['follower_id']	=	$follower_id;
			}	
			
			//返回结果
			//$p	= I("p",1,"int");
			//$page_limit  =   C("RECORDS_PER_PAGE");
			//$balance_list = D('Balance')->where($map)->listPage($p,$page_limit);
			$balance_list = D('BalanceView')->where($map)->listAll();
			$balance_count	=	count($balance_list);
			
			//判断是否完全分摊、以及统计总金额
			for($j=0;$j<$balance_count;$j++){
				//取出 Claim 信息
				$balance_id	=	$balance_list[$j]['balance_id'];
				$map_claim['balance_id']	=	$balance_id;
				$claim_list	=	D('ClaimView')->field(true)->where($map_claim)->listAll();
				
				//将 Claim 结果加入到 $balance_list
				$balance_list[$j]['Claim']	=	$claim_list;
				
				//判断是否完全分摊、以及统计总金额
				$balance_income_amount	=	$balance_list[$j]['income_amount'];
				$balance_outcome_amount	=	$balance_list[$j]['outcome_amount'];
				
				$claim_income_amount	=	0;
				$claim_outcome_amount	=	0;
				for($k=0;$k<count($claim_list);$k++){
					$claim_income_amount	+=	$claim_list[$k]['income_amount'];
					$claim_outcome_amount	+=	$claim_list[$k]['outcome_amount'];
				}
				
				//将统计结果加入到 $balance_list			
				if($balance_income_amount	==	$claim_income_amount	and	$balance_outcome_amount	==	$claim_outcome_amount){
					$balance_list[$j]['is_claimed']	=	1;
				}else{
					$balance_list[$j]['is_claimed']	=	0;
				}
			}
			
			$this->assign('balance_list',$balance_list);
			$this->assign('balance_count',$balance_count);
			

			$this->assign('income_amount_total',$income_amount_total);
			$this->assign('outcome_amount_total',$outcome_amount_total);
			
			//返回搜索参数
			$this->assign('account_id',$account_id);
			$this->assign('start_time',$start_time);
			$this->assign('end_time',$end_time);
			$this->assign('follower_id',$follower_id);
		
		} 
	
	$this->display();
	}
}