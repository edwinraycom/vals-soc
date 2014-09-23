<?php
module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail_messages');

/**
 * Use this function to send ONE email immediately,
 * rather than use the cron mail queue
 * Do not use when the user is waiting for a response in the UI, instead use
 *			  vals_soc_send_emails_cron($items)
 *
 * Example of what an $item is...
 * $item[0] = array('key' => 'vals_soc_invite_new_user', 'to' => $email, 'from' => NULL, 'subject' => $subject, 'body' => $body);
 * when $from is null, the system admin emails address is used
 */
function vals_soc_send_single_email_now($item){
	if (!isset($item['subject'])){ // system messages currently dont have subjects attached at this stage.
		$mail_results='';
		$message = drupal_mail('vals_soc', $item['key'], $item['to'], language_default());
		if (!empty($message['result'])) {
			$mail_results .= $item['message_type'] . ' sent!!.' .'<br/>';
		}
		watchdog('debug', $mail_results);
	}
	else{ // Preferred method for sending out mails based on events.
		$message = drupal_mail('vals_soc', $item['key'], $item['to'], language_default(),
				array('body' => $item['body'],
						'subject' => $item['subject'],
						$item['from'],
						TRUE)
		);
		if (!empty($message['result'])) {
			watchdog('debug', 'Email sent. Result: '.$message['result']);
			return true;
		}else{
			watchdog('debug', 'Problem sending email.');
			return false;
		}
	}
}

/**
 * Use this function to send an array of emails immediately,
 * Do not use when the user is waiting for a response in the UI.
 *
 * Example of what an $items are...
 * $items[0] = array('key' => 'vals_soc_invite_new_user', 'to' => $email, 'from' => NULL, 'subject' => $subject, 'body' => $body);
 * $items[1] = array('key' => 'vals_soc_invite_new_user', 'to' => $email2, 'from' => NULL, 'subject' => $subject, 'body' => $body);
 * etc....
 * when $from is null, the system admin emails address is used
 */
function vals_soc_send_emails_now($items){
	foreach($items as $item) {
		vals_soc_send_single_email_now($item);
	}
}

/**
 * Use this function to send an array of emails on the cron email
 * queue which means they will be sent later.
 * Use this function when the UI has called for an email to sent
 *
 * Example of what an $items are...
 * $items[0] = array('key' => 'vals_soc_invite_new_user', 'to' => $email, 'from' => NULL, 'subject' => $subject, 'body' => $body);
 * $items[1] = array('key' => 'vals_soc_invite_new_user', 'to' => $email2, 'from' => NULL, 'subject' => $subject, 'body' => $body);
 * etc....
 * when $from is null, the system admin emails address is used
 */
function vals_soc_send_emails_cron($items){
	// put them on the queue...
	$queue = DrupalQueue::get('vals_soc_cron_email');
	foreach($items as $item) {
		$queue->createItem($item);
	}
}

/**
 * This is the callback function for all entries posted on to the
 * 'vals_soc_cron_email' queue
 * and any email sent via 'vals_soc_send_emails_cron($items)' above
 * 
 * We don't call this directly ourselves as each email item is
 * dequeued one by one from 'DrupalQueue::get('vals_soc_cron_email');'
 * and this function is then executed.
 */
function vals_soc_cron_email_handler($item){
	// because cron has run this function
	// we can send any email now...
	vals_soc_send_single_email_now($item);
}

/**
 * TODO (Paul) - can we refactor this out
 * Handle mail requests
 * @param unknown $key
 * @param unknown $message
 * @param unknown $params
 */
function vals_soc_mail_handler($key, &$message, $params) {
	//All these messages are saved with rte textareas and so already in html format
	$html_format = true;
	switch($key) {
		case 'vals_soc_notify_new_proposal':
			$message['subject'] = $params['subject'];
			$message['body'][] = $params['body'];
			break;
		case 'vals_soc_invite_new_user':
			$message['subject'] = $params['subject'];
			$message['body'][] = $params['body'];
			break;
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
		//So the headers below are all the same as set by the Drupal mail system except for the 
		//content-type which is html.
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
 * @param $message the parameters for the message: array(headers, body, subject, to, ....)
 */
function vals_soc_mail_alter_handler(&$message) {
	//We now directly apply these header changes for the vals_soc mails only in the function
	//vals_soc_mail_handler.
}

function notify_mentor_and_supervisor_of_proposal_update($record, $is_update){
	// TODO - fix the name missing on one of the emails
	// TODO - load the test setting using the debug flag
	//$test_email = 'your_test_email_address';

	$subject = t('VALS Proposal Notification');

	if(isset($record->supervisor_user_mail) && isset($record->supervisor_user_name)){
		$supervisor_body = get_proposal_email_body($record, $is_update, 'supervisor');
		$items[0] = array('key' => 'vals_soc_notify_new_proposal', 'to' => $record->supervisor_user_mail, 'from' => NULL, 'subject' => $subject, 'body' => $supervisor_body);
		//$items[0] = array('key' => 'vals_soc_notify_new_proposal', 'to' => $test_email, 'from' => NULL, 'subject' => $subject, 'body' => $supervisor_body);
	}else{
		drupal_set_message(t('unable to send supervisor proposal notification email: missing email or username'));
	}

	if(isset($record->mentor_user_mail) && isset($record->mentor_user_name)){
		$mentor_body = get_proposal_email_body($record, $is_update, 'mentor');
		$items[1] = array('key' => 'vals_soc_notify_new_proposal', 'to' => $record->mentor_user_mail, 'from' => NULL, 'subject' => $subject, 'body' => $mentor_body);
		//$items[1] = array('key' => 'vals_soc_notify_new_proposal', 'to' => $test_email, 'from' => NULL, 'subject' => $subject, 'body' => $mentor_body);
	}else{
		drupal_set_message(t('unable to send supervisor proposal notification email: missing email or username'));
	}

	vals_soc_send_emails_cron($items);
}

/******************************************************************************************************
 *  TESTING - used by the admin at present to send the system email messages out to a test email address 
 ******************************************************************************************************/
/**
 * Fire out the system test emails
 * @param $to - user email to send the test mails to
 */
function vals_soc_handle_test_emails($to, $use_cron = TRUE){
	drupal_set_message(t('vals_soc_handle_cron_test_emails called'));
	$items = array();
	$items[0] = array('to' => $to, 'key' => 'vals_soc_email_accepted_organisation');
	$items[1] = array('to' => $to, 'key' => 'vals_soc_email_rejected_organisation');
	$items[2] = array('to' => $to, 'key' => 'vals_soc_email_mentor_welcome');
	$items[3] = array('to' => $to, 'key' => 'vals_soc_email_student_welcome');
	$items[4] = array('to' => $to, 'key' => 'vals_soc_accepted_students');
	$items[5] = array('to' => $to, 'key' => 'vals_soc_rejected_students');
	if($use_cron){
		vals_soc_send_emails_cron($items);
	}
	else{
		vals_soc_send_emails_now($items);
	}
}
