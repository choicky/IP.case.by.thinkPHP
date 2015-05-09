<?php
namespace Home\Controller;
use Think\Controller;

class CostCenterController extends Controller {
    
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
		$limit	= 10;
		$cost_center_list = D('CostCenter')->listPage($p,$limit);
		$this->assign('cost_center_list',$cost_center_list['list']);
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

		$result = M('CostCenter')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listPage');
		}else{
			$this->error('增加失败');
		}
	}
		
	public function update(){
		if(IS_POST){
			
			$cost_center_id	=	trim(I('post.cost_center_id'));
			
			$data=array();
			$data['cost_center_name']	=	trim(I('post.cost_center_name'));

			$result = D('CostCenter')->update($cost_center_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listPage');
			}else{
				$this->error('修改失败', 'listPage');
			}
		} else{
			$cost_center_id = I('get.cost_center_id',0,'int');

			if(!$cost_center_id){
				$this->error('未指明要编辑的客户');
			}

			$cost_center_list = M('CostCenter')->getByCostCenterId($cost_center_id);
			
			$this->assign('cost_center_list',$cost_center_list);

			$this->display();
		}
	}
	
	//显示所有账户列表，并显示账户余额
	public function listBalance(){
		$cost_center_list = D('CostCenter')->listAll();
		
		
		for($j=0;$j<count($cost_center_list);$j++){
			//获取每一条记录的 cost_center_id
			$cost_center_id	=	$cost_center_list[$j]['cost_center_id'];
			
			//构造 mapping
			$map_cost_center_balance['cost_center_id']	=	$cost_center_id;
			$map_cost_center_balance['end_date']	=	array('LT',time());
			
			//获取数据
			$cost_center_balance_list	=	M('CostCenterBalance')->where($map_cost_center_balance)->field('sum(balance_amount) as balance_amount')->select();			
			
			$cost_center_list[$j]['balance_amount']	=	$cost_center_balance_list[0]['balance_amount'];
		}
		
		$this->assign('cost_center_list',$cost_center_list);
		$this->assign('cost_center_count',count($cost_center_list));

		$this->display();
	}
}