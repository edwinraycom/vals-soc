<?php 

/**
 * Returns the module version as defined in the .info file for this module
 * @return unknown
 */
function get_vals_version(){
	$info_data = system_get_info('module', 'vals_soc');
	return $info_data['version'];
}

function tableName($type){
	return "soc_${type}s";
}

function debugDbQuery($query){
	die($query->__toString());
}


function startException($error_msg){
	throw new Exception($error_msg);
}

function altSubValue($arr, $field, $default=''){
	if ($arr && isset($arr[$field])) {
		return $arr[$field];
	} else {
		return $default;
	}
}

function deriveTypeAndAction($derive_type=true){
	$current_path = explode('/', $_SERVER['HTTP_REFERER']);
	$action = array_pop($current_path);
	$derived = array('show_action'=> $action);
	if ($derive_type){
		$type = array_pop($current_path);
		$derived['type'] = 'DEZE WEG';// $type;
	}
	return $derived;

}

function getRequestVar($field, $default='', $request='all'){
	if ($request == 'post'){
		return altSubValue($_POST, $field, $default);
	} elseif ($request == 'get'){
		return altSubValue($_GET, $field, $default);
	} else {
		return (($return = altSubValue($_GET, $field, $default)) && ($return != $default)) ? $return : altSubValue($_POST, $field, $default);
	}
}

//This function was necessary to remove the syntax error in vals_soc_mail_handler
/*
 * Gets a system variable with variable_get which is expected to be an array with
* the field supplied as a key, if not: a default will be returned
*/

function variableGetFromStruct($var, $field, $default='')
{
	$arr = variable_get($var);
	if ($arr && isset($arr[$field])){
		return $arr[$field];
	} else {
		return $default;
	}
}

function src_getJs($src){
	return "<script type='text/javascript' src='$src'></script>";
}

function script_getJs($script){
	return "<script type='text/javascript'>$script</script>";
}

function errorDiv($msg){
	return "<div class='messages error'>$msg</div>";
}

function successDiv($msg){
	return "<div class='messages status'>$msg</div>";
}

function tt($str){
	$args = func_get_args();
	$args[0] = t($str);
	return call_user_func_array('sprintf', $args);
}

function objectToArray($o) {
	$a = array();
	foreach ($o as $k => $v) 
		$a[$k] = (is_array($v) || is_object($v)) ? objectToArray($v): $v; 
	return $a;
}

function simpleObjectToArray($o){
	return (array) $o;
}

function doAssocQuery($q){
	return db_query($q)->fetchAllKeyed();
}

function doQuery2($q){
	return array_map('simpleObjectToArray', db_query($q)->fetchAll());
}

function mapOrganisation($org){
	switch ($org){
		case 'studentgroup': return 'studentgroup';break;
		case 'institute': return 'supervisor';break;
		case 'organisation': return 'mentor';break;
		default: return $org;
	}
}

function renderForm(&$form, $target, $return=false){
	$s = "<div id='msg_$target'></div>";
	// Print $form
	$s .= drupal_render($form);
	$s .= valssoc_form_get_js($form);
	$form['#attached']['js'] = array();//Sometimes it seems Drupal uses the same struct to do a rebuild. We do not
	//want Drupal to interprete the attached js at the moment: the path is relative to the module and D does not 
	//know that
	if ($return) return $s;
	echo $s;
	return true;
}

function valssoc_form_get_js($form){
	if ($form['#attached']['js']){
		$js = '';
		foreach ($form['#attached']['js'] as $incl){
			if ($incl['type'] == 'file'){
				$js .= src_getJs(_VALS_SOC_FULL_URL.$incl['data']);//we assume all paths start with /
			} else {
				$js .= script_getJs($incl['data']);
			}
		}
		return $js;
	} else {
		return '';	
	}
}

function createRandomCode($org, $id=1 ){
	$fixed_prefixes = array(
			'administrator' => 1,
			'soc' => 2,
			'organisation_admin' => 3,
			'institute_admin' => 4,
			'supervisor' => 5,
			'student' => 6,
			//'institute_admin' => 7,
			'organisation' => 7,
			'institute' => 8,
			'studentgroup' => 9,
			'mentor' => 0

	);
	$int = (int) $id.$fixed_prefixes[$org];
	return my_convert($int);
}

/* Taken from the Opendocument project
 * This function may be used under the Creative Commons license: by-nc-sa
* Original written by E. Veenendaal, spring 2006
*/
//I have left out the forbidden words and set the desired length to 9, meaning it has max length 10.
//Note that every code produced is unique as long as the int passed in is unique
//The length adapts itself to the number with a length of 5 we already have 26^5 >= 11,000,000 elements to cover
//Note that if the full length of chars -say 5- is needed to code the int, we need an extra char in front telling
//the first char is not a padding indicator, but an included char. In that case we would have a 6-char string
//starting with an A. As an invariant we have that the 10 positions shifted result of base_convert can never start
//with an 'a' since base_convert returns a string with 1-9,[a-q] (so it never starts with 0).

function my_convert($int, $wishedlen = 9){
	$map = array(
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
			'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
			'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
			'Y', 'Z');
	$map2 = array_merge(array(
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), $map);
	//base_convert uses int chars as well, So the mapping set is 0-9 + a-q, so 15 maps to f and not to p. We correct for
	//that so that we do not have integers in the resulting code
	$start = str_split(strtoupper(base_convert($int, 10, 26)));
	$string = '';
	foreach($start as $c){
		$pos = array_search($c, $map2);
		$string .= $map2[$pos + 10];
	}

	$len = count($start);
	if ($len < $wishedlen){
		$pad = $wishedlen - $len - 1;//Leave one position as pad number indicator
		if ($pad > 0){
			for ($i=1; $i <= $pad;$i++){
				$string = $map[rand(0,25)] . $string;
			}
		}
		$string = $map[max($pad, 0)]. "$string";
	} elseif ($len == $wishedlen){
		$string = "A$string";
	}

	return $string;
}