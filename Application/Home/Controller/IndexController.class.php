<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $case_list = D('Case')->relation('CasePriority','Country')->listBasic();
		print_r($case_list);
	
    }
}