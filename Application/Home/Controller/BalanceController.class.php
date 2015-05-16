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
		$balance_list = D('Balance')->listPage($p,$page_limit);
		
		//判断是否完全分摊
		for($j=0;$j<count($balance_list['list']);$j++){
			$balance_income_amount	=	$balance_list['list'][$j]['income_amount'];
			$balance_outcome_amount	=	$balance_list['list'][$j]['outcome_amount'];
			
			$claim_income_amount	=	0;
			$claim_outcome_amount	=	0;
			for($k=0;$k<count($balance_list['list'][$j]['Claim']);$k++){
				$claim_income_amount	+=	$balance_list['list'][$j]['Claim'][$k]['income_amount'];
				$claim_outcome_amount	+=	$balance_list['list'][$j]['Claim'][$k]['outcome_amount'];
			}
			
			if($balance_income_amount	==	$claim_income_amount	and	$balance_outcome_amount	==	$claim_outcome_amount){
				$balance_list['list'][$j]['claimed']	=	1;
			}else{
				$balance_list['list'][$j]['claimed']	=	0;
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
		$data	=	array();
		$data['account_id']	=	trim(I('post.account_id'));
		$data['deal_date']	=	trim(I('post.deal_date'));
		$data['deal_date']	=	strtotime($data['deal_date']);
		$data['income_amount']	=	trim(I('post.income_amount'));
		$data['income_amount']	=	$data['income_amount']*100;
		$data['outcome_amount']	=	trim(I('post.outcome_amount'));
		$data['outcome_amount']	=	$data['outcome_amount']*100;
		$data['summary']	=	trim(I('post.summary'));
		$data['other_party']	=	trim(I('post.other_party'));
		$data['follower_id']	=	trim(I('post.follower_id'));

		if(!$data['account_id']){
			$this->error('未填写账户名称');
		} 

		$result = M('Balance')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listAll');
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			
			$data=array();
			$data['balance_id']	=	trim(I('post.balance_id'));
			$data['account_id']	=	trim(I('post.account_id'));
			$data['deal_date']	=	trim(I('post.deal_date'));
			$data['deal_date']	=	strtotime($data['deal_date']);
			$data['income_amount']	=	trim(I('post.income_amount'));
			$data['income_amount']	=	$data['income_amount']*100;
			$data['outcome_amount']	=	trim(I('post.outcome_amount'));
			$data['outcome_amount']	=	$data['outcome_amount']*100;
			$data['summary']	=	trim(I('post.summary'));
			$data['other_party']	=	trim(I('post.other_party'));
			$data['follower_id']	=	trim(I('post.follower_id'));

			$result = D('Balance')->save($data);
			if(false !== $result){
				$this->success('修改成功', U('Claim/view','balance_id='.$data['balance_id']));
			}else{
				$this->error('修改失败', U('Claim/view','balance_id='.$data['balance_id']));
			}
		} else{
			$balance_id = I('get.balance_id',0,'int');

			if(!$balance_id){
				$this->error('未指明要编辑的账户');
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

			$balance_list = D('Balance')->relation(true)->field(true)->getByBalanceId($balance_id);			
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
			$start_time	=	trim(I('post.start_time'));
			$start_time	=	$start_time	?	strtotime($start_time)	:	strtotime('-1 month');
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	$end_time	?	strtotime($end_time)	:	time();
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
			$balance_list = D('Balance')->where($map)->listAll();
			$balance_count	=	count($balance_list);
			$this->assign('balance_list',$balance_list);
			$this->assign('balance_count',$balance_count);
			
			
			//返回统计信息
			//$balance_list_tmp = D('Balance')->where($map)->select();
			//$balance_count	=	count($balance_list_tmp);
			$income_amount_total	=	0;
			$outcome_amount_total	=	0;			
			for($j=0;$j<$balance_count;$j++){
				$income_amount_total	+=	$balance_list[$j]['income_amount']/100;
				$outcome_amount_total	+=	$balance_list[$j]['outcome_amount']/100;
			}
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