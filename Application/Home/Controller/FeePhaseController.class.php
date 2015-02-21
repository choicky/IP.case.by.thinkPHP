<?php
namespace Home\Controller;
use Think\Controller;

class FeePhaseController extends Controller {
    
	//默认跳转到pageList，分页显示
	public function index(){
        header("Location: pageList");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function pageList(){
		$p	= I("p",1,"int");
		$limit	= 10;
		$fee_phase_list = D('FeePhase')->pageList($p,$limit);
		$this->assign('fee_phase_list',$fee_phase_list['list']);
		$this->assign('fee_phase_page',$fee_phase_list['page']);

		$this->display();
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['fee_phase_name']	=	trim(I('post.fee_phase_name'));
		
		if(!$data['fee_phase_name']){
			$this->error('未填写费用名称');
		} 

		$result = M('FeePhase')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'pageList');
		}else{
			$this->error('增加失败', 'pageList');
		}
	}
		
	public function edit(){
		if(IS_POST){
			$fee_phase_id	=	trim(I('post.fee_phase_id'));
			
			$data=array();
			$data['fee_phase_name']	=	trim(I('post.fee_phase_name'));

			$result = D('FeePhase')->edit($fee_phase_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'pageList');
			}else{
				$this->error('修改失败', 'pageList');
			}
		} else{
			$fee_phase_id = I('get.id',0,'int');

			if(!$fee_phase_id){
				$this->error('未指明要编辑的客户');
			}

			$fee_phase_list = M('FeePhase')->getByFeePhaseId($fee_phase_id);
			
			$this->assign('fee_phase_list',$fee_phase_list);

			$this->display();
		}
	}
}