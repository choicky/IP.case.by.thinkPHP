<?php
namespace Home\Controller;
use Think\Controller;

class MailController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= C('RECORDS_PER_PAGE');
		$mail_list = D('MailView')->field(true)->listPage($p,$limit);
		
        /**
		//判断到账情况
		for($j=0;$j<count($mail_list['list']);$j++){
			$mail_id	=	$mail_list['list'][$j]['mail_id'];
			$total_amount	=	$mail_list['list'][$j]['total_amount'];
			
			//查找到账情况，并赋值
			$map_balance['mail_id']	=	$mail_id;
			$balance_list	=	M('Balance')->field('balance_id,income_amount,outcome_amount')->where($map_balance)->select();
			$mail_list['list'][$j]['Balance']	=	$balance_list;
			
			//判断是否全额到账
			$total_income_amount	=	0;
			$total_outcome_amount	=	0;
			for($k=0;$k<count($balance_list);$k++){
				$total_income_amount	+=	$balance_list[$k]['income_amount'];
				$total_outcome_amount	+=	$balance_list[$k]['outcome_amount'];
			}			
			$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;			
			if($total_amount	==	$true_income_amount){
				$mail_list['list'][$j]['is_paid']	=	"全额到账";
			}else{
				$mail_list['list'][$j]['is_paid']	=	"尚未全额到账";
			}
		}**/
		
		$this->assign('mail_list',$mail_list['list']);
		$this->assign('mail_page',$mail_list['page']);
		$this->assign('mail_count',$mail_list['count']);
		
		//取出 Member 的数据
		$member_list	=	D('Member')->field(true)->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出其他的数据
		$today	=	date("Y-m-d",time());
		$this->assign('today',$today);

		$this->display();
	}
	
	//新增
	public function add(){
		$tacking_number	=	trim(I('post.tacking_number'));
        $Model	=	D('Mail');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}

		if(false !== $result){
			$this->success('新增成功',U('Mail/listPage'));
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function update(){
		if(IS_POST){
			
			$mail_id	=	I('post.mail_id',0,'int');
			
			if(!$mail_id){
				$this->error('未指明要编辑的寄件记录');
			}

			$Model	=	D('Mail');
			if (!$Model->create()){ // 创建数据对象
				 // 如果创建失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 //exit($Model->getError());
			}else{
				 // 验证通过 写入新增数据
				 $result	=	$Model->save();		 
			}

			if(false !== $result){
				//$this->success('修改成功',U('Mail/listPage'));
                $this->success('修改成功', U('Mail/view','mail_id='.$mail_id));
			}else{
				$this->error('修改失败');
			}
			
		} else{
			$mail_id = I('get.mail_id',0,'int');

			if(!$mail_id){
				$this->error('未指明要编辑的寄件记录');
			}
			
			$mail_list = D('MailView')->getByMailId($mail_id);
			$this->assign('mail_list',$mail_list);

			$member_list	=	D('Member')->listBasic();
			$member_count	=	count($member_list);
			$this->assign('member_list',$member_list);
			$this->assign('member_count',$member_count);
			
			$client_list	=	D('Client')->listBasic();
			$client_count	=	count($client_list);
			$this->assign('client_list',$client_list);
			$this->assign('client_count',$client_count);

			$this->display();
		}
	}
	
	//查看主键为 $mail_id 对应的寄件
	public function view(){
		
		//接收对应的 $mail_id
		$mail_id = I('get.mail_id',0,'int');
		if(!$mail_id){
			$this->error('未指明要查看的寄件记录');
		}
		
		//取出寄件的基本信息
		$mail_list = D('MailView')->field(true)->getByMailId($mail_id);
			
		$this->assign('mail_list',$mail_list);

		$this->display();
	}
	
	//查看主键为 $mail_id 对应的开票信息、到账信息
	public function detail(){
		
		//接收对应的 $mail_id
		$mail_id = I('get.mail_id',0,'int');
		if(!$mail_id){
			$this->error('未指明要查看的寄件记录');
		}
		
		//取出寄件记录的基本信息
		$mail_list = D('MailView')->field(true)->getByMailId($mail_id);
				
		//取出到账信息
		$map_balance['mail_id']	=	$mail_id;
		$balance_list	=	D('BalanceView')->where($map_balance)->listAll();
		$balance_count	=	count($balance_list);		
				
		//判断到账情况
		$total_amount	=	$mail_list['total_amount'];
		
		$total_income_amount	=	0;
		$total_outcome_amount	=	0;		
		for($j=0;$j<$balance_count;$j++){
			$balance_id_list[$j]	=	$balance_list[$j]['balance_id'];
			$total_income_amount	+=	$balance_list[$j]['income_amount'];
			$total_outcome_amount	+=	$balance_list[$j]['outcome_amount'];
		}
		
		$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;		
		
		if($total_amount	==	$true_income_amount){
			$mail_list['is_paid']	=	"全额到账";
		}else{
			$mail_list['is_paid']	=	"尚未全额到账";
		}
		
		$this->assign('mail_list',$mail_list);		
		
		//定义查询
		$map_file_or_fee['mail_id']	=	$mail_id;
		
		//取出交文产生的信息
		$case_file_list	=	D('CaseFileView')->where($map_file_or_fee)->listAll();
		
		//交文费用统计信息
		$case_file_count	=	count($case_file_list);	
		$file_official_fee=0;
		$file_service_fee=0;
		for($j=0;$j<$case_file_count;$j++){
			$file_official_fee	+=	$case_file_list[$j]['official_fee']/100;
			$file_service_fee	+=	$case_file_list[$j]['service_fee']/100;
		}
		$file_total_amount	=	$file_official_fee	+	$file_service_fee;
		
		//传递结果
		$this->assign('case_file_list',$case_file_list);
		$this->assign('case_file_count',$case_file_count);
		$this->assign('file_total_amount',$file_total_amount);
		$this->assign('file_official_fee',$file_official_fee);
		$this->assign('file_service_fee',$file_service_fee);
		
		
		//取出交费的信息
		$case_fee_list	=	D('CaseFeeView')->where($map_file_or_fee)->listAll();
		
		//交费费用统计信息
		$case_fee_count	=	count($case_fee_list);	
		$fee_official_fee=0;
		$fee_service_fee=0;
		for($j=0;$j<$case_fee_count;$j++){
			$fee_official_fee	+=	$case_fee_list[$j]['official_fee']/100;
			$fee_service_fee	+=	$case_fee_list[$j]['service_fee']/100;
		}
		$fee_total_amount	=	$fee_official_fee	+	$fee_service_fee;
		
		//传递结果
		$this->assign('case_fee_list',$case_fee_list);
		$this->assign('case_fee_count',$case_fee_count);
		$this->assign('fee_total_amount',$fee_total_amount);
		$this->assign('fee_official_fee',$fee_official_fee);
		$this->assign('fee_service_fee',$fee_service_fee);
		
		//汇总总费用
		$total_amount	=	$file_total_amount	+	$fee_total_amount;
		$total_official_fee	=	$file_official_fee	+	$fee_official_fee;
		$total_service_fee	=	$file_service_fee	+	$fee_service_fee;
		$this->assign('total_amount',$total_amount);
		$this->assign('total_official_fee',$total_official_fee);
		$this->assign('total_service_fee',$total_service_fee);

		$this->display();
	}
	
	//搜索
	public function search(){
		//取出 Client 表的基本内容，作为 options
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//默认查询最近一个月
		$start_time	=	strtotime("-1 month");
		$end_time	=	time();
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		
		//默认查询 0 元 至 20000 元
		$start_amount	=	0;
		$end_amount	=	20000000;
		$this->assign('start_amount',$start_amount);
		$this->assign('end_amount',$end_amount);
		
		//取出 Member 表的基本内容，作为 options
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		if(IS_POST){
			
			//接收搜索参数
			$client_id	=	I('post.client_id','0','int');
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	$end_time	?	strtotime($end_time)	:	time();
			$start_time	=	trim(I('post.start_time'));
			$start_time	=	$start_time	?	strtotime($start_time)	:	strtotime("-1 month",$end_time);
			$start_amount	=	trim(I('post.start_amount'))*100;			
			$end_amount	=	trim(I('post.end_amount'))*100;	
			$follower_id	=	I('post.follower_id','0','int');
			$is_paid	=	I('post.is_paid','0','int');			
			
			//构造 maping
			$map_mail['mail_date']	=	array('between',$start_time.','.$end_time);
			$map_mail['total_amount']	=	array('between',$start_amount.','.$end_amount);
			if($client_id){
				$map_mail['client_id']	=	$client_id;
			}
			if($follower_id){
				$map_mail['follower_id']	=	$follower_id;
			}	
			
			//根据到账情况不同进行处理
			if(0==$is_paid){
				$mail_list = D('MailView')->field(true)->where($map_mail)->listAll();
				
				//判断到账情况
				for($j=0;$j<count($mail_list);$j++){
					$mail_id	=	$mail_list[$j]['mail_id'];
					$total_amount	=	$mail_list[$j]['total_amount'];
					
					//查找到账情况，并赋值
					$map_balance['mail_id']	=	$mail_id;
					$balance_list	=	M('Balance')->field('balance_id,income_amount,outcome_amount')->where($map_balance)->select();
					$mail_list[$j]['Balance']	=	$balance_list;
					
					//判断是否全额到账
					$total_income_amount	=	0;
					$total_outcome_amount	=	0;
					for($k=0;$k<count($balance_list);$k++){
						$total_income_amount	+=	$balance_list[$k]['income_amount'];
						$total_outcome_amount	+=	$balance_list[$k]['outcome_amount'];
					}			
					$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;			
					if($total_amount	==	$true_income_amount){
						$mail_list[$j]['is_paid']	=	"全额到账";
					}else{
						$mail_list[$j]['is_paid']	=	"尚未全额到账";
					}
					/*
					//查找到账情况
					$map_balance['mail_id']	=	$mail_id;
					$balance_list	=	M('Balance')->field('sum(income_amount) as total_income_amount,sum(outcome_amount) as total_outcome_amount')->where($map_balance)->select();
					$true_income_amount	=	$claim_list[0]['total_income_amount']	-	$claim_list[0]['total_outcome_amount'];		
					if($total_amount	==	$true_income_amount){
						$mail_list[$j]['is_paid']	=	"全额到账";
					}else{
						$mail_list[$j]['is_paid']	=	"尚未全额到账";
					}*/
				}
			}
			if(1==$is_paid){
				$mail_list_tmp = D('MailView')->field(true)->where($map_mail)->listAll();
								
				for($j=0;$j<count($mail_list_tmp);$j++){
					$mail_id	=	$mail_list_tmp[$j]['mail_id'];
					$total_amount	=	$mail_list_tmp[$j]['total_amount'];
					
					//查找到账情况，并赋值
					$map_balance['mail_id']	=	$mail_id;
					$balance_list	=	M('Balance')->field('balance_id,income_amount,outcome_amount')->where($map_balance)->select();
					$mail_list_tmp[$j]['Balance']	=	$balance_list;
					
					//判断是否全额到账
					$total_income_amount	=	0;
					$total_outcome_amount	=	0;
					for($k=0;$k<count($balance_list);$k++){
						$total_income_amount	+=	$balance_list[$k]['income_amount'];
						$total_outcome_amount	+=	$balance_list[$k]['outcome_amount'];
					}			
					$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;			
					if($total_amount	==	$true_income_amount){
						$mail_list[$j]	=	$mail_list_tmp[$j];
						$mail_list[$j]['is_paid']	=	"全额到账";
					}
					/*
					//查找到账情况
					$map_balance['mail_id']	=	$mail_id;
					$balance_list	=	M('Balance')->field('sum(income_amount) as total_income_amount,sum(outcome_amount) as total_outcome_amount')->where($map_balance)->select();
					$true_income_amount	=	$claim_list[0]['total_income_amount']	-	$claim_list[0]['total_outcome_amount'];		
					if($total_amount	==	$true_income_amount){
						$mail_list[$j]	=	$mail_list_tmp[$j];
						$mail_list[$j]['is_paid']	=	"全额到账";
					}*/
				}					
			}
			if(2==$is_paid){
				$mail_list_tmp = D('MailView')->field(true)->where($map_mail)->listAll();
								
				for($j=0;$j<count($mail_list_tmp);$j++){
					$mail_id	=	$mail_list_tmp[$j]['mail_id'];
					$total_amount	=	$mail_list_tmp[$j]['total_amount'];
					
					//查找到账情况，并赋值
					$map_balance['mail_id']	=	$mail_id;
					$balance_list	=	M('Balance')->field('balance_id,income_amount,outcome_amount')->where($map_balance)->select();
					$mail_list_tmp[$j]['Balance']	=	$balance_list;
					
					//判断是否全额到账
					$total_income_amount	=	0;
					$total_outcome_amount	=	0;
					for($k=0;$k<count($balance_list);$k++){
						$total_income_amount	+=	$balance_list[$k]['income_amount'];
						$total_outcome_amount	+=	$balance_list[$k]['outcome_amount'];
					}			
					$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;			
					if($total_amount	!=	$true_income_amount){
						$mail_list[$j]	=	$mail_list_tmp[$j];
						$mail_list[$j]['is_paid']	=	"尚未全额到账";
					}
					
					/*
					//查找到账情况
					$map_balance['mail_id']	=	$mail_id;
					$balance_list	=	M('Balance')->field('sum(income_amount) as total_income_amount,sum(outcome_amount) as total_outcome_amount')->where($map_balance)->select();
					$true_income_amount	=	$claim_list[0]['total_income_amount']	-	$claim_list[0]['total_outcome_amount'];					
					if($total_amount	!=	$true_income_amount){
						$mail_list[$j]	=	$mail_list_tmp[$j];
						$mail_list[$j]['is_paid']	=	"尚未全额到账";
					}*/
				}					
			}
			
			$mail_count	=	count($mail_list);
			
			$this->assign('mail_list',$mail_list);
			$this->assign('mail_count',$mail_count);
			
			//返回搜索参数
			$this->assign('client_id',$client_id);
			$this->assign('start_time',$start_time);
			$this->assign('end_time',$end_time);
			$this->assign('start_amount',$start_amount);
			$this->assign('end_amount',$end_amount);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_paid',$is_paid);		
		} 
	//取出其他的数据
	$today	=	date("Y-m-d",time());
	$this->assign('today',$today);
	
	$this->display();
	}
	
	//快捷更新	
	public function adjust(){
		if(IS_POST){
			
			$data=array();
			$data['mail_id']	=	trim(I('post.mail_id'));
			$data['total_amount']	=	100*trim(I('post.total_amount',0));
			$data['official_fee']	=	100*trim(I('post.official_fee',0));
			$data['service_fee']	=	100*trim(I('post.service_fee',0));
			$data['other_fee']	=	100*trim(I('post.other_fee',0));
			
			$data['total_amount']	=	$data['total_amount']	+	$data['other_fee'];

			$result = M('Mail')->save($data);
			if(false !== $result){
				$this->success('修改成功', U('Mail/detail','mail_id='.$data['mail_id']));
			}else{
				$this->error('修改失败');
			}
		} 
	}
	
}