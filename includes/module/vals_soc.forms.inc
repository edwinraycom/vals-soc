<?php 
module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
/*
 * We use this function to populate fields in the form of an entity if it already exists
 */
function populate($obj, $prop, $default=''){
	return ($obj && isset($obj->$prop)) ? $obj->$prop : $default;
}

/**
 * Form to edit the programme email messages
 * @param unknown $form
 * @param unknown $form_state
 */
function vals_soc_admin_messages_form($form, $form_state){
	$accepted_orgs_prev_input = variable_get('vals_accepted_organisations_message',
			array('value'=>'Complete this section', 'format' => 'full_html'));
	$form['vals_accepted_organisations_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Accepted organisations message'),		
		'#default_value' => $accepted_orgs_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	
	$rejected_orgs_prev_input = variable_get('vals_rejected_organisations_message',
			array('value'=>'Complete this section', 'format' => 'full_html'));
	$form['vals_rejected_organisations_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Rejected organisations message'),
		'#default_value' => $rejected_orgs_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	
	$mentor_welcome_prev_input = variable_get('vals_mentor_welcome_message',
			array('value'=>'Complete this section', 'format' => 'full_html'));
	$form['vals_mentor_welcome_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Mentor welcome message'),
		'#default_value' => $mentor_welcome_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	
	$student_welcome_prev_input = variable_get('vals_student_welcome_message',
			array('value'=>'Complete this section', 'format' => 'full_html'));
	$form['vals_student_welcome_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Student welcome message'),
		'#default_value' => $student_welcome_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	
	$accepted_students_prev_input = variable_get('vals_accepted_students_message',
			array('value'=>'Complete this section', 'format' => 'full_html'));
	$form['vals_accepted_students_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Accepted students message'),
		'#default_value' => $accepted_students_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	
	$rejected_students_prev_input = variable_get('vals_rejected_students_message',
			array('value'=>'Complete this section', 'format' => 'full_html'));
	$form['vals_rejected_students_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Rejected students message'),
		'#default_value' => $rejected_students_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	
	$form['vals_messages_test_email'] = array(
		'#type' => 'textfield',
		'#title' => t('Test email address'),
		'#default_value' => '', // dont store this
		'#size' => 60,
		'#maxlength' => 128,
		'#required' => FALSE,
		'#prefix' => '',
		'#suffix' => '<div>' .t('Email address to which test messages must be sent. If provided, a test email is sent for each of the messages on this page to the given address').'.</div>',		
	);
	$form['vals_messages_test_cron_email'] = array(
			'#type' => 'checkbox',
			'#title' => 'cron email test',
			'#tree' => TRUE,
			'#default_value' => 0,
	);
	
	
	$form['#validate'][] = 'vals_soc_admin_messages_form_validate';
	$form['#submit'][] = 'vals_soc_admin_messages_form_submit';
	return system_settings_form($form);
}


/**
 * A validation routine for the admin messages form which checks that if
 * an email address was entered, it is parsed and checked to try to ascertain
 * if it is valid or not.
 * @param unknown $form
 * @param unknown $form_state
 */
function vals_soc_admin_messages_form_validate($form, $form_state) {
	if (!empty($form_state['values']['vals_messages_test_email'])){
		$mail = $form_state['values']['vals_messages_test_email'];
		if (!valid_email_address($mail)) {
			form_set_error('vals_messages_test_email', t('Please Enter a valid email address or leave blank'));
		}
	}
	
}

/**
 * Function to call the test email function on form submission
 * @param unknown $form
 * @param unknown $form_state
 */
function vals_soc_admin_messages_form_submit($form, $form_state) {
	// fire off our test email handler
	if (!empty($form_state['values']['vals_messages_test_email'])){
		drupal_set_message(t('Sending test emails to ').  $form_state['values']['vals_messages_test_email'] ."");
		module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');
		vals_soc_handle_test_emails($form_state['values']['vals_messages_test_email']);
	}
	// A test here to email via queues and cron
	if ($form_state['values']['vals_messages_test_cron_email']==1){
		drupal_set_message('cron email test started');
		module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');
		vals_soc_cron_email_test_setup();
	}
	
}


