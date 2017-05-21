<?php
namespace Home\Controller;
use Think\Controller;

class TestController extends Controller {

	public function index(){
    
    //返回当前时间
    $title = "测试页面";
    $this->assign('title',$title);
      
		$this->display();
  }
  
  public function testV3Dashboard(){
    
    //返回当前时间
    $title = "测试页面";
    $this->assign('title',$title);
      
		$this->display();
  }
  
  public function testV4(){
    
    //返回当前时间
    $title = "测试页面";
    $this->assign('title',$title);
      
		$this->display();
  }
}