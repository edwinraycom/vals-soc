<?php
//module_load_include('inc', 'vals_soc', 'includes/vals_soc.helper');

/**
 * Handle mail requests
 * @param unknown $key
 * @param unknown $message
 * @param unknown $params
 */
function vals_soc_mail_handler($key, &$message, $params) {
	//All these messages are saved with rte textareas and so already in html format
	$html_format = true;
	switch($key) {
		case 'vals_soc_email_accepted_organisation':
			$message['subject'] = t('Organisation application for VALS summer of code');
			$message['body'][] = variableGetFromStruct('vals_accepted_organisations_message', 'value');
			if (isset($params['headers']) && is_array($params['headers'])) {
				$message['headers'] += $params['headers'];
			}
			break;

		case 'vals_soc_email_rejected_organisation':
			$message['subject'] = t('Organisation application for VALS summer of code');
			$message['body'][] = variableGetFromStruct('vals_rejected_organisations_message', 'value');
			break;

		case 'vals_soc_email_mentor_welcome':
			$message['subject'] = t('Welcome mentor to the VALS summer of code');
			$message['body'][] = variableGetFromStruct('vals_mentor_welcome_message', 'value');
			break;

		case 'vals_soc_email_student_welcome':
			$message['subject'] = t('Welcome student to the VALS summer of code');
			$message['body'][] = variableGetFromStruct('vals_student_welcome_message', 'value');
			break;

		case 'vals_soc_accepted_students':
			$message['subject'] = t('Your project proposal to the VALS summer of code');
			$message['body'][] = variableGetFromStruct('vals_accepted_students_message', 'value');
			break;

		case 'vals_soc_rejected_students':
			$message['subject'] = t('Your project proposal to the VALS summer of code');
			$message['body'][] = variableGetFromStruct('vals_rejected_students_message', 'value');
			break;
		default:
			$html_format = false;
		break;
	}
	if ($html_format){
		//change header format for html formatted emails (in effect all the vals_soc emails now)
		$message['headers'] = array(
			'MIME-Version' => '1.0',
			'Content-Type' => 'text/html; charset=iso-8859-1; format=flowed',
			'Content-Transfer-Encoding' => '8Bit',
			'X-Mailer' => 'Drupal',
		);
	}
}

/**
 * Add params to a messages header
 * TODO - not quite working yet!
 * @param unknown $message
 */
function vals_soc_mail_alter_handler(&$message) {
	//We now directly apply these header changes for the vals_soc mails only in the function
	//vals_soc_mail_handler.
	
// 	$headers = array(
// 			'MIME-Version' => '1.0',
// 			'Content-Type' => 'text/html; charset=iso-8859-1; format=flowed',
// 			'Content-Transfer-Encoding' => '8Bit',
// 			'X-Mailer' => 'Drupal',
// 		);
// 	$headers2 = $message['headers'];
// 	if (strpos($headers2['Content-Type'], 'plain')){
// 		$headers['Content-Type'] = str_replace('text/html', 'text/plain', $headers['Content-Type']);
// 	}
// 	foreach ($headers as $key => $value) {
// 		$message['headers'][$key] = $value;
// 	}
}

/**
 * 
 * @param $to - user email to send the test mails to
 */
function vals_soc_handle_test_emails($to){
	$mail_results='';
	$message = drupal_mail('vals_soc', 'vals_soc_email_accepted_organisation', $to, language_default());
	if (!empty($message['result'])) {		
		$mail_results .= t('accepted organisation mail sent').'<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_email_rejected_organisation', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= t('rejected organisation mail sent').'<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_email_mentor_welcome', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= t('mentor welcome mail sent').'<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_email_student_welcome', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= t('student welcome mail sent').'<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_accepted_students', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= t('accepted students mail sent').'<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_rejected_students', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= t('rejected students mail sent').'<br/>';
	}
	drupal_set_message($mail_results);
}

/*****************
 *  TESTING
 */

// populate our queue with email recipients - Normally we'd get these from the DB
function vals_soc_handle_cron_test_emails($to){
	drupal_set_message(t('vals_soc_handle_cron_test_emails called'));
	$items = array();
	//$items[0] = array('to' => 'p.sharples@bolton.ac.uk', 'bar' => 'foo',);
	//$items[1] = array('to' => 'ps3comps3com@googlemail.com', 'bar' => 'foo',);

	//$items[0] = array('to' => 'p.sharples@bolton.ac.uk', 'message_type' => 'vals_soc_email_accepted_organisation',);
	///$items[1] = array('to' => $to, 'message_type' => 'vals_soc_email_accepted_organisation',);";

	$items[0] = array('to' => $to, 'message_type' => 'vals_soc_email_accepted_organisation');
	$items[1] = array('to' => $to, 'message_type' => 'vals_soc_email_rejected_organisation');
	$items[2] = array('to' => $to, 'message_type' => 'vals_soc_email_mentor_welcome');
	$items[3] = array('to' => $to, 'message_type' => 'vals_soc_email_student_welcome');
	$items[4] = array('to' => $to, 'message_type' => 'vals_soc_accepted_students');
	$items[5] = array('to' => $to, 'message_type' => 'vals_soc_rejected_students');
	
	
	$queue = DrupalQueue::get('vals_soc_cron_email');
	foreach($items as $item) {
		$queue->createItem($item);
	}
}


// this is the callback function for all entries posted on to the
// 'vals_soc_cron_email' queue
function vals_soc_cron_email_handler($item){
	drupal_set_message('TEST: vals_soc_cron_email_handler called with:'.$item['to']);
	$mail_results='';
	$message = drupal_mail('vals_soc', $item['message_type'], $item['to'], language_default());
	if (!empty($message['result'])) {
		$mail_results .= $item['message_type'] . ' sent!!.' .'<br/>';
	}
	drupal_set_message($mail_results);
}