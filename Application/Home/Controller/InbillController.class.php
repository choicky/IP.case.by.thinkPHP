<?php
namespace Home\Controller;
use Think\Controller;

class InbillController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= C('RECORDS_PER_PAGE');
		$inbill_list = D('InbillView')->field(true)->listPage($p,$limit);
		
		$this->assign('inbill_list',$inbill_list['list']);
		$this->assign('inbill_page',$inbill_list['page']);
		$this->assign('inbill_count',$inbill_list['count']);
		
		//取出 Member 的数据
		$member_list	=	D('Member')->field(true)->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出 Supplier 的数据
		$supplier_list	=	D('Supplier')->field(true)->listBasic();
		$this->assign('supplier_list',$supplier_list);
		
		//取出其他的数据
		$today	=	date("Y-m-d",time());
		$this->assign('today',$today);

		$this->display();
	}
	
	//新增
	public function add(){
		$Model	=	D('Inbill');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}

		if(false !== $result){
			$this->success('新增成功',U('Inbill/listPage'));
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function update(){
		if(IS_POST){
			
			$inbill_id	=	I('post.inbill_id',0,'int');
			
			if(!$inbill_id){
				$this->error('未指明要编辑的账单编号');
			}
			
			$Model	=	D('Inbill');
			if (!$Model->create()){ // 创建数据对象
				 // 如果创建失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 //exit($Model->getError());
			}else{
				 // 验证通过 写入新增数据
				 $result	=	$Model->save();		 
			}

			if(false !== $result){
				$this->success('修改成功', U('Inbill/view','inbill_id='.$inbill_id));
			}else{
				$this->error('修改失败');
			}
			
		} else{
			$inbill_id = I('get.inbill_id',0,'int');

			if(!$inbill_id){
				$this->error('未指明要编辑的账单编号');
			}
			
			$inbill_list = D('InbillView')->getByInbillId($inbill_id);
			$this->assign('inbill_list',$inbill_list);

			$member_list	=	D('Member')->listBasic();
			$member_count	=	count($member_list);
			$this->assign('member_list',$member_list);
			$this->assign('member_count',$member_count);
			
			$supplier_list	=	D('Supplier')->listBasic();
			$supplier_count	=	count($supplier_list);
			$this->assign('supplier_list',$supplier_list);
			$this->assign('supplier_count',$supplier_count);

			$this->display();
		}
	}
	
	//查看主键为 $inbill_id 对应的开票信息、到账信息
	public function view(){
		
		//接收对应的 $inbill_id
		$inbill_id = I('get.inbill_id',0,'int');
		if(!$inbill_id){
			$this->error('未指明要查看的账单');
		}
		
		//取出账单的基本信息
		$inbill_list = D('InbillView')->field(true)->getByInbillId($inbill_id);
		$this->assign('inbill_list',$inbill_list);
		$this->display();
	}
	
	//搜索
	public function search(){
		//取出 Supplier 表的基本内容，作为 options
		$supplier_list	=	D('Supplier')->listBasic();
		$this->assign('supplier_list',$supplier_list);
		
		//默认查询最近一个月
		$start_time	=	strtotime("-1 month");
		$end_time	=	time();
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		
		//默认查询 0 元 至 200,000 元
		$start_amount	=	0;
		$end_amount	=	20000000;
		$this->assign('start_amount',$start_amount);
		$this->assign('end_amount',$end_amount);
		
		//取出 Member 表的基本内容，作为 options
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		if(IS_POST){
			
			//接收搜索参数
			$supplier_id	=	I('post.supplier_id','0','int');
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	$end_time	?	strtotime($end_time)	:	time();
			$start_time	=	trim(I('post.start_time'));
			$start_time	=	$start_time	?	strtotime($start_time)	:	strtotime("-1 month",$end_time);
			$start_amount	=	trim(I('post.start_amount'))*100;			
			$end_amount	=	trim(I('post.end_amount'))*100;	
			$follower_id	=	I('post.follower_id','0','int');
			$is_paid	=	I('post.is_paid','0','int');			
			
			//构造 maping
			$map_inbill['inbill_date']	=	array('between',$start_time.','.$end_time);
			$map_inbill['total_amount']	=	array('between',$start_amount.','.$end_amount);
			if($supplier_id){
				$map_inbill['supplier_id']	=	$supplier_id;
			}
			if($follower_id){
				$map_inbill['follower_id']	=	$follower_id;
			}	
			
			//根据付款情况不同进行处理,0表示不限，1表示已付，2表示未付
			if(0==$is_paid){
					// 0 表示不限，所以不用修改检索条件
			}
			if(1==$is_paid){
				$map_inbill['case_payment_id']	=	array('gt',0);
			}
			if(2==$is_paid){
				$map_inbill['case_payment_id']	=	array('lt',1);
			}
			
			$inbill_list = D('InbillView')->field(true)->where($map_inbill)->listAll();
			
			$inbill_count	=	count($inbill_list);
			
			$this->assign('inbill_list',$inbill_list);
			$this->assign('inbill_count',$inbill_count);
			
			//返回搜索参数
			$this->assign('supplier_id',$supplier_id);
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
}