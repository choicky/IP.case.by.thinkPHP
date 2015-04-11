<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseFeeController,FeeTypeController
// +----------------------------------------------------------------------

namespace Home\Model;

//因启用了模型视图，所以禁用了关联定义
use Think\Model;
//use Think\Model\RelationModel;

class FeeTypeModel extends Model {
	
	//定义本数据表的自动完成
	protected $_auto = array(		
		// 将金额乘以100，multiplyByHundred 自定义于 common 文件夹的 function.php
		array('fee_default_amount','multiplyByHundred',3,'function') , 		
	);
	
	//定义本数据表的自动验证，0为存在字段就验证，1为必须验证，2是值不为空时才验证
	protected $_validate = array(
		 
		 //定义必须的字段
		 array('fee_type_name','require','必须填写费用类型',1), 

   );
	
   //返回本数据表的基本数据，可作为选单
	public function listBasic() {			
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->field('fee_type_id,fee_type_name,fee_default_amount')->order($order)->select();
		return $list;
	}
	
	//返回本数据表的所有数据
	public function listAll() {			
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->select();
		return $list;
	}

	//分页返回本数据表的所有数据，$p为当前页数，$limit为每页显示的记录条数
	public function listPage($p,$limit) {
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->field(true)->order($order)->page($p.','.$limit)->select();
		
		$count	= $this->count();
		
		$Page	= new \Think\Page($count,$limit);
		$show	= $Page->show();
		
		return array("list"=>$list,"page"=>$show,"count"=>$count);
	}
	
	//返回本数据表的“专利”数据
	public function listAllPatent() {
		$map['fee_type_name']	=	array('like','%专利%');
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表的“发明”数据
	public function listAllInvention() {
		$map['fee_type_name']	=	array('like','%发明%');
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表的“实用新型”数据
	public function listAllUtility() {
		$map['fee_type_name']	=	array('like','%实用新型%');
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表的“外观设计”数据
	public function listAllDesign() {
		$map['fee_type_name']	=	array('like','%外观设计%');
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表的“商标”数据
	public function listAllTrademark() {
		$map['fee_type_name']	=	array('like','%商标%');
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表的“版权”数据
	public function listAllCopyright() {
		$map['fee_type_name']	=	array('like','%版权%');
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}
	
	//返回本数据表的“PCT国际”数据
	public function listAllPCT() {
		$map['fee_type_name']	=	array('like','%PCT%');
		$order['convert(fee_type_name using gb2312)']	=	'asc';
		$list	=	$this->where($map)->order($order)->select();
		return $list;
	}

}