<?php 

/**
 * Returns the module version as defined in the .info file for this module
 * @return unknown
 */
function get_vals_version(){
	$info_data = system_get_info('module', 'vals_soc');
	return $info_data['version'];
}

function altSubValue($arr, $field, $default=''){
	if ($arr && isset($arr[$field])) return $arr[$field];
	else return $default;
}

//It was necessary to remove the syntax error in vals_soc_mail_handler
/*
 * Gets a system variable with variable_get which is expected to be an array with
* the field supplied as a key, if not: a default will be returned
*/

function variableGetFromStruct($var, $field, $default='')
{
	$arr = variable_get($var);
	if ($arr && issset($arr[$field])){
		return $arr[$field];
	} else {
		return $default;
	}
}