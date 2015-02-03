<?php
namespace Home\Controller;
use Think\Controller;

class ClientController extends Controller {
    public function index(){
        $this->display();
    }
	
	public function listClient(){
		$client = D('Client');
		$clientList = $client->relation(true)->select();
		//var_dump($clientList);
		$this->assign('clientList',$clientList);
		$this->display();
	}
	
	public function editClient(){
		$arr=I('post.client_name');
		var_dump ($arr);
	}
	
	public function addClient(){
		$arr=I('post.client_name');
		var_dump ($arr);
	}
}