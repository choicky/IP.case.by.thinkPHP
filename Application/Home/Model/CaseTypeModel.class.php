<?php
namespace Home\Model;

//use Think\Model;
//因为需要数据表关联，启用RelationModel
use Think\Model\RelationModel;

class CaseTypeModel extends RelationModel {
	
	//定义本数据表的数据关联
	protected $_link = array(
	
		'CaseGroup'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CaseGroup',			//重新定义本数据关联的名称
			'class_name'		=>	'CaseGroup',			//被关联的数据表
			'mapping_type'		=>	self::BELONGS_TO,	//属于关系一对一关联			
			'foreign_key'		=>	'case_group_id',		//外键，
			'mapping_fields'	=>	'case_group_name',	//关联字段
			'as_fields'			=>	'case_group_name'	//字段别名
		),
    );
    
	//返回本数据表中与专利有关的数据
	public function listAllPatent() {
		$case_group_list	=	D('CaseGroup')->listAllPatent();
		var_dump(count($case_group_list));
		
		for($i=0;$i<count($case_group_list);$i++){
			$map['case_group_id']	=	$case_group_list[$i]['case_group_id'];
			$case_type_list[]	=	$this->field('case_type_id')->where($map)->select();
			var_dump($case_type_list);
		}
		var_dump(count($case_type_list));
		var_dump($case_type_list);
		/*$order['convert(case_group_name using gb2312)']	=	'asc';
		$data	=	$this->field(true)->where($map)->order($order)->select();
		return $case_type_list;*/
	}
	
    //返回本数据表的基本数据，可作为选项用
	public function listBasic() {
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$this->field('case_type_id,case_group_name')->order($order)->select();
		return $list;
	}
	
	//返回本数据表的所有数据
	public function listAll() {
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$this->order($order)->select();
		return $list;
	}
	
	//返回本数据表的 case_type_name 含有 $p 所有数据
	public function listAllLike($p) {
		$Model	=	M('CaseType');
		$map['case_type_name']	=	array('like', $p);
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	=	$Model->field(true)->where($map)->order($order)->select();
		return $list;
	}
		
	//返回本数据表的 case_type_name 含有 $p 所有数据
	public function listBasicLike($p) {
		$list	=	$this->listAllLike($p);
		return $list;
	}
	
	//分页返回本数据表的所有数据，$current_page 为当前页数，$recodes_per_page 为每页显示的记录条数
	public function listPage($current_page,$recodes_per_page) {
		$Model	=	D('CaseType');
		$order['convert(case_type_name using gb2312)']	=	'asc';
		$list	= $Model->field(true)->order($order)->page($current_page.','.$recodes_per_page)->select();
		
		$count	= $Model->count();
		
		$Page	= new \Think\Page($count,$recodes_per_page);
		$show	= $Page->show();
		
		return array("data"=>$list,"page"=>$show);
	}
	
	//更新本数据表中主键为$case_type_id的记录，$data是数组
	public function update($case_type_id,$data){
		$Model	=	M('CaseType');
		$map['case_type_id']	=	$case_type_id;
		$result	=	$Model->where($map)->save($data);
		return $result;
	}
    
    //返回某个 $case_group_id 的所有 case_type_id
	public function getCaseTypeId($case_group_id) {
		$map['case_group_id']    =   $case_group_id;
        $result   =   $this->field('case_type_id')->where($map)->select();
        
        for($i=0;   $i<count($result);  $i++){
            $data[$i]   =   $result[$i]['case_type_id'];
        }
        
        return $data;
	}

}