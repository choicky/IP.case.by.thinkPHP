<?php
namespace Home\Model;

use Think\Model;
//因为没有数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class FileTypeModel extends Model {
	
	//获取file_type表的列表，$p为当前页数，$limit为每页显示的记录条数
	public function listFileType($p,$limit) {
		$file_type_list	= $this->order('convert(file_name using gb2312) asc')->page($p.','.$limit)->select();
		
		$file_type_count	= $this->count();
		
		$Page	= new \Think\Page($file_type_count,$limit);
		$show	= $Page->show();
		
		return array("file_type_list"=>$file_type_list,"file_type_page"=>$show);
	}
	
	//向file_type表插入记录，$data是数组，且不包含主键
	public function addFileType($data){
		$result	=	$this->add($data);
		return $result;
	}
	
	//更新file_type表中主键为$file_type_id的记录，$data是数组
	public function editFileType($file_type_id,$data){
		$map['file_type_id']	=	$file_type_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}