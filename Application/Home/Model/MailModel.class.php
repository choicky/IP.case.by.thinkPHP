<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: MailController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class MailModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		array('mail_date','stringToTimestamp',3,'function') , // 将 yyyy-mm-dd 转换时间戳
		array('mail_fee','multiplyByHundred',3,'function') , // 将金额乘以100
        array('tacking_number','trim',3,'function'), //去掉快递单号的前后空格
	);
	
	//定义本数据表的自动验证
	protected $_validate = array(
		array('follower_id','require','必须指明寄件人/跟案人',1), //必须验证非空
		
   );
	
	
}