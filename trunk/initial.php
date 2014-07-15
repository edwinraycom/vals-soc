<?php
//Copy this file to the root of the Drupal installation
function getInitialPath($path1, $path2){
	$pos = strpos($path1, $path2);
	if (! $pos) {
		$pos = strpos($path1, '/index.php');
		if (!$pos){
			return '';
		} else {
			$arr1 = str_split($path1, $pos);
			return $arr1[0];
		}	
	}
	$arr1 = str_split($path1, $pos);
	return $arr1[0];
} 
$vals = getInitialPath($_SERVER['PHP_SELF'], '/sites/all/modules');
define('_VALS_SOC_URL', $vals);