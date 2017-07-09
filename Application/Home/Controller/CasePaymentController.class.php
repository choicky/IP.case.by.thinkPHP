<?php
namespace Home\Controller;
use Think\Controller;

class CasePaymentController extends Controller {
    
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
		$case_payment_list = D('CasePaymentView')->listPage($p,$page_limit);
		
		//关联到收支流水
		for($j=0;$j<count($case_payment_list['list']);$j++){
			$case_payment_id	=	$case_payment_list['list'][$j]['case_payment_id'];
			
			$map_balance['case_payment_id']	=	$case_payment_id;
			$balance_list	=	M('Balance')->field('balance_id,deal_date')->where($map_balance)->select();
			
			$case_payment_list['list'][$j]['balance']	=	$balance_list;			
		}
		
		$this->assign('case_payment_list',$case_payment_list['list']);
		$this->assign('case_payment_page',$case_payment_list['page']);
		$this->assign('case_payment_count',$case_payment_list['count']);
		
		//取出 Payer 表的内容以及数量
		$payer_list	=	D('Payer')->field(true)->listBasic();
		$payer_count	=	count($payer_list);
		$this->assign('payer_list',$payer_list);
		$this->assign('payer_count',$payer_count);
		
		
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
		$data['payment_name']	=	trim(I('post.payment_name'));
		$data['payment_date']	=	strtotime(trim(I('post.payment_date')));
		$data['payer_id']	=	trim(I('post.payer_id'));
		$data['official_fee']	=	bcmul(trim(I('post.official_fee')),100);
		$data['other_fee']	=	bcmul(trim(I('post.other_fee')),100);
		$data['total_amount']	=	bcmul(trim(I('post.total_amount')),100);
		
		if(!$data['payment_name']){
			$this->error('未填写缴费单名称');
		} 

		$result = M('CasePayment')->add($data);
		
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
			$data['case_payment_id']	=	trim(I('post.case_payment_id'));
			$data['payment_name']	=	trim(I('post.payment_name'));
			$data['payment_date']	=	strtotime(trim(I('post.payment_date')));
			$data['payer_id']	=	trim(I('post.payer_id'));
			$data['official_fee']	=	bcmul(trim(I('post.official_fee')),100);
			$data['other_fee']	=	bcmul(trim(I('post.other_fee')),100);
			$data['total_amount']	=	bcmul(trim(I('post.total_amount')),100);

			$result = M('CasePayment')->save($data);
			if(false !== $result){
				$this->success('修改成功', U('CasePayment/view','case_payment_id='.$data['case_payment_id']));
			}else{
				$this->error('修改失败', U('CasePayment/view','case_payment_id='.$data['case_payment_id']));
			}
		} else{
			$case_payment_id = I('get.case_payment_id',0,'int');

			if(!$case_payment_id){
				$this->error('未指明要编辑的缴费单');
			}

			$case_payment_list = D('CasePaymentView')->getByCasePaymentId($case_payment_id);			
			$this->assign('case_payment_list',$case_payment_list);
			
			//取出 Payer 表的内容以及数量
			$payer_list	=	D('Payer')->field(true)->listBasic();
			$payer_count	=	count($payer_list);
			$this->assign('payer_list',$payer_list);
			$this->assign('payer_count',$payer_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);

			$this->display();
		}
	}
	
	//查看主键为 $case_payment_id 的收支流水的所有 case_file
	public function view(){
		$case_payment_id = I('get.case_payment_id',0,'int');

		if(!$case_payment_id){
			$this->error('未指明要查看的缴费单');
		}
		
		//定义检索条件
		$map['case_payment_id']	=	$case_payment_id;
		
		//取出 CasePayment 的信息
		$case_payment_list = D('CasePaymentView')->field(true)->getByCasePaymentId($case_payment_id);
		
		//关联到收支流水
		$case_payment_id	=	$case_payment_list['case_payment_id'];		
		$map_balance['case_payment_id']	=	$case_payment_id;
		$balance_list	=	M('Balance')->field('balance_id,deal_date')->where($map_balance)->select();		
		$case_payment_list['balance']	=	$balance_list;	
		
		$this->assign('case_payment_list',$case_payment_list);

		//取出 CaseFee 的信息
		$case_fee_list = D('CaseFeeView')->field(true)->where($map)->listAll();	
		$case_fee_count	=	count($case_fee_list);		

		//统计信息
		$service_fee_total	=0;
		$official_fee_total	=0;
		for($j=0;$j<$case_fee_count;$j++){
			$service_fee_total	+=	bcdiv($case_fee_list[$j]['service_fee'],100,2);
			$official_fee_total	+=	bcdiv($case_fee_list[$j]['official_fee'],100,2);
		}
		$fee_total	=	$service_fee_total	+	$official_fee_total;
		
        //返回当前时间
        $today = time();
        $this->assign('today',$today);
        
		$this->assign('case_fee_list',$case_fee_list);
		$this->assign('case_fee_count',$case_fee_count);
		$this->assign('service_fee_total',$service_fee_total);
		$this->assign('official_fee_total',$official_fee_total);
		$this->assign('fee_total',$fee_total);

		$this->display();
	}
	
	
	//更新	
	public function adjust(){
		if(IS_POST){
			
			$data=array();
			$data['case_payment_id']	=	trim(I('post.case_payment_id'));
			$data['official_fee']	=	bcmul(trim(I('post.official_fee')),100);
			$data['other_fee']	=	bcmul(trim(I('post.other_fee')),100);
			$data['total_amount']	=	$data['official_fee']	+	$data['other_fee'];

			$result = M('CasePayment')->save($data);
			if(false !== $result){
				$this->success('修改成功', U('CasePayment/view','case_payment_id='.$data['case_payment_id']));
			}else{
				$this->error('修改失败');
			}
		} 
	}
	
	
}