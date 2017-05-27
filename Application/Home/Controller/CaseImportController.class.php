<?php
namespace Home\Controller;
use Think\Controller;

class CaseImportController extends Controller {
    
	//默认显示
	public function index(){
        
        $result = FALSE;
        $case_source_data['tentative_title'] = '亿漂,EPURBIOSOLUTION';
        $case_target_data['tentative_title'] = '億漂及图[亿漂,EPURBIOSOLUTION]';
        $tentative_title_diff = !(FALSE !== mb_strpos($case_target_data['tentative_title'],$case_source_data['tentative_title']));
        
        echo($tentative_title_diff);
    }
    
    //分页显示第三方信息源的案件，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listSourceCaseByPage() {
		$p	= I("p",1,"int");
        $page_limit  =   C("RECORDS_PER_PAGE");
        
        $order['case_source_id']	=	'asc';
        
		$case_source_list	=	M('CaseSource')->order($order)->page($p.','.$page_limit)->select();
        
        $case_source_count	=	M('CaseSource')->count();
        $case_target_count = M('Case')->count();
        
        $Page	= new \Think\Page($count,$page_limit);
		$show	= $Page->show();
        
		$this->assign('case_list',$case_source_list);
		$this->assign('case_page',$show);
		$this->assign('case_source_count',$case_source_count);
        $this->assign('case_target_count',$case_target_count);
		
