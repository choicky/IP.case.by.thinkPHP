<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: BillController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class BillModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		array('bill_date','stringToTimestamp',3,'function') , // 将 yyyy-mm-dd 转换时间戳
		array('total_amount','multiplyByHundred',3,'function') , // 将金额乘以100
		array('official_fee','multiplyByHundred',3,'function') , // 将金额乘以100
		array('service_fee','multiplyByHundred',3,'function') , // 将金额乘以100
		array('other_fee','multiplyByHundred',3,'function') , // 将金额乘以100	
	);
	
	//定义本数据表的自动验证
	protected $_validate = array(
		array('follower_id','require','必须指明跟案人/开单人',1), //必须验证非空
		array('client_id','require','必须指明客户/收单人',1), //必须验证非空
		//array('total_amount','require','必须填写金额',1), //必须验证非空
		
   );
	
	
}