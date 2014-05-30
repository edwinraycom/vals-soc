<?php
function jsonResult($result, $type, $show_always=FALSE){
	$msgs = drupal_get_messages($type);
	if ($msgs){
		if ($type){
			$msg = implode('<br/>', $msgs[$type]);
		} else {
			$msg = '';
			foreach ($msgs as $cat => $msg_arr){
				$msg .= "$cat:".implode('<br/>', $msg_arr);
			}
		}
	} else {
		$msg = (_DEBUG && $show_always ? sprintf(t(' No %1$s message available'), $type): '');
	}
	$struct = array();
	if (($result === false) || is_nan($result)|| ($result === 'error')) {
		$struct['error'] = $msg;
		$struct['result'] = 'error';
	} else {
		$struct['msg'] = $msg;
		if ($result == 'html'){
			$struct['result'] = 'html';
			$struct['html'] = $result;
		} else {
			$struct['result'] = '';
			$struct['msg'] .= print_r($result,1);
		}
		
	}
	echo json_encode($struct);
}

function jsonBadResult($result='error', $type='error'){
	jsonResult($result, $type, TRUE);
}

function jsonGooResult($result=TRUE, $type='status'){
	jsonResult($result, $type);
}

function isValidOrganisationType($type){
	return in_array($type, array('organisation', 'institute', 'group'));
}

function showDrupalMessages($category='status', $echo=FALSE){
	if (empty($category)){
		$s = '';
		$msgs = drupal_get_messages();
		foreach ($msgs as $type =>$msgs1){
			$s .= "<br/>$type :<br/>";
			$s.= implode('<br/>', $msgs1);
		}
	} else {
		$msgs = drupal_get_messages($category);
		$s = $msgs[$category] ? "<br/>$category:<br/>".implode('<br/>', $msgs[$category]) : '';
	}

	if ($echo) echo $s;
	return $s;
}

function showError($msg='') {
	$msg .= showDrupalMessages('error');
	if ($msg){
		echo "<div class='messages error'>'$msg'</div>";
	}
}