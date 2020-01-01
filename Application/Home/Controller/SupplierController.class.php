<?php
namespace Home\Controller;
use Think\Controller;

class SupplierController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listAll");
    }
	
	//显示全部
	public function listAll(){
		$supplier_list	=	D('Supplier')->field(true)->listAll();
		$supplier_count	=	count($supplier_list);
		$this->assign('supplier_list',$supplier_list);
		$this->assign('supplier_count',$supplier_count);
		$this->display();
	}
	
	//列表，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		header("Location: listAll");
		/*
		$p	= I("p",1,"int");
		$limit	= C('RECORDS_PER_PAGE');
		$supplier_list = D('Supplier')->field(true)->listPage($p,$limit);
		$this->assign('supplier_list',$supplier_list['list']);
		$this->assign('supplier_page',$supplier_list['page']);
		$this->assign('supplier_count',$supplier_list['count']);

		$this->display();*/
	}
	
	//新增
	public function add(){
		$data	=	array();
		$data['supplier_name']	=	trim(I('post.supplier_name'));
		$data['supplier_email']	=	trim(I('post.supplier_email'));
		$data['supplier_address']	=	trim(I('post.supplier_address'));
		$data['supplier_phone']	=	trim(I('post.supplier_phone'));
		
		if(!$data['supplier_name']){
			$this->error('未填写姓名');
		} 

		$result = M('Supplier')->add($data);
		
		if(false !== $result){
			$this->success('新增成功', 'listAll');
		}else{
			$this->error('增加失败', 'listAll');
		}
	}
		
	public function update(){
		if(IS_POST){
			
			$data=array();
			$data['supplier_id']	=	trim(I('post.supplier_id'));
			$data['supplier_name']	=	trim(I('post.supplier_name'));
			$data['supplier_email']	=	trim(I('post.supplier_email'));
			$data['supplier_phone']	=	trim(I('post.supplier_phone'));
			$data['supplier_qq']	=	trim(I('post.supplier_qq'));

			$result = M('Supplier')->save($data);
			if(false !== $result){
				$this->success('修改成功', 'listAll');
			}else{
				$this->error('修改失败','listAll');
			}
		} else{
			$supplier_id = I('get.supplier_id',0,'int');

			if(!$supplier_id){
				$this->error('未指明要编辑的成员');
			}

			$supplier_list = M('Supplier')->getBySupplierId($supplier_id);
			
			$this->assign('supplier_list',$supplier_list);

			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 supplier_id
			$supplier_id	=	trim(I('post.supplier_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			if(1==$no_btn){
				$this->success('取消删除', 'listAll');
			}
			
			if(1==$yes_btn){
				
				//判断是否有账单信息
				$map_bill['exbill_id']	=	$supplier_id;
				$condition_bill	=	M('Exbill')->where($map_bill)->find();
				if(is_array($condition_bill)){
					$this->error('该供应商已关联过账单，不可删除，只能修改');
				}
				
				$map_supplier['supplier_id']	=	$supplier_id;
				$result = M('Supplier')->where($map_supplier)->delete();
				if($result){
					$this->success('删除成功', 'listAll');
				}
			}
			
		} else{
			$supplier_id = I('get.supplier_id',0,'int');

			if(!$supplier_id){
				$this->error('未指明要删除的客户/申请人');
			}

			$supplier_list = M('Supplier')->field(true)->getBySupplierId($supplier_id);			
			$this->assign('supplier_list',$supplier_list);

			$this->display();
		}
	}
}