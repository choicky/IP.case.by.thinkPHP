<?php
namespace Home\Model;
use Think\Model\RelationModel;

class UserModel extends RelationModel {
	
	//定义本模型对应的数据表
    protected $tableName = 'member';
    
    //定义本数据表的自动完成，1表示新增时处理，2表示更新时处理，3表示都处理
	protected $_auto = array(		
		array('user_password','md5',3,'function') , // 对用户密码加密	
	);
	
	//检测 $user_name 与 $user_password 是否匹配
	public function checkLogin($user_name,$user_password) {			
		//构造 $mapping
		$map_user['user_name']	=	$user_name;
		$map_user['user_password']	=	md5($user_password);
		
		//从数据库获取数据
		$list	=	$this->where($map_user)->field('member_id,user_group_id')->select();
		
		//检测是否匹配
		if(is_array($list) and 1==count($list)){
			$result	=	1;
			$member_id	=	$list[0]['member_id'];
			$user_group_id	=	$list[0]['user_group_id'];
		}else{
			$result	=	0;
		}

		//返回
		return array("result"=>$result,"member_id"=>$member_id,"user_group_id"=>$user_group_id);
	}

	//检测 $member_id 与 $user_password 是否匹配
	public function checkPassword($member_id,$user_password) {			
		//构造 $mapping
		$map_user['member_id']	=	$member_id;
		$map_user['user_password']	=	md5($user_password);
		
		//从数据库获取数据
		$list	=	$this->where($map_user)->field('member_id,user_group_id')->select();
		
		//检测是否匹配
		if(is_array($list) and 1==count($list)){
			$result	=	1;
		}else{
			$result	=	0;
		}		
		return $result;
	}
}