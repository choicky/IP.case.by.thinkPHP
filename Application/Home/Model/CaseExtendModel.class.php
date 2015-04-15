<?php
namespace Home\Model;
use Think\Model\RelationModel;

class CaseExtendModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		array('expired_date','strtotime',3,'function') , // 将 yyyy-mm-dd 转换时间戳		
	);

	//根据 $case_id 返回 $case_extend_id
	public function returnCaseExtendId($case_id){
		$map['case_id']	=	$case_id;
		$case_extend_list	=	$this->field('case_extend_id')->where($map)->find();
		$case_extend_id	=	$case_extend_list['case_extend_id'];
		return $case_extend_id;
	}
	
}