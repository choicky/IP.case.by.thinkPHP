<?php
namespace Home\Model;
use Think\Model;

class FeePhaseModel extends Model {
	
	public function listFeePhase($p,$limit) {
		//$fee_phase_list	=	$this->order(array('convert(fee_phase_name using gb2312)'=>'asc'))->page($p.','.$limit)->select();
		$fee_phase_list	=	$this->page($p.','.$limit)->select();
		
		$fee_phase_count	= $this->count();
		
		$Page	= new \Think\Page($fee_phase_count,$limit);
		$show	= $Page->show();
		
		return array("fee_phase_list"=>$fee_phase_list,"fee_phase_page"=>$show);
	}
}