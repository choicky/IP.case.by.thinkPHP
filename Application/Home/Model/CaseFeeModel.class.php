<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseFeeController,
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class CaseFeeModel extends RelationModel {
	
	//定义本数据表的自动完成，1为新增时自动完成，2为更新时自动完成，3为所有情况都自动完成
	protected $_auto = array(		
		
		// 将 yyyy-mm-dd 转换时间戳
		array('oa_date','strtotime',3,'function') , 
		array('due_date','strtotime',3,'function') , 
		array('allow_date','strtotime',3,'function') , 
		
		// 将金额乘以100，multiplyByHundred 自定义于 common 文件夹的 function.php
		array('official_fee','multiplyByHundred',3,'function') , 
		array('service_fee','multiplyByHundred',3,'function') , 
		array('cost_amount','multiplyByHundred',3,'function') , 
	);
	
	//定义本数据表的自动验证，0为存在字段就验证，1为必须验证，2是值不为空时才验证
	protected $_validate = array(
		 
		 //定义必须的字段
		 array('case_id','require','必须指明案号',1), 
		 array('case_phase_id','require','必须选择费用所述的阶段',1),
		 array('fee_type_id','require','必须选择费用名称',1), 
		 array('due_date','require','必须选择缴费期限',1),
   ); 

	//根据 $case_fee_id 返回对应的案子的 $case_type_name
	public function returnCaseTypeName($case_fee_id) {
		
		//从 CaseFee 表获得 $case_id
		$map_case_fee['case_fee_id']	=	$case_fee_id;
		$case_fee_list	=	M('CaseFee')->field('case_id')->where($map_case_fee)->find();
		$case_id	=	$case_fee_list['case_id'];
		
		//从 Case 表获得 $case_type_id
		$map_case['case_id']	=	$case_id;
		$case_list	=	M('Case')->field('case_type_id')->where($map_case)->find();
		$case_type_id	=	$case_list['case_type_id'];
		
		//从 CaseType 表获得 $case_type_name
		$map_case_type['case_type_id']	=	$case_type_id;
		$case_type_list	=	M('CaseType')->field('case_type_name')->where($map_case_type)->find();
		$case_type_name	=	$case_type_list['case_type_name'];
				
		return $case_type_name;
	}
	
	//返回所有结果
	public function listAll(){
		$order['our_ref']	=	'asc';
		$order['due_date']	=	'asc';
		$list	=	$this
					//定义本表的别名
					->alias('case_fee')
					
					//左连接 Case 表
					->join('LEFT JOIN __CASE__ case_info ON case_info.case_id = case_fee.case_id')
					
					//左连接 CasePhase 表
					->join('LEFT JOIN __CASE_PHASE__ case_phase ON case_phase.case_phase_id = case_fee.case_phase_id')
					
					//左连接 FeeType 表
					->join('LEFT JOIN __FEE_TYPE__ fee_type ON fee_type.fee_type_id = case_fee.fee_type_id')
					
					//左连接 Payer 表
					->join('LEFT JOIN __PAYER__ payer ON payer.payer_id = case_fee.payer_id')
					
					//左连接 CasePayment 表
					->join('LEFT JOIN __CASE_PAYMENT__ case_payment ON case_payment.case_payment_id = case_fee.case_payment_id')
					
					//左连接 CostCenter 表
					->join('LEFT JOIN __COST_CENTER__ cost_center ON cost_center.cost_center_id = case_fee.cost_center_id')					
					
					//左连接 Member 表
					->join('LEFT JOIN __MEMBER__ follower ON follower.member_id = case_info.follower_id')
					
					//左连接 Client 表
					->join('LEFT JOIN __CLIENT__ client ON client.client_id = case_info.client_id')
					
					//左连接 Client 表
					->join('LEFT JOIN __CLIENT__ applicant ON applicant.client_id = case_info.applicant_id')
					
					//左连接 Member 表
					->join('LEFT JOIN __MEMBER__ handler ON handler.member_id = case_info.handler_id')
					
					//左连接 CaseExtend 表
					->join('LEFT JOIN __CASE_EXTEND__ case_extend ON case_extend.case_id = case_info.case_id')
					
					//定义字段
					->field(
						array(
						'case_fee.case_fee_id'	=>	'case_fee_id',
						'case_fee.case_id'			=>	'case_id',
						'case_fee.case_phase_id'	=>	'case_phase_id',
						'case_fee.fee_type_id'		=>	'fee_type_id',
						'case_fee.official_fee'		=>	'official_fee',
						'case_fee.service_fee'		=>	'service_fee',
						'case_fee.oa_date'			=>	'oa_date',
						'case_fee.due_date'			=>	'due_date',
						'case_fee.allow_date'		=>	'allow_date',
						'case_fee.payer_id'			=>	'payer_id',
						'case_fee.case_payment_id'	=>	'case_payment_id',
						'case_fee.bill_id'			=>	'bill_id',
						'case_fee.invoice_id'		=>	'invoice_id',
						'case_fee.claim_id'			=>	'claim_id',
						'case_fee.cost_center_id'	=>	'cost_center_id',
						'case_fee.cost_amount'		=>	'cost_amount',
						'case_phase.case_phase_name'=>	'case_phase_name',
						'fee_type.fee_type_name'	=>	'fee_type_name',
						'payer.payer_name'			=>	'payer_name',
						'case_payment.payment_date'	=>	'payment_date',
						'cost_center.cost_center_name'	=>	'cost_center_name',
						'case_info.our_ref'			=>	'our_ref',
						'case_info.follower_id'		=>	'follower_id',
						'case_info.client_id'		=>	'client_id',
						'case_info.client_ref'		=>	'client_ref',
						'case_info.applicant_id'	=>	'applicant_id',
						'case_info.tentative_title'	=>	'tentative_title',
						'case_info.handler_id'		=>	'handler_id',
						'case_info.application_date'	=>	'application_date',
						'case_info.application_number'	=>	'application_number',
						'case_info.formal_title'	=>	'formal_title',
						'case_info.tm_category_id'	=>	'tm_category_id',
						'case_info.publication_date'	=>	'publication_date',
						'case_info.issue_date'		=>	'issue_date',
						'follower.member_name'		=>	'follower_name',
						'client.client_name'		=>	'client_name',
						'applicant.client_name'		=>	'applicant_name',
						'handler.member_name'		=>	'handler_name',
						'case_extend.expired_date'	=>	'expired_date',
						'case_extend.related_our_ref'	=>	'related_our_ref',
						'case_extend.remarks'		=>	'remarks',						
						)
					)
					
					//排序
					->order($order)
					->select();
					
		$count	=	count($list);
		return array("list"=>$list,"count"=>$count);
	}
	
	//分页返回所有结果
	public function listPage($p,$limit) {
		$order['our_ref']	=	'asc';
		$order['due_date']	=	'asc';
		$list	=	$this
					//定义本表的别名
					->alias('case_fee')
					
					//左连接 Case 表
					->join('LEFT JOIN __CASE__ case_info ON case_info.case_id = case_fee.case_id')
					
					//左连接 CasePhase 表
					->join('LEFT JOIN __CASE_PHASE__ case_phase ON case_phase.case_phase_id = case_fee.case_phase_id')
					
					//左连接 FeeType 表
					->join('LEFT JOIN __FEE_TYPE__ fee_type ON fee_type.fee_type_id = case_fee.fee_type_id')
					
					//左连接 Payer 表
					->join('LEFT JOIN __PAYER__ payer ON payer.payer_id = case_fee.payer_id')
					
					//左连接 CasePayment 表
					->join('LEFT JOIN __CASE_PAYMENT__ case_payment ON case_payment.case_payment_id = case_fee.case_payment_id')
					
					//左连接 CostCenter 表
					->join('LEFT JOIN __COST_CENTER__ cost_center ON cost_center.cost_center_id = case_fee.cost_center_id')					
					
					//左连接 Member 表
					->join('LEFT JOIN __MEMBER__ follower ON follower.member_id = case_info.follower_id')
					
					//左连接 Client 表
					->join('LEFT JOIN __CLIENT__ client ON client.client_id = case_info.client_id')
					
					//左连接 Client 表
					->join('LEFT JOIN __CLIENT__ applicant ON applicant.client_id = case_info.applicant_id')
					
					//左连接 Member 表
					->join('LEFT JOIN __MEMBER__ handler ON handler.member_id = case_info.handler_id')
					
					//左连接 CaseExtend 表
					->join('LEFT JOIN __CASE_EXTEND__ case_extend ON case_extend.case_id = case_info.case_id')
					
					//定义字段
					->field(
						array(
						'case_fee.case_fee_id'	=>	'case_fee_id',
						'case_fee.case_id'			=>	'case_id',
						'case_fee.case_phase_id'	=>	'case_phase_id',
						'case_fee.fee_type_id'		=>	'fee_type_id',
						'case_fee.official_fee'		=>	'official_fee',
						'case_fee.service_fee'		=>	'service_fee',
						'case_fee.oa_date'			=>	'oa_date',
						'case_fee.due_date'			=>	'due_date',
						'case_fee.allow_date'		=>	'allow_date',
						'case_fee.payer_id'			=>	'payer_id',
						'case_fee.case_payment_id'	=>	'case_payment_id',
						'case_fee.bill_id'			=>	'bill_id',
						'case_fee.invoice_id'		=>	'invoice_id',
						'case_fee.claim_id'			=>	'claim_id',
						'case_fee.cost_center_id'	=>	'cost_center_id',
						'case_fee.cost_amount'		=>	'cost_amount',
						'case_phase.case_phase_name'=>	'case_phase_name',
						'fee_type.fee_type_name'	=>	'fee_type_name',
						'payer.payer_name'			=>	'payer_name',
						'case_payment.payment_date'	=>	'payment_date',
						'cost_center.cost_center_name'	=>	'cost_center_name',
						'case_info.our_ref'			=>	'our_ref',
						'case_info.follower_id'		=>	'follower_id',
						'case_info.client_id'		=>	'client_id',
						'case_info.client_ref'		=>	'client_ref',
						'case_info.applicant_id'	=>	'applicant_id',
						'case_info.tentative_title'	=>	'tentative_title',
						'case_info.handler_id'		=>	'handler_id',
						'case_info.application_date'	=>	'application_date',
						'case_info.application_number'	=>	'application_number',
						'case_info.formal_title'	=>	'formal_title',
						'case_info.tm_category_id'	=>	'tm_category_id',
						'case_info.publication_date'	=>	'publication_date',
						'case_info.issue_date'		=>	'issue_date',
						'follower.member_name'		=>	'follower_name',
						'client.client_name'		=>	'client_name',
						'applicant.client_name'		=>	'applicant_name',
						'handler.member_name'		=>	'handler_name',
						'case_extend.expired_date'	=>	'expired_date',
						'case_extend.related_our_ref'	=>	'related_our_ref',
						'case_extend.remarks'		=>	'remarks',						
						)
					)
					
					//排序
					->order($order)
					->page($p.','.$limit)
					->select();
		
		
		$count	= $this->count();	//待确定
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//分页返回搜索结果
	public function listPageSearch($p,$limit,$map) {
		$order['our_ref']	=	'asc';
		$order['due_date']	=	'asc';
		$list	=	$this
					//定义本表的别名
					->alias('case_fee')
					
					//左连接 Case 表
					->join('LEFT JOIN __CASE__ case_info ON case_info.case_id = case_fee.case_id')
					
					//左连接 CasePhase 表
					->join('LEFT JOIN __CASE_PHASE__ case_phase ON case_phase.case_phase_id = case_fee.case_phase_id')
					
					//左连接 FeeType 表
					->join('LEFT JOIN __FEE_TYPE__ fee_type ON fee_type.fee_type_id = case_fee.fee_type_id')
					
					//左连接 Payer 表
					->join('LEFT JOIN __PAYER__ payer ON payer.payer_id = case_fee.payer_id')
					
					//左连接 CasePayment 表
					->join('LEFT JOIN __CASE_PAYMENT__ case_payment ON case_payment.case_payment_id = case_fee.case_payment_id')
					
					//左连接 CostCenter 表
					->join('LEFT JOIN __COST_CENTER__ cost_center ON cost_center.cost_center_id = case_fee.cost_center_id')					
					
					//左连接 Member 表
					->join('LEFT JOIN __MEMBER__ follower ON follower.member_id = case_info.follower_id')
					
					//左连接 Client 表
					->join('LEFT JOIN __CLIENT__ client ON client.client_id = case_info.client_id')
					
					//左连接 Client 表
					->join('LEFT JOIN __CLIENT__ applicant ON applicant.client_id = case_info.applicant_id')
					
					//左连接 Member 表
					->join('LEFT JOIN __MEMBER__ handler ON handler.member_id = case_info.handler_id')
					
					//左连接 CaseExtend 表
					->join('LEFT JOIN __CASE_EXTEND__ case_extend ON case_extend.case_id = case_info.case_id')
					
					//定义字段
					->field(
						array(
						'case_fee.case_fee_id'	=>	'case_fee_id',
						'case_fee.case_id'			=>	'case_id',
						'case_fee.case_phase_id'	=>	'case_phase_id',
						'case_fee.fee_type_id'		=>	'fee_type_id',
						'case_fee.official_fee'		=>	'official_fee',
						'case_fee.service_fee'		=>	'service_fee',
						'case_fee.oa_date'			=>	'oa_date',
						'case_fee.due_date'			=>	'due_date',
						'case_fee.allow_date'		=>	'allow_date',
						'case_fee.payer_id'			=>	'payer_id',
						'case_fee.case_payment_id'	=>	'case_payment_id',
						'case_fee.bill_id'			=>	'bill_id',
						'case_fee.invoice_id'		=>	'invoice_id',
						'case_fee.claim_id'			=>	'claim_id',
						'case_fee.cost_center_id'	=>	'cost_center_id',
						'case_fee.cost_amount'		=>	'cost_amount',
						'case_phase.case_phase_name'=>	'case_phase_name',
						'fee_type.fee_type_name'	=>	'fee_type_name',
						'payer.payer_name'			=>	'payer_name',
						'case_payment.payment_date'	=>	'payment_date',
						'cost_center.cost_center_name'	=>	'cost_center_name',
						'case_info.our_ref'			=>	'our_ref',
						'case_info.follower_id'		=>	'follower_id',
						'case_info.client_id'		=>	'client_id',
						'case_info.client_ref'		=>	'client_ref',
						'case_info.applicant_id'	=>	'applicant_id',
						'case_info.tentative_title'	=>	'tentative_title',
						'case_info.handler_id'		=>	'handler_id',
						'case_info.application_date'	=>	'application_date',
						'case_info.application_number'	=>	'application_number',
						'case_info.formal_title'	=>	'formal_title',
						'case_info.tm_category_id'	=>	'tm_category_id',
						'case_info.publication_date'	=>	'publication_date',
						'case_info.issue_date'		=>	'issue_date',
						'follower.member_name'		=>	'follower_name',
						'client.client_name'		=>	'client_name',
						'applicant.client_name'		=>	'applicant_name',
						'handler.member_name'		=>	'handler_name',
						'case_extend.expired_date'	=>	'expired_date',
						'case_extend.related_our_ref'	=>	'related_our_ref',
						'case_extend.remarks'		=>	'remarks',						
						)
					)
					
					//排序
					->order($order)
					
					//查询
					->where($map)
					
					//分页
					->page($p.','.$limit)
					->select();		
		
		$count	= $this->where($map)->count();	//待确定
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
}