/**
 * Form to edit the timeline
 * @param unknown $form
 * @param unknown $form_state
 */
function vals_soc_admin_timeline_form($form, &$form_state){
	$form['vals_timeline_program_active'] = array(
			'#type' => 'checkbox',
			'#title' => t('Enable program'),
			'#default_value' => variable_get('vals_timeline_program_active', 0),
	);
	
	$form['vals_timeline_program_start_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Program start date'),
			'#default_value' => variable_get('vals_timeline_program_start_date', ''),
	);
	
	$form['vals_timeline_program_end_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Program end date'),
			'#default_value' => variable_get('vals_timeline_program_end_date', ''),
	);
	
	$form['vals_timeline_org_app_start_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Organisation signup start date'),
			'#default_value' => variable_get('vals_timeline_org_app_start_date', ''),
	);

	$form['vals_timeline_org_app_end_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Organisation signup end date'),
			'#default_value' => variable_get('vals_timeline_org_app_end_date', ''),
	);
	
	$form['vals_timeline_accepted_org_announced_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Accepted organisations announced deadline'),
			'#default_value' => variable_get('vals_timeline_accepted_org_announced_date', ''),
	);
	
	$form['vals_timeline_student_signup_start_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Student signup start date'),
			'#default_value' => variable_get('vals_timeline_student_signup_start_date', ''),
	);
	
	$form['vals_timeline_student_signup_end_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Student signup end date'),
			'#default_value' => variable_get('vals_timeline_student_signup_end_date', ''),
	);

	$form['vals_timeline_org_review_student_applications_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Organisations review student applications deadline'),
			'#default_value' => variable_get('vals_timeline_org_review_student_applications_date', ''),
	);
	
	$form['vals_timeline_students_matched_to_mentors_deadline_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Students matched to mentors deadline'),
			'#default_value' => variable_get('vals_timeline_students_matched_to_mentors_deadline_date', ''),
	);
	
	$form['vals_timeline_accepted_students_announced_deadline_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Accepted students announced deadline'),
			'#default_value' => variable_get('vals_timeline_accepted_students_announced_deadline_date', ''),
	);
/*
	$form['vals_timeline_students_start_submit_forms_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Students start submitting their forms'),
			'#default_value' => variable_get('vals_timeline_students_start_submit_forms_date', ''),
	);
	
	$form['vals_timeline_community_bonding_start_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Community bonding period start date'),
			'#default_value' => variable_get('vals_timeline_community_bonding_start_date', ''),
	);

	$form['vals_timeline_community_bonding_end_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Community bonding period end date'),
			'#default_value' => variable_get('vals_timeline_community_bonding_end_date', ''),
	);
*/	
	$form['vals_timeline_coding_start_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Coding start date'),
			'#default_value' => variable_get('vals_timeline_coding_start_date', ''),
	);

	$form['vals_timeline_coding_end_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Coding end date'),
			'#default_value' => variable_get('vals_timeline_coding_end_date', ''),
	);
	
	$form['vals_timeline_suggested_coding_deadline'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Suggested coding deadline'),
			'#default_value' => variable_get('vals_timeline_suggested_coding_deadline', ''),
	);
	
	return system_settings_form($form);
}


