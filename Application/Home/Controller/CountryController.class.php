<?php
namespace Home\Controller;
use Think\Controller;

class CountryController extends Controller {
    
	//默认跳转到listPage，分页显示
	public function index(){
        header("Location: listCountry");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPage(){
		$p	= I("p",1,"int");
		$limit	= C('RECORDS_PER_PAGE');
		$country_list = D('Country')->field(true)->listPage($p,$limit);

		$this->assign('country_list',$country_list['list']);
		$this->assign('country_page',$country_list['page']);
		$this->assign('country_count',$country_list['count']);		
		
		$this->display();
	}
	
	//新增	
	public function add(){
		$Model	=	D('Country');
		if (!$Model->create()){ 
			
			 // 如果创建数据对象失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());

		}else{
			 
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			
			// 写入新增数据成功，返回案件信息页面
			$this->success('新增成功', 'listPage#addNew');
			
		}else{
			$this->error('增加失败');
		}
	}
	
	//新增
	public function update(){
		//针对 POST 的处理方式
		if(IS_POST){
			$country_id	=	trim(I('post.country_id'));
			
			$Model	=	D('Country');
			if (!$Model->create()){ 
				 
				 // 如果创建数据对象失败 表示验证没有通过 输出错误提示信息
				 $this->error($Model->getError());
				 
			}else{
				 
				 // 验证通过 修改数据
				 $result	=	$Model->save();		 
			}
			if(false !== $result){
				
				// 修改数据成功，返回案件信息页面
				$this->success('修改成功', 'listPage#addNew');
				
			}else{
				$this->error('修改失败');
			}
			
		//针对 GET 的处理方式
		}else{
			$country_id = I('get.country_id',0,'int');

			if(!$country_id){
				$this->error('未指明要编辑的客户');
			}

			$country_list = M('Country')->getByCountryId($country_id);
			
			$this->assign('country_list',$country_list);
			$this->display();
		}
	}
}