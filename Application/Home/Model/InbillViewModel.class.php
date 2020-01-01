<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: InbillController,
// +----------------------------------------------------------------------

namespace Home\Model;

//因为启动数据表视图模型，必须继承ViewModel，注释Model
//use Think\Model;
use Think\Model\ViewModel;

class InbillViewModel extends ViewModel {
	
	//定义Inbill表与fee_phase表的视图关系
	protected $viewFields = array(
		'Inbill'	=>	array(
			'inbill_id',
			'follower_id',
			'inbill_date',
			'supplier_id',
			'inbill_number',
			'inbill_name',
			'total_amount',
			'monetary_unit',
			'due_date',
			'case_payment_id',
			'_type'=>'LEFT'
		),
		
		'Member'	=>	array(
			'member_name',
			'_type'=>'LEFT',
			'_on'	=>	'Member.member_id=Inbill.follower_id'
		),	
		
		'Supplier'	=>	array(
			'supplier_name',
			'_type'=>'LEFT',
			'_on'	=>	'Supplier.supplier_id=Inbill.supplier_id'
		),
	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['inbill_date']	=	'desc';
		$list	=	$this->order($order)->select();
		return $list;
	}
		
	//返回本数据视图的基本数据
	public function listBasic() {
		$list	=	$this->listAll();
		return $list;
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['inbill_date']	=	'desc';
        
        //如 user_group_id 小于 11 ，则只显示自己跟进的账单
        if(cookie('user_group_id') < 11){
            $map['follower_id'] = cookie('member_id');
            $list	=	$this->order($order)->page($p.','.$limit)->where($map)->select();
        }else{
            $list	=	$this->order($order)->page($p.','.$limit)->select();
        }        
				
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPageSearch($p,$limit,$map) {
		$order['inbill_date']	=	'desc';	
		$list	=	$this->order($order)->where($map)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
}