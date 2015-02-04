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
}