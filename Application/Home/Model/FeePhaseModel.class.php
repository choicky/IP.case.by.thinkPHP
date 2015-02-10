<?php
namespace Home\Model;
use Think\Model;

class FeePhaseModel extends Model {
	
	// 获取fee_phase表所有信息
	public function listFeePhase() {
		$fee_phase_list	= $this->select();
		return $fee_phase_list;
	}
}