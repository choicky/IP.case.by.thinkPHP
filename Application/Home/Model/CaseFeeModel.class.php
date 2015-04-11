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
}