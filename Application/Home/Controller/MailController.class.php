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
	

	
	//搜索
	public function search(){
		
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
			$end_time	=	trim(I('post.end_time'));
			$end_time	=	$end_time	?	strtotime($end_time)	:	time();
			$start_time	=	trim(I('post.start_time'));
			$start_time	=	$start_time	?	strtotime($start_time)	:	strtotime("-1 month",$end_time);
			$follower_id	=	I('post.follower_id','0','int');
			
			//构造 maping
			$map_mail['mail_date']	=	array('between',$start_time.','.$end_time);

			if($follower_id){
				$map_mail['follower_id']	=	$follower_id;
			}	
			
			$mail_list = D('MailView')->field(true)->where($map_mail)->searchAll();
			//var_dump($mail_list);
			//统计金额
            //初始化总邮费
            $total_mail_fee =   0;
			for($j=0;$j<count($mail_list);$j++){
				$total_mail_fee +=	bcdiv($mail_list[$j]['mail_fee'],100,2);
			}

			$mail_count	=	$j;

			$this->assign('mail_list',$mail_list);
			$this->assign('mail_count',$mail_count);
            $this->assign('total_mail_fee',$total_mail_fee);
			
			//返回搜索参数
			$this->assign('start_time',$start_time);
			$this->assign('end_time',$end_time);
			$this->assign('start_amount',$start_amount);
			$this->assign('end_amount',$end_amount);
			$this->assign('follower_id',$follower_id);
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