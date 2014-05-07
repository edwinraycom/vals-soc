<?php 

/**
 * Handle mail requests
 * @param unknown $key
 * @param unknown $message
 * @param unknown $params
 */
function vals_soc_mail_handler($key, &$message, $params) {
	switch($key) {
		case 'vals_soc_email_accepted_organisation':
			$message['subject'] = t('Organisation application for VALS summer of code');
			$message['body'][] = variable_get('vals_accepted_organisations_message')['value'];
			if (isset($params['headers']) && is_array($params['headers'])) {
				$message['headers'] += $params['headers'];
			}
			break;

		case 'vals_soc_email_rejected_organisation':
			$message['subject'] = t('Organisation application for VALS summer of code');
			$message['body'][] = variable_get('vals_rejected_organisations_message')['value'];
			break;

		case 'vals_soc_email_mentor_welcome':
			$message['subject'] = t('Welcome mentor to the VALS summer of code');
			$message['body'][] = variable_get('vals_mentor_welcome_message')['value'];
			break;

		case 'vals_soc_email_student_welcome':
			$message['subject'] = t('Welcome student to the VALS summer of code');
			$message['body'][] = variable_get('vals_student_welcome_message')['value'];
			break;

		case 'vals_soc_accepted_students':
			$message['subject'] = t('Your project proposal to the VALS summer of code');
			$message['body'][] = variable_get('vals_accepted_students_message')['value'];
			break;

		case 'vals_soc_rejected_students':
			$message['subject'] = t('Your project proposal to the VALS summer of code');
			$message['body'][] = variable_get('vals_rejected_students_message')['value'];
			break;
	}
}

/**
 * Add params to a messages header
 * TODO - not quite working yet!
 * @param unknown $message
 */
function vals_soc_mail_alter_handler(&$message) {
	$headers = array(
		'MIME-Version' => '1.0',
		'Content-Type' => 'text/html; charset=iso-8859-1; format=flowed',
		'Content-Transfer-Encoding' => '8Bit',
		'X-Mailer' => 'Drupal',
	);
	foreach ($headers as $key => $value) {
		$message['headers'][$key] = $value;
	}
}

/**
 * 
 * @param $to - user email to send the test mails to
 */
function vals_soc_handle_test_emails($to){
	$mail_results='';
	$message = drupal_mail('vals_soc', 'vals_soc_email_accepted_organisation', $to, language_default());
	if (!empty($message['result'])) {		
		$mail_results .= 'accepted organisation mail sent<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_email_rejected_organisation', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= 'rejected organisation mail sent<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_email_mentor_welcome', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= 'mentor welcome mail sent<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_email_student_welcome', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= 'student welcome mail sent<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_accepted_students', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= 'accepted students mail sent<br/>';
	}
	$message = drupal_mail('vals_soc', 'vals_soc_rejected_students', $to, language_default());
	if (!empty($message['result'])) {
		$mail_results .= 'rejected students mail sent<br/>';
	}
	drupal_set_message($mail_results);
}

/*****************
 *  TESTING
 */

// populate our queue with email recipients - Normally we'd get these from the DB
function vals_soc_cron_email_test_setup(){
	drupal_set_message('vals_soc_cron_email_test_setup called');
	//$items = array("Hello", "World"); // Put everything in a queue for processing.
	$items = array();
	$items[0] = array('to' => 'ps3com40@gmail.com', 'bar' => 'foo',);
	$items[1] = array('to' => 'ps3comps3com@googlemail.com', 'bar' => 'foo',);
	
	$queue = DrupalQueue::get('vals_soc_cron_email');
	foreach($items as $item) {
		$queue->createItem($item);
	}
}


// this is the callback function for all entries posted on to the
// 'vals_soc_cron_email' queue
function vals_soc_cron_email_handler($item){
	drupal_set_message('vals_soc_cron_email_handler called with:'.$item['to']);
	//drupal_mail('vals_soc', 'vals_soc_email_accepted_organisation', $item['to'], language_default());
	$mail_results='';
	$message = drupal_mail('vals_soc', 'vals_soc_email_accepted_organisation', $item['to'], language_default());
	if (!empty($message['result'])) {
		$mail_results .= 'accepted organisation mail sent<br/>';
	}
	drupal_set_message($mail_results);
}