        $this->display();
	}
    
     //分页显示待核对的案件，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listOutputCaseByPage() {
		$p	= I("p",1,"int");
        $page_limit  =   C("RECORDS_PER_PAGE");
        
        $order['notfound']	=	'desc';
        $order['issue_date_notes']	=	'asc';
        $order['formal_title_notes']	=	'asc';
        $order['tentative_title_notes']	=	'asc';
        $order['remarks_notes']	=	'asc';
        
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
					$this->error('本案未经核对，不能删除', U('CaseImport/listOutputCaseByPage'));
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
        $number_of_new_case = 0; // $number_of_new_case 为 case_source 表中有的，而 case 表没有的案件数量，要另存到 case_output 表
        $number_of_same_case = 0; // $number_of_same_case 为 case 表中有的，且信息与 case_source 表相同的案件，不用处理
        $number_of_diff_case = 0; // $number_of_diff_case 为 case 表中有的，且信息与 case_source 表不相同的案件，不一定需要更新
        $number_of_diff_case_should_update = 0; // $number_of_diff_case_should_update 为 number_of_diff_case 需要被更新、另存的数量，number_of_diff_case_should_update <= number_of_diff_case
        $number_of_diff_case_updated = 0; // $number_of_diff_case_updated 为 number_of_diff_case_should_update 中被成功更新的案件数量，如数据库操作正常，number_of_diff_case_should_update == number_of_diff_case_updated
        $number_of_output_case = 0; // $number_of_output_case 为实际存入 case_output 待核对的 case 数量，如数据库操作正常 number_of_output_case =  number_of_new_case + number_of_update_should_output
        
        //读取 case_source 表
        $case_source_list = array();
        $case_source_list = M( 'CaseSource')->field($case_source_field_for_read)->select();
        
        //读取 case 表
        $case_list_count = 0;
        $case_list_count = M( 'Case' )->count();
        
        $case_handle_number = 0;
        $case_handle_number = $case_list_count * count($case_source_list);
        
        //进行简单的提示
        echo('<hr>');
        echo('开始处理案件信息：<br>');
        echo('第三方共提供了'.count($case_source_list).'个案件的信息；');
        echo('管理系统有'.$case_list_count.'个案件的信息；');
        $case_compare_number = count($case_source_list) * $case_list_count * 7;
        echo('要进行'.$case_compare_number.'次案件对比，耗时较长……<br>');
        echo('处理完成后，统计结果会在本页面底部显示；有变化的案件信息会另存到 <a href="/Home/CaseImport/listOutputCaseByPage" target="_blank">case_output</a> 表备查，请在系统处理完成后去核对；<br><br><hr>');

                
        for( $i=0; $i<count($case_source_list); $i++){
        //for( $i=0; $i<100; $i++){
                   
            //初始化变量
            $case_source_data = array();
            $application_number = '';
            $map_case_for_find = array();
            $case_list = array();
            
            //赋值到变量
            $case_source_data = $case_source_list[$i];
            $case_source_data['application_date'] = trim( $case_source_data['application_date']) ? strtotime(trim( $case_source_data['application_date'])) : '';
            $case_source_data['issue_date'] = trim( $case_source_data['issue_date']) ? strtotime(trim( $case_source_data['issue_date'])) : '';
       
            $application_number = $case_source_data['application_number'];
            $map_case_for_find['application_number'] = $application_number;
            
            //找出 case 表中的匹配记录
            $case_list = M( 'Case' )->field($case_field_for_find)->where( $map_case_for_find)->select();
            
            if(count($case_list) < 1){ //如果 case 表没有匹配的记录，则原数据存入到 CaseOutput 表
                $number_of_new_case = $number_of_new_case + 1;
                echo('从第三方提供的数据表中发现第'.$number_of_new_case.'个新案，<font color="red">应另存到 case_output 表</font>，且以后<font color="red">【手工录入】</font>管理系统；<br>');                
                $case_source_data['notfound'] = '第三方系统有这个案件，但盈方管理系统没有；要录入 管理系统 ';
                $result = addToCaseOutput($case_source_data);                
                if(false !== $result){
                    echo('该第'.$number_of_new_case.'个新案已保存到 case_output 数据表，以便事后【手工】把这新案录入系统。<hr>');                 
                    $number_of_output_case = $number_of_output_case  + 1;
                }else{
                    echo('但是，该第'.$number_of_new_case.'个新案未能保存到 case_output 数据表。<br>');
                    
                }
            }else{  //如果 case 表有匹配的记录，则根据差异情况决定是否更新                 
                for($j=0;$j<count($case_list);$j++){
                    //初始化变量
                    $case_id = '';
                    $case_data = array();
                    $map_case_for_update = array();
                    $case_compare = array();
                    $case_extend_compare = array();
                    $case_result = FALSE;
                    $case_extend_result = FALSE;
                    $case_output_result = FALSE;
                    
                    //赋值到变量
                    $case_id = $case_list[$j]['case_id'];
                    $map_case_for_update['case_id'] = $case_id;
                    $case_data = M('Case')->field($case_field_for_update)->where($map_case_for_update)->find();
                    
                    //比较两个案件的基本信息
                    $case_compare = caseCompare($case_source_data,$case_data);
                    
                    //比较两个案件的备注信息
                    $case_extend_compare = caseExtendCompare($case_source_data,$case_data);
                    
                    //统计信息相同、有差异的案件数量
                    if($case_compare['case_diff'] OR $case_extend_compare['remarks_diff']){    
                        $number_of_diff_case = $number_of_diff_case + 1;
                        echo('管理系统中发现第'.$number_of_diff_case.'个信息不同的案件（基本信息或法律状态不同），');
                        
                    }else{
                        $number_of_same_case = $number_of_same_case + 1;
                        echo('管理系统中发现第'.$number_of_same_case.'个信息相同的案件，');
                        
                    }
                    
                    //统计应当更新的案件数量，并把 case_extend 中的数据附加到 case 中
                    if($case_compare['case_diff_should_update'] OR $case_extend_compare['remarks_diff_should_update']){    
                        $number_of_diff_case_should_update = $number_of_diff_case_should_update + 1;
                        echo('该案件<font color = "red">需要更新到系统</font></a>，');
                        
                        $case_compare['case_target_data']['remarks'] = $case_extend_compare['case_extend_target_data']['remarks'];
                        $case_compare['case_target_data']['remarks_notes'] = $case_extend_compare['case_extend_target_data']['remarks_notes'];
                        dump($case_compare['case_target_data']);
                    }else{
                        echo('该案件不需要更新到系统</a>。<hr>');
                    }
                    
                    if($case_compare['case_diff_should_update'] and $case_extend_compare['remarks_diff_should_update']){
                        $case_result = M('Case')->field($case_field_for_update)->save($case_compare['case_target_data']);
                        $case_extend_result = addToCaseExtend($case_compare['case_target_data']);
                        if((FALSE !== $case_result) and (FALSE !== $case_extend_result)){
                            $number_of_diff_case_updated = $number_of_diff_case_updated + 1;
                            echo('该第'.$number_of_diff_case.'个信息不同案件的信息 <font color = "red">已更新到管理系统</font>。<hr>');
                            
                        }else{
                            echo('但是，该第'.$number_of_diff_case.'个信息不同的案件的信息<font color = "red">未能被更新</font>。<hr>');
                        }
                    }elseif($case_compare['case_diff_should_update']){
                        $case_result = M('Case')->field($case_field_for_update)->save($case_compare['case_target_data']);
                        if(FALSE !== $case_result){
                            $number_of_diff_case_updated = $number_of_diff_case_updated + 1;
                            echo('该第'.$number_of_diff_case.'个信息不同案件的信息 <font color = "red">已更新到管理系统</font>。<hr>');
                            
                        }else{
                            echo('但是，该第'.$number_of_diff_case.'个信息不同的案件的信息<font color = "red">未能被更新</font>。<hr>');
                        }
                    }elseif($case_extend_compare['remarks_diff_should_update']){
                        $case_extend_result = addToCaseExtend($case_compare['case_target_data']);
                        if(FALSE !== $case_extend_result){
                            $number_of_diff_case_updated = $number_of_diff_case_updated + 1;
                            echo('该第'.$number_of_diff_case.'个信息不同案件的信息 <font color = "red">已更新到管理系统</font>。<hr>');
                        }else{
                            echo('但是，该第'.$number_of_diff_case.'个信息不同的案件的信息<font color = "red">未能被更新</font>。<hr>');
                        }
                    }
                    
                    //将应当更新的案件信息存入 case_output
                    if($case_compare['case_diff_should_update'] OR $case_extend_compare['remarks_diff_should_update']){    
                        //dump($case_compare['case_target_data']);
                        $case_output_result = addToCaseOutput($case_compare['case_target_data']);

                        if(FALSE !== $case_output_result){                            
                            $number_of_output_case = $number_of_output_case + 1;
                            echo('已将管理系统中第'.$number_of_diff_case.'个基本信息或法律状态不同的案件<font color = "red">成功另存入 case_output 表备查</font>。<hr>');
                                                        
                        }else{
                            echo('管理系统中第'.$number_of_diff_case.'个信息不同的案件<font color = "red">未能另存到 case_output 表</font>。<hr>');
                        }
                    }
                }  
            }
        }
        
        /*
        $number_of_new_case = 0; // $number_of_new_case 为 case_source 表中有的，而 case 表没有的案件数量，要另存到 case_output 表
        $number_of_same_case = 0; // $number_of_same_case 为 case 表中有的，且信息与 case_source 表相同的案件，不用处理
        $number_of_diff_case = 0; // $number_of_diff_case 为 case 表中有的，且信息与 case_source 表不相同的案件，不一定需要更新
        $number_of_diff_case_should_update = 0; // $number_of_diff_case_should_update 为 number_of_diff_case 需要被更新、另存的数量，number_of_diff_case_should_update <= number_of_diff_case
        $number_of_diff_case_updated = 0; // $number_of_diff_case_updated 为 number_of_diff_case_should_update 中被成功更新的案件数量，如数据库操作正常，number_of_diff_case_should_update == number_of_diff_case_updated
        $number_of_output_case = 0; // $number_of_output_case 为实际存入 case_output 待核对的 case 数量，如数据库操作正常 number_of_output_case =  number_of_new_case + number_of_update_should_output
        */
                
        echo('<br>');
        echo('<hr>');
        echo('案件信息处理完成，汇总如下：<br>');
        echo('第三方共提供了'.$i.'个案件的信息；');
        echo('其中共有<font color = "red">'.$number_of_new_case.'个案件是新的</font>，应存入 <a href="/Home/CaseImport/listOutputCaseByPage" target="_blank">case_output</a> 表备查，要<font color = "red">尽快补录入管理系统</font><br>');
        echo('管理系统中共有'.$number_of_same_case.'个案件的信息与第三方提供的信息相同<br>');
        echo('管理系统中共有'.$number_of_diff_case.'个案件的信息与第三方提供的信息不相同，');
        echo('其中有<font color = "red">'.$number_of_diff_case_should_update.'个案件的信息应当更新到数据库</font>，实际<font color = "red">成功更新了'.$number_of_diff_case_updated.'个</font>案件。<br>');
        echo('本次实际上<font color = "red">共另存了'.$number_of_output_case.'个案件</font>信息到 <a href="/Home/CaseImport/listOutputCaseByPage" target="_blank">case_output</a> 备查，包括管理系统中因更新而另存的案件（<font color = "red">'.$number_of_diff_case_should_update.'个</font>）、以及管理系统中没有而第三方数据源有的案件（<font color = "red">'.$number_of_new_case.'个</font>）<br>');
      
    }
    
    

}