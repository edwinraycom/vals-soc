<?php
function jsonResult($result, $msg='', $type='', $args=array(), $show_always=FALSE){
	if (!$msg){
		//Get the messages set by drupal_set_messages, but if we pass deliberately null on, we expect no messages
		$msgs = is_null($msg) ? '' : drupal_get_messages($type);
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
			$msg = (_DEBUG && $show_always ? tt(' No %1$s messages available', $type): '');
		}
	}
	$struct = $args;
	if ( ($result === 'error') || ($result === false) || is_null($result)) {
		$struct['result'] = 'error';
		$struct['error'] = $msg;
		
	} else {
		$struct['result'] = $result;
		$struct['msg'] = $msg;
		
	}
	echo json_encode($struct);
}

function jsonBadResult($msg='', $type='error', $args=array(), $show_always=TRUE){
	jsonResult('error', $msg, $type, $args, $show_always);
}

function jsonGoodResult($result=TRUE, $msg='', $type='status', $args=array(), $show_always=FALSE){
	jsonResult($result, $msg, $type, $args, $show_always);
}

function jsonBadResultJT($msg=''){
	$result = array(
		'Result' => 'ERROR',
		'Message' => $msg		
	);
	echo json_encode($result);
}

function jsonGoodResultJT($records, $cnt=-1, $msg=''){
	$result = array(
		'Result' => 'OK',
		'Records' => $records,
		'TotalRecordCount' => (($cnt < 0)? count($records): $cnt),
		'Message' => $msg		
	);
	echo json_encode($result);
}

function isValidOrganisationType($type){
	return in_array($type, array('organisation', 'institute', 'studentgroup', 'project'));
}

function showDrupalMessages($category='status', $echo=FALSE){
	if (empty($category)){
		$s = '';
		$msgs = drupal_get_messages();
		foreach ($msgs as $type => $msgs1){
			$s .= "<br/>$type :<br/>";
			$s.= implode('<br/>', $msgs1);
		}
	} else {
		$msgs = drupal_get_messages($category);
		$s = $msgs[$category] ? "<br/>".implode('<br/>', $msgs[$category]) : '';
	}

	if ($echo) echo $s;
	return $s;
}

function showError($msg='') {
	$msg .= showDrupalMessages('error');
	if ($msg){
		echo errorDiv($msg);//echo "<div class='messages error'>'$msg'</div>";
	}
}

function showSuccess($msg='') {
	$msg .= showDrupalMessages('status');
	if ($msg){
		echo successDiv($msg);//"<div class='messages status'>'$msg'</div>";
	}
}