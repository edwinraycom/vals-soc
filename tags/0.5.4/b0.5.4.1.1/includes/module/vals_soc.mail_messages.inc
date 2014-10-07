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
			if($record->state != 'published'){
				$alert .= t('An existing proposal has been updated by one of your students.');
			}
			else{
				$alert .= t('An existing proposal has been marked as final version by one of your students.');
			}
		}else{
			if($record->state != 'published'){
				$alert .= t('A new proposal has been made available by one of your students.');
			}
			else{
				$alert .= t('A new proposal has been marked as final version by one of your students.');
			}
		}
	}
	else{
		$recipient_username = $record->mentor_user_name;
		if($is_update){
			if($record->state != 'published'){
				$alert .= t('An existing proposal for one of your projects, has been updated by the student.');
			}
			else{
				$alert .= t('An existing proposal for one of your projects, has been marked as final by the student.');
			}
		}else{
			if($record->state != 'published'){
				$alert .= t('A new proposal for one of your projects, has been made available .');
			}
			else{
				$alert .= t('A new proposal for one of your projects, has been marked as final by the student.');
			}
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
function get_invite_email_body($entity, $subtype='', $organisation_name=''){
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


function get_project_status_update_email_body($record, $is_final, $is_student, $is_successful){
	$output = '';
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
	
	if(!$is_student){ // supervisor 
		$recipient_username = $record->supervisor_user_name;
		if($is_final && !$is_successful){
			$alert .= t('One of your students has previously made a proposal to the project idea below. '.
					'The owner of that project has however decided to mark another students proposal as their FINAL choice. '.
					'This decision is final and so unfortunately your students proposal has been declined. '. 
					'This gives you both advance warning in case you wish to submit another proposal for another project idea before the end of the student signup period. ');
		}
		else if(!$is_final && !$is_successful){
			$alert .= t('One of your students has previously made a proposal to the project idea below. '.
					'The owner of that project has however decided to mark another students proposal as his/her preferred interim one at this time. '.
					'This decision is NOT final however and the owner may in the future decide to change this to another students proposal. '. 
					'This gives your student an opportunity to revise and/or restructure his/her proposal if they wish to do so. '.
					'This also gives you both advance warning in case you wish to submit another proposal for another project idea before the end of the student signup period. '.
					'If the project owners decision does become final, you will be informed by email.');
		}
		else if($is_final && $is_successful){
			$alert .= t('One of your students has previously made a proposal to the project idea below. '.
					'The owner of that project has decided to mark your students proposal as their FINAL choice. '.
					'This decision is final and so your students proposal is the accepted solution for this project idea. ');
		}
		else if(!$is_final && $is_successful){
			$alert .= t('One of your students has previously made a proposal to the project idea below. '.
					'The owner of that project has decided to mark your students proposal as his/her preferred interim one at this time. '.
					'This decision is NOT final however and the owner may in the future decide to change this to another students proposal. '.
					'If the project owners decision does become final or he/she changes their preferred interim proposal, you will be informed by email.');
		}
	}
	else{// student 
		$recipient_username = $record->name;
		if($is_final && !$is_successful){
			$alert .= t('You have previously made a proposal to the project idea below. '.
					'The owner of that project has however decided to mark another students proposal as their FINAL choice. '.
					'This decision is final and so unfortunately your proposal has been declined. '. 
					'This gives you advance warning in case you wish to submit another proposal for another project idea before the end of the student signup period. ');
		}
		else if(!$is_final && !$is_successful){
			$alert .= t('You have previously made a proposal to the project idea below. '.
					'The owner of that project has however decided to mark another students proposal as his/her interim preferred one at this time. '.
					'This decision is NOT final however and the owner may in the future decide to change this to another students proposal. '. 
					'This gives you an opportunity to revise and/or restructure your proposal if you wish to do so. '.
					'This also gives you advance warning in case you wish to submit another proposal for another project idea before the end of the student signup period. '.
					'If the project owners decision does become final, you will be informed by email.');
		}
		else if($is_final && $is_successful){
			$alert .= t('You have previously made a proposal to the project idea below. '.
					'The owner of that project has decided to mark your proposal as their FINAL choice. '.
					'This decision is final and so your proposal is the accepted solution for this project idea. ');
		}
		else if(!$is_final && $is_successful){
			$alert .= t('You have previously made a proposal to the project idea below. '.
					'The owner of that project has decided to mark your proposal as his/her preferred interim one at this time. '.
					'This decision is NOT final however and the owner may in the future decide to change this to another students proposal. '.
					'If the project owners decision does become final or he/she changes their preferred interim proposal, you will be informed by email.');
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
	$text= '';
	if(!$is_student){
		$text='students ';
	}
	$output .= t('For information, here is an overview of your '.$text . 'submitted proposal...');
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
	//

	return $output;
}

function get_new_comment_email_body($entity_details, $properties, $recipient_username){
	$output = '';
	$server = _VALS_SOC_FULL_URL;
	
	$type = $properties['entity_type'];
	$message =  $properties['description'];
	$sender_name = $properties['name'];
	$sender_mail = $properties['mail'];
	
	$title = $entity_details['title']; //entity title
	$owner_name = $entity_details['name'];
	$owner_mail = $entity_details['mail'];
	
	$legend = tt('A new message was posted for the following %1$s', $type);
 	$legend .= '<br/><br/>';
 	$legend .= '<i>'. $title . '</i>';
 	
	$output .= t('Hello').' '. $recipient_username . ',';
	$output .= '<br/>';
	$output .= '<br/>';
	$output .= $legend;
	$output .= '<br/>';
	$output .= '<br/>';
	$output .= $message;
	$output .= '<br/>';
	$output .= '<br/>';
	$output .= t('Message posted by') .' '.  $sender_name .' (' . $sender_mail. ')';
	$output .= '<br/>';
	$output .= tt('Owner of %1$s', $type) .' '.  $owner_name .' (' . $owner_mail. ')';
	$output .= '<br/>';
	$output .= '<br/>';
	
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