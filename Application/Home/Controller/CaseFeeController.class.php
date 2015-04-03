<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Controller" is for normal Controller.
// +----------------------------------------------------------------------
// | This file is required by:
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class CaseFeeController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listPage");
    }
	
	//默认跳转到listPage，分页显示
	public function listAll(){
        header("Location: listPage");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		$case_fee_list = D('CaseFee')->listPage($p,$limit);
		$this->assign('case_fee_list',$case_fee_list['list']);
		$this->assign('case_fee_page',$case_fee_list['page']);
		$this->assign('case_fee_count',$case_fee_list['count']);
		
		$this->display();
	}

	//新增	
	public function add(){
		$case_id	=	trim(I('post.case_id'));
		
		$Model	=	D('CaseFee');
		if (!$Model->create()){ 
			
			 // 如果创建数据对象失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());

		}else{
			 
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			
			// 写入新增数据成功，返回案件信息页面
			$this->success('新增成功', 'view/case_id/'.$case_id);
			
		}else{
			$this->error('增加失败');
		}
	}
	
	//更新
	public function update(){
		
		//针对 POST 的处理方式
		if(IS_POST){
			$case_id	=	trim(I('post.case_id'));
			$case_fee_id	=	trim(I('post.case_fee_id'));
			
			$Model	=	D('CaseFee');
			if (!$Model->create()){ 
				 
				 // 如果创建数据对象失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 
			}else{
				 
				 // 验证通过 修改数据
				 $result	=	$Model->save();		 
			}
			if(false !== $result){
				
				// 修改数据成功，返回案件信息页面
				$this->success('修改成功', 'view/case_id/'.$case_id);
				
			}else{
				$this->error('修改失败');
			}
			
		//针对 GET 的处理方式
		} else{
			
			//接收要编辑的 $case_fee_id
			$case_fee_id = I('get.case_fee_id',0,'int');
			if(!$case_fee_id){
				$this->error('未指明要编辑的费用编号');
			}
			
			//取出相应的信息
			$case_fee_list = M('CaseFee')->getByCaseFeeId($case_fee_id);
			$this->assign('case_fee_list',$case_fee_list);
			
			//取出 CasePhase 表的内容以及数量
			$case_phase_list	=	D('CasePhase')->listBasic();
			$case_phase_count	=	count($case_phase_list);
			$this->assign('case_phase_list',$case_phase_list);
			$this->assign('case_phase_count',$case_phase_count);
			
			//获取本条费用的案子的 $case_type_name
			$case_type_name	=	D('CaseFee')->returnCaseTypeName($case_fee_id);
			
			//根据 $case_type_name 判断本案是专利、还是非专利
			if(false	!==	strpos($case_type_name,'专利')){
				$is_patent	=	1;
			}else{
				$is_patent	=	0;
			}
						
			//取出 FeeType 表中 与 $case_type_name 对应的内容以及数量
			if($is_patent){
				$map_fee_type['fee_type_name']	=	array('like','%专利%');
			}else{
				$map_fee_type['fee_type_name']	=	array('notlike','%专利%');
			}
			$fee_type_list	=	D('FeeType')->listBasic();
			$fee_type_count	=	count($fee_type_list);
			$this->assign('fee_type_list',$fee_type_list);
			$this->assign('fee_type_count',$fee_type_count);
			
			//取出 Payer 表的内容以及数量
			$payer_list	=	D('Payer')->listBasic();
			$payer_count	=	count($payer_list);
			$this->assign('payer_list',$payer_list);
			$this->assign('payer_count',$payer_count);
			
			//取出 CostCenter 表的内容以及数量
			$cost_center_list	=	D('CostCenter')->listBasic();
			$cost_center_count	=	count($cost_center_list);
			$this->assign('cost_center_list',$cost_center_list);
			$this->assign('cost_center_count',$cost_center_count);
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);
			
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_fee_id 和 case_id
			$case_fee_id	=	trim(I('post.case_fee_id'));
			$case_id	=	trim(I('post.case_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', 'view/case_id/'.$case_id);
			}
			
			if(1==$yes_btn){
				
				$map['case_fee_id']	=	$case_fee_id;

				$result = M('CaseFee')->where($map)->delete();
				if($result){
					$this->success('删除成功', 'view/case_id/'.$case_id);
				}
			}
			
		} else{
			$case_fee_id = I('get.case_fee_id',0,'int');

			if(!$case_fee_id){
				$this->error('未指明要删除的流水');
			}
			
			$case_fee_list = D('CaseFee')->relation(true)->field(true)->getByCaseFeeId($case_fee_id);			
			$this->assign('case_fee_list',$case_fee_list);
			

			$this->display();
		}
	}
	//搜索
	public function search(){
		
		//取出 Member 表的基本内容，作为 options
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//取出 CostCenter 表的基本内容，作为 options
		$cost_center_list	=	D('CostCenter')->listBasic();
		$this->assign('cost_center_list',$cost_center_list);
				
		//默认查询 0 元 至 20000 元
		$start_amount	=	0;
		$end_amount	=	20000;
		$this->assign('start_amount',$start_amount);
		$this->assign('end_amount',$end_amount);
				
		//取出 Client 表的基本内容，作为 options
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		if(IS_POST){
			
			//接收搜索参数
			$case_feeer_id	=	I('post.case_feeer_id','0','int');
			$cost_center_id	=	I('post.cost_center_id','0','int');				
			$client_id	=	I('post.client_id','0','int');
			$start_amount	=	trim(I('post.start_amount'))*100;
			$end_amount	=	trim(I('post.end_amount'))*100;			
			
			
			//构造 maping
			if($case_feeer_id){
				$map['case_feeer_id']	=	$case_feeer_id;
			}
			if($cost_center_id){
				$map['cost_center_id']	=	$cost_center_id;
			}
			if($client_id){
				$map['client_id']	=	$client_id;
			}	
			
			
			$map_amount['income_amount']	=	array('between',array($start_amount,$end_amount));

			$map_amount['outcome_amount']	=	array('between',array($start_amount,$end_amount));
			$map_amount['_logic'] = 'OR';
			
			
			$p	= I("p",1,"int");
			$page_limit  =   C("RECORDS_PER_PAGE");
			$case_fee_list = D('CaseFee')->where($map)->where($map_amount)->listPage($p,$page_limit);
			$case_fee_count = D('CaseFee')->where($map)->where($map_amount)->count();
			$this->assign('case_fee_list',$case_fee_list['list']);
			$this->assign('case_fee_page',$case_fee_list['page']);
			$this->assign('case_fee_count',$case_fee_count);
		
		} 
	
	$this->display();
	}
	
	//查看主键为 $case_id 的收支流水的所有 case_fee
	public function view(){
		
		//接收对应的 $case_id
		$case_id = I('get.case_id',0,'int');
		if(!$case_id){
			$this->error('未指明要查看的收支流水');
		}
		
		//从 Case 表取出与 $case_id 对应的信息
		$case_list = D('Case')->relation(true)->field(true)->getByCaseId($case_id);			
		$this->assign('case_list',$case_list);
		
		//从 CaseFee 表取出与 $case_id 对应的信息
		$map_case_fee['case_id']	=	$case_id;
		$case_fee_list	=	D('CaseFee')->where($map_case_fee)->listAll();
		$case_fee_count	=	count($case_fee_list);
		$this->assign('case_fee_list',$case_fee_list);
		$this->assign('case_fee_count',$case_fee_count);
		
		//取出 CasePhase 表的内容以及数量
		$case_phase_list	=	D('CasePhase')->listBasic();
		$case_phase_count	=	count($case_phase_list);
		$this->assign('case_phase_list',$case_phase_list);
		$this->assign('case_phase_count',$case_phase_count);
		
		//获取本案子的 $case_type_name
		$case_type_name	=	$case_list['CaseType']['case_type_name'];
		
		//根据 $case_type_name 判断本案是专利、还是非专利
		if(false	!==	strpos($case_type_name,'专利')){
			$is_patent	=	1;
		}else{
			$is_patent	=	0;
		}
		
		//取出 FeeType 表中 与 $case_type_name 对应的内容以及数量
		if($is_patent){
			$map_fee_type['fee_type_name']	=	array('like','%专利%');
		}else{
			$map_fee_type['fee_type_name']	=	array('notlike','%专利%');
		}
		$fee_type_list	=	D('FeeType')->where($map_fee_type)->listBasic();
		$fee_type_count	=	count($fee_type_list);
		$this->assign('fee_type_list',$fee_type_list);
		$this->assign('fee_type_count',$fee_type_count);
		
		//取出 Payer 表的内容以及数量
		$payer_list	=	D('Payer')->listBasic();
		$payer_count	=	count($payer_list);
		$this->assign('payer_list',$payer_list);
		$this->assign('payer_count',$payer_count);
		
		//取出 CostCenter 表的内容以及数量
		$cost_center_list	=	D('CostCenter')->listBasic();
		$cost_center_count	=	count($cost_center_list);
		$this->assign('cost_center_list',$cost_center_list);
		$this->assign('cost_center_count',$cost_center_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();
		$this->assign('row_limit',$row_limit);
        $this->assign('today',$today);

		$this->display();
	}
}