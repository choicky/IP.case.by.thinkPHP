<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseFileController
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;

class CaseFileModel extends RelationModel {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		// 将 yyyy-mm-dd 转换时间戳
		array('oa_date','strtotime',3,'function') , 
		array('due_date','strtotime',3,'function') , 
		array('completion_date','strtotime',3,'function') , 
	);
	
	//定义本数据表的自动验证
	protected $_validate = array(
		 //定义必须的字段
		 array('case_id','require','必须指明案号',1), 
		 array('file_type_id','require','必须选择文件名称',1),
		 array('due_date','require','必须填写期限日',1), 
   );
   
}