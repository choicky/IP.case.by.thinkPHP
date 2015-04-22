<?php
function yearOption(){
	$current_year	=	intval(date("Y",time()));
	$start_year	=	2006;
	$year_date	=	array();
	for ($current_year;$current_year>$start_year;$current_year--){
		$year_list[]	=	$current_year;
	}
	return($year_list);	
}

function patentCaseGroupOption(){
	$map['case_group_name']	=	array('like','%专利%');
	$order['convert(case_type_group_name using gb2312)']	=	'asc';
	$patent_type_data	=	M('CaseGroup')->field(true)->where($map)->order($order)->select();
	return $patent_type_data;
}

function multiplyByHundred($number){
	$number	=	$number	*	100;	
	return $number;
}

function numberOption($j){
	for($i=1;$i<=$j;$i++){
		$number_list[$i]=$i;
	}
	return $number_list;
}

//返回两个时间戳相差的年数，进1法返回
function yearInterval($application_date,$target_date){
	
	//确保第二个参数大于第一个参数
	if($application_date	>	$target_date){
		$tmp	=	$application_date;
		$application_date	=	$target_date;
		$target_date	=	$tmp;
	}
	
	//转为 yyyy-mm-dd 格式
	$application_date	=	date("Y-m-d",$application_date);
	$target_date	=	date("Y-m-d",$target_date);
	
	//将年月日赋到Y/m/d
	list($Y1,$m1,$d1)=explode('-',$application_date);
	list($Y2,$m2,$d2)=explode('-',$target_date);
	
	$Y	=	$Y2	-	$Y1;
	$m	=	$m2	-	$m1;
	$d	=	$d2	-	$d1;
	
	if($m	==	0){
		if($d	>=	0){
			$Y	=$Y	+	1;			
		}
	}elseif($m	>	0){
		$Y	=$Y	+	1;
	}
	
	return $Y;
}
