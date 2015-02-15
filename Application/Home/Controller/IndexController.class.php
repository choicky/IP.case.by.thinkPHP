<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $op	=	D('CaseTypeGroup')->listAllPatent();
		var_dump($op);
    }
}