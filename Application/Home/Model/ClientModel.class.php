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
	
	//获取client表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listClient($p,$limit) {
		$client_list	= $this->relation(true)->order('convert(client_name_zh using gb2312) asc')->page($p.','.$limit)->select();
		
		$client_count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("client_list"=>$client_list,"client_page"=>$show);
	}
	
	//向client表插入记录，$data是数组，且不包含主键
	public function addClient($data){
		$result	=	$this->relation(true)->add($data);
		return $result;
	}
	
	//更新client表中主键为$client_id的记录，$data是数组
	public function editClient($client_id,$data){
		//先在client_extend表新建与client_id对应的记录，否则无法更新；这是thinkPHP的数据关联的bug
		$client_extend = M('ClientExtend');
		$result = $client_extend->where(array('client_id'=>$client_id))->find();
		if(!is_array($result)){
			$extend['client_id']=$client_id;
			$client_extend->add($extend);
		}
		
		$map['client_id']	=	$client_id;
		$result	=	$this->relation(true)->where($map)->save($data);
		return $result;
	}
}