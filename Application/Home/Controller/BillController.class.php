<?php
namespace Home\Controller;
use Think\Controller;

class BillController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= C('RECORDS_PER_PAGE');
		$bill_list = D('BillView')->field(true)->listPage($p,$limit);
		
		//判断到账情况
		for($j=0;$j<count($bill_list['list']);$j++){
			$bill_id	=	$bill_list['list'][$j]['bill_id'];
			$total_amount	=	$bill_list['list'][$j]['total_amount'];
			
			//查找到账情况，并赋值
			$map_balance['bill_id']	=	$bill_id;
			$balance_list	=	M('Balance')->field('balance_id,income_amount,outcome_amount')->where($map_balance)->select();
			$bill_list['list'][$j]['Balance']	=	$balance_list;
			
			//判断是否全额到账
			$total_income_amount	=	0;
			$total_outcome_amount	=	0;
			for($k=0;$k<count($balance_list);$k++){
				$total_income_amount	+=	$balance_list[$k]['income_amount'];
				$total_outcome_amount	+=	$balance_list[$k]['outcome_amount'];
			}			
			$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;			
			if($total_amount	==	$true_income_amount){
				$bill_list['list'][$j]['is_paid']	=	"全额到账";
			}else{
				$bill_list['list'][$j]['is_paid']	=	"尚未全额到账";
			}
		}
		
		$this->assign('bill_list',$bill_list['list']);
		$this->assign('bill_page',$bill_list['page']);
		$this->assign('bill_count',$bill_list['count']);
		
		//取出 Member 的数据
		$member_list	=	D('Member')->field(true)->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出 Client 的数据
		$client_list	=	D('Client')->field(true)->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出其他的数据
		$today	=	date("Y-m-d",time());
		$this->assign('today',$today);

		$this->display();
	}
	
	//新增
	public function add(){
		$Model	=	D('Bill');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}

		if(false !== $result){
			$this->success('新增成功',U('Bill/listPage'));
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function update(){
		if(IS_POST){
			
			$bill_id	=	I('post.bill_id',0,'int');
			
			if(!$bill_id){
				$this->error('未指明要编辑的账单编号');
			}
			
			$Model	=	D('Bill');
			if (!$Model->create()){ // 创建数据对象
				 // 如果创建失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 //exit($Model->getError());
			}else{
				 // 验证通过 写入新增数据
				 $result	=	$Model->save();		 
			}

			if(false !== $result){
				$this->success('修改成功', U('Bill/view','bill_id='.$bill_id));
			}else{
				$this->error('修改失败');
			}
			
		} else{
			$bill_id = I('get.bill_id',0,'int');

			if(!$bill_id){
				$this->error('未指明要编辑的账单编号');
			}
			
			$bill_list = D('BillView')->getByBillId($bill_id);
			$this->assign('bill_list',$bill_list);

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
	
	//查看主键为 $bill_id 对应的开票信息、到账信息
	public function view(){
		
		//接收对应的 $bill_id
		$bill_id = I('get.bill_id',0,'int');
		if(!$bill_id){
			$this->error('未指明要查看的账单');
		}
		
		//取出账单的基本信息
		$bill_list = D('BillView')->field(true)->getByBillId($bill_id);
			
		//取出到账信息
		$map_balance['bill_id']	=	$bill_id;
		$balance_list	=	D('BalanceView')->field(true)->where($map_balance)->listAll();		

		$balance_count	=	count($balance_list);		
		$this->assign('balance_list',$balance_list);
		$this->assign('balance_count',$balance_count);
		
		//判断到账情况
		$total_amount	=	$bill_list['total_amount'];
		
		$total_income_amount	=	0;
		$total_outcome_amount	=	0;		
		for($j=0;$j<$balance_count;$j++){
			$balance_id_list[$j]	=	$balance_list[$j]['balance_id'];
			
			$total_income_amount	+=	$balance_list[$j]['income_amount'];
			$total_outcome_amount	+=	$balance_list[$j]['outcome_amount'];
		}
		
		$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;		
		
		if($total_amount	==	$true_income_amount){
			$bill_list['is_paid']	=	1;
		}else{
			$bill_list['is_paid']	=	0;
		}
		
		$this->assign('bill_list',$bill_list);
		
		/*
		$map_claim['balance_id']	=	array('in',$balance_id_list);
		$claim_list	=	D('ClaimView')->where($map_claim)->listAll();
		$claim_count	=	count($claim_list);		
		$this->assign('claim_list',$claim_list);
		$this->assign('claim_count',$claim_count);
		*/
		
		//取出发票信息
		$map_bill['bill_id']	=	$bill_id;
		$invoice_list	=	D('InvoiceView')->field(true)->where($map_bill)->listAll();
		$invoice_count	=	count($invoice_list);		
		$this->assign('invoice_list',$invoice_list);
		$this->assign('invoice_count',$invoice_count);		

		$this->display();
	}
	
	//查看主键为 $bill_id 对应的开票信息、到账信息
	public function detail(){
		
		//接收对应的 $bill_id
		$bill_id = I('get.bill_id',0,'int');
		if(!$bill_id){
			$this->error('未指明要查看的账单');
		}
		
		//取出账单的基本信息
		$bill_list = D('BillView')->field(true)->getByBillId($bill_id);
				
		//取出到账信息
		$map_balance['bill_id']	=	$bill_id;
		$balance_list	=	D('BalanceView')->where($map_balance)->listAll();
		$balance_count	=	count($balance_list);		
				
		//判断到账情况
		$total_amount	=	$bill_list['total_amount'];
		
		$total_income_amount	=	0;
		$total_outcome_amount	=	0;		
		for($j=0;$j<$balance_count;$j++){
			$balance_id_list[$j]	=	$balance_list[$j]['balance_id'];
			$total_income_amount	+=	$balance_list[$j]['income_amount'];
			$total_outcome_amount	+=	$balance_list[$j]['outcome_amount'];
		}
		
		$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;		
		
		if($total_amount	==	$true_income_amount){
			$bill_list['is_paid']	=	"全额到账";
		}else{
			$bill_list['is_paid']	=	"尚未全额到账";
		}
		
		$this->assign('bill_list',$bill_list);		
		
		//定义查询
		$map_file_or_fee['bill_id']	=	$bill_id;
		
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
			$map_bill['bill_date']	=	array('between',$start_time.','.$end_time);
			$map_bill['total_amount']	=	array('between',$start_amount.','.$end_amount);
			if($client_id){
				$map_bill['client_id']	=	$client_id;
			}
			if($follower_id){
				$map_bill['follower_id']	=	$follower_id;
			}	
			
			//根据到账情况不同进行处理
			if(0==$is_paid){
				$bill_list = D('BillView')->field(true)->where($map_bill)->listAll();
				
				//判断到账情况
				for($j=0;$j<count($bill_list);$j++){
					$bill_id	=	$bill_list[$j]['bill_id'];
					$total_amount	=	$bill_list[$j]['total_amount'];
					
					//查找到账情况，并赋值
					$map_balance['bill_id']	=	$bill_id;
					$balance_list	=	M('Balance')->field('balance_id,income_amount,outcome_amount')->where($map_balance)->select();
					$bill_list[$j]['Balance']	=	$balance_list;
					
					//判断是否全额到账
					$total_income_amount	=	0;
					$total_outcome_amount	=	0;
					for($k=0;$k<count($balance_list);$k++){
						$total_income_amount	+=	$balance_list[$k]['income_amount'];
						$total_outcome_amount	+=	$balance_list[$k]['outcome_amount'];
					}			
					$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;			
					if($total_amount	==	$true_income_amount){
						$bill_list[$j]['is_paid']	=	"全额到账";
					}else{
						$bill_list[$j]['is_paid']	=	"尚未全额到账";
					}
					/*
					//查找到账情况
					$map_balance['bill_id']	=	$bill_id;
					$balance_list	=	M('Balance')->field('sum(income_amount) as total_income_amount,sum(outcome_amount) as total_outcome_amount')->where($map_balance)->select();
					$true_income_amount	=	$claim_list[0]['total_income_amount']	-	$claim_list[0]['total_outcome_amount'];		
					if($total_amount	==	$true_income_amount){
						$bill_list[$j]['is_paid']	=	"全额到账";
					}else{
						$bill_list[$j]['is_paid']	=	"尚未全额到账";
					}*/
				}
			}
			if(1==$is_paid){
				$bill_list_tmp = D('BillView')->field(true)->where($map_bill)->listAll();
								
				for($j=0;$j<count($bill_list_tmp);$j++){
					$bill_id	=	$bill_list_tmp[$j]['bill_id'];
					$total_amount	=	$bill_list_tmp[$j]['total_amount'];
					
					//查找到账情况，并赋值
					$map_balance['bill_id']	=	$bill_id;
					$balance_list	=	M('Balance')->field('balance_id,income_amount,outcome_amount')->where($map_balance)->select();
					$bill_list_tmp[$j]['Balance']	=	$balance_list;
					
					//判断是否全额到账
					$total_income_amount	=	0;
					$total_outcome_amount	=	0;
					for($k=0;$k<count($balance_list);$k++){
						$total_income_amount	+=	$balance_list[$k]['income_amount'];
						$total_outcome_amount	+=	$balance_list[$k]['outcome_amount'];
					}			
					$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;			
					if($total_amount	==	$true_income_amount){
						$bill_list[$j]	=	$bill_list_tmp[$j];
						$bill_list[$j]['is_paid']	=	"全额到账";
					}
					/*
					//查找到账情况
					$map_balance['bill_id']	=	$bill_id;
					$balance_list	=	M('Balance')->field('sum(income_amount) as total_income_amount,sum(outcome_amount) as total_outcome_amount')->where($map_balance)->select();
					$true_income_amount	=	$claim_list[0]['total_income_amount']	-	$claim_list[0]['total_outcome_amount'];		
					if($total_amount	==	$true_income_amount){
						$bill_list[$j]	=	$bill_list_tmp[$j];
						$bill_list[$j]['is_paid']	=	"全额到账";
					}*/
				}					
			}
			if(2==$is_paid){
				$bill_list_tmp = D('BillView')->field(true)->where($map_bill)->listAll();
								
				for($j=0;$j<count($bill_list_tmp);$j++){
					$bill_id	=	$bill_list_tmp[$j]['bill_id'];
					$total_amount	=	$bill_list_tmp[$j]['total_amount'];
					
					//查找到账情况，并赋值
					$map_balance['bill_id']	=	$bill_id;
					$balance_list	=	M('Balance')->field('balance_id,income_amount,outcome_amount')->where($map_balance)->select();
					$bill_list_tmp[$j]['Balance']	=	$balance_list;
					
					//判断是否全额到账
					$total_income_amount	=	0;
					$total_outcome_amount	=	0;
					for($k=0;$k<count($balance_list);$k++){
						$total_income_amount	+=	$balance_list[$k]['income_amount'];
						$total_outcome_amount	+=	$balance_list[$k]['outcome_amount'];
					}			
					$true_income_amount	=	$total_income_amount	-	$total_outcome_amount;			
					if($total_amount	!=	$true_income_amount){
						$bill_list[$j]	=	$bill_list_tmp[$j];
						$bill_list[$j]['is_paid']	=	"尚未全额到账";
					}
					
					/*
					//查找到账情况
					$map_balance['bill_id']	=	$bill_id;
					$balance_list	=	M('Balance')->field('sum(income_amount) as total_income_amount,sum(outcome_amount) as total_outcome_amount')->where($map_balance)->select();
					$true_income_amount	=	$claim_list[0]['total_income_amount']	-	$claim_list[0]['total_outcome_amount'];					
					if($total_amount	!=	$true_income_amount){
						$bill_list[$j]	=	$bill_list_tmp[$j];
						$bill_list[$j]['is_paid']	=	"尚未全额到账";
					}*/
				}					
			}
			
			$bill_count	=	count($bill_list);
			
			$this->assign('bill_list',$bill_list);
			$this->assign('bill_count',$bill_count);
			
			//返回搜索参数
			$this->assign('client_id',$client_id);
			$this->assign('start_time',$start_time);
			$this->assign('end_time',$end_time);
			$this->assign('start_amount',$start_amount);
			$this->assign('end_amount',$end_amount);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_paid',$is_paid);		
		} 
	
	$this->display();
	}
	
	//快捷更新	
	public function adjust(){
		if(IS_POST){
			
			$data=array();
			$data['bill_id']	=	trim(I('post.bill_id'));
			$data['total_amount']	=	100*trim(I('post.total_amount',0));
			$data['official_fee']	=	100*trim(I('post.official_fee',0));
			$data['service_fee']	=	100*trim(I('post.service_fee',0));
			$data['other_fee']	=	100*trim(I('post.other_fee',0));
			
			$data['total_amount']	=	$data['total_amount']	+	$data['other_fee'];

			$result = M('Bill')->save($data);
			if(false !== $result){
				$this->success('修改成功', U('Bill/detail','bill_id='.$data['bill_id']));
			}else{
				$this->error('修改失败');
			}
		} 
	}
	
}