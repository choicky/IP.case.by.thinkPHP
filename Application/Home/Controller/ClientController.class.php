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
}