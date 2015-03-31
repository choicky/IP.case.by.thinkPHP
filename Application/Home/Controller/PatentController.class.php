<?php
namespace Home\Controller;
use Think\Controller;

class PatentController extends Controller {
    
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
		$patent_list = D('Case')->listPagePatent($p,$page_limit);
		$this->assign('patent_list',$patent_list['list']);
		$this->assign('patent_page',$patent_list['page']);
		/*
		//取出 Account 表的内容以及数量
		$account_list	=	D('Account')->field(true)->listAll();
		$account_count	=	count($account_list);
		$this->assign('account_list',$account_list);
		$this->assign('account_count',$account_count);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->field(true)->listAll();
		$member_count	=	count($member_list);
		$this->assign('member_list',$member_list);
		$this->assign('member_count',$member_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
        $this->assign('today',$today);*/
	
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

		if(!$data['account_id']){
			$this->error('未填写账户名称');
		} 

		$result = M('Patent')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listAll');
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新	
	public function update(){
		if(IS_POST){
			
			$patent_id	=	trim(I('post.patent_id'));
			
			$data=array();
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
			$data['bill_id']	=	trim(I('post.bill_id'));

			$result = D('Patent')->update($patent_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$patent_id = I('get.patent_id',0,'int');

			if(!$patent_id){
				$this->error('未指明要编辑的账户');
			}

			$patent_list = M('Patent')->getByPatentId($patent_id);			
			$this->assign('patent_list',$patent_list);
			
			//取出 Account 表的内容以及数量
			$account_list	=	D('Account')->field(true)->listAll();
			$account_count	=	count($account_list);
			$this->assign('account_list',$account_list);
			$this->assign('account_count',$account_count);
			
			//取出 Member 表的内容以及数量
			$member_list	=	D('Member')->field(true)->listAll();
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
			
			//通过 I 方法获取 post 过来的 patent_id
			$patent_id	=	trim(I('post.patent_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'listAll');
			}
			
			if(1==$yes_btn){
				$map['patent_id']	=	$patent_id;
				$condition	=	M('Claim')->where($map)->find();
				if(is_array($condition)){
					$this->error('本收支流水已结算，不可删除，只能修改');
				}
				
				$result = M('Patent')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'listAll');
				}
			}
			
		} else{
			$patent_id = I('get.patent_id',0,'int');

			if(!$patent_id){
				$this->error('未指明要删除的流水');
			}

			$patent_list = D('Patent')->relation(true)->field(true)->getByPatentId($patent_id);			
			$this->assign('patent_list',$patent_list);

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
			$start_time	=	strtotime($start_time);
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	strtotime($end_time);
			$follower_id	=	I('post.follower_id','0','int');
			
			//构造 maping
			$map['deal_date']	=	array('EGT',$start_time);
			$map['deal_date']	=	array('ELT',$end_time);			
			if($account_id){
				$map['account_id']	=	$account_id;
			}
			if($member_id){
				$map['follower_id']	=	$follower_id;
			}	
			
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$patent_list = D('Patent')->where($map)->listPage($p,$page_limit);
			$this->assign('patent_list',$patent_list['list']);
			$this->assign('patent_page',$patent_list['page']);
		
		} 
	
	$this->display();
	}
}