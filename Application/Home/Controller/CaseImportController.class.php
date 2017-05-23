<?php
namespace Home\Controller;
use Think\Controller;

class CaseImportController extends Controller {
    
	//默认显示
	public function index(){
        //$this->display();
        $map_case_extend['case_id'] = 527; 
        $case_extend_list = M('CaseExtend')->where($map_case_extend)->find();
        dump($case_extend_list);
    }
	
	//导入
	public function import(){
		
    //读取原表的数据
    $case_source_list  = M('CaseSource')->select();
    $case_source_count  = count($case_source_list);
    
    
    for($i=0,$n=0;$i<$case_source_count;$i++,$n++){
           
      //构造maping
      $map_case_for_find['application_number']  = trim($case_source_list[$i]['application_number']);
      
      //读取Case表，并计数
      $case_list  = M('Case')->where($map_case_for_find)->select();
      $case_count  = count($case_list);
      
      //如果没有匹配，则原数据存入到 CaseOutput 表；否则进行处理
      if(!$case_count){
        $case_source_list[$i]['notfound'] = '白兔系统找到这个案子，但盈方管理系统没有找到；如该商标是有效的，要录入 admin.ipinfo.cn ';
        dump($case_source_list[$i]);
       $result	=	M('CaseOutput')->add($case_source_list[$i]);

       if(false !== $result){
          //$this->success('修改成功', U('Case/view','case_id='.$case_id));
        }else{
          $this->error('notfound的案件保存失败'.$i);
        }
        
      }else{
        
        // n 用来统计存入到 CaseOutput 表 的数量
        $n  = $n  + $case_count -1;
        
        for($j=0;$j<$case_count;$j++){
          
          //先更新 Case_extend
          $map_case_extend_for_find['case_id'] = $case_list[$j]['case_id']; 
          $case_extend_list = M('CaseExtend')->where($map_case_extend_for_find)->find();
          $case_extend_count  = count($case_extend_list);
          
          if($case_extend_count){
            
            $map_case_extend_for_update['case_extend_id'] = $case_extend_list['case_extend_id'];
            $case_extend_data['remarks'] = $case_extend_list['remarks'].'+'.$case_source_list[$i]['remarks'];
            $result	=	M('CaseExtend')->where($map_case_extend_for_update)->save($case_extend_data);
            
            if(false !== $result){
              //$this->success('修改成功', U('Case/view','case_id='.$case_id));
            }else{
              $this->error('更新 Extend 失败'.$i.'和'.$j);
            }
            
          }else{
            
            $case_extend_data['remarks'] = $case_source_list[$i]['remarks'];
            $case_extend_data['case_id'] = $case_list[$j]['case_id'];
            $result	=	M('CaseExtend')->add($case_extend_data);
            
            if(false !== $result){
              //$this->success('修改成功', U('Case/view','case_id='.$case_id));
            }else{
              $this->error('新增 Extend 失败'.$i.'和'.$j);
            }
          }
          
          //下面更新主表
          
          
          //判断分类号是否相同
          //先判断 case_source 是否有数据
          if(!trim($case_source_list[$i]['formal_title'])){
            $case_source_list[$i]['formal_title'] = $case_list[$j]['formal_title'];
          }else{            
            //如 case 有数据，才比较差异
            if(!trim($case_list[$j]['formal_title'])){
              $formal_title_test  = trim($case_source_list[$i]['formal_title'])  - trim($case_list[$j]['formal_title']);
              if($formal_title_test){
                $case_source_list[$i]['formal_title_test'] = '盈方系统原来登记的分类号是：'.trim($case_list[$j]['formal_title']).'，'.'已将白兔的分类号：'.$case_source_list[$i]['formal_title'].'，加到中括号里';
              }
            }
            
            //将 case_source 的数据附加到 case 的数据后面
            $case_source_list[$i]['formal_title'] = trim($case_list[$j]['formal_title']).'['.$case_source_list[$i]['formal_title'].']';            
          }
          
          //判断商标名称是否相同
          //先判断 case_source 是否有数据
          if(!trim($case_source_list[$i]['tentative_title'])){
            $case_source_list[$i]['tentative_title'] = $case_list[$j]['tentative_title'];
          }else{            
            //如 case 有数据，才比较差异
            if(trim($case_list[$j]['tentative_title'])){
              $tentative_title_test  = strcmp(trim($case_source_list[$i]['tentative_title']),trim($case_list[$j]['tentative_title']));
              if($tentative_title_test){
                $case_source_list[$i]['tentative_title_test'] = '盈方系统原来登记的商标名称是：'.trim($case_list[$j]['tentative_title']).'，'.'已将白兔的商标名称：'.$case_source_list[$i]['tentative_title'].'加到中括号里';
              }
            }
            
            //将 case_source 的数据附加到 case 的数据后面
            $case_source_list[$i]['tentative_title'] = trim($case_list[$j]['tentative_title']).'['.$case_source_list[$i]['tentative_title'].']';            
          }
          
          //判断申请日是否相同
          //先判断 case_source 是否有数据
          if(!trim($case_source_list[$i]['application_date'])){
            $case_source_list[$i]['application_date'] = $case_list[$j]['application_date'];
          }else{            
            //如 case 有数据，才比较差异
            if(trim($case_list[$j]['application_date'])){
              $application_date_test  = strtotime(trim($case_source_list[$i]['application_date']))  - trim($case_list[$j]['application_date']);
              if($application_date_test){
                $case_source_list[$i]['application_date_test'] = '盈方系统原来登记的申请日是：'.date("Y-m-d",trim($case_list[$j]['application_date'])).'，'.'已更新为白兔的申请日：'.trim($case_source_list[$i]['application_date']).'，请核对';
              }
            }
            //将 case_source 的数据变换格式
            $case_source_list[$i]['application_date'] = strtotime(trim($case_source_list[$i]['application_date']));            
          }
          
          //判断发证日是否相同
          //先判断 case_source 是否有数据
          if(!trim($case_source_list[$i]['issue_date'])){
            $case_source_list[$i]['issue_date'] = $case_list[$j]['issue_date'];
          }else{
            //如 case 有数据，才比较差异
            if(trim($case_list[$j]['issue_date'])){
              $issue_date_test  = strtotime(trim($case_source_list[$i]['issue_date']))  - trim($case_list[$j]['issue_date']);
              if($issue_date_test){
                $case_source_list[$i]['issue_date_test'] = '盈方系统原来登记的发证日是：'.date("Y-m-d",trim($case_list[$j]['issue_date'])).'，'.'已更新为白兔的发证日：'.trim($case_source_list[$i]['issue_date']).'，请核对';
              }
            }
            //将 case_source 的数据变换格式
            $case_source_list[$i]['issue_date'] = strtotime(trim($case_source_list[$i]['issue_date']));            
          }
          
          $map_case_for_update['case_id']   =  $case_list[$j]['case_id'];
          $result	=	M('Case')->where($map_case_for_update)->field('formal_title,tentative_title,application_date,issue_date')->save($case_source_list[$i]);
          
          if(false !== $result){
            //$this->success('修改成功', U('Case/view','case_id='.$case_id));
          }else{
            $this->error('修改 Case 失败'.$i.'和'.$j);
          }
          
          if($formal_title_test OR $tentative_title_test OR $application_date_test OR $issue_date_test){
            $case_source_list[$i]['case_id'] = $case_list[$j]['case_id'];
            dump($case_source_list[$i]);
            $result	=	M('CaseOutput')->add($case_source_list[$i]);
          }       
          
          if(false !== $result){
            //$this->success('修改成功', U('Case/view','case_id='.$case_id));
          }else{
            $this->error('校验后新增 CaseExtend 失败'.$i.'和'.$j);
          }

          
        }
      }
     
      
    }
    dump($n);
    
    
		
	}
	
}