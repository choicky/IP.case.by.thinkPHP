<?php
namespace Home\Model;
use Think\Model;
use Think\Model\RelationModel;

class ClientModel extends RelationModel {
	protected $_link = array(
		'ClientExtend'	=>	array(
			'mapping_type'		=>	self::HAS_ONE,
			'class_name'		=>	'ClientExtend',
			'foreign_key'		=>	'client_id',
			'mapping_fields'	=>	'name_en,address_zh,address_en,id_number,business_number,tax_number',
			'as_fields'			=>	'name_en,address_zh,address_en,id_number,business_number,tax_number',
		),
		
	);
	
	public function listClient($p,$limit) {
		//$p为当前页数，$limit为每页显示的记录条数
		$data	= $this->relation(true)->order('convert(name_zh using gb2312) asc')->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$data,"page"=>$show);
	}
	
	public function addClient($data){
		//$data是数组，且不包含主键
		$result	=	$this->add($data);
		return $result;
	}
	
	public function editClient($client_id,$data){
		//$case_type_id为主键，$data是数组，且不包含主键
		
		$client_extend = M('ClientExtend');
		$result = $client_extend->where(array('client_id'=>$client_id))->find();
		if(!is_array($result)){
			$extend['client_id']=$client_id;
			$client_extend->add($extend);
		}
			
		$map['client_id']	=	$client_id;
		$result = $this->relation(true)->where($map)->save($data);
		
		return $result;
	}
}