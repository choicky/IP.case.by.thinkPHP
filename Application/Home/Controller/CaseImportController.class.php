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
    
    //分页显示第三方信息源的案件，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listSourceCaseByPage() {
		$p	= I("p",1,"int");
        $page_limit  =   C("RECORDS_PER_PAGE");
        
        $order['case_source_id']	=	'asc';
        
		$case_source_list	=	M('CaseSource')->order($order)->page($p.','.$page_limit)->select();
        
        $count	=	M('CaseSource')->count();
        
        $Page	= new \Think\Page($count,$page_limit);
		$show	= $Page->show();
		
		$this->assign('case_list',$case_source_list);
		$this->assign('case_page',$show);
		$this->assign('case_count',$count);
        
        $this->display();
	}
    
     //分页显示待核对的案件，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listOutputCaseByPage() {
		$p	= I("p",1,"int");
        $page_limit  =   C("RECORDS_PER_PAGE");
        
        $order['notfound']	=	'desc';
        $order['issue_date_test']	=	'asc';
        $order['formal_title_test']	=	'asc';
        $order['tentative_title_test']	=	'asc';
        $order['remarks_test']	=	'asc';
        
		$case_source_list	=	M('CaseOutput')->order($order)->page($p.','.$page_limit)->select();
        
        $count	=	M('CaseOutput')->count();
        
        $Page	= new \Think\Page($count,$page_limit);
		$show	= $Page->show();
		
		$this->assign('case_list',$case_source_list);
		$this->assign('case_page',$show);
		$this->assign('case_count',$count);
        
        $this->display();
	}
    
    //删除 Case_Output 的案件
	public function deleteOutputCase(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_output_id
			$case_output_id	=	trim(I('post.case_output_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', U('CaseImport/listOutputCaseByPage'));
			}
			
			if(1==$yes_btn){
				
				$map['case_output_id']	=	$case_output_id;
				
				//获取基本信息
				$case_output_list	=	M('CaseOutput')->getByCaseOutputId($map['case_output_id']);
				
				//判断
				if($case_output_list['confirm'] == 0){
					$this->error('本案尚未确认，不能删除', U('CaseImport/listOutputCaseByPage'));
				}
                
				$result = M('CaseOutput')->where($map)->delete();
				if(FALSE !== $result){
					$this->success('删除成功', U('CaseImport/listOutputCaseByPage'));
				}
			}
			
		} else{
			$case_output_id = I('get.case_output_id',0,'int');

			if(!$case_output_id){
				$this->error('未指明要删除的记录');
			}
			
			$case_output_list = M('CaseOutput')->getByCaseOutputId($case_output_id);

			$this->assign('case_list',$case_output_list);
			
      //返回当前时间
      $today = time();
      $this->assign('today',$today);
      
			$this->display();
		}
	}
    
    //更新 Case_Output 的案件，主要就是更改是否确认
	public function updateOutputCase(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_output_id
			$case_output_id	=	trim(I('post.case_output_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$data_for_update['case_output_id'] = $case_output_id;
                $data_for_update['confirm'] = 0;
                
                $result = M('CaseOutput')->save($data_for_update);
				if(FALSE !== $result){
					$this->success('已将案件变更为【未核对】', U('CaseImport/listOutputCaseByPage'));
				}
			}
			
			if(1==$yes_btn){
				
				$data_for_update['case_output_id'] = $case_output_id;
                $data_for_update['confirm'] = 1;
                $result = M('CaseOutput')->save($data_for_update);
				if(FALSE !== $result){
					$this->success('已将案件变更为【已核对】状态', U('CaseImport/listOutputCaseByPage'));
				}
			}
			
		} else{
			$case_output_id = I('get.case_output_id',0,'int');

			if(!$case_output_id){
				$this->error('未指明要删除的记录');
			}
			
			$case_output_list = M('CaseOutput')->getByCaseOutputId($case_output_id);

			$this->assign('case_list',$case_output_list);
			
      //返回当前时间
      $today = time();
      $this->assign('today',$today);
      
			$this->display();
		}
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
        $number_of_new_case = 1; // $number_of_new_case 为 case_source 表中有的，而 case 表没有的案件数量，要另存到 case_output 表
        $number_of_same_case = 1; // $number_of_same_case 为 case 表中有的，且信息与 case_source 表相同的案件，不用处理
        $number_of_diff_case = 1; // $number_of_diff_case 为 case 表中有的，且信息与 case_source 表不相同的案件，该案件被更新后要另存到 case_output 表
        $number_of_update_case = 1; // $number_of_update_case 为 case 中被更新的记录的数量，如数据库操作正常 number_of_update_case == number_of_diff_case
        $number_of_output_case = 1; // $number_of_output_case 为存入 case_output 待核对的case数量，如数据库操作正常 number_of_output_case =  number_of_new_case + number_of_diff_case
        
        //读取 case_source 表
        $case_source_list = array();
        $case_source_list = M( 'CaseSource')->field($case_source_field_for_read)->select();
                
        for( $i=0; $i<50; $i++){
                   
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
                
                // $number_of_new_case 为 case_source 表中有的，而 case 表没有的案件数量，要另存到 case_output 表
                echo('从第三方提供的数据表中发现第'.$number_of_new_case.'个新案，需要事后【手工】把这新案录入系统<br>');
                
                if(false !== $result){
                    echo('该第'.$number_of_new_case.'个新案已保存到 case_output 数据表，以便事后【手工】把这新案录入系统<br>');
                    
                    // $number_of_output_case 为存入 case_output 待核对的case数量，如数据库操作正常 number_of_output_case =  number_of_new_case + number_of_diff_case
                    $number_of_output_case = $number_of_output_case  + 1;
                }else{
                    echo('但是，该第'.$number_of_new_case.'个新案未能保存到 case_output 数据表。<br>');
                    
                }
                $number_of_new_case = $number_of_new_case  + 1;
                
            }else{  //如果 case 表有匹配的记录，则根据差异情况决定是否更新
                 
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
                        
                        // $number_of_same_case 为 case 表中有的，且信息与 case_source 表相同的案件，不用处理
                        echo('管理系统中发现第'.$number_of_same_case.'个信息相同的案件。<br>');
                        $number_of_same_case = $number_of_same_case +1;
                        
                    }elseif((!$case_test[0]) and (!$remarks_test[0])){ //如果基本信息与备注信息都不相同
                        
                        $case_result = M('Case')->field($case_field_for_update)->save($case_test[1]);
                        $case_extend_result = addToCaseExtend($remarks_test[1]);
                        
                        dump($case_test[1]);
                        dump($remarks_test[1]);
                        
                        // $number_of_diff_case 为 case 表中有的，且信息与 case_source 表不相同的案件，该案件被更新后要另存到 case_output 表
                        echo('管理系统中发现第'.$number_of_diff_case.'个信息不同的案件，');
                        
                        if((FALSE !== $case_result) and (FALSE !== $case_extend_result)){
                            echo('该第'.$number_of_diff_case.'个信息不同案件的信息已被更新。<br>');
                            
                            // $number_of_update_case 为 case 中被更新的记录的数量，如数据库操作正常 number_of_update_case == number_of_diff_case
                            $number_of_update_case  = $number_of_update_case  +1;
                        }else{
                            echo('但是，该第'.$number_of_diff_case.'个信息不同的案件的信息未能被更新。<br>');
                        }
                        $number_of_diff_case = $number_of_diff_case + 1;
                        
                    }elseif((!$case_test[0]) && ($remarks_test[0])){    //如果基本信息不相同，备注信息相同
                        $case_result = M('Case')->field($case_field_for_update)->save($case_test[1]);
                        
                        dump($case_test[1]);
                        echo('管理系统中发现第'.$number_of_diff_case.'个信息不同的案件，');
                        
                        if(FALSE !== $case_result){
                            echo('该第'.$number_of_diff_case.'个信息不同案件的信息已被更新。<br>');
                            
                            // $number_of_update_case 为 case 中被更新的记录的数量，如数据库操作正常 number_of_update_case == number_of_diff_case
                            $number_of_update_case  = $number_of_update_case  +1;
                        }else{
                            echo('但是，该第'.$number_of_diff_case.'个信息不同的案件的信息未能被更新。<br>');
                        }
                        $number_of_diff_case = $number_of_diff_case + 1;
                        
                    }else{  //如果基本信息相同，备注信息不相同
                        $case_extend_result = addToCaseExtend($remarks_test[1]);
                        
                        dump($remarks_test[1]);

                        echo('管理系统中发现第'.$number_of_diff_case.'信息不同的案件，');
                        if(FALSE !== $case_extend_result){
                            echo('该第'.$number_of_diff_case.'个信息不同案件的信息已被更新。<br>');
                            
                            // $number_of_update_case 为 case 中被更新的记录的数量，如数据库操作正常 number_of_update_case == number_of_diff_case
                            $number_of_update_case  = $number_of_update_case  +1;
                        }else{
                            echo('但是，该第'.$number_of_diff_case.'个信息不同的案件的信息未能被更新。<br>');
                        }
                        $number_of_diff_case = $number_of_diff_case + 1;
                    }
                    
                    if((!$case_test[0]) OR (!$remarks_test[0])){ //如果基本信息与备注信息不完全相同，则另存到 case_output
                        $case_test[1]['remarks_test'] = $remarks_test[1]['remarks_test'];
                        $case_output_result = addToCaseOutput($case_test[1]);
                        
                        if(FALSE !== $case_output_result){
                            
                            $number_of_diff_case = $number_of_diff_case - 1;
                            echo('已将管理系统中第'.$number_of_diff_case.'个信息不同的案件另存入了 case_output 表备查<br>');
                            $number_of_diff_case = $number_of_diff_case + 1;
                            
                            $number_of_output_case = $number_of_output_case + 1;
                        }else{
                            $number_of_diff_case = $number_of_diff_case - 1;
                            echo('管理系统中第'.$number_of_diff_case.'个信息不同的案件未能存入了 case_output 表备查<br>');
                            $number_of_diff_case = $number_of_diff_case + 1;
                        }
                    }

                }  
            }
        }
        
        // $number_of_new_case 为 case_source 表中有的，而 case 表没有的案件数量，要另存到 case_output 表
        // $number_of_same_case 为 case 表中有的，且信息与 case_source 表相同的案件，不用处理
        // $number_of_diff_case 为 case 表中有的，且信息与 case_source 表不相同的案件，该案件被更新后要另存到 case_output 表
        // $number_of_update_case 为 case 中被更新的记录的数量，如数据库操作正常 number_of_update_case == number_of_diff_case
        // $number_of_output_case 为存入 case_output 待核对的case数量，如数据库操作正常 number_of_output_case =  number_of_new_case + number_of_diff_case
        if($number_of_new_case > 1){
            $number_of_new_case = $number_of_new_case - 1;
        }
        if($number_of_same_case > 1){
            $number_of_same_case = $number_of_same_case - 1;
        }
        if($number_of_diff_case > 1){
            $number_of_diff_case = $number_of_diff_case - 1;
        }
        if($number_of_update_case > 1){
            $number_of_update_case = $number_of_update_case - 1;
        }
        if($number_of_output_case > 1){
            $number_of_output_case = $number_of_output_case - 1;
        }
        
        echo('<hr>');
        echo('<hr>');
        echo('汇总<br>');
        echo('第三方共提供了'.$i.'个案件的信息；');
        echo('其中共有'.$number_of_new_case.'个案件是新的，已存入 case_output 表备查，请尽快补录入管理系统<br>');
        echo('管理系统中共有'.$number_of_same_case.'个案件的信息与第三方提供的信息相同<br>');
        echo('管理系统中共有'.$number_of_diff_case.'个案件的信息与第三方提供的信息不相同，');
        echo('管理系统中本次共有'.$number_of_update_case.'个案件的信息被更新，被更新的信息已另存到 case_output 表备查，请尽快核对 <br>');
        echo('本次共另存了'.$number_of_output_case.'个案件信息备查，包括管理系统中被更新的案件（'.$number_of_update_case.'个）、以及管理系统中没有而第三方数据源有的案件（'.$number_of_new_case.'个）<br>');
      
    }
    
    

}