function vals_soc_form_user_profile_form_alter_handler(&$form, &$form_state) {
    global $user;
    $role = getRole();
    
    if (in_array($role, array('supervisor', 'institute_admin', 'student'))){
        $member_inst_rec = db_select('soc_user_membership', 'mem')->fields('mem')->condition('uid', $user->uid)->
            condition('type', 'institute')->execute()->fetchAssoc();
        $member_inst = $member_inst_rec ? $member_inst_rec['oid'] : 0;
        $institutes = db_select('soc_institutes', 'i')->fields('i', array('inst_id', 'name'))->execute()->fetchAll(PDO::FETCH_ASSOC);
        $options_institutes = array(0 => t('Fill in later'));
        foreach ($institutes as $ins){
            $options_institutes[$ins['inst_id']] = $ins['name'];
        }

        $form['account']['institute'] = array(
                "#type" => "select",
                "#title" => t("Select the institute you are in $member_inst"),
                "#options" => $options_institutes,
                "#description" => t("(For students and tutors only)"),
                "#default_value" => $member_inst,
        );
    } elseif (in_array($role, array('organisation_admin', 'mentor'))) {
        $member_org_rec = db_select('soc_user_membership', 'mem')->fields('mem')->condition('uid', $user->uid)->
            condition('type', 'organisation')->execute()->fetchAssoc();
        $member_org = $member_org_rec ? $member_org_rec['oid'] : 0;
        $organisations = db_select('soc_organisations', 'o')->fields('o', array('org_id', 'name'))->execute()->fetchAll(PDO::FETCH_ASSOC);
        $options_organisation = array(0 => t('Fill in later'));
        foreach ($organisations as $org){
            $options_organisation[$org['org_id']] = $org['name'];
        }

        $form['account']['organisation'] = array(
                "#type" => "select",
                "#title" => t("Select the organisation you are in"),
                "#options" => $options_organisation,
                "#description" => t("(For organisation admins and mentors only)"),
                "#default_value" => $member_org,
        );
    }
    
    $form['#submit'][] = 'vals_soc_form_user_profile_form_submit_handler';
    //$form['#validate'][] = 'vals_soc_form_user_register_form_validate_handler';
	return $form;
}

/**
 * Override the register form so we can add custom elements to it
 * @param array $form
 * @param array $form_state
 * @return multitype:string number NULL
 */
function vals_soc_form_user_register_form_alter_handler(&$form, &$form_state) {
    $institutes = db_select('soc_institutes', 'i')->fields('i', array('inst_id', 'name'))->execute()->fetchAll(PDO::FETCH_ASSOC);
    $options_institutes = array(0 => t('Fill in later'));
    foreach ($institutes as $ins){
        $options_institutes[$ins['inst_id']] = $ins['name'];
    }
   
    $form['account']['institute'] = array(
			"#type" => "select",
			"#title" => t("Select the institute you are in"),
			"#options" => $options_institutes,
			"#description" => t("(for students and tutors only)"),
	);
    
    $organisations = db_select('soc_organisations', 'o')->fields('o', array('org_id', 'name'))->execute()->fetchAll(PDO::FETCH_ASSOC);
    $options_organisation = array(0 => t('Fill in later'));
    foreach ($organisations as $org){
        $options_organisation[$org['org_id']] = $org['name'];
    }

    $form['account']['organisation'] = array(
			"#type" => "select",
			"#title" => t("Select the organisation you are in"),
			"#options" => $options_organisation,
			"#description" => t("(for organisation admins and mentors only)"),
	);
    $current_role = getRole();
    if ($current_role !== 'administrator'){
        //If the admin adds users directly we do not want the following fields as these are already 
        //provided in one or another way
        
        $form['account']['account_type'] = array(
                "#type" => "select",
                "#title" => t("What is your role"),
                "#options" => array(
                        'student' => t("Student"),
                        'supervisor' => t("Supervisor"),
                        'mentor' => t("Mentor"),
                        'organisation_admin' => t('Organisation Administrator'),
                        'institute_admin' => t('Institute Admin'),
                ),
                "#description" => t("Select your role in Semester of Code."),
        );

        $form['account']['account_key'] = array(
                "#type" => "textfield",
                "#title" => t("Type the key you got from the admin"),
                "#size" => 10,
                "#description" => t("This code is different per role."),
        );
        
        $default_language = language_default()->language;
        $languages = db_select('languages', 'l')->fields('l', array('language', 'native'))->execute()->fetchAll(PDO::FETCH_ASSOC);
        $options_lang = array();
        foreach ($languages as $lang){
            $options_lang[$lang['language']] = $lang['native'];
        }

        $form['account']['language'] = array(
                "#type" => "select",
                "#title" => t("Select the language you want for Semester of Code"),
                "#options" => $options_lang,
                "#description" => t("This will be the default language"),
                "#default_value" => $default_language,
        );
    }
    
    $form['#submit'][] = 'vals_soc_form_user_register_form_submit_handler';
    $form['#validate'][] = 'vals_soc_form_user_register_form_validate_handler';
	return $form;
}

