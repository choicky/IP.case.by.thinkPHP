<?php
namespace Home\Controller;
use Think\Controller;

class CaseImportController extends Controller {
    
	//默认显示
	public function index(){
        
        $result = FALSE;
        $case_source_data = array();
        $map_case_source['application_number'] = 12418718;
        $case_source_data = M('CaseSource')->where($map_case_source)->find();
        
        
        $case_data = array();
        $map_case['case_id'] = 2797;
        $case_data = M('Case')->where($map_case)->find();
        
        $a = $case_data['tentative_title'];
        $b = $case_source_data['tentative_title'];
        
        echo('$a：');
        echo($a).'<br>';
        echo('$b：');
        echo($b).'<br>';
        
        echo('$a 包含 $b 的比较结果：');
    
        $result = mb_strpos( trim( $a),trim( $b),0,'UTF-8');
        if(FALSE !== $result){
            $result = TRUE;
        }

        echo($result);
    }

    
	//导入
	public function import(){

        //预定义读取 case_source 的字段
        $case_source_field_for_read = 'formal_title,application_number,tentative_title,application_date,issue_date,remarks';

        //预定义查询 case 的字段
        $case_field_for_find = 'case_id,application_number';

        //预定义更新 case 的字段
        $case_field_for_update = 'case_id,formal_title,application_number,tentative_title,application_date,issue_date';
        
        //初始化变量
        $number_of_new_case = 0; // 为新case数量，
        $number_of_same_case = 0; // 为信息完全相同的数量
        $number_of_update_case = 0; // 为更新的case数量，
        $number_of_diff_case = 0; // 为信息有差异的case数量，
        $number_of_add_extend = 0; // 为新增到 case_extend 的数量，
        $number_of_update_extend = 0; // 为修改 case_extend 的数量
        
        //读取 case_source 表
        $case_source_list = array();
        $case_source_list = M( 'CaseSource')->field($case_source_field_for_read)->select();
                
        for( $i=0; $i<10; $i++){
                   
            //初始化变量
            $case_source_data = array();
            $application_number = '';
            $map_case_for_find = array();
            $case_list = array();
            
            //赋值到变量
            $case_source_data = $case_source_list[$i];
            if(trim( $case_source_data['application_date'])){
                $case_source_data['application_date'] = strtotime(trim( $case_source_data['application_date']));
            }
            if(trim( $case_source_data['issue_date'])){
                $case_source_data['issue_date'] = strtotime(trim( $case_source_data['issue_date']));
            }            
            $application_number = $case_source_data['application_number'];
            $map_case_for_find['application_number'] = $application_number;
            
            //找出 case 表中的匹配记录
            $case_list = M( 'Case' )->field($case_field_for_find)->where( $map_case_for_find)->select();
            
            if(count($case_list) < 1){ //如果 case 表没有匹配的记录，则原数据存入到 CaseOutput 表
                $case_source_data['notfound'] = '白兔系统找到这个案子，但盈方管理系统没有找到；如该商标是有效的，要录入 管理系统 ';

                $result = FALSE;
                $result = addToCaseOutput($case_source_data);
                
                dump($case_source_data);
                echo('第三方提供的案件信息表目前'.$number_of_new_case.'+1个新案，<br>');
                if(false !== $result){
                    // $number_of_new_case 为新case数量
                    echo('该第'.$number_of_new_case.'+1个新案已保存到 case_output 数据表。<br>');
                    $number_of_new_case = $number_of_new_case  + 1;
                }else{
                    echo('但是，该第'.$number_of_new_case.'个新案未能保存到 case_output 数据表。<br>');
                }
                
            }else{  //如果 case 表有匹配的记录，则更新
                 
                // $j 为 case 匹配的记录数量，先更新主表
                for($j=0;$j<count($case_list);$j++){
                    //初始化变量
                    $case_id = '';
                    $case_data = array();
                    $map_case_for_update = array();
                    $case_test = array();
                    $remarks_test = array();
                    $case_result = FALSE;
                    $case_extend_result = FALSE;
                    $case_output_result = FALSE;
                    
                    //赋值到变量
                    $case_id = $case_list[$j]['case_id'];
                    $map_case_for_update['case_id'] = $case_id;
                    $case_data = M('Case')->field($case_field_for_update)->where($map_case_for_update)->find();
                    
                    //比较两个案件的基本信息
                    $case_test = caseCompare($case_source_data,$case_data);
                    
                    //比较两个案件的备注信息
                    $remarks_test = remarksCompare($case_source_data,$case_data);
                    
                    if($case_test[0] and $remarks_test[0]){ //如果基本信息与备注信息都相同
                        // $number_of_same_case 为信息完全相同的数量
                        echo('第三方提供的案件信息表目前共有'.$number_of_same_case.'+1个案件的信息与管理系统的相同。<br>');
                        $number_of_same_case = $number_of_same_case +1;
                    }elseif((!$case_test[0]) and (!$remarks_test[0])){ //如果基本信息与备注信息都不相同
                        $case_result = M('Case')->field($case_field_for_update)->save($case_test[1]);
                        echo('这是saveToCase');
                        dump($case_test[1]);
                        
                        echo('第三方提供的案件信息表目前共有'.$number_of_update_case.'+1个案件的基本信息与管理系统目前登记的不同，<br>');
                        // $number_of_update_case 为更新了基本信息的案件数量，$number_of_update_extend 为更新了备注信息的案件数量 
                        if(FALSE !== $case_result){
                            echo('该第'.$number_of_update_case.'+1个案件的基本信息更新到管理系统 case 表。<br>');
                            $number_of_update_case = $number_of_update_case +1;
                        }else{
                            echo('但是，该第'.$number_of_update_case.'+1个案件的基本信息未能更新到管理系统 case 表。<br>');
                        }
                        
                        $case_extend_result = addToCaseExtend($remarks_test[1]);
                        dump($remarks_test[1]);
                        echo('第三方提供的案件信息表目前共有'.$number_of_update_extend.'+1个案件的备注信息与管理系统目前登记的不同，<br>');
                        if(FALSE !== $case_extend_result){
                            echo('该第'.$number_of_update_extend.'+1个案件的备注信息更新到管理系统 case_extend 表。<br>');
                            $number_of_update_extend = $number_of_update_extend +1;
                        }else{
                             echo('但是，该第'.$number_of_update_extend.'+1个案件的备注信息未能更新到管理系统 case_extend 表。<br>');
                        }
                    }elseif((!$case_test[0]) && ($remarks_test[0])){    //如果基本信息不相同，备注信息相同
                        $case_result = M('Case')->field($case_field_for_update)->save($case_test[1]);
                        
                        dump($case_test[1]);
                        echo('第三方提供的案件信息表目前共有'.$number_of_update_case.'+1个案件的基本信息与管理系统目前登记的不同，<br>');
                        // $number_of_update_case 为更新了基本信息的案件数量，$number_of_update_extend 为更新了备注信息的案件数量 
                        if(FALSE !== $case_result){
                            echo('该第'.$number_of_update_case.'+1个案件的基本信息更新到管理系统 case 表。<br>');
                            $number_of_update_case = $number_of_update_case +1;
                        }else{
                            echo('但是，该案件的基本信息未能更新到管理系统 case 表。<br>');
                        }                       
                    }else{  //如果基本信息相同，备注信息不相同
                        $case_extend_result = addToCaseExtend($remarks_test[1]);
                        
                        dump($remarks_test[1]);
                        echo('第三方提供的案件信息表目前共有'.$number_of_update_extend.'+1个案件的备注信息与管理系统目前登记的不同，<br>');
                        // $number_of_update_case 为更新了基本信息的案件数量，$number_of_update_extend 为更新了备注信息的案件数量 
                        if(FALSE !== $case_extend_result){
                            echo('该第'.$number_of_update_extend.'+1个案件的备注信息更新到管理系统 case_extend 表。<br>');
                            $number_of_update_extend = $number_of_update_extend +1;
                        }else{
                           echo('但是，该第'.$number_of_update_extend.'+1个案件的备注信息未能更新到管理系统 case_extend 表。<br>');
                        }
                    }
                    
                    if((!$case_test[0]) OR (!$remarks_test[0])){ //如果基本信息与备注信息不完全相同，则复制到 case_output
                        $case_test[1]['remarks_test'] = $remarks_test[1]['remarks_test'];
                        $case_output_result = addToCaseOutput($case_test[1]);
                        
                        dump($case_test[1]);
                        echo('第三方提供的案件信息表目前共有'.$number_of_diff_case.'+1个案件的信息与管理系统有差异，<br>');
                        // $number_of_diff_case 信息有差异的案件数量 
                        if(FALSE !== $case_output_result){
                            //$number_of_diff_case 为信息有差异的case数量
                            $number_of_diff_case = $number_of_diff_case +1;
                            echo('该第'.$number_of_diff_case.'+1个案件已存入 case_output 表<br>');
                            
                        }else{
                             echo('但是，该第'.$number_of_diff_case.'+1个案件未能存入 case_output 表<br>');
                        }
                    }

                }  
            }
        }
        
        echo('第三方共提供了'.$i.'个案件的信息<br>');
        echo('其中共有'.$number_of_new_case.'个案件是新的，请尽快补录入管理系统<br>');
        echo('其中共有'.$number_of_same_case.'个案件与管理系统的相同<br>');
        echo('其中共有'.$number_of_update_case.'个案件的基本信息已更新到管理系统<br>');
        echo('其中共有'.$number_of_update_extend.'个案件的备注信息已更新到管理系统<br>');
        echo('其中共有'.$number_of_diff_case.'个案件的基本信息、备注信息已更新到管理系统<br>');
      
    }
    
    

}