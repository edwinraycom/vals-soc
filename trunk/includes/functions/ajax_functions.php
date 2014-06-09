<?php
function jsonResult($result, $msg='', $type='', $show_always=FALSE){
	if (!$msg){
		//Get the messages set by drupal_set_messages
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
			$msg = (_DEBUG && $show_always ? tt(' No %1$s messages available', $type): '');
		}
	}
	$struct = array();
	if ( ($result === 'error') || ($result === false) || is_null($result)) {
		$struct['result'] = 'error';
		$struct['error'] = $msg;
		
	} else {
		
		if ($result == 'html'){
			$struct['result'] = 'html';
			$struct['html'] = $result;
		} else {
			$struct['result'] = $result;
		}
		$struct['msg'] = $msg;
		
	}
	echo json_encode($struct);
}

function jsonBadResult($msg='', $type='error', $show_always=TRUE){
	jsonResult('error', $msg, $type, $show_always);
}

function jsonGoodResult($result=TRUE, $msg='', $type='status', $show_always=FALSE){
	jsonResult($result, $msg, $type, $show_always);
}

function isValidOrganisationType($type){
	return in_array($type, array('organisation', 'institute', 'studentgroup'));
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