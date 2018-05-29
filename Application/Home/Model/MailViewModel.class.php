<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: MailController,
// +----------------------------------------------------------------------

namespace Home\Model;

//因为启动数据表视图模型，必须继承ViewModel，注释Model
//use Think\Model;
use Think\Model\ViewModel;

class MailViewModel extends ViewModel {
	
	//定义Mail表与fee_phase表的视图关系
	protected $viewFields = array(
		'Mail'	=>	array(
			'mail_id',
			'follower_id',
			'mail_date',
			'mail_summary',
			'recipient_name',
            'recipient_phone',
			'recipient_entity',
			'recipient_address',
			'recipient_zipcode',
			'deliver_name',
            'tacking_number',
            'mail_fee',
            'inner_balance_id',
			'remarks',
			'_type'=>'LEFT'
		),
		
		'Member'	=>	array(
			'member_name',
			'_type'=>'LEFT',
			'_on'	=>	'Member.member_id=Mail.follower_id'
		),	

	);
	
	//返回本数据视图的所有数据
	public function listAll() {
		$order['mail_date']	=	'desc';
		$list	=	$this->order($order)->select();
		return $list;
	}
    
    //返回本数据视图的所有数据，先后顺序排列
	public function searchAll() {
		$order['mail_date']	=	'asc';
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
		$order['mail_date']	=	'desc';
        
        $list	=	$this->order($order)->page($p.','.$limit)->select();
				
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//分页返回本数据视图的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPageSearch($p,$limit,$map) {
		$order['mail_date']	=	'desc';	
		$list	=	$this->order($order)->where($map)->page($p.','.$limit)->select();
		
		$count	= $this->where($map)->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
}