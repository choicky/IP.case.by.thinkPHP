<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {

	public function index(){
		$this->show('还没有做好，先到 <a href="./Home/Group/listAll">这里</a> 看看吧 或 <a href="./Home/Account/">这里</a>看看吧。。');
		
		
		$list	=	D('Case')->field(true)->listAllPatent();
		print_r($list);
    }
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listAll(){
		
		//通过系统继承的 D 方法实例化 IndexModel并调用其 listAll 方法
		$data = D('Index')->listAll();
		
		//将 $data 的数据赋给变量名 data ，以便于模板调用
		$this->assign('data',$data);
		
		//渲染模板，模板名与方法名对应，即，应该是 listAll.html
		$this->display();
	}
	
	//新增
	public function add(){
		//定义空数组
		$data	=	array();
		
		//通过系统继承的 I 方法接收 post 过来的 index_content 值
		$data['index_content']	=	I('post.index_content');
		
		//检测是否为空值		
		if(!$data['index_content']){
			$this->error('未填写');
		} 
		
		//写入，返回值 $result 为 false 才是写入失败，其他情况都是成功
		$result = D('Index')->add($data);
		
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
			
			//通过 I 方法获取 post 过来的 index_id
			$index_id	=	I('post.index_id');
			
			//定义空数组
			$data=array();
			
			//通过 I 方法获取 post 过来的 index_content ，并去掉两端的空格
			$data['index_content']	=	trim(I('post.index_content'));
			
			//通过 D 方法实例化 IndexModel 并调用其 update 方法
			$result = D('Index')->update($index_id,$data);
			if(false !== $result){
				$this->success('修改成功', 'listAll');
			}else{
				$this->error('修改失败', 'listAll');
			}
		} else{			
			//这是对于 get 方式的
			
			//通过 I 方法获取 get 过来的 id ，并赋给 $index_id
			$index_id	=	I('get.id');
			
			// id 为空时报错
			if(!$index_id){
				$this->error('未指明要编辑的主键');
			}
			
			//通过 D 方法调用 IndexModel 并调用其 getBy 方法，并查到 index_id=$index_id 的值
			$data = D('Index')->getByIndexId($index_id);
			
			//将 $data 赋给 data ，以便于模板调用
			$this->assign('data',$data);
			
			//渲染模板，模板名与方法名对应，即，应该是 update.html
			$this->display();
		}
	}
}