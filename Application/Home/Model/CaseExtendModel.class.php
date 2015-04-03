<?php
namespace Home\Model;
use Think\Model\RelationModel;

class CaseExtendModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		array('expired_date','strtotime',3,'function') , // 将 yyyy-mm-dd 转换时间戳		
	);

	
	
}