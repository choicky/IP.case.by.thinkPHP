<?php
namespace Home\Model;

//因为数据表关联，必须继承RelationModel，注释Model
//use Think\Model;
use Think\Model\RelationModel;

class ClientModel extends RelationModel {
	
	//定义client表与client_extend表的关联性
	protected $_link = array(
		'ClientExtend'	=>	array(
			'mapping_type'		=>	self::HAS_ONE,
			'class_name'		=>	'ClientExtend',
			'foreign_key'		=>	'client_id',
			'mapping_fields'	=>	'client_name_en,client_address_zh,client_address_en,client_id_number,client_business_number,client_tax_number',
			'as_fields'			=>	'client_name_en,client_address_zh,client_address_en,client_id_number,client_business_number,client_tax_number',
		),
	);
	
	//返回本数据表的所有数据
	public function listAll() {
		$order['convert(client_name using gb2312)']	=	'asc';
		$list	=	$this->relation(true)->field(true)->order($order)->select();
		return $list;
	}
	
	//返回本数据表的基本数据
	public function listBasic() {
		$Model	=	M('Client');
		$order['convert(client_name using gb2312)']	=	'asc';
		$list	= $Model->field(true)->order()->select();
		return $list;
	}
	
	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['convert(client_name using gb2312)']	=	'asc';
		$list	= $this->relation(true)->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"client_page"=>$show);
	}
	
	//更新本数据表中主键为$client_id的记录，$data是数组
	public function edit($client_id,$data){
		//先在client_extend表新建与client_id对应的记录，否则无法更新；这是thinkPHP的数据关联的bug
		$Model = M('ClientExtend');
		$map['client_id']	=	$client_id;
		$result = $Model->where($map)->find();
		if(!is_array($result)){
			$dataExtend['client_id']=$client_id;
			$Model->add($dataExtend);
		}
		
		$result	=	$this->relation(true)->where($map)->save($data);
		return $result;
	}
}