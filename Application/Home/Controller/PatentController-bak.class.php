<?php
namespace Home\Controller;
use Think\Controller;

class PatentController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//初步开案登记
	public function addBasic(){
        $year_option_data	=	yearOption();
		$this->assign('year_option_data',$year_option_data);
		
		$patent_type_data	=	D('CaseTypeGroup')->field(true)->listAllPatent();
		$this->assign('patent_type_data',$patent_type_data);
		
		
		
		$this->display();
    }
	
	//单页式立案
	public function addNewOne(){
        $year_option_data	=	yearOption();
		$this->assign('year_option_data',$year_option_data);
		
		$patent_type_data	=	D('CaseTypeGroup')->field(true)->listAllPatent();
		$this->assign('patent_type_data',$patent_type_data);
		
		if(IS_POST){
			$data	=	array();
			$data['our_ref'] = I('post.our_ref');
			$data['case_type_id']	=	I('post.case_type_id');
			$data['follower_id']	=	I('post.follower_id');
			$data['create_date']	=	I('post.create_date');
			$data['form_date']	=	I('post.form_date');
			//转为时间戳
			$data['create_date']	=	time($data['create_date']);
			$data['form_date']	=	time($data['form_date']);
			$data['client_id']	=	I('post.client_id');
			$data['client_ref']	=	I('post.client_ref');
			$data['applicant_id']	=	I('post.applicant_id');
			$data['tentative_title']	=	I('post.tentative_title');
			for($i=0;	$i<count(I('post.priority_country_id'));	$i++){
				$priority_country_id	=	I('post.priority_country_id')[$i];
				$priority_number	=	I('post.priority_number')[$i];
				$priority_date	=	I('post.priority_date')[$i];
				//转为时间戳
				$priority_date	=	time($priority_date);
				if('none'!=$priority_country_id){
					$data['CasePriority'][$i]	=	array(
					'priority_country_id'	=>	$priority_country_id,
					'priority_number'	=>	$priority_number,
					'priority_date'	=>	$priority_date
					);
				}
			}
			$data['handler_id']	=	I('post.handler_id');
			$data['CaseExtend']['remarks']	=	I('post.remarks');
			$data['CaseExtend']['related_our_ref']	=	I('post.related_our_ref');
			$data['application_date']	=	I('post.application_date');
			//转为时间戳
			$data['application_date']	=	time($data['application_date']);
			$data['application_number']	=	I('post.application_number');
			$data['formal_title']	=	I('post.formal_title');
			$data['CaseExtend']['registration_date']	=	I('post.registration_date');
			//转为时间戳
			$data['CaseExtend']['registration_date']	=	time($data['CaseExtend']['registration_date']);
			
			$Model	=	D('Case');
			$result	=	$Model->relation(true)->add($data);
			
			if(false !== $result){
				$this->success('新增成功', '/');
			}else{
				$this->error('增加失败');
			}
				
			
		} else{
			
			$year_option	=	date("Y",time());
			$case_type_group_id	=	1;
			$case_prefix	=	'P'.$year_option.'%';
			$case_type_group_id	=	I('post.case_type_group_option');
			
			$basic_map['our_ref']	=	array('like',$case_prefix);
			$order['convert(our_ref using gb2312)']	=	'desc';
			$Model	=	D('Case');
			$current_case	=	$Model->field('case_id','our_ref')->where($basic_map)->order($order)->limit(1)->select();
			$current_case_id	=	$current_case['case_id'];
			$case_data	=	$Model->relation(true)->field(true)->getByCaseId($current_case_id);
			
			$this->assign('case_data',$case_data);

		}
		
		$this->display();
    }

	
	//搜索现有的案号
	public function searchCurrentCase(){
        $year_option	=	I('post.year_option');
		$case_prefix	=	'P'.$year_option.'%';
		
		$case_type_group_id	=	I('post.case_type_group_option');
	
		$map['our_ref']	=	array('like',$case_prefix);
		$map['CaseTypeGroup']['case_type_group_id']	=	$case_type_group_id;
		$map['_logic'] = 'AND';
		$order['convert(our_ref using gb2312)']	=	'desc';
		
		$Model	=	D('Case');
		$case_data	=	$Model->relation(true)->field(true)->where($map)->order($order)->limit(2)->select();
		
		$this->ajaxReturn($case_data);
		
    }

	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$case_data = D('Case')->listPage($p,$limit);
		$this->assign('case_data',$case_data['list']);
		$this->assign('patent_page',$case_data['page']);
		
		$case_type_data	=	D('CaseType')->listBasic();
		$case_type_count	=	count($case_type_data);
		$this->assign('case_type_data',$case_type_data);
		$this->assign('case_type_count',$case_type_count);
		
		$follower_data	=	D('Member')->listBasic();
		$follower_count	=	count($follower_data);
		$this->assign('follower_data',$follower_data);
		$this->assign('follower_count',$follower_count);
				
		$handler_data	=	D('Member')->listBasic();
		$handler_count	=	count($handler_data);
		$this->assign('handler_data',$handler_data);
		$this->assign('handler_count',$handler_count);
		
		var_dump($follower_data);
		varm_dump('---');
		var_dump($handler_data);
		
		$client_data	=	D('Client')->listBasic();
		$client_count	=	count($client_data);
		$this->assign('client_data',$client_data);
		$this->assign('client_count',$client_count);
		
		$today	=	date("Y-m-d",time());
		$this->assign('today',$today);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['handler_id'] = I('post.handler_id',0,'int');
		$data['patent_date']	=	trim(I('post.patent_date'));
		//转为时间戳
		$data['patent_date']	=	time($data['patent_date']);
		$data['client_id'] = I('post.client_id',0,'int');
		$data['total_amount'] = I('post.total_amount',0,'int');
		$data['official_fee'] = I('post.official_fee',0,'int');
		$data['service_fee'] = I('post.service_fee',0,'int');
		
		if(!$data['handler_id']	or	!$data['client_id']){
			$this->error('未填写开开单人、收单人');
		} 

		$result = M('Patent')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function update(){
		if(IS_POST){
			
			/*
			$case_id = I('post.case_id',0,'int');
			
			$data	=	array();
			$data['handler_id'] = I('post.handler_id',0,'int');
			$data['patent_date']	=	trim(I('post.patent_date'));
			//转为时间戳
			$data['patent_date']	=	time($data['patent_date']);
			$data['client_id'] = I('post.client_id',0,'int');
			$data['total_amount'] = I('post.total_amount',0,'int');
			$data['official_fee'] = I('post.official_fee',0,'int');
			$data['service_fee'] = I('post.service_fee',0,'int');
			
			if(!$data['handler_id']	or	!$data['client_id']){
				$this->error('未填写开开单人、收单人');
			} 
			
			$map['case_id']	=	I('post.case_id',0,'int');
			$result = M('Patent')->where($map)->save($data);

			
			$Model	=	M('PatentInvoice');
			for($i=0;	$i<count(I('post.invoice_id'));	$i++){
				$patent_invoice_id	=	trim(I('post.patent_invoice_id')[$i]);
				$invoice_id	=	trim(I('post.invoice_id')[$i]);
				
				var_dump($patent_invoice_id);
				var_dump($invoice_id);
				var_dump('aaaa');
				
				//如果主键不存在
				if(!$patent_invoice_id){
					//如果主键不存在，且 invoice_id 不为零，就增加
					if($invoice_id){
						$patent_invoice[$i]	=	array(
							'case_id'			=>	$case_id,
							'invoice_id'		=>	$invoice_id
						);
						$result	=	$Model->add($patent_invoice[$i]);

					}else{
					//如果主键不存在，且 invoice_id 为0，不做处理；
					}
				
				//如果主键存在
				}else{
					//如果主键存在，且 invoice_id 不为零，就更新
					if($invoice_id){ 
						$map['patent_invoice_id']	=	$patent_invoice_id;
						$patent_invoice[$i]	=	array(
							'case_id'			=>	$case_id,
							'invoice_id'		=>	$invoice_id
						);
						$result	=	$Model->where($map)->save($patent_invoice[$i]);

					}else{
					//如果主键存在，且invoice_id 为零，就删除
						$map['patent_invoice_id']	=	$patent_invoice_id;
						$result	=	$Model->where($map)->delete();

					}
					
				}
				
			}*/
		} else{
			$case_id = I('get.id',0,'int');

			if(!$case_id){
				$this->error('未指明要编辑的案号');
			}
			//$map['case_id']	=	$case_id;
			$case_data = D('Case')->relation(true)->field(true)->getByCaseId($case_id);
			$priority_count	=	count($case_data['CasePriority']);
			$priority_limit	=	$priority_count+2;
			$this->assign('case_data',$case_data);
			$this->assign('priority_count',$priority_count);
			$this->assign('priority_limit',$priority_limit);
						
			$p	=	'P%';
			$case_type_data	=	D('CaseType')->listBasicLike($p);
			$case_type_count	=	count($case_type_data);
			$this->assign('case_type_data',$case_type_data);
			$this->assign('case_type_count',$case_type_count);

			$member_data	=	D('Member')->listBasic();
			$member_count	=	count($member_data);
			$this->assign('member_data',$member_data);
			$this->assign('member_count',$member_count);
						
			$client_data	=	D('Client')->listBasic();
			$client_count	=	count($client_data);
			$this->assign('client_data',$client_data);
			$this->assign('client_count',$client_count);
		
			$country_data	=	D('Country')->listBasic();
			$country_count	=	count($country_data);
			$this->assign('country_count',$country_count);
			$this->assign('country_data',$country_data);	
			//var_dump($priority_count);
			
			$today	=	time();
			$this->assign('today',$today);
			
			$rows_limit	=	C('ROWS_LIMIT_PER_SELECT');
			$this->assign('rows_limit',$rows_limit);
			

			$this->display();
		}
	}
	
		public function testa(){
		$case_data = D('Patent')->listAll();
		print_r($case_data);
		print_r($case_data['PatentInvoice']);
		
	}
}