<?php 

/**
 * Returns the module version as defined in the .info file for this module
 * @return unknown
 */
function get_vals_version(){
	$info_data = system_get_info('module', 'vals_soc');
	return $info_data['version'];
}