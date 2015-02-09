<?php
namespace Home\Model;
use Think\Model;

class FeePhaseModel extends Model {

	public function listFeePhase() {
		//排序
		//$list	= $this->order('convert(fee_phase_name using gb2312) asc')->select();
		$list	= $this->select();
		return $list;
	}
}