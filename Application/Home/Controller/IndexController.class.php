<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
       $D   =   D('CaseType');
       $data    =   $D->relation(true)->select();
       var_dump($data);
    }
}