<?php
namespace Home\Model;

use Think\Model;
//因为没有数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class AccountModel extends Model {
	
	//获取account表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listAccount($p,$limit) {
		$account_list	= $this->order('convert(account_name using gb2312) asc')->page($p.','.$limit)->select();
		
		$account_count	= $this->count();
		
		$Page	= new \Think\Page($account_count,$limit);
		$show	= $Page->show();
		
		return array("account_list"=>$account_list,"account_page"=>$show);
	}
	
	//向account表插入记录，$data是数组，且不包含主键
	public function addAccount($data){
		$result	=	$this->add($data);
		return $result;
	}
	
	//更新account表中主键为$account_id的记录，$data是数组
	public function editAccount($account_id,$data){
		$map['account_id']	=	$account_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}