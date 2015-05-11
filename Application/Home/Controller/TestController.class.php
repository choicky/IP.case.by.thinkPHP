<?php
namespace Home\Controller;
use Think\Controller;

class TestController extends Controller {

	public function index(){
		$case_list_tmp	=	M('Case')->field('case_id,our_ref')->select();
		
		for($j=0;$j<count($case_list_tmp);$j++){
			$case_id	=	$case_list_tmp[$j]['case_id'];
			$our_ref	=	$case_list_tmp[$j]['our_ref'];
			
			$map_case_fee['case_id']	=	$case_id;
			$map_case_fee['case_payment_id']	=	array('GT',2);
			$case_fee_list	=	M('CaseFee')->where($map_case_fee)->count();
			
			if($case_fee_list>2){
				$this->show('<a href="'.U('Case/view','case_id='.$case_id).'">'.$our_ref.'</a><br>');
			}
			
		}
	
    }
	
}