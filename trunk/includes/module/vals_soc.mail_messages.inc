<?php
/*
 * Put any email message formatting in here rather than vals_soc.mail.inc 
 *
 */

/**
 * Message body for when a new proposal has been added by a student & published
 * @param unknown $record
 * @param unknown $is_update
 * @param unknown $user_type
 * @return string
 */
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

/**
 * This is used to format the email message when inviting new participants
 * @param unknown $entity
 * @param string $subtype
 * @param string $organisation_name
 * @return string
 */
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