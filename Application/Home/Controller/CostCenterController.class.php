<?php
namespace Home\Controller;
use Think\Controller;

class CostCenterController extends Controller {
    
	//默认跳转到listCostCenter，显示cost_center表列表
	public function index(){
        header("Location: listCostCenter");
    }
	
	//列表，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listCostCenter(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$cost_center_list = D('CostCenter')->listCostCenter($p,$limit);
		$this->assign('cost_center_list',$cost_center_list['cost_center_list']);
		$this->assign('cost_center_page',$cost_center_list['page']);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['cost_center_name']	=	trim(I('post.cost_center_name'));
		
		if(!$data['cost_center_name']){
			$this->error('未填写费用名称');
		} 

		$result = D('CostCenter')->addCostCenter($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listCostCenter');
		}else{
			$this->error('增加失败');
		}
	}
		
	public function edit(){
		if(IS_POST){
			
			$cost_center_id	=	trim(I('post.cost_center_id'));
			
			$data=array();
			$data['cost_center_name']	=	trim(I('post.cost_center_name'));

			$result = D('CostCenter')->editCostCenter($cost_center_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listCostCenter');
				//header("Location: listCostCenter");
			}else{
				$this->error('修改失败');
			}
		} else{
			$cost_center_id = I('get.id',0,'int');

			if(!$cost_center_id){
				$this->error('未指明要编辑的客户');
			}

			$cost_center_list = D('CostCenter')->getByCostCenterId($cost_center_id);
			
			$this->assign('cost_center_list',$cost_center_list);

			$this->display();
		}
	}
}