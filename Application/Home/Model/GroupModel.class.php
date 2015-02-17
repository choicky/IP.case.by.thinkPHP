<?php
namespace Home\Model;		//定义 namespace
use Think\Model;		//定义继承的 Model

//自定义模块 Group , 继承上述的 Model ，默认情况下对应于数据表 group
class GroupModel extends Model {		
	
	//自定义 listAll方法，返回本数据表的所有数据
	public function listAll() {			
		$order['convert(group_name using gb2312)']	=	'asc';		//以数组方式定义 order() 语句的参数
		$data_array	=	$this->order($order)->select();		//将 select 结果赋给 $data_array
		return $data_array;		//返回值
	}

	//更新 group 数据表 中主键为 $group_id 的记录，$data 是与 $group_id 对应的数据
	public function updateById($group_id,$data){
		$map['group_id']	=	$group_id;	//自定义 where() 语句的参数
		$result	=	$this->where($map)->save($data);	//基于 where() 更新对应的数据，
		return $result;	//返回值 $result 为 false 就是更新失败，其他返回值都是更新成功
	}
	
	//从 group 数据表 中删除主键为 $group_id 的记录，$data 是与 $group_id 对应的数据
	public function deleteById($group_id){
		$map['group_id']	=	$group_id;	//自定义 where() 语句的参数
		$result	=	$this->where($map)->delete();	//基于 where() 删除对应的数据，
		return $result;	//返回值为删除的记录数量
	}
	
	//从 group 数据表返回主键为 $group_id 的记录
	public function returnById($group_id){
		$map['group_id']	=	$group_id;	//自定义 where() 语句的参数
		$data_array	=	$this->where($map)->select();	//基于 where() 查找对应的数据，
		return $data_array[0];	//返回值 $data_array
	}
}