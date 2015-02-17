<?php
namespace Home\Model;		//定义 namespace
use Think\Model\RelationModel;		//定义继承的 Model

//自定义模块 Type , 继承上述的 RelationModel ，默认情况下对应于数据表 type
class TypeModel extends RelationModel {		
	
	//定义本数据表与 group 表的数据关联
	protected $_link = array(
		'Group'	=>	array(							//本数据关联的名称
						'mapping_name'		=>	'Group',			//重新定义本数据关联的名称
						'class_name'		=>	'Group',			//被关联的数据表
						'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
						'foreign_key'		=>	'group_id',		//外键，
						'mapping_fields'	=>	'group_name',	//关联字段
						'as_fields'			=>	'group_name'	//字段别名
		),
	
	);
	
		
			//自定义 listAll方法，返回本数据表的所有数据
	public function listAll() {			
		$order['convert(type_name using gb2312)']	=	'asc';		//以数组方式定义 order() 语句的参数
		$data_array	=	$this->order($order)->select();		//将 select 结果赋给 $data_array
		return $data_array;		//返回值
	}

	//更新 type 数据表 中主键为 $type_id 的记录，$data 是与 $type_id 对应的数据
	public function updateById($type_id,$data){
		$map['type_id']	=	$type_id;	//自定义 where() 语句的参数
		$result	=	$this->where($map)->save($data);	//基于 where() 更新对应的数据，
		return $result;	//返回值 $result 为 false 就是更新失败，其他返回值都是更新成功
	}
	
	//从 type 数据表 中删除主键为 $type_id 的记录，$data 是与 $type_id 对应的数据
	public function deleteById($type_id){
		$map['type_id']	=	$type_id;	//自定义 where() 语句的参数
		$result	=	$this->where($map)->delete();	//基于 where() 删除对应的数据，
		return $result;	//返回值为删除的记录数量
	}
	
	//从 type 数据表返回主键为 $type_id 的记录
	public function returnById($type_id){
		$map['type_id']	=	$type_id;	//自定义 where() 语句的参数
		$data_array	=	$this->where($map)->select();	//基于 where() 查找对应的数据，
		return $data_array[0];	//返回值 $data_array
	}
}