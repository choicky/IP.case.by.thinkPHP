<?php
function yearOption(){
	$current_year	=	intval(date(Y,time()));
	$start_year	=	2006;
	$year_date	=	array();
	for ($current_year;$current_year>$start_year;$current_year--){
		$year_date[]	=	$current_year;
	}
	return($year_date);	
}
