<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | It's recommended to use more ViewModel and less RelationModel
// +----------------------------------------------------------------------
// | This file is required by: CaseController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class CaseModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		array('create_date','stringToTimestamp',3,'function') , // 将 yyyy-mm-dd 转换时间戳
		array('application_date','stringToTimestamp',3,'function') , // 将 yyyy-mm-dd 转换时间戳
		array('issue_date','stringToTimestamp',3,'function') , // 将 yyyy-mm-dd 转换时间戳
		array('expired_date','stringToTimestamp',3,'function') , // 将 yyyy-mm-dd 转换时间戳
		array('applicant_id','checkApplicant',3,'callback') , // 将 yyyy-mm-dd 转换时间戳
	);
	
	//定义本数据表的自动验证
	protected $_validate = array(
		 array('our_ref','require','必须指明我方案号',1), //必须验证非空
		 array('case_type_id','require','必须指明案件类型',1), //必须验证非空
		// array('follower_id','require','必须指明跟案人/开案人',1), //必须验证非空
		// array('client_id','require','必须指明客户',1), //必须验证非空

   );
   		
	//判断申请人与客户是否相同
	protected function checkApplicant($applicant_id){
		$client_id	=	I('post.client_id');
		if($applicant_id	==	"same"){
			$applicant_id	=	$client_id;
		}		
		return $applicant_id;
	}
   
   //基于案号 $our_ref 返回对应的 $case_id 
	public function returnCaseId($our_ref){
		$map['our_ref']	=	$our_ref;
		$case_id_list	=	$this->field('case_id')->where($map)->find();
		$case_id	=	$case_id_list['case_id'];
		return $case_id;
	}
	
}