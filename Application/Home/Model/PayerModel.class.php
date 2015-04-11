<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseFeeController, PayerController
// +----------------------------------------------------------------------

namespace Home\Model;

//因启用了模型视图，所以禁用了关联定义
use Think\Model;
//use Think\Model\RelationModel;

class PayerModel extends Model {
	
	//返回本数据表的基本数据，可作为选单
	public function listBasic() {			
		$order['convert(payer_name using gb2312)']	=	'asc';
		$list	=	$this->field('payer_id,payer_name')->order($order)->select();
		return $list;
	}
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['convert(payer_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->select();
		$count	=	count($list);
		return array("list"=>$list,"count"=>$count);
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['convert(payer_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show);
	}
}