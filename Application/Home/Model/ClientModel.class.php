<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: ClientController,
// +----------------------------------------------------------------------

namespace Home\Model;

//因为数据表关联，必须继承RelationModel，注释Model
//use Think\Model;
use Think\Model\RelationModel;

class ClientModel extends RelationModel {
	
	//定义本数据表的自动完成，1为新增时自动完成，2为更新时自动完成，3为所有情况都自动完成
	protected $_auto = array(		
		
		// 将 yyyy-mm-dd 转换时间戳
		array('update_date','stringToTimestamp',3,'function') , 
	);

   //定义本数据表的自动验证，0为存在字段就验证，1为必须验证，2是值不为空时才验证
	protected $_validate = array(
		 
		 //定义必须的字段
		 array('client_name','require','必须输入',1), 
   );
	
	//定义本数据表的关联性
	protected $_link = array(							
		'ClientExtend'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'ClientExtend',			//重新定义本数据关联的名称
			'class_name'		=>	'ClientExtend',			//被关联的数据表
			'mapping_type'		=>	self::HAS_ONE,			//主从关系的一对一关联
			'foreign_key'		=>	'client_id',			//外键
			'mapping_fields'	=>	'client_name_en,client_id_number,client_business_number,client_tax_number',		//关联字段
			'as_fields'			=>	'client_name_en,client_id_number,client_business_number,client_tax_number',		//关联字段别名
		),
		'ClientContact'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'ClientContact',			//重新定义本数据关联的名称
			'class_name'		=>	'ClientContact',			//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,			//主从关系的一对一关联
			'foreign_key'		=>	'client_id',			//外键
			'mapping_fields'	=>	'contact_person,contact_address,contact_address_en,update_date',		//关联字段
			'mapping_order' 	=> 'update_date asc',		//排序
			//'as_fields'			=>	'client_name_en,client_address_zh,client_address_en,client_id_number,client_business_number,client_tax_number',		//关联字段别名
		),

	);
	
	//返回本数据表的基本数据，可作为选单
	public function listBasic() {			
		$order['convert(client_name using gb2312)']	=	'asc';
		$list	=	$this->field('client_id,client_name')->order($order)->select();
		return $list;
	}
	
	//返回本数据表的所有数据
	public function listAll() {
		$order['convert(client_name using gb2312)']	=	'asc';
		$list	=	$this->relation(true)->order($order)->select();
		return $list;
	}
	
	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['convert(client_name using gb2312)']	=	'asc';
		$list	= $this->relation(true)->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//更新本数据表中主键为$client_id的记录，$data是数组
	public function update($client_id,$data){
		//先在client_extend表新建与client_id对应的记录，否则无法更新；这是thinkPHP的数据关联的bug
		$Model = M('ClientExtend');
		$map_client_extend['client_id']	=	$client_id;
		$result = $Model->where($map_client_extend)->find();
		if(!is_array($result)){
			$data_client_extend['client_id']=$client_id;
			$Model->add($data_client_extend);
		}
		
		$result	=	$this->relation(true)->where($map_client_extend)->save($data);
		return $result;
	}
}