function vals_soc_organisation_form($form, &$form_state, $org='', $target=''){
    
    $form['name'] = array(
            "#type" => "textfield",
			'#title' => t('What is the name of your organisation?'),
            "#size" => 60,
            '#required' => '1',
             "#default_value" => populate($org, 'name'),
            //"#description" => t("This code is different per role."),
	);
	$form['description'] = array(
            "#type" => "textarea",
			'#title' => t('Describe your organisation, if you like'),
            "#size" => 60,
             "#default_value" => populate($org, 'description'),
            //"#description" => t("This code is different per role."),
	);
    $form['url'] = array(
            "#type" => "textfield",
			'#title' => t('At what website can your organisation be found'),
            "#size" => 150,
            '#required' => '1',
             "#default_value" => populate($org, 'url'),
            //"#description" => t("This code is different per role."),
	);
    $form['contact_name'] = array(
            "#type" => "textfield",
			'#title' => t('Who is the contact person (you?)'),
            "#size" => 100,
            '#required' => '1',
             "#default_value" => populate($org, 'contact_name'),
            //"#description" => t("This code is different per role."),
	);
    $form['contact_email'] = array(
            "#type" => "textfield",
			'#title' => t('What is the email of the contact person'),
            "#size" => 100,
            '#required' => '1',
             "#default_value" => populate($org, 'contact_email'),
            //"#description" => t("This code is different per role."),
	);
	if (!$org){
//          $form['submit'] = array(
//              '#type' => 'submit',
//              '#value' => t('Save'),
//          );
// 		$form['#submit'][] = 'vals_soc_organisation_form_submit_handler';
//     	$form['#validate'][] = 'vals_soc_organisation_form_validate_handler';
		unset($form['#submit']);
		$form['sumbit'] = array(
				'#type' => 'button',
				'#button_type' => 'button',
				'#value' => t('Save'),
				//'#theme' => 'garland_button',
				'#attributes' => array(
						'onclick' => 'ajaxFormCall("vals-soc-organisation-form", "administration", "save",'.
						' {type: "organisation", id:0}, "refreshTabs", "json", ["organisation", "'.$target.'"]);')//'.$target.' instead of refreshTabs
		);
		
     } else {
         unset($form['#submit']);
         $form['sumbit'] = array(
           '#type' => 'button',
           '#button_type' => 'button',
           '#value' => t('Save'),
           //'#theme' => 'garland_button',module, action, data, target, type, err
           '#attributes' => array(
           		'onclick' => 'ajaxFormCall("vals-soc-organisation-form", "administration", "save",'.
           		' {type: "organisation", id:'.$org->org_id.',  target: "'.$target.'" }, "refreshSingleTab", "json", ["'.$target.'"]);')
        );
     }
    
    return $form;
}

