<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $year_option	=	I('post.year_option');
		$case_prefix	=	'P'.$year_option.'%';
		
		$case_type_group_id	=	I('post.case_type_group_option');
	
		$map['our_ref']	=	array('like',$case_prefix);
		$map['CaseTypeGroup']['case_type_group_id']	=	$case_type_group_id;
		$map['_logic'] = 'AND';
		$order['convert(our_ref using gb2312)']	=	'desc';
		
		$Model	=	D('Case');
		$case_data	=	$Model->relation(true)->field(true)->where($map)->order($order)->find();
		
		//$this->ajaxReturn($case_data);
		print_r($case_data);
    }
}