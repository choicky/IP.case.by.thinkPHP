<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseController
// +----------------------------------------------------------------------

namespace Home\Model;

//因为启动数据表视图模型，必须继承ViewModel，注释Model
//use Think\Model;
use Think\Model\ViewModel;

class CaseFileTaskViewModel extends ViewModel {
	
	//定义本表与其他数据表的视图关系
	protected $viewFields = array(
		
		//定义本表与 CaseFile 表的视图关系
		'CaseFile'	=>	array(
			'case_file_id',
			'case_id',
			'file_type_id',
			'oa_date',
			'due_date',
			'completion_date',
			'_table'=>"__CASE_FILE__",
			'_type'=>'LEFT'
		),
		
		//定义本表与 Country 表的视图关系
		'FileType'	=>	array(
			'file_type_name',
			'_table'=>"__FILE_TYPE__",
			'_on'	=>	'FileType.file_type_id=CaseFile.file_type_id'
		),
		
		//定义本表与 CaseBasicView 表的视图关系
		'CaseBasicView'	=>	array(
			'case_id',
			'our_ref',
			'case_type_id',
			'follower_id',
			'create_date',
			'form_date',
			'client_id',
			'client_ref',
			'applicant_id',
			'tentative_title',
			'handler_id',
			'application_number',
			'formal_title',
			'tm_category_id',
			'publication_date',
			'issue_date',
			'expired_date',
			'related_our_ref',
			'remarks',
			'case_type_name',
			'follower_name',
			'client_name',
			'applicant_name',
			'handler_name',
			'tm_category_number',
			'_table'=>"__CASE_BASIC_VIEW__",
			'_on'	=>	'CaseBasicView.case_id=CaseFile.case_id'
		),

	);	
}