function vals_soc_institute_form($form, &$form_state, $institute='', $target=''){
    
     $form['name'] = array(
             "#type" => "textfield",
 			'#title' => t('What is the name of your institute?'),
             "#size" => 60,
             "#default_value" => populate($institute, 'name')
             //"#description" => t("This code is different per role."),
 	);
//  	$form['description'] = array(
//              "#type" => "textarea",
//  			'#title' => t('Describe your institute, if you like'),
//              "#size" => 60,
//              "#default_value" => populate($institute, 'description')
//              //"#description" => t("This code is different per role."),
//  	);
//      $form['url'] = array(
//              "#type" => "textfield",
//  			'#title' => t('At what website can your institute be found').$form['id'],
//              "#size" => 120,
//              "#default_value" => populate($institute, 'url')
//              //"#description" => t("This code is different per role."),
//  	);
     $form['contact_name'] = array(
             "#type" => "textfield",
 			'#title' => t('Who is the contact person (you?)'),
             "#size" => 100,
             "#default_value" => populate($institute, 'contact_name')
             //"#description" => t("This code is different per role."),
 	);
     $form['contact_email'] = array(
             "#type" => "textfield",
 			'#title' => t('What is the email of the contact person'),
             "#size" => 100,
             "#default_value" => populate($institute, 'contact_email')
             //"#description" => t("This code is different per role."),
 	);
     if (!$institute){
     	//          $form['submit'] = array(
     	//              '#type' => 'submit',
     	//              '#value' => t('Save'),
     	//          );
     	// 		$form['#submit'][] = 'vals_soc_organisation_form_submit_handler';
     	//     	$form['#validate'][] = 'vals_soc_organisation_form_validate_handler';
     	unset($form['#submit']);
     	$form['sumbit'] = array(
     			'#type' => 'button',
     			'#button_type' => 'button',
     			'#value' => t('Save'),
     			//'#theme' => 'garland_button',
     			'#attributes' => array(
     					'onclick' => 'ajaxFormCall("vals-soc-institute-form", "administration", "save",'.
     					' {type: "institute", id:0}, "refreshTabs", "json", ["institute", "'.$target.'"]);')//'.$target.' instead of refreshTabs
     	);
     
     } else {
     	unset($form['#submit']);
     	$form['sumbit'] = array(
     			'#type' => 'button',
     			'#button_type' => 'button',
     			'#value' => t('Save'),
     			//'#theme' => 'garland_button',module, action, data, target, type, err
     			'#attributes' => array(
     					'onclick' => 'ajaxFormCall("vals-soc-institute-form", "administration", "save",'.
     					' {type: "institute", id:'.$institute->inst_id.',  target: "'.$target.'" }, "refreshSingleTab", "json", ["'.$target.'"]);')
     	);
     } 
/*      
     
     
     
     if (!$institute){
         $form['submit'] = array(
             '#type' => 'submit',
             '#value' => t('Save'),
         );
         $form['#submit'][] = 'vals_soc_institute_form_submit_handler';
         $form['#validate'][] = 'vals_soc_institute_form_validate_handler';
     } else {
         unset($form['#submit']);
         $form['sumbit'] = array(
           '#type' => 'button',
           '#button_type' => 'button',
           '#value' => t('Save'),
           //'#theme' => 'garland_button',
           '#attributes' => array(
               'onclick' => 'ajaxFormCall("vals-soc-institute-form", "administration", "save", {type: "institute", id: '.
                  $institute->inst_id.' }, "'.$target.'", "json");')
         );
     } */
    return $form;
}

function vals_soc_group_form($form, &$form_state, $group='', $target=''){ 
    $form['name'] = array(
            "#type" => "textfield",
			'#title' => t('Give a name to remember your student group'),
            "#size" => 60,
            '#required' => '1',
            "#default_value" => populate($group, 'name')
            //"#description" => t("This code is different per role."),
	);
	$form['description'] = array(
            "#type" => "textarea",
			'#title' => t('Some additional note to describe the group'),
            "#size" => 1024,
            "#default_value" => populate($group, 'description')
            //"#description" => t("This code is different per role."),
	);
  	
	//If we never need a stand alone form and only use ajax, remove the commented code 
	//and get common code outside
	if (!$group){
		unset($form['#submit']);
		$form['sumbit'] = array(
				'#type' => 'button',
				'#button_type' => 'button',
				'#value' => t('Save'),
				//'#theme' => 'garland_button',
				'#attributes' => array(
					'onclick' => 'ajaxFormCall("vals-soc-group-form", "administration", "save",'.
						' {type: "group", id:0}, "refreshTabs", "json", ["group", "'.$target.'"]);')//'.$target.' instead of refreshTabs
		);
//          $form['submit'] = array(function ajaxFormCall(frm, category, action, data, target, type, err) {
//              '#type' => 'submit',
//              '#value' => 'Save',
//          );
//          $form['#submit'][] = 'vals_soc_group_form_submit_handler';
//          $form['#validate'][] = 'vals_soc_group_form_validate_handler';
     } else {
         unset($form['#submit']);
         $form['sumbit'] = array(
         		'#type' => 'button',
         		'#button_type' => 'button',
         		'#value' => t('Save'),
         		//'#theme' => 'garland_button',
         		'#attributes' => array(
         				'onclick' => 'ajaxFormCall("vals-soc-group-form", "administration", "save",'.
         				' {type: "group", id:'.$group->group_id.',  target: "'.$target.'" }, "refreshSingleTab", "json", ["'.$target.'"]);')
         );
     }

    return $form;
}


