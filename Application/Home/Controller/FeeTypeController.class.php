<?php
namespace Home\Controller;
use Think\Controller;

class FeeTypeController extends Controller {
    public function index(){
        header("Location: listFeeType");
    }
	
	public function listFeeType(){
		$p	= I("p",1,"int");
		$limit	= 10;		
		$list = D('FeeType')->listFeeType($p,$limit);
		$fee_phase_list	=	D('FeePhase')->listFeePhase();

		//var_dump($list);
		$this->assign('list',$list);
		$this->assign('fee_phase_list',$fee_phase_list);
		$this->display();
	}
	
	public function add(){
		$data	=	array();
		$data['fee_type_name']	=	trim(I('post.fee_type_name'));
		$data['fee_type_name_cpc']	=	trim(I('post.fee_type_name_cpc'));
		$data['fee_type_default_amount']	=	trim(I('post.fee_type_default_amount'));
		$data['fee_phase_id']	=	trim(I('post.fee_phase_id'));
		
		if(!$data['fee_type_name']){
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
			$data['fee_type_name']	=	trim(I('post.fee_type_name'));
			$data['fee_type_name_cpc']	=	trim(I('post.fee_type_name_cpc'));
			$data['fee_type_default_amount']	=	trim(I('post.fee_type_default_amount'));
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

			$fee_type_info = D('FeeType')->relation(true)->getByFeeTypeId($fee_type_id);
			$fee_phase_list	=	D('FeePhase')->listFeePhase();
			//var_dump($fee_phase_list);
			
			$this->assign('fee_type_info',$fee_type_info);
			$this->assign('fee_phase_list',$fee_phase_list);
			$this->display();
		}
	}
}