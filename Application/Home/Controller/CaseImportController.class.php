<?php
namespace Home\Controller;
use Think\Controller;

class CaseImportController extends Controller {
    
	//默认显示
	public function index(){
        //$this->display();
        $map['application_number'] = 18702085;
        
        $case_list = M('Case')->where($map)->find();
        
        $case_title = $case_list['tentative_title'];
        
        $case_source_list = M('CaseSource')->where($map)->find();
        $case_source_title = $case_source_list['tentative_title'];
        
        echo($case_title);
        
        echo('<br>'.'上面是 $case_title ，下面是 !$case_source_title <br>');
        
        echo($case_source_title);
        
        $result = mb_strrpos($case_source_title, $case_title, 0);
        
                echo('<br>');


        echo($result);
        
        echo('<br>'.'上面是 $result ，下面是 !$result <br>');
        
        echo(!$result);
        
        echo('<br>');

        


    }
	
	//导入
	public function import(){
		
        //读取 case_source 表
        $case_source_list  = M( 'CaseSource')->select();
        
        $case_source_count = count($case_source_list);
        
        /* 
        $i 为case_source 表的记录数量
        $number_of_new_case 为新case数量，
        $number_of_update_case 为更新的case数量，
        $number_of_diff_case 为信息有差异的case数量，
        $number_of_add_extend 为新增到 case_extend 的数量，
        $number_of_update_extend 为修改 case_extend 的数量         */        
        for( $i=0,$number_of_new_case=0,$number_of_update_case=0,$number_of_diff_case=0,$number_of_add_extend,$number_of_update_extend; $i<count( $case_source_list); $i++){
                   
            //用 application_number 作为匹配关键词
            $map_case_for_find[$i]['application_number']  = trim( $case_source_list[$i]['application_number'] );
            
            //找出 case 表中的匹配记录
            $case_list  = M( 'Case' )->where( $map_case_for_find[$i])->select();
            
            if(count($case_list) < 1){ //如果 case 表没有匹配的记录，则原数据存入到 CaseOutput 表
                $case_source_list[$i]['notfound'] = '白兔系统找到这个案子，但盈方管理系统没有找到；如该商标是有效的，要录入 admin.ipinfo.cn ';
                $case_source_list[$i]['application_date'] = strtotime(trim( $case_source_list[$i]['application_date']));
                $case_source_list[$i]['issue_date'] = strtotime(trim( $case_source_list[$i]['issue_date']));

                $result	=	M('CaseOutput')->field('tentative_title,application_number,application_date,formal_title,issue_date,remarks')->add($case_source_list[$i]);

                if(false !== $result){
                    // $number_of_new_case 为新case数量
                    $number_of_new_case  = $number_of_new_case  + 1;
                    echo('这是在 count($case_list) <1 里面'.count($case_list).'<br>');
                    echo('case_source表第'.$i.'case表第'.$j.',新增到 case_output<br>');
                    dump($case_source_list[$i]);
                    
                }else{
                    $this->error('notfound的案件保存失败'.$i);
                }
                
            }else{  //如果 case 表有匹配的记录，则更新
                 
                // $j 为 case 匹配的记录数量，先更新主表
                for($j=0;$j<count($case_list);$j++){                

                    $case_id = $case_list[$j]['case_id'];
                    $case_data[$case_id] = $case_list[$j];
                    
                    //判断分类号是否相同
                    if(trim( $case_source_list[$i]['formal_title'])){  //如果 case_source 有相应数据
                        if(trim( $case_data[$case_id]['formal_title'])){ //如果 case 有相应数据
                            $formal_title_test  = trim( $case_source_list[$i]['formal_title'])  - trim( $case_data[$case_id]['formal_title']);
                            if( $formal_title_test){    //如果数据不相同
                                $case_data[$case_id]['formal_title_test'] = '盈方系统原来登记的分类号是：'.trim($case_data[$case_id]['formal_title']).'，'.'已将白兔的分类号：'.$case_source_list[$i]['formal_title'].'，加到中括号里';
                                $case_data[$case_id]['formal_title'] = trim( $case_data[$case_id]['formal_title']).'['.trim( $case_source_list[$i]['formal_title']).']';
                            }
                        }else{  ////如果 case 没有相应数据，或者两者数据相同
                            $case_data[$case_id]['formal_title'] = trim( $case_source_list[$i]['formal_title']);
                        }
                    }
                      
                    //判断商标名称是否相同
                    if(trim( $case_source_list[$i]['tentative_title'])){   //如果 case_source 有相应数据
                        if(trim( $case_data[$case_id]['tentative_title'])){   //如果 case 有相应数据
                            $tentative_title_test  = mb_strpos( trim( $case_data[$case_id]['tentative_title']),trim( $case_source_list[$i]['tentative_title']),0,'UTF-8');
                            if( !($tentative_title_test >= 0 ) ){   //如果 Case 的数据未包含 Case_Source 的数据
                                $case_data[$case_id]['tentative_title_test'] = '盈方系统原来登记的商标名称是：'.trim( $case_data[$case_id]['tentative_title']).'，'.'已将白兔的商标名称：'.trim( $case_source_list[$i]['tentative_title']).'加到中括号里';
                                $case_data[$case_id]['tentative_title'] = trim( $case_data[$case_id]['tentative_title']).'['.trim( $case_source_list[$i]['tentative_title']).']'; 
                            }
                        }else{  ////如果 case 没有相应数据，或者两者数据相同
                            $case_data[$case_id]['tentative_title'] = trim( $case_source_list[$i]['tentative_title']);
                        }
                    }
                      
                    //判断申请日是否相同
                    if(trim( $case_source_list[$i]['application_date'])){  //如果 case_source 有相应数据
                        if(trim( $case_data[$case_id]['application_date'])){  //如果 case 有相应数据
                            $application_date_test  = strtotime(trim( $case_source_list[$i]['application_date']))  - trim( $case_data[$case_id]['application_date']);
                            if( $application_date_test){    //如果数据不相同
                                $case_data[$case_id]['application_date_test'] = '盈方系统原来登记的申请日是：'.date("Y-m-d",trim( $case_data[$case_id]['application_date'])).'，'.'已更新为白兔的申请日：'.trim( $case_source_list[$i]['application_date']).'，请核对';
                                $case_data[$case_id]['application_date'] = strtotime(trim( $case_source_list[$i]['application_date'])); 
                            }
                        }else{  ////如果 case 没有相应数据，或者两者数据相同
                            $case_data[$case_id]['application_date'] = strtotime(trim( $case_source_list[$i]['application_date']));
                        }
                    }
                      
                    //判断发证日是否相同
                    if(trim( $case_source_list[$i]['issue_date'])){    //如果 case_source 有相应数据
                        if(trim( $case_data[$case_id]['issue_date'])){    //如果 case 有相应数据
                            $issue_date_test  = strtotime(trim( $case_source_list[$i]['issue_date']))  - trim( $case_data[$case_id]['issue_date']);
                            if( $issue_date_test){  //如果数据不相同
                                $case_data[$case_id]['issue_date_test'] = '盈方系统原来登记的发证日是：'.date("Y-m-d",trim( $case_data[$case_id]['issue_date'])).'，'.'已更新为白兔的发证日：'.trim( $case_source_list[$i]['issue_date']).'，请核对';
                                $case_data[$case_id]['issue_date'] = strtotime(trim( $case_source_list[$i]['issue_date']));
                            }
                        }else{  ////如果 case 没有相应数据，或者两者数据相同
                            $case_data[$case_id]['issue_date'] = strtotime(trim( $case_source_list[$i]['issue_date']));
                        }
                    }
                      
                    $map_case_for_update[$case_id]['case_id']   =  $case_id;
                    
                    $result	=	M('Case')->where( $map_case_for_update[$case_id])->field('tentative_title,application_number,application_date,formal_title,issue_date')->save( $case_data[$case_id]);
                    //$result	=	M('Case')->field('case_id,tentative_title,application_number,application_date,formal_title,issue_date')->save( $case_data[$case_id]);
                    
                    if(false !== $result){
                        // $number_of_update_case 用来统计有差异的case数量
                        $number_of_update_case  = $number_of_update_case  + 1;
                        echo('case_source表第'.$i.'case表第'.$j.',更新到 case<br>');
                        dump($case_data[$case_id]);
                        //$this->success('修改成功', U('Case/view','case_id='.$case_id));
                    }else{
                        $this->error('修改 Case 失败'.$i.'和'.$j);
                    }
                    
                    // 将有差异的记录存储到 case_output
                    if($formal_title_test OR ($tentative_title_test >=0 ) OR $application_date_test OR $issue_date_test){
                
                        $result	=	M('CaseOutput')->field('case_id,tentative_title,tentative_title_test,application_number,application_number_test,application_date,application_date_test,formal_title,formal_title_test,issue_date,issue_date_test')->add( $case_data[$case_id]);

                    } 
                      
                    if(false !== $result){
                        // $number_of_diff_case 用来统计有差异的case数量
                        $number_of_diff_case  = $number_of_diff_case  + 1;
                        echo('case_source表第'.$i.'case表第'.$j.',差异案记录到 case_output<br>');
                        dump($case_source_list[$i]);
                        //$this->success('修改成功', U('Case/view','case_id='.$case_id));
                    }else{
                        $this->error('校验后新增 CaseExtend 失败'.$i.'和'.$j);
                    }
                    

                    // 下面更新麻烦的 case_extend 表
                    if(trim( $case_source_list[$i]['remarks'])){   //如果 case_source 有 remarks
                        
                        $map_case_extend_for_find['case_id'] = $case_list[$j]['case_id'];
                        $case_extend_list = M('CaseExtend')->where( $map_case_extend_for_find)->find();
                        
                        if(count($case_extend_list) < 1 ){   // case_extend 表没有对应于 case_id 的记录
                            $case_extend_data[$case_id]['case_id'] = $case_list[$j]['case_id'];
                            $case_extend_data[$case_id]['remarks'] = trim( $case_source_list[$i]['remarks']);
                            $result	=	M('CaseExtend')->field('case_id,remarks')->add(  $case_extend_data[$case_id]);
                            if(false !== $result){
                                // $number_of_add_extend 为新增到 case_extend 的数量
                                $number_of_add_extend  = $number_of_add_extend  + 1;
                                echo('case_source表第'.$i.'case表第'.$j.',新增到 case_extend<br>');
                                dump($case_extend_data[$case_id]);
                                //$this->success('修改成功', U('Case/view','case_id='.$case_id));
                            }else{
                                $this->error('校验后新增 CaseExtend 失败'.$i.'和'.$j);
                            }
                        }else{  // case_extend 表有对应于 case_id 的记录 
                            $map_case_extend_for_update[$case_id]['case_extend_id'] =   $case_extend_list['case_extend_id'];
                            
                            if(empty( $case_extend_list['remarks'])){   //如果 case_extend 没有相应 remarks
                                $case_extend_data[$case_id]['remarks'] = trim( $case_source_list[$i]['remarks']);
                            }else{  //如果 case_extend 有相应 remarks
                                $remarks_test  = mb_strpos( trim( $$case_extend_list['remarks']),trim( $case_source_list[$i]['remarks']),0,'UTF-8');
                                if( !($remarks_test >= 0)){   //如果 Case_Extend 的数据未包含 Case_Source 的数据
                                    $case_extend_data[$case_id]['remarks'] = trim( $case_extend_list['remarks']).'['.trim( $case_source_list[$i]['remarks']).']';
                                }
                            }                                                                       
                             
                             $result	=	M('CaseExtend')->where($map_case_extend_for_update[$case_id])->field('remarks')->save(  $case_extend_data[$case_id]);

                             if(false !== $result){
                                // $number_of_update_extend 为修改 case_extend 的数量
                                $number_of_update_extend  = $number_of_update_extend  + 1;
                                echo('case_source表第'.$i.'case表第'.$j.',更新到 case_extend<br>');
                                dump($case_extend_data[$case_id]);
                                //$this->success('修改成功', U('Case/view','case_id='.$case_id));
                            }else{
                                $this->error('校验后新增 CaseExtend 失败'.$i.'和'.$j);
                            }
                            
                        }

                    }
                    
                   
                }  
            }
        }
         
        echo('$number_of_new_case 为新case数量，等于'.$number_of_new_case.'<br>');
        echo('$number_of_update_case 为更新的case数量，等于'.$number_of_update_case.'<br>');
        echo('$number_of_diff_case 为信息有差异的case数量，等于'.$number_of_diff_case.'<br>');
        echo('$number_of_add_extend 为新增到 case_extend 的数量，等于'.$number_of_add_extend.'<br>');
        echo('$number_of_update_extend 为修改 case_extend 的数量，等于'.$number_of_update_extend.'<br>');
       
    }
    
    
    

}