function vals_soc_proposal_form($form, &$form_state, $application='', $target='', $project_id=''){
	$project_id = $application ? $application->pid : $project_id;
	if (! $project_id){
		echo showError(t('The project id has not been passed on well. Cannot continue. Please inform the system administrator'));
		return;
	}
	$form['name'] = array(
			"#type" => "textfield",
			'#title' => t('Give your full name'),
			"#size" => 60,
			'#required' => '1',
			"#default_value" => populate($application, 'name'),
			"#description" => t("For easy communication, it is nice to have your name next to your email address"),
	);
	$form['solution_short'] = array(
			"#type" => "textarea",
			'#title' => t('Give a summary of your solution'),
			"#size" => 1024,
			"#default_value" => populate($application, 'solution_short')
			//"#description" => t("This code is different per role."),
	);
	$form['solution_long'] = array(
			"#type" => "textarea",
			'#title' => t('Give a more detailed description of your solution'),
			"#size" => 1024,
			"#default_value" => populate($application, 'solution_long')
			//"#description" => t("This code is different per role."),
	);
	$form['modules'] = array(
			"#type" => "textarea",
			'#title' => t('Which extra modules and libraries will you use, if any?'),
			"#size" => 1024,
			"#default_value" => populate($application, 'modules'),
			"#description" => t("Write the name of the library, it's url and its purpose, divided by bars ('|')"),
	);
	$form['cv'] = array(
			"#type" => "textarea",
			'#title' => t('Describe your Curriculum Vitae'),
			"#size" => 1024,
			"#default_value" => populate($application, 'description'),
			"#description" => t("You might want to discuss with your supervisor which courses you should put on your cv."),
	);	 
	//If we never need a stand alone form and only use ajax, remove the commented code
	//and get common code outside
	if (!$application){
		unset($form['#submit']);
		$form['save'] = array(
				'#type' => 'button',
				'#button_type' => 'button',
				'#value' => t('Save draft'),
				//'#theme' => 'garland_button',
				'#attributes' => array(
						'onclick' => 'ajaxFormCall("vals-soc-proposal-form", "student", "save",'.
						' {project_id: '.$project_id.', id:0}, "handleResult", "json", ["'.$target.'", "student_details"]);')//'.$target.' instead of refreshTabs
		);
		$form['sumbit'] = array(
				'#type' => 'button',
				'#button_type' => 'button',
				'#value' => t('Submit proposal'),
				//'#theme' => 'garland_button',
				'#attributes' => array(
						'onclick' => 'if (confirm("'.t('Are you sure you want to submit your proposal now?').'")){'.
						'ajaxFormCall("vals-soc-proposal-form", "student", "save",'.
						' {project_id: '.$project_id.', id:0}, "refreshTabs", "json", ["group", "'.$target.'"]);}')//'.$target.' instead of refreshTabs
		);
		$form['cancel'] = array(
				'#type' => 'button',
				'#button_type' => 'button',
				'#value' => t('Cancel'),
				//'#theme' => 'garland_button',
				'#attributes' => array(
						'onclick' => 'ajaxCall("project", "project_page", null, "content", "html", ["'.$target.'"]);')
		);
	} else {
		unset($form['#submit']);
		$form['save'] = array(
				'#type' => 'button',
				'#button_type' => 'button',
				'#value' => t('Save draft'),
				//'#theme' => 'garland_button',
				'#attributes' => array(
						'onclick' => 'ajaxFormCall("vals-soc-proposal-form", "student", "save",'.
						' {id:'.$application->propid.',  target: "'.$target.'" }, "refreshSingleTab", "json", ["'.$target.'"]);')
		);
		$form['sumbit'] = array(
				'#type' => 'button',
				'#button_type' => 'button',
				'#value' => t('Submit proposal'),
				//'#theme' => 'garland_button',
				'#attributes' => array(
						'onclick' => 'ajaxFormCall("vals-soc-proposal-form", "student", "save",'.
						' {id:'.$application->propid.',  target: "'.$target.'" }, "refreshSingleTab", "json", ["'.$target.'"]);')
		);
	}
	return $form;
}