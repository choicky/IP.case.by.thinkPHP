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
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['convert(fee_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->select();
		return $list;
	}
		
	//返回本数据视图的基本数据
	public function listBasic() {
		$list	=	$this->listAll();
		return $list;
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
}