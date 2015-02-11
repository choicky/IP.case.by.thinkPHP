<?php
namespace Home\Model;

//因为启动数据表视图模型，必须继承ViewModel，注释Model
//use Think\Model;
use Think\Model\ViewModel;

class FeeTypeViewModel extends ViewModel {
	
	//定义fee_type表与fee_phase表的关联性
	protected $viewFields = array(
		'FeeType'	=>	array(
			'fee_type_id',
			'fee_name',
			'fee_name_cpc',
			'fee_default_amount',
			'fee_phase_id',
			'_type'=>'LEFT'
		),
		'FeePhase'	=>	array(
			'fee_phase_name',
			'_on'	=>	'FeeType.fee_phase_id=FeePhase.fee_phase_id'
		),	
	);
	
	//获取fee_type表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listFeeType($p,$limit) {
		$fee_type_list	=	$this->order(array('convert(fee_name using gb2312)'=>'asc'))->page($p.','.$limit)->select();
		
		$fee_type_count	= $this->count();
		
		$Page	= new \Think\Page($fee_type_count,$limit);
		$show	= $Page->show();
		
		return array("fee_type_list"=>$fee_type_list,"fee_type_page"=>$show);
	}
}