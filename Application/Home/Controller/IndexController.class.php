<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $case_list = D('Case')->listAll();
		var_dump($case_list);
	
    }
}