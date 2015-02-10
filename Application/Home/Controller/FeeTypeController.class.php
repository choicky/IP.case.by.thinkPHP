<?php
namespace Home\Controller;
use Think\Controller;

class FeeTypeController extends Controller {
    
	//默认跳转带listFreeType显示列表
	public function index(){
        header("Location: listFeeType");
    }
	
	//列表，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listFeeType(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$fee_type_list = D('FeeType')->listFeeType($p,$limit);
		$this->assign('fee_type_list',$fee_type_list['fee_type_list']);
		$this->assign('fee_type_page',$fee_type_list['page']);
		
		$fee_phase_list	=	D('FeePhase')->listFeePhase();
		$this->assign('fee_phase_list',$fee_phase_list);
		
		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['fee_name']	=	trim(I('post.fee_name'));
		$data['fee_name_cpc']	=	trim(I('post.fee_name_cpc'));
		$data['fee_default_amount']	=	trim(I('post.fee_default_amount'));
		$data['fee_phase_id']	=	trim(I('post.fee_phase_id'));
		
		if(!$data['fee_name']){
			$this->error('未填写费用名称');
		} 
				
		$result = D('FeeType')->addFeeType($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listFeeType');
		}else{
			$this->error('增加失败');
		}
	}
		
	public function edit(){
		if(IS_POST){
			
			$fee_type_id	=	trim(I('post.fee_type_id'));
			
			$data=array();
			$data['fee_name']	=	trim(I('post.fee_name'));
			$data['fee_name_cpc']	=	trim(I('post.fee_name_cpc'));
			$data['fee_default_amount']	=	trim(I('post.fee_default_amount'));
			$data['fee_phase_id']	=	trim(I('post.fee_phase_id'));
						
			//var_dump($data);
			$result = D('FeeType')->editFeeType($fee_type_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listFeeType');
				//header("Location: listFeeType");
			}else{
				$this->error('修改失败');
			}
		} else{
			$fee_type_id = I('get.id',0,'int');

			if(!$fee_type_id){
				$this->error('未指明要编辑的客户');
			}

			$fee_type_list = D('FeeType')->relation(true)->getByFeeTypeId($fee_type_id);
			$fee_phase_list	=	D('FeePhase')->listFeePhase();
			//var_dump($fee_phase_list);
			
			$this->assign('fee_type_list',$fee_type_list);
			$this->assign('fee_phase_list',$fee_phase_list);
			$this->display();
		}
	}
}