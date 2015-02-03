<?php
namespace Home\Model;
use Think\Model;
use Think\Model\RelationModel;

class ClientModel extends RelationModel {
	protected $_link = array(
		'ClientAddress'	=>	array(
			'mapping_type'		=>	self::HAS_ONE,
			'foreign_key'		=>	'client_id',
//			'class_name'		=>	'ClientAddress',
			'mapping_fields'	=>	'client_address_en,client_address_zh',
			'as_fields'			=>	'client_address_en,client_address_zh',
		),
		
		'ClientId'	=>	array(
			'mapping_type'		=>	self::HAS_ONE,
			'foreign_key'		=>	'client_id',
//			'class_name'		=>	'ClientAddress',
			'mapping_fields'	=>	'client_id_number,client_business_number,client_tax_number',
			'as_fields'			=>	'client_id_number,client_business_number,client_tax_number',
		),
		
	);
}