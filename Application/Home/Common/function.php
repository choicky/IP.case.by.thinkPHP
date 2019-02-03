<?php

//返回自2006年开始至目前的年份，可作为选单
function yearOption(){
	$current_year	=	intval(date("Y",time()));
	$start_year	=	2006;
	$year_list	=	array();
	for ($current_year;$current_year>$start_year;$current_year--){
		$year_list[]	=	$current_year;
	}
	return($year_list);	
}

//返回包含了专利这个大类的列表
function patentCaseGroupOption(){
	$map['case_group_name']	=	array('like','%专利%');
	$order['convert(case_type_group_name using gb2312)']	=	'asc';
	$patent_type_data	=	M('CaseGroup')->field(true)->where($map)->order($order)->select();
	return $patent_type_data;
}

//100倍的函数
function multiplyByHundred($number){
	$number	=	$number	*	100;	
	return $number;
}

//自定义 strtotime
function stringToTimestamp($time_string){
	$result	=	$time_string	?	strtotime($time_string)	:	'';
	return $result;
}

//数字选项
function numberOption($j){
	for($i=1;$i<=$j;$i++){
		$number_list[$i]=$i;
	}
	return $number_list;
}

//10年序列，商标续展用
function tenYearOption($j){
	for($i=1;$i<=$j;$i++){
		$number_list[$i]=bcmul($i,'10');
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


//预定义新增到 case_output 的方法，没有成功录入就返回 FALSE
function addToCaseOutput($case_source_para_array){
    
    //预定义查询 case_output 的字段
    $case_output_field_for_find = 'case_output_id,case_id,application_number';
    
    //预定义新增 case_output 的字段
    $case_output_field_for_add = 'case_id,notfound,formal_title,formal_title_notes,application_number,application_number_notes,tentative_title,tentative_title_notes,application_date,application_date_notes,issue_date,issue_date_notes,remarks,remarks_notes';
    
    //预定义更新 case_output 的字段
    $case_output_field_for_update = 'case_output_id,case_id,notfound,formal_title,formal_title_notes,application_number,application_number_notes,tentative_title,tentative_title_notes,application_date,application_date_notes,issue_date,issue_date_notes,remarks,remarks_notes';

    //变量初始化
    $case_source_data = array();
    $application_number = '';
    $case_id = '';
    $map_case_output_for_find = array();
    $case_output_id = '';
    $result = FALSE;
    $case_output_list = array();
    
    //参数赋值到变量
    $case_source_data = $case_source_para_array;
    $application_number = $case_source_data['application_number'];
    $case_id = $case_source_data['case_id'];
    $map_case_output_for_find['application_number'] = $application_number;
    if($case_id){
        $map_case_output_for_find['case_id'] = $case_id;
    }
    
    //根据 case_output 是否存在相同的申请号进行不同的处理
    $case_output_list = M('CaseOutput')->field($case_output_field_for_find)->where($map_case_output_for_find)->find();            
    if(!(count($case_output_list) > 0)){    //如果 case_output 没有相同 application_number、case_id 的记录
        $result	=	M('CaseOutput')->field($case_output_field_for_add)->add($case_source_data);
        return  $result;
    }else{  //如果 case_output 有相同 application_number、case_id 的记录
        $case_source_data['case_output_id'] = $case_output_list['case_output_id'];
        $result	=	M('CaseOutput')->field($case_output_field_for_update)->save($case_source_data);
        return  $result;
    }
}

//预定义新增到 case_extend 的方法，没有成功录入就返回 FALSE
function addToCaseExtend($case_source_para_array){
    
    //预定义查询 case_extend 的字段
    $case_extend_field_for_find = 'case_extend_id,case_id,remarks';
    
    //预定义新增 case_extend 的字段
    $case_extend_field_for_add = 'case_id,remarks';
    
    //预定义更新 case_extend 的字段
    $case_extend_field_for_update = 'case_extend_id,case_id,remarks';

    //变量初始化
    $case_source_data = array();
    $case_id = '';
    $map_case_extend_for_find = array();
    $case_extend_id = '';
    $result = FALSE;
    $case_extend_list = array();
    
    //参数赋值到变量
    $case_source_data = $case_source_para_array;
    $case_id = $case_source_data['case_id'];
    $map_case_extend_for_find['case_id'] = $case_id;            
    
    //根据 case_extend 是否存在相同的 case_id 的记录进行不同的处理
    $case_extend_list = M('CaseExtend')->field($case_extend_field_for_find)->where($map_case_extend_for_find)->find();            
    if(!(count($case_extend_list) > 0)){    //如果 case_extend 没有相同案件编号的记录
        $result	=	M('CaseExtend')->field($case_extend_field_for_add)->add($case_source_data);
        return  $result;
    }else{  //如果 case_extend 有相同 case_id 的记录
        $case_source_data['case_extend_id'] = $case_extend_list['case_extend_id'];
        $result	=	M('CaseExtend')->field($case_extend_field_for_update)->save($case_source_data);
        return  $result;
    }
}


//预定义附加函数，把字符串放在 [] 里面
function strInBracket($str_para){

    //变量初始化
    $str_data = '';
    $result = '';
    
    //赋值处理
    $str_data = $str_para;
    $result = '['.$str_data.']';
    
    return $result;
}

//预定义案件基本信息比较函数，返回一个数组，数组第一元素为 TRUE 就表示完全相同，为 FALSE 表示不完全相同；数组第二元素为处理过的 $case_target_para_array
function caseCompare($case_source_para_array,$case_target_para_array){

    //变量初始化
    $case_source_data = array();
    $case_target_data = array();
    $result = array();
    
    //参数赋值到变量
    $case_source_data = $case_source_para_array;
    $case_target_data = $case_target_para_array;
    
    //判断管理系统的商标名称是否包含第三方数据源的商标名称
    // $tentative_title 的变量初始化
    $tentative_title_diff = FALSE; //TRUE 表示 case_target 的 $tentative_title 的内容 未包含 case_source 的 $tentative_title 的内容
    $tentative_title_diff_should_update = FALSE; //TRUE 表示需要更新 case_target 
    
    $case_source_data['tentative_title'] = trim($case_source_data['tentative_title']);
    $case_target_data['tentative_title'] = trim($case_target_data['tentative_title']);
    $tentative_title_diff = (0 == substr_count($case_target_data['tentative_title'],$case_source_data['tentative_title']));
    $tentative_title_diff_should_update = ($tentative_title_diff AND $case_source_data['tentative_title']);
    if($tentative_title_diff_should_update){
        $case_target_data['tentative_title'] = $case_target_data['tentative_title'].strInBracket($case_source_data['tentative_title']);    
        $case_target_data['tentative_title_notes'] = '管理系统原来登记的商标名称是：'.$case_target_para_array['tentative_title'].'，已把第三方提供商标名称 【'.$case_source_data['tentative_title'].'】放到中括号并附加到后面，请核对';
    }
        
    //判断分类号是否相同
    // $formal_title 的变量初始化
    $formal_title_diff = FALSE; //TRUE 表示 case_target 的 $formal_title 信息未包含 case_source 的 $formal_title 信息
    $formal_title_diff_should_update = FALSE; //TRUE 表示 formal_title_diff 情况中需要更新到 case 表的记录
    
    $case_source_data['formal_title'] = trim($case_source_data['formal_title']);
    $case_target_data['formal_title'] = trim($case_target_data['formal_title']);
    $formal_title_diff = !($case_source_data['formal_title'] == $case_target_data['formal_title']);
    $formal_title_diff_should_update = ($formal_title_diff AND $case_source_data['formal_title']);
    if($formal_title_diff_should_update){
        $case_target_data['formal_title'] = $case_target_data['formal_title'].strInBracket($case_source_data['formal_title']);
        $case_target_data['formal_title_notes'] = '管理系统原来登记的商标类别是：'.$case_target_para_array['formal_title'].'，已把第三方提供商标名称 【'.$case_source_data['formal_title'].'】放到中括号并附加到后面，请核对';
    }
    

    //判断申请日是否相同
    // $application_date 的变量初始化
    $application_date_diff = FALSE; //TRUE 表示 case_target 的 $application_date 信息未包含 case_source 的 $application_date 信息
    $application_date_diff_should_update = FALSE; //TRUE 表示 application_date_diff 情况中需要更新到 case 表的记录

    $application_date_diff = !($case_source_data['application_date'] == $case_target_data['application_date']);
    $application_date_diff_should_update = ($application_date_diff AND $case_source_data['application_date']);
    if($application_date_diff_should_update){
        $case_target_data['application_date'] = $case_source_data['application_date'];
        $case_target_data['application_date_notes'] = '管理系统原来登记的申请日是：'.date("Y-m-d",$case_target_para_array['application_date']).'，已用第三方提供的申请日 【'.date("Y-m-d",$case_source_data['application_date']).'】去替换';
    }
    
    //判断发证日是否相同
    // $issue_date 的变量初始化
    $issue_date_diff = FALSE; //TRUE 表示 case_target 的 $issue_date 与 case_source 的 $issue_date 不同
    $issue_date_diff_should_update = FALSE; //TRUE 表示需要更新 case_target

    $issue_date_diff = !($case_source_data['issue_date'] == $case_target_data['issue_date']);
    $issue_date_diff_should_update = ($issue_date_diff AND $case_source_data['issue_date']);
    if($issue_date_diff_should_update){
        $case_target_data['issue_date'] = $case_source_data['issue_date'];
        $case_target_data['issue_date_notes'] = '管理系统原来登记的发证日是：'.date("Y-m-d",$case_target_para_array['issue_date']).'，已用第三方提供的发证日 【'.date("Y-m-d",$case_source_data['issue_date']).'】去替换';
    }
    
    //构造反馈结果
    $result['case_diff'] = $issue_date_diff OR $application_date_diff OR $tentative_title_diff OR $formal_title_diff;
    $result['case_diff_should_update'] = $issue_date_diff_should_update OR $application_date_diff_should_update OR $tentative_title_diff_should_update OR $formal_title_diff_should_update;
    
    $result['case_target_data'] = $case_target_data;
    
    return $result;
}

//预定义案件备注信息比较函数，返回一个数组，数组第一元素为 TRUE 就表示完全相同，为 FALSE 表示不完全相同；数组第二元素为处理过的 $case_target_para_array
function caseExtendCompare($case_source_para_array,$case_target_para_array){

    //预定义查询 case_extend 的字段
    $case_extend_field_for_find = 'case_extend_id,case_id,remarks';
    
    //变量初始化
    $case_extend_source_data = array();
    $case_extend_target_data = array();
    $case_extend_list = array();
    $result = array();
    $case_id = '';
    $map_case_extend_for_find = array();
    
    //参数赋值到变量
    $case_extend_source_data = $case_source_para_array;
    $case_extend_target_data = $case_target_para_array;
    $case_id = $case_extend_target_data['case_id'];
    $map_case_extend_for_find['case_id'] = $case_id;
    
    //根据 case_extend 是否存在相同的申请号进行不同的处理
    $case_extend_list = M('CaseExtend')->field($case_extend_field_for_find)->where($map_case_extend_for_find)->find();
    $case_extend_target_data['remarks'] = $case_extend_list['remarks'];
    
    //判断备注信息是否相同
    // $remarks 的变量初始化
    $remarks_diff = FALSE; //TRUE 表示 case_target 的 $remarks 信息未包含 case_source 的 $remarks 信息
    $remarks_diff_should_update = FALSE; //TRUE 表示 remarks_diff 情况中需要更新到 case 表的记录
    
    $case_extend_source_data['remarks'] = trim($case_extend_source_data['remarks']);
    $case_extend_target_data['remarks'] = trim($case_extend_target_data['remarks']);
    $remarks_diff = (0 == substr_count($case_extend_target_data['remarks'],$case_extend_source_data['remarks']));
    $remarks_diff_should_update = ($remarks_diff AND $case_extend_source_data['remarks']);
    if($remarks_diff_should_update){
        $case_extend_target_data['remarks'] = $case_extend_target_data['remarks'].strInBracket($case_extend_source_data['remarks']);
        $case_extend_target_data['remarks_notes'] = '管理系统原来登记的备注是：'.$case_target_para_array['remarks'].'，已把第三方提供商标名称 【'.$case_extend_source_data['remarks'].'】放到中括号并附加到后面，请核对';
    }
        
    $result['remarks_diff'] = $remarks_diff;
    $result['remarks_diff_should_update'] = $remarks_diff_should_update;
    
    $result['case_extend_target_data'] = $case_extend_target_data;

    return $result;
}
