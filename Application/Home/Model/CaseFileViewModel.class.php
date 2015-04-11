<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseModel, CaseController
// +----------------------------------------------------------------------

namespace Home\Model;

//因为启动数据表视图模型，必须继承ViewModel，注释Model
//use Think\Model;
use Think\Model\ViewModel;

class CaseFileViewModel extends ViewModel {
	
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
			'_type'=>'LEFT'
		),
		
		//定义本表与 Country 表的视图关系
		'FileType'	=>	array(
			'file_type_name',
			'_on'	=>	'FileType.file_type_id=CaseFile.file_type_id'
		),

	);	
}