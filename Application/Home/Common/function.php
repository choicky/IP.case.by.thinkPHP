<?php
function yearOption(){
	$current_year	=	intval(date("Y",time()));
	$start_year	=	2006;
	$year_date	=	array();
	for ($current_year;$current_year>$start_year;$current_year--){
		$year_date[]	=	$current_year;
	}
	return($year_date);	
}

function patentCaseGroupOption(){
	$map['case_group_name']	=	array('like','%专利%');
	$order['convert(case_type_group_name using gb2312)']	=	'asc';
	$patent_type_data	=	M('CaseGroup')->field(true)->where($map)->order($order)->select();
	return $patent_type_data;
}
