<?php

//返回自2006年开始至目前的年份，可作为选单
function yearOption(){
	$current_year	=	intval(date("Y",time()));
	$start_year	=	2006;
	$year_date	=	array();
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
function addToCaseOutput($case_source_array){
    echo('addToCaseOutput函数<br>');
    dump($case_source_array);
    
    //预定义查询 case_output 的字段
    $case_output_field_for_find = 'case_output_id,application_number';
    
    //预定义新增 case_output 的字段
    $case_output_field_for_add = 'case_id,notfound,formal_title,formal_title_test,application_number,application_number_test,tentative_title,tentative_title_test,application_date,application_date_test,issue_date,issue_date_test,remarks,remarks_test';
    
    //预定义更新 case_output 的字段
    $case_output_field_for_update = 'case_output_id,case_id,notfound,formal_title,formal_title_test,application_number,application_number_test,tentative_title,tentative_title_test,application_date,application_date_test,issue_date,issue_date_test,remarks,remarks_test';

    //变量初始化
    $case_source_data = array();
    $application_number = '';
    $map_case_output_for_find = array();
    $case_output_id = '';
    $result = FALSE;
    $case_output_list = array();
    
    //参数赋值到变量
    $case_source_data = $case_source_array;
    $application_number = $case_source_data['application_number'];
    $map_case_output_for_find['application_number'] = $application_number;            
    
    //根据 case_output 是否存在相同的申请号进行不同的处理
    $case_output_list = M('CaseOutput')->field($case_output_field_for_find)->where($map_case_output_for_find)->find();            
    if(!(count($case_output_list) > 0)){    //如果 case_output 没有相同申请号的记录
        $result	=	M('CaseOutput')->field($case_output_field_for_add)->add($case_source_data);
        return  $result;
    }else{  //如果 case_output 有相同申请号的记录
        $case_source_data['case_output_id'] = $case_output_list['case_output_id'];
        $result	=	M('CaseOutput')->field($case_output_field_for_update)->save($case_source_data);
        return  $result;
    }
}

//预定义新增到 case_extend 的方法，没有成功录入就返回 FALSE
function addToCaseExtend($case_source_array){
    echo('addToCaseExtend函数<br>');
    dump($case_source_array);
    
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
    $case_source_data = $case_source_array;
    $case_id = $case_source_data['case_id'];
    $map_case_extend_for_find['case_id'] = $case_id;            
    
    //根据 case_extend 是否存在相同的申请号进行不同的处理
    $case_extend_list = M('CaseExtend')->field($case_extend_field_for_find)->where($map_case_extend_for_find)->find();            
    if(!(count($case_extend_list) > 0)){    //如果 case_extend 没有相同案件编号的记录
        $result	=	M('CaseExtend')->field($case_extend_field_for_add)->add($case_source_data);
        return  $result;
    }else{  //如果 case_extend 有相同申请号的记录
        $case_source_data['case_extend_id'] = $case_extend_list['case_extend_id'];
        $result	=	M('CaseExtend')->field($case_extend_field_for_update)->save($case_source_data);
        return  $result;
    }
}

//预定义数字比较函数，相同就返回 True，不相同就返回 FALSE
function dataCompare($data_1,$data_2){
    echo('dataCompare函数<br>');
    dump($data_1);
    dump($data_2);
    
    //变量初始化
    $result = FALSE;
    
    if(trim($data_1) == trim($data_2)){
        $result = TRUE;
    }
    
    return $result;
}

//预定义字符串比较函数，后者包含前者就返回 True，不包含就返回 FALSE
function strCompare($str_1,$str_2){
    echo('strCompare函数<br>');
    dump($str_1);
    dump($str_2);

    //变量初始化
    $result = FALSE;
    
    $result = mb_strpos(trim($str_2),trim($str_1),0,'UTF-8');
    if(FALSE !== $result){
        $result = TRUE;
    }
    
    return $result;
}

//预定义案件基本信息比较函数，返回一个数组，数组第一元素为 TRUE 就表示完全相同，为 FALSE 表示不完全相同；数组第二元素为处理过的 $case_array_2
function caseCompare($case_array_1,$case_array_2){
    echo('caseCompare函数<br>');
    dump($case_array_1);
    dump($case_array_2);
    //变量初始化
    $case_data_1 = array();
    $case_data_2 = array();
    $test_result = FALSE;
    $result = array();
    $formal_title_test = FALSE; //TRUE表示不用修改
    $tentative_title_test = FALSE;  //TRUE表示不用修改
    $application_date_test = FALSE; //TRUE表示不用修改
    $issue_date_test = FALSE;   //TRUE表示不用修改
    
    //参数赋值到变量
    $case_data_1 = $case_array_1;
    $case_data_2 = $case_array_2;
    
    //判断分类号是否相同
    if(!trim( $case_data_1['formal_title']) and !trim( $case_data_2['formal_title'])){   //两个数组都没有相应数据，即，数据相同
        $formal_title_test = TRUE;
    }elseif(trim( $case_data_1['formal_title']) and trim( $case_data_2['formal_title'])){  //两个数组都有相应数据
        $formal_title_test  = dataCompare( $case_data_1['formal_title'], $case_data_2['formal_title']); //如数据相同则返回 TRUE
        if( !$formal_title_test){    //如果数据不相同
            $case_data_2['formal_title_test'] = '管理系统原来登记的分类号是：'.trim($case_data_2['formal_title']).'，'.'已将第三方提供的分类号：'.$case_data_1['formal_title'].'，加到中括号里';
            $case_data_2['formal_title'] = trim( $case_data_2['formal_title']).'['.trim( $case_data_1['formal_title']).']';
        }
    }elseif(trim( $case_data_1['formal_title']) and !trim( $case_data_2['formal_title'])){    //数组1有相应数据，数组2没有
        $case_data_2['formal_title_test'] = '管理系统原来未登记分类号，已将第三方提供的分类号：'.$case_data_1['formal_title'].'登记到系统';
        $case_data_2['formal_title'] = trim( $case_data_1['formal_title']);
    }else{  //数组1没有相应数据，数组2有
        $formal_title_test = TRUE;
    }

    
    //判断管理系统的商标名称是否包含第三方数据源的商标名称
    if(!trim( $case_data_1['tentative_title']) and !trim( $case_data_2['tentative_title'])){   //两个数组都没有相应数据，即，数据相同
        $tentative_title_test = TRUE;
    }elseif(trim( $case_data_1['tentative_title']) and trim( $case_data_2['tentative_title'])){  //两个数组都有相应数据
        $tentative_title_test  = strCompare( $case_data_1['tentative_title'], $case_data_2['tentative_title']); //如后者包括前者，则返回 TRUE
        if( !$tentative_title_test){    //如果后者未包括前者，就将前者以[]的方式附加在后面
            $case_data_2['tentative_title_test'] = '管理系统原来登记的商标名称是：'.trim($case_data_2['tentative_title']).'，'.'已将第三方提供的商标名称：'.$case_data_1['tentative_title'].'，加到中括号里';
            $case_data_2['tentative_title'] = trim( $case_data_2['tentative_title']).'['.trim( $case_data_1['tentative_title']).']';
        }
    }elseif(trim( $case_data_1['tentative_title']) and !trim( $case_data_2['tentative_title'])){    //数组1有相应数据，数组2没有
        $case_data_2['tentative_title_test'] = '管理系统原来未登记商标名称，已将第三方提供的商标名称：'.$case_data_1['tentative_title'].'登记到系统';
        $case_data_2['tentative_title'] = trim( $case_data_1['tentative_title']);
    }else{  //数组1没有相应数据，数组2有
        $tentative_title_test = TRUE;
    }
      
    //判断申请日是否相同
    if(!trim( $case_data_1['application_date']) and !trim( $case_data_2['application_date'])){   //两个数组都没有相应数据，即，数据相同
        $application_date_test = TRUE;
    }elseif(trim( $case_data_1['application_date']) and trim( $case_data_2['application_date'])){  //两个数组都有相应数据
        $application_date_test  = dataCompare( $case_data_1['application_date'], $case_data_2['application_date']); //如数据相同则返回 TRUE
        if( !$application_date_test){    //如果数据不相同
            $case_data_2['application_date_test'] = '管理系统原来登记的申请日是：'.trim($case_data_2['application_date']).'，'.'已将第三方提供的申请日：'.$case_data_1['application_date'].'，加到中括号里';
            $case_data_2['application_date'] = trim( $case_data_2['application_date']).'['.trim( $case_data_1['application_date']).']';
        }
    }elseif(trim( $case_data_1['application_date']) and !trim( $case_data_2['application_date'])){    //数组1有相应数据，数组2没有
        $case_data_2['application_date_test'] = '管理系统原来未登记申请日，已将第三方提供的申请日：'.$case_data_1['application_date'].'登记到系统';
        $case_data_2['application_date'] = trim( $case_data_1['application_date']);
    }else{  //数组1没有相应数据，数组2有
        $application_date_test = TRUE;
    }

      
    //判断发证日是否相同
    if(!trim( $case_data_1['issue_date']) and !trim( $case_data_2['issue_date'])){   //两个数组都没有相应数据，即，数据相同
        $issue_date_test = TRUE;
    }elseif(trim( $case_data_1['issue_date']) and trim( $case_data_2['issue_date'])){  //两个数组都有相应数据
        $issue_date_test  = dataCompare( $case_data_1['issue_date'], $case_data_2['issue_date']); //如数据相同则返回 TRUE
        if( !$issue_date_test){    //如果数据不相同
            $case_data_2['issue_date_test'] = '管理系统原来登记的发证日是：'.trim($case_data_2['issue_date']).'，'.'已将第三方提供的发证日：'.$case_data_1['issue_date'].'，加到中括号里';
            $case_data_2['issue_date'] = trim( $case_data_2['issue_date']).'['.trim( $case_data_1['issue_date']).']';
        }
    }elseif(trim( $case_data_1['issue_date']) and !trim( $case_data_2['issue_date'])){    //数组1有相应数据，数组2没有
        $case_data_2['issue_date_test'] = '管理系统原来未登记发证日，已将第三方提供的发证日：'.$case_data_1['issue_date'].'登记到系统';
        $case_data_2['issue_date'] = trim( $case_data_1['issue_date']);
    }else{  //数组1没有相应数据，数组2有
        $issue_date_test = TRUE;
    }
    
    $test_result = ($formal_title_test and $tentative_title_test and $application_date_test and $issue_date_test);
    
    $result = array($test_result, $case_data_2);
    
    return $result;
}

//预定义案件备注信息比较函数，返回一个数组，数组第一元素为 TRUE 就表示完全相同，为 FALSE 表示不完全相同；数组第二元素为处理过的 $case_array_2
function remarksCompare($case_array_1,$case_array_2){
    echo('remarksCompare函数<br>');
    dump($case_array_1);
    dump($case_array_2);
    //预定义查询 case_extend 的字段
    $case_extend_field_for_find = 'case_extend_id,case_id,remarks';
    
    //变量初始化
    $case_data_1 = array();
    $case_data_2 = array();
    $case_data_3 = array();
    $test_result = FALSE;
    $result = array();
    $remarks_test = FALSE;  //TRUE表示不用修改           
    $case_id = '';
    $map_case_extend_for_find = array();
    
    //参数赋值到变量
    $case_data_1 = $case_array_1;
    $case_data_2 = $case_array_2;
    $case_id = $case_data_2['case_id'];
    $map_case_extend_for_find['case_id'] = $case_id;
    
    //根据 case_extend 是否存在相同的申请号进行不同的处理
    $case_data_3 = M('CaseExtend')->field($case_extend_field_for_find)->where($map_case_extend_for_find)->find();
    $case_data_2['remarks'] = $case_data_3['remarks'];
    
    //判断管理系统的备注信息是否包含第三方信息源提供的法律状态
    if(!trim( $case_data_1['remarks']) and !trim( $case_data_2['remarks'])){   //两个数组都没有相应数据，即，数据相同
        $remarks_test = TRUE;
    }elseif(trim( $case_data_1['remarks']) and trim( $case_data_2['remarks'])){  //两个数组都有相应数据
        $remarks_test  = strCompare( $case_data_1['remarks'], $case_data_2['remarks']); //如后者包含前者，就返回 TRUE
        if( !$remarks_test){    //如果后者未包含
            $case_data_2['remarks_test'] = '管理系统原来登记的备注信息是：'.trim($case_data_2['remarks']).'，'.'已将第三方提供的备注信息：'.$case_data_1['remarks'].'，加到中括号里';
            $case_data_2['remarks'] = trim( $case_data_2['remarks']).'['.trim( $case_data_1['remarks']).']';
        }
    }elseif(trim( $case_data_1['remarks']) and !trim( $case_data_2['remarks'])){    //数组1有相应数据，数组2没有
        $case_data_2['remarks_test'] = '管理系统原来未登记备注信息，已将第三方提供的备注信息：'.$case_data_1['remarks'].'登记到系统';
        $case_data_2['remarks'] = trim( $case_data_1['remarks']);
    }else{  //数组1没有相应数据，数组2有
        $remarks_test = TRUE;
    }        
    
    $test_result = $remarks_test ;
    
    $result = array($test_result, $case_data_2);
    
    return $result;
}
