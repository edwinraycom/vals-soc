<?php
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

function vals_soc_send_email($key, $to, $from, $subject, $body){
	$message = drupal_mail('vals_soc', $key, $to, user_preferred_language($user),
		array('body' => $body,
			'subject' => $subject,
			$from,
			TRUE)
	);
	if (!empty($message['result'])) {
		return true;
	}else{
		return false;
	}
}

/*****************
 *  TESTING
 */

// populate our queue with email recipients - Normally we'd get these from the DB
function vals_soc_handle_cron_test_emails($to){
	drupal_set_message(t('vals_soc_handle_cron_test_emails called'));
	$items = array();
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
	if (!isset($item['subject'])){ // system messages currently dont have subjects attached at this stage.
		$mail_results='';
		$message = drupal_mail('vals_soc', $item['message_type'], $item['to'], language_default());
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

function get_proposal_email_body($record, $is_update, $user_type){
	//echo var_dump($record);
	$server = _VALS_SOC_FULL_URL;
	
	$student_username = $record->name; //student userid
	$student_email = $record->mail; // student mail
	$student_institute = $record->i_name; //Institute name where student is from
	
	$supervisor_username = $record->supervisor_user_name;
	$supervisor_email = $record->supervisor_user_mail;
	
	$mentor_username = $record->mentor_user_name;
	$mentor_email = $record->mentor_user_mail;
	$mentor_organisation = $record->o_name;
	
	$project_title = $record->pr_title; //project title
	$proposal_title = $record->title; //proposal title
	$proposal_synopsis = $record->solution_short;
	$alert='';
	
	if($user_type=='supervisor'){
		$recipient_username = $record->supervisor_user_name;
		if($is_update){
			$alert .= t('An existing proposal has been updated by one of your students.');
		}else{
			$alert .= t('A new proposal has been published by one of your students.');
		}
	}else{
		$recipient_username = $record->mentor_user_name;
		if($is_update){
			$alert .= t('An existing proposal has been updated for one of your projects.');
		}else{
			$alert .= t('A new proposal has been published for one of your projects.');
		}
	}
	$output='';
	$output .= '<p>';
	$output .= t('Hello').' '. $recipient_username . ',';
	$output .= '<br/>';
	$output .= '<br/>';
	$output .= $alert;
	$output .= '<br/>';
	$output .= '<br/>';
	$output .= t('Here is an overview...');
	$output .= '<br/>';
	
	$output .= "<p>";
	$output .= "<b>" . t('Project idea') . "</b>";
	$output .= "<br/>";
	$output .= $project_title;
	$output .= "</p>";
		
	$output .= "<p>";
	$output .= "<b>" . t('Proposal title') . "</b>";
	$output .= "<br/>";
	$output .= $proposal_title;
	$output .= "</p>";
	
	$output .= "<p>";
	$output .= "<b>" . t('Proposal synopsis') . "</b>";
	$output .= "<br/>";
	$output .= $proposal_synopsis;
	$output .= "</p>";
	
	
	$output .= "<b>" . t('Contact details') . "</b>";
	$output .= '<ul class=\'grouplist\'>';

		$output .= "<li>";
		$output .= t('Student username') . ": <i>" . $student_username . "</i>";
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('Student email') . ": <i>" . $student_email . "</i>";
		$output .= "</li>";
		
		$output .= '<br/>';
		
		$output .= "<li>";
		$output .= t('Student supervisors username') . ": <i>" . $supervisor_username . "</i>";
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('Student supervisors email') . ": <i>" . $supervisor_email . "</i>";
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('Students Institution') . ": <i>" . $student_institute . "</i>";
		$output .= "</li>";
		
		$output .= '<br/>';
		
		$output .= "<li>";
		$output .= t('Initial mentors username') . ": <i>" . $mentor_username . "</i>";
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('Initial mentors email') . ": <i>" . $mentor_email . "</i>";
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('Initial mentors Organisation') . ": <i>" . $mentor_organisation . "</i>";
		$output .= "</li>";
	
	$output .= "</ul>";
	
	$output .= "<p>";
	$output .= tt('Go to <a href="%1$s/dashboard" target="_blank">%1$s/dashboard</a> to view more details.', $server);
	$output .= "</p>";
	
	$output .= '<br/>';
	$output .= t('Many Thanks,');
	$output .= '<br/>';
	$output .= t('The VALS team');
	$output .= '<br/>';
	$output .= '<br/>';
	$output .= sprintf('<img src="%1$s/sites/all/themes/media/logo.png">', $server);
	$output .= '<p>';
	return $output;
}

function notify_mentor_and_supervisor_of_proposal_update($record, $is_update){
	$test_email = 'ps3com@hotmail.com';
	
	$subject = t('VALS Proposal Notification');
	
	if(isset($record->supervisor_user_mail) && isset($record->supervisor_user_name)){
		$supervisor_body = get_proposal_email_body($record, $is_update, 'supervisor');
		//$items[0] = array('key' => 'vals_soc_notify_new_proposal', 'to' => $record->supervisor_user_mail, 'from' => NULL, 'subject' => $subject, 'body' => $supervisor_body);
		$items[0] = array('key' => 'vals_soc_notify_new_proposal', 'to' => $test_email, 'from' => NULL, 'subject' => $subject, 'body' => $supervisor_body);
	}else{
		drupal_set_message(t('unable to send supervisor proposal notification email: missing email or username'));
	}
	
	if(isset($record->mentor_user_mail) && isset($record->mentor_user_name)){
		$mentor_body = get_proposal_email_body($record, $is_update, 'mentor');
		//$items[1] = array('key' => 'vals_soc_notify_new_proposal', 'to' => $record->mentor_user_mail, 'from' => NULL, 'subject' => $subject, 'body' => $mentor_body);
		$items[1] = array('key' => 'vals_soc_notify_new_proposal', 'to' => $test_email, 'from' => NULL, 'subject' => $subject, 'body' => $mentor_body);
	}else{
		drupal_set_message(t('unable to send supervisor proposal notification email: missing email or username'));
	}
	
	// put them on the queue....
	$queue = DrupalQueue::get('vals_soc_cron_email');
	foreach($items as $item) {
		$queue->createItem($item);
	}
}

function format_invite_message($entity, $subtype='', $organisation_name=''){
	$output = '';
	
	$entity_name = $organisation_name ? : '<i>'. $entity->name. '</i>';
	$db_code_key = '';
	if($subtype == _MENTOR_TYPE || $subtype == _SUPERVISOR_TYPE || $subtype == _STUDENT_TYPE){
		$db_code_key = 'code';
	}else{
		$db_code_key = 'owner_code';
	}
	$invite_key = $entity->$db_code_key;
	$server = _VALS_SOC_FULL_URL;
	
	$output .= '<p>';
	$output .= t('Hello').',';
	$output .= '<br/>';
	$output .= '<br/>';
	$output .= tt('You have been invited to participate in the VALS Semester of code program, as a %1$s of %2$s.', $subtype, $entity_name);
	$output .= '<br/>';
	$output .= '<br/>';
	$output .= t('The first step is to sign up...'); 
	$output .= '<br/>';
	$output .= '<ol class=\'grouplist\'>';
	
		$output .= "<li>";
		$output .= tt('Go to <a href="%1$s/user/register/%2$s" target="_blank">%1$s/user/register/%2$s</a> to register.', $server, $invite_key);
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('Fill in your basic details.'); 
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('Enter the following unique key if it has not been filled yet: ' . $invite_key);
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('Click "Create New Account"');
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('You will recieve an email with a one-time login link.');
		$output .= "</li>";
		$output .= "<li>";
		$output .= t('Follow that link, log in and set your password.');
		$output .= "</li>";
		
	$output .= "</ol>";
	$output .= '<br/>';
	$output .= t('Many Thanks,');
	$output .= '<br/>';
	$output .= t('The VALS team');
	$output .= '<br/>';
	$output .= '<br/>';
	$output .= sprintf('<img src="%1$s/sites/all/themes/media/logo.png">', $server);
	$output .= '<p>';
	return $output;
}