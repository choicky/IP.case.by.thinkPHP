<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $case_list = D('Case')->listBasic();
		print_r($case_list);
	
    }
}