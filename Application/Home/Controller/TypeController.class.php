<?php
namespace Home\Controller;		//定义 namespace
use Think\Controller;			//定义继承的 Controller

//自定义 TypeController
class TypeController extends Controller {
    
	//默认跳转到 listAll 
	public function index(){
		header("Location: listAll");
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listAll(){
		
		//通过系统继承的 D 方法实例化 TypeModel并调用其 listAll 方法 ，其中，relaiton('Group') 获取关联表的信息
		$type_data = D('Type')->relation('Group')->listAll();
		
		//将 $type_data 的数据赋给变量名 data ，以便于模板调用
		$this->assign('type_data',$type_data);
		
		//取出 Group 表的内容 以及 数量，
		$group_data	=	D('Group')->listAll();
		$group_count	=	count($group_data);
		$this->assign('group_data',$group_data);
		$this->assign('group_count',$group_count);
		
		//渲染模板，模板名与方法名对应，即，应该是 listAll.html
		$this->display();
	}
	
	//新增
	public function add(){
		//定义空数组
		$data	=	array();
		
		//通过系统继承的 I 方法接收 post 过来的 type_name 值 和 group_id 值
		$data['type_name']	=	I('post.type_name');
		$data['group_id']	=	I('post.group_id');
		
		//检测是否为空值		
		if(!$data['type_name']){
			$this->error('未填写');
		} 
		
		//写入，返回值 $result 为 false 才是写入失败，其他情况都是成功
		$result = D('Type')->add($data);
		
		if(false !== $result){
			
			// success 方法，第一个参数是提示信息，第二个参数是跳转到哪个 url
			$this->success('新增成功', 'listAll');
		}else{
			
			// error 方法，第一个参数是提示信息，第二个参数是跳转到哪个 url，第二个参数为空时，就是返回上一页
			$this->error('增加失败');
		}
	}
	
	//编辑
	public function update(){
		
		//检测是否 post ，此时是接收数据并保存
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 type_id
			$type_id	=	I('post.type_id');
			
			//定义空数组
			$data=array();
			
			//通过 I 方法获取 post 过来的 type_name ，并去掉两端的空格
			$data['type_name']	=	trim(I('post.type_name'));
		
			$data['group_id']	=	I('post.group_id');
			
			//通过 D 方法实例化 TypeModel 并调用其 updateById 方法
			$result = D('Type')->updateById($type_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listAll');
			}else{
				$this->error('修改失败', 'listAll');
			}
		} else{			
			//这是对于 get 方式的
			
			//通过 I 方法获取 get 过来的 id ，并赋给 $type_id
			$type_id	=	I('get.id');
			
			// id 为空时报错
			if(!$type_id){
				$this->error('未指明要编辑的主键');
			}
			
			//通过 D 方法调用 TypeModel 并调用其 returnById 方法，并查到 type_id=$type_id 的值
			$type_data = D('Type')->returnById($type_id);
			
			//将 $data 赋给 data ，以便于模板调用
			$this->assign('type_data',$type_data);
			
			//取出 Group 表的内容 以及 数量
			$group_data	=	D('Group')->listAll();
			$group_count	=	count($group_data);
			$this->assign('group_data',$group_data);
			$this->assign('group_count',$group_count);
			
			//渲染模板，模板名与方法名对应，即，应该是 update.html
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		
		//检测是否 post
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 type_id
			$type_id	=	I('post.type_id');
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');
			
			
			if(1==$no_btn){
				$this->success('取消删除', 'listAll');
			}
			
			if(1==$yes_btn){
				//实例化TypeModel 并调用其 deleteById 方法
				$result = D('Type')->deleteById($type_id);
				if($result){
					$this->success('删除成功', 'listAll');
				}
			
			}
			
		} else{							//这是针对 get 方式的
			
			
			//通过 I 方法获取 get 过来的 id ，并赋给 $type_id
			$type_id	=	I('get.id');
			
			// id 为空时报错
			if(!$type_id){
				$this->error('未指明要删除的主键');
			}
			
			//通过 D 方法调用 TypeModel 并调用其 returnById 方法，得到 type_id=$type_id 的值
			$type_data = D('Type')->returnById($type_id);
			
			//将 $data 赋给 data ，以便于模板调用
			$this->assign('type_data',$type_data);
			
			//渲染模板，模板名与方法名对应，即，应该是 delete.html
			$this->display();
		}
	}
}