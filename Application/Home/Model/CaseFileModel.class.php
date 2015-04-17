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
		
		// 将金额乘以100，multiplyByHundred 自定义于 common 文件夹的 function.php
		array('service_fee','multiplyByHundred',3,'function') , 
		array('cost_amount','multiplyByHundred',3,'function') , 
	);
	
	//定义本数据表的自动验证
	protected $_validate = array(
		 //定义必须的字段
		 array('case_id','require','必须指明案号',1), 
		 array('file_type_id','require','必须选择文件名称',1),
		 array('due_date','require','必须填写期限日',1), 
   );
	
	//查询所有的列表
	public function listAll(){
		$order['our_ref']	=	'asc';
		$order['due_date']	=	'asc';
		$list	=	$this
					//定义本表的别名
					->alias('case_file')
					
					//左连接 FileType 表
					->join('LEFT JOIN __FILE_TYPE__ file_type ON file_type.file_type_id = case_file.file_type_id')
					
					//左连接 Case 表
					->join('LEFT JOIN __CASE__ case_info ON case_info.case_id = case_file.case_id')
					
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
						'case_file.case_file_id'	=>	'case_file_id',
						'case_file.case_id'			=>	'case_id',
						'case_file.file_type_id'	=>	'file_type_id',
						'case_file.oa_date'			=>	'oa_date',
						'case_file.due_date'		=>	'due_date',
						'case_file.completion_date'	=>	'completion_date',
						'file_type.file_type_name'	=>	'file_type_name',
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
	
	//分页返回本数据视图的所有专利数据
	public function listPage($p,$limit) {
		$order['our_ref']	=	'asc';
		$order['due_date']	=	'asc';
		$list	=	$this
					//定义本表的别名
					->alias('case_file')
					
					//左连接 FileType 表
					->join('LEFT JOIN __FILE_TYPE__ file_type ON file_type.file_type_id = case_file.file_type_id')
					
					//左连接 Case 表
					->join('LEFT JOIN __CASE__ case_info ON case_info.case_id = case_file.case_id')
					
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
						'case_file.case_file_id'	=>	'case_file_id',
						'case_file.case_id'			=>	'case_id',
						'case_file.file_type_id'	=>	'file_type_id',
						'case_file.oa_date'			=>	'oa_date',
						'case_file.due_date'		=>	'due_date',
						'case_file.completion_date'	=>	'completion_date',
						'file_type.file_type_name'	=>	'file_type_name',
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
	
	//分页返回本数据视图的所有专利数据
	public function listPageSearch($p,$limit,$map) {
		$order['our_ref']	=	'asc';
		$order['due_date']	=	'asc';
		$list	=	$this
					//定义本表的别名
					->alias('case_file')
					
					//左连接 FileType 表
					->join('LEFT JOIN __FILE_TYPE__ file_type ON file_type.file_type_id = case_file.file_type_id')
					
					//左连接 Case 表
					->join('LEFT JOIN __CASE__ case_info ON case_info.case_id = case_file.case_id')
					
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
						'case_file.case_file_id'	=>	'case_file_id',
						'case_file.case_id'			=>	'case_id',
						'case_file.file_type_id'	=>	'file_type_id',
						'case_file.oa_date'			=>	'oa_date',
						'case_file.due_date'		=>	'due_date',
						'case_file.completion_date'	=>	'completion_date',
						'file_type.file_type_name'	=>	'file_type_name',
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