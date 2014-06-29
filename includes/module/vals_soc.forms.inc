<?php 
/*
 * We use this function to populate fields in the form of an entity if it already exists
 */
function populate($obj, $prop, $default=''){
	return ($obj && isset($obj->$prop)) ? $obj->$prop : $default;
}

function entityCall($type, $obj, $target){
	$key = AbstractEntity::keyField($type);
	$id = $obj && $obj->$key ? $obj->$key : 0;
	$call = "ajaxFormCall('vals-soc-$type-form', 'administration', 'save', {type: '$type',id: $id},".
		"'refreshTabs','json',['$type', '$target']);";
	return $call;
}

/**
 * Form to edit the programme email messages
 * @param unknown $form
 * @param unknown $form_state
 */
function vals_soc_admin_messages_form($form, $form_state){
	$complete_this_section = t('Complete this section');
	$accepted_orgs_prev_input = variable_get('vals_accepted_organisations_message',
		array('value'=>$complete_this_section, 'format' => 'full_html'));
	$form['vals_accepted_organisations_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Accepted organisations message'),		
		'#default_value' => $accepted_orgs_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	$rejected_orgs_prev_input = variable_get('vals_rejected_organisations_message',
		array('value'=>$complete_this_section, 'format' => 'full_html'));
	$form['vals_rejected_organisations_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Rejected organisations message'),
		'#default_value' => $rejected_orgs_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	$mentor_welcome_prev_input = variable_get('vals_mentor_welcome_message',
		array('value'=>$complete_this_section, 'format' => 'full_html'));
	$form['vals_mentor_welcome_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Mentor welcome message'),
		'#default_value' => $mentor_welcome_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	$student_welcome_prev_input = variable_get('vals_student_welcome_message',
			array('value'=>$complete_this_section, 'format' => 'full_html'));
	$form['vals_student_welcome_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Student welcome message'),
		'#default_value' => $student_welcome_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	$accepted_students_prev_input = variable_get('vals_accepted_students_message',
			array('value'=>$complete_this_section, 'format' => 'full_html'));
	$form['vals_accepted_students_message'] = array(
		'#type' => 'text_format',
		'#base_type' => 'textarea',
		'#title' => t('Accepted students message'),
		'#default_value' => $accepted_students_prev_input['value'],
		'#suffix' => '<p></p>',
	);
	$rejected_students_prev_input = variable_get('vals_rejected_students_message',
		array('value'=>$complete_this_section, 'format' => 'full_html'));
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
		'#suffix' => '<div>' .t('Email address to which test messages must be sent. If provided, a '.
			'test email is sent for each of the messages on this page to the given address').'.</div>',		
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
		drupal_set_message(t('cron email test started'));
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
	/*$form['vals_timeline_students_start_submit_forms_date'] = array(
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
	);*/
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
    //find out whether user is admin, if so: get role of the user he/she is editing
    if ($is_admin = ($role  === 'administrator')){
    	$roles = $form['#user']->roles;
    	$role = getUserRoleName($roles);
    	$uid = $form['#user']->uid;
    } else {
    	$uid = $user->uid;
    }
   
    $name_rec = db_select('soc_names', 'name')->fields('name')->condition('names_uid', $uid)->
    	condition('type', $role)->execute()->fetchAssoc();
    $name = $name_rec ? $name_rec['name'] : $form['#user']->name;
    $form['account']['fullname'] = array(
    		"#type" => "textfield",
    		"#title" => t('You can correct your name'),
    		//"#options" => $options_institutes,
    		"#description" => t("(If you leave this empty, we will use your account name instead)"),
    		"#default_value" => $name,
    );
    if (in_array($role, array('supervisor', 'institute_admin', 'student'))){
        $member_inst_rec = db_select('soc_user_membership', 'mem')->fields('mem')->condition('uid', $uid)->
            condition('type', 'institute')->execute()->fetchAssoc();
        $member_inst = $member_inst_rec ? $member_inst_rec['group_id'] : 0;
       
        if (! $member_inst  || $is_admin){
	        //So we know the user is not in an institute yet
	        $institutes = db_select('soc_institutes', 'i')->fields('i', array('inst_id', 'name'))->execute()->fetchAll(PDO::FETCH_ASSOC);
	        $options_institutes = array(0 => t('Fill in later'));
	        foreach ($institutes as $ins){
	            $options_institutes[$ins['inst_id']] = $ins['name'];
	        }
	       
	        $form['account']['institute'] = array(
	                "#type" => "select",
	                "#title" => t("Select the institute you are in"),
	                "#options" => $options_institutes,
	                "#description" => t("(For students and supervisors only)"),
	                "#default_value" => $member_inst,
	        );
	        if (!$is_admin){
		        $form['account']['account_key'] = array(
		        		"#type" => "textfield",
		        		"#title" => t("Type the key you got from your colleague in the institute (or create one)"),
		        		"#size" => 10,
		        		"#description" => t("The code is for this institute only."),
		        );
	        }
        }
    } elseif (in_array($role, array('organisation_admin', 'mentor'))) {
        $member_org_rec = db_select('soc_user_membership', 'mem')->fields('mem')->condition('uid', $uid)->
            condition('type', 'organisation')->execute()->fetchAssoc();
        $member_org = $member_org_rec ? $member_org_rec['group_id'] : 0;
    	if (! $member_org  || $is_admin){
	        //So we know the user is not in an organisation yet
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
	        if (!$is_admin){
		        $form['account']['account_key'] = array(
		        		"#type" => "textfield",
		        		"#title" => t("Type the key you got from your colleague in the organisation (or create one)"),
		        		"#size" => 10,
		        		"#description" => t("The code is for this organisation only."),
		        );
	        }
    	}
    }

    $form['#submit'][] = 'vals_soc_form_user_profile_form_submit_handler';
    $form['#validate'][] = 'vals_soc_form_user_profile_form_validate_handler';
	return $form;
}

/**
 * Override the register form so we can add custom elements to it
 * @param array $form
 * @param array $form_state
 * @return multitype:string number NULL
 */
function vals_soc_form_user_register_form_alter_handler(&$form, &$form_state) {
	$form['account']['fullname'] = array(
			"#type" => "textfield",
			"#title" => t("What is your full name?"),
			//"#options" => $options_institutes,
			"#description" => t("(If you leave this empty, we will use your account name instead)"),
			//"#default_value" => '',
	);
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
	$form = array(
		'#prefix' => '<div id="vals_soc_entity_form_wrapper">',
		'#suffix' => '</div>',
	);
// 	$form['key'] = array(
// 		'#type' => 'hidden', 
// 		"#default_value" => populate($org, 'org_id'),
// 	);
// 	$form['tab_target'] = array(
// 		'#type' => 'hidden',
// 		"#default_value" => $target,
// 	);
// 	$form['entity_type'] = array(
// 		'#type' => 'hidden',
// 		"#default_value" => 'organisation',
// 	); 
	/*Still experimenting with using paths -possible will be removed soon.
	$form['tRequest_path'] = array(
		'#type' => 'hidden',
		'#default_value' => request_path(),
	);*/
	$form['name'] = array(
		"#type" => "textfield",
		'#title' => t('What is the name of your organisation?'),
		"#size" => 60,
		'#required' => '1',
		"#default_value" => populate($org, 'name'),
	);
	$form['description'] = array(
		"#type" => "textarea",
		'#title' => t('Describe your organisation, if you like'),
		"#size" => 60,
		//"#size" => 20,
		//'#cols' =>5,
		//'#rows' =>4,
		"#default_value" => populate($org, 'description'),
	);
	$form['url'] = array(
		"#type" => "textfield",
		'#title' => t('At what website can your organisation be found'),
		"#size" => 100,
		'#required' => '1',
		"#default_value" => populate($org, 'url'),
	);
	$form['contact_name'] = array(
		"#type" => "textfield",
		'#title' => t('Who is the contact person (you?)'),
		"#size" => 100,
		'#required' => '1',
		"#default_value" => populate($org, 'contact_name'),
	);
	$form['contact_email'] = array(
		"#type" => "textfield",
		'#title' => t('What is the email of the contact person'),
		"#size" => 100,
		'#required' => '1',
		"#default_value" => populate($org, 'contact_email'),
	);
	$form['submit'] = array(
			'#type' => 'submit',
			'#attributes' => array('onclick'=> entityCall('organisation', $org, $target)),
			'#value' => t('Save'),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
// 	$form['submit'] = array(
// 		'#type' => 'submit',
// 		'#value' => t('Save'),
// 		'#ajax' => array(
// 			'callback' => 'vals_soc_entity_form_callback',
// 			'wrapper' => 'vals_soc_entity_form_wrapper',
// 		),
// 		'#post_render' => array('vals_soc_fix_submit_button'),
// 	);
	$form['cancel'] = array(
		'#type' => 'button',
		'#value' => t('Cancel'),
		'#prefix' => '&nbsp; &nbsp; &nbsp;',
		'#attributes' => array('onClick' => 'location.reload(); return true;'),
		'#post_render' => array('vals_soc_fix_submit_button'),
	);
// 	$form['#attached']['js'] = array(
// 		array(
// 			'type'  => 'file',
// 			'data' => drupal_get_path('module', 'vals_soc') .'/includes/js/drupal_ajax_functions.js',
// 		),
// 	);
	return $form;
}

function vals_soc_institute_form($form, &$form_state, $institute='', $target=''){
	$form = array(
		'#prefix' => '<div id="vals_soc_entity_form_wrapper">',
		'#suffix' => '</div>',
	);
	$form['name'] = array(
		"#type" => "textfield",
		'#title' => t('What is the name of your institute?'),
		"#size" => 60,
		"#default_value" => populate($institute, 'name'),
		'#required' => '1'
 	);
// 	$form['key'] = array(
// 		'#type' => 'hidden',
// 		"#default_value" => populate($institute, 'inst_id'),
// 	);
// 	$form['tab_target'] = array(
// 		'#type' => 'hidden',
// 		"#default_value" => $target,
// 	);
// 	$form['entity_type'] = array(
// 		'#type' => 'hidden',
// 		"#default_value" => 'institute',
// 	);
	$form['contact_name'] = array(
		"#type" => "textfield",
		'#title' => t('Who is the contact person (you?)'),
		"#size" => 100,
		"#default_value" => populate($institute, 'contact_name'),
		'#required' => '1'
 	);
	$form['contact_email'] = array(
		"#type" => "textfield",
		'#title' => t('What is the email of the contact person'),
		"#size" => 100,
		"#default_value" => populate($institute, 'contact_email'),
		'#required' => '1'
	);

	$form['submit'] = array(
		'#type' => 'submit',
		'#attributes' => array('onclick'=> entityCall('institute', $institute, $target)),
		'#value' => t('Save'),
		'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$form['cancel'] = array(
		'#type' => 'button',
		'#value' => t('Cancel'),
		'#prefix' => '&nbsp; &nbsp; &nbsp;',
		'#attributes' => array('onClick' => 'location.reload(); return true;'),
		'#post_render' => array('vals_soc_fix_submit_button'),
	);
// 	$form['#attached']['js'] = array(
// 		array(
// 			'type'  => 'file',
// 			'data' => drupal_get_path('module', 'vals_soc') .'/includes/js/drupal_ajax_functions.js',
// 		),
// 	);
	return $form;
}

function vals_soc_studentgroup_form($form, &$form_state, $group='', $target=''){
	$form = array(
		'#prefix' => '<div id="vals_soc_entity_form_wrapper">',
		'#suffix' => '</div>',
	);
	$form['key'] = array(
		'#type' => 'hidden',
		"#default_value" => populate($group, 'studentgroup_id'),
	);
	$form['tab_target'] = array(
		'#type' => 'hidden',
		"#default_value" => $target,
	);
	$form['entity_type'] = array(
		'#type' => 'hidden',
		"#default_value" => 'studentgroup',
	);
	$form['name'] = array(
		"#type" => "textfield",
		'#title' => t('Give a name to remember your student group'),
		"#size" => 60,
		'#required' => '1',
		"#default_value" => populate($group, 'name')
	);
	$form['description'] = array(
		"#type" => "textarea",
		'#title' => t('Some additional note to describe the group'),
		"#size" => 1024,
		"#default_value" => populate($group, 'description')
	);
// 	$form['submit'] = array(
// 		'#type' => 'submit',
// 		'#value' => t('Save'),
// 		'#ajax' => array(
// 			'callback' => 'vals_soc_entity_form_callback',
// 			'wrapper' => 'vals_soc_entity_form_wrapper',
// 		),
// 		'#post_render' => array('vals_soc_fix_submit_button'),
// 	);
	$form['submit'] = array(
			'#type' => 'submit',
			'#attributes' => array('onclick'=> entityCall('studentgroup', $group, $target)),
			'#value' => t('Save'),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$form['cancel'] = array(
		'#type' => 'button',
		'#value' => t('Cancel'),
		'#prefix' => '&nbsp; &nbsp; &nbsp;',
		'#attributes' => array('onClick' => 'location.reload(); return true;'),
		'#post_render' => array('vals_soc_fix_submit_button'),
	);
// 	$form['#attached']['js'] = array(
// 		array(
// 			'type'  => 'file',
// 			'data' => drupal_get_path('module', 'vals_soc') .'/includes/js/drupal_ajax_functions.js',
// 		),
// 	);
	return $form;
}

function vals_soc_project_form($form, &$form_state, $project='', $target=''){
	$form = array(
			'#prefix' => '<div id="vals_soc_entity_form_wrapper">',
			'#suffix' => '</div>',
	);
	
	$form['key'] = array(
			'#type' => 'hidden',
			"#default_value" => populate($project, 'pid'),
	);
	$form['tab_target'] = array(
			'#type' => 'hidden',
			"#default_value" => $target,
	);
	$form['entity_type'] = array(
			'#type' => 'hidden',
			"#default_value" => 'project',
	);
	
	$my_organisations = Groups::getGroups('organisation');
	$nr = $my_organisations->rowCount();
	if ($nr < 2){
		$form['org_id'] = array(
				'#type' => 'hidden',
				"#default_value" => ($nr ? $my_organisations->fetchCol(): 0),
		);
	} else {
		$form['org_id'] = array(
			"#type" => "select",
			'#title' => t('For which organisation will this project be?'),
			"#options" => $my_organisations->fetchAll(),
			'#required' => '1',
			"#default_value" => populate($project, 'org_id')
		);
	}

	
	$form['title'] = array(
			"#type" => "textfield",
			'#title' => t('Give a name to the project'),
			"#size" => 60,
			'#required' => '1',
			"#default_value" => populate($project, 'title')
	);
	$form['description'] = array(
			"#type" => "textarea",
			'#title' => t('Describe the project'),
			//"#size" => 1024,
			"#default_value" => populate($project, 'description')
	);
	$form['url'] = array(
			"#type" => "textfield",
			'#title' => t('The url of this project'),
			"#size" => 60,
			'#required' => '1',
			"#default_value" => populate($project, 'url')
	);
	$form['tags'] = array(
			"#type" => "textfield",
			'#title' => t('Some tags to facilitate searching (php,...)'),
			"#size" => 60,
			'#required' => '1',
			"#default_value" => populate($project, 'tags')
	);
// 	$form['submit'] = array(
// 			'#type' => 'submit',
// 			'#value' => t('Save'),
// 			'#ajax' => array(
// 					'callback' => 'vals_soc_entity_form_callback',
// 					'wrapper' => 'vals_soc_entity_form_wrapper',
// 			),
// 			'#post_render' => array('vals_soc_fix_submit_button'),
// 	);
	$form['submit'] = array(
			'#type' => 'submit',
			'#attributes' => array('onclick'=> entityCall('studentgroup', $group, $target)),
			'#value' => t('Save'),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$form['cancel'] = array(
			'#type' => 'button',
			'#value' => t('Cancel'),
			'#prefix' => '&nbsp; &nbsp; &nbsp;',
			'#attributes' => array('onClick' => 'location.reload(); return true;'),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
// 	$form['#attached']['js'] = array(
// 			array(
// 					'type'  => 'file',
// 					'data' => drupal_get_path('module', 'vals_soc') .'/includes/js/drupal_ajax_functions.js',
// 			),
// 	);
	return $form;
}

function vals_soc_proposal_form($form, &$form_state, $proposal='', $target='', $project_id=''){
	$project_id = $proposal ? $proposal->pid : $project_id;
	if (! $project_id){
		drupal_set_message(showError(t('The project id has not been passed on well. Cannot continue. Please inform the system administrator')),
			'error');
		return FALSE;
	}
// 	$form['name'] = array(
//		"#type" => "textfield",
//		'#title' => t('Give your full name'),
//		"#size" => 60,
//		'#required' => '1',
//		"#default_value" => populate($proposal, 'name'),
//		"#description" => t("For easy communication, it is nice to have your name next to your email address"),
// 	);
	$form['solution_short'] = array(
		"#type" => "textarea",
		'#title' => t('Give a summary of your solution'),
		'#cols' => 50,//"#size" => 1024,
		"#default_value" => populate($proposal, 'solution_short')
	);
	$form['solution_long'] = array(
		"#type" => "textarea",
		'#title' => t('Give a more detailed description of your solution'),
		'#cols' => 50,
		//"#size" => 750,
		"#default_value" => populate($proposal, 'solution_long')
	);
	$form['modules'] = array(
		"#type" => "textarea",
		'#title' => t('Which extra modules and libraries will you use, if any?'),
		'#cols' => 50,//"#size" => 1024,
		"#default_value" => populate($proposal, 'modules'),
		"#description" => t("Write the name of the library, it's url and its purpose."),
		
	);
	$form['cv'] = array(
		"#type" => "textarea",
		'#title' => t('Describe your Curriculum Vitae'),
		'#cols' => 50,//"#size" => 1024,
		"#default_value" => populate($proposal, 'cv'),
		"#description" => t("You might want to discuss with your supervisor which courses you should put on your cv."),
	);
	//If we never need a stand alone form and only use ajax, remove the commented code
	//and get common code outside
	$form['cancel'] = array(
			'#type' => 'button',
			'#button_type' => 'button',
			'#value' => t('Cancel'),
			//'#theme' => 'garland_button',
			'#attributes' => array(
					'onclick' => 'if (confirm("'.t('Do you want to leave the proposal for now?').'")){'.
						(($target != 'content') ? 'Drupal.CTools.Modal.dismiss();}':
 						'ajaxCall("project", "project_page", "", "content");}'),
			'#post_render' => array('vals_soc_fix_submit_button'),
		)
	);
	$proposal_id = ((!$proposal) ? 0 : $proposal->proposal_id);
	unset($form['#submit']);		
	$form['save'] = array(
			'#type' => 'button',
			'#button_type' => 'button',
			'#value' => t('Save draft'),
			//'#theme' => 'garland_button',
			'#attributes' => array(
					'onclick' => 'ajaxFormCall("vals-soc-proposal-form", "student", "save",'.
					' {id:'.$proposal_id .', project_id: '.$project_id.',  target: "'.$target.'" }, "handleSaveResult", "json", ["'.$target.'"]);')
			,'#post_render' => array('vals_soc_fix_submit_button')
	);
	$form['sumbit'] = array(
			'#type' => 'button',
			'#button_type' => 'button',
			'#value' => t('Submit proposal'),
			//'#theme' => 'garland_button',
			'#attributes' => array(
					'onclick' => 'if (confirm("'.t('Are you sure you want to submit your proposal now?').'")){'.
					'ajaxFormCall("vals-soc-proposal-form", "student", "submit",'.
					' {id:'.$proposal_id.', project_id: '.$project_id.',  target: "'.$target.'" }, "handleSubmitResult", "json", ["'.$target.'"]);}')
			,'#post_render' => array('vals_soc_fix_submit_button')
	);

	return $form;	
}

/*
* Drupal has a nasty habit of making buttons into "submit" buttons
* when you dont want it to.  This function sets the cancel button
* back to type "button"
*/
function vals_soc_fix_submit_button($markup, $element) {
	$markup = str_replace('type="submit', 'type="button', $markup);
	return $markup;
}

function vals_soc_entity_form_callback($form, &$form_state){
	// if there are errors, return the form to display the error messages
	$target='';
	if (form_get_errors()) {
		$form_state['rebuild'] = TRUE;
		return $form;
	}
	else{
		$commands = array();
		$commands[] = ajax_command_html('#vals_soc_entity_form_wrapper', render($form));
		$commands[] = array(
			'command' => 'jsEntitySaveCallback',
			'key' => $form_state['values']['key'],
			'tabTarget' => $form_state['values']['tab_target'],
			'entityType' => $form_state['values']['entity_type'],
		);
		return array('#type' => 'ajax', '#commands' => $commands);
	}
}

//Note: this is the form for the NEW timeline per program
//rather than the older one above
function vals_soc_program_timeline_form($form, &$form_state, $timeline='', $target=''){
	if(!isset($target)){
		$target='content';
	}
	drupal_add_js (_VALS_SOC_PATH .'/includes/js/ajax.js');
	$form = array(
			'#prefix' => '<div id="vals_soc_entity_form_wrapper">',
			'#suffix' => '</div>',
	);
	$form['key'] = array(
			'#type' => 'hidden',
			"#default_value" => populate($timeline, 'timeline_id'),
	);
	$form['tab_target'] = array(
			'#type' => 'hidden',
			"#default_value" => $target,
	);
	$form['entity_type'] = array(
			'#type' => 'hidden',
			"#default_value" => 'program-timeline',
	);
	$form['program_active'] = array(
			'#type' => 'checkbox',
			'#title' => t('Enable program'),
			'#default_value' => populate($timeline, 'program_active'),
	);
	$form['program_start_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Program start date'),
			'#default_value' => populate($timeline, 'program_start_date'),
	);

	$form['org_app_start_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Organisation signup start date'),
			'#default_value' => populate($timeline, 'org_app_start_date'),
	);

	$form['org_app_end_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Organisation signup end date'),
			'#default_value' => populate($timeline, 'org_app_end_date'),
	);

	$form['accepted_org_announced_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Accepted organisations announced deadline'),
			'#default_value' => populate($timeline, 'accepted_org_announced_date'),
	);

	$form['student_signup_start_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Student signup start date'),
			'#default_value' => populate($timeline, 'student_signup_start_date'),
	);

	$form['student_signup_end_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Student signup end date'),
			'#default_value' => populate($timeline, 'student_signup_end_date'),
	);

	$form['org_review_student_applications_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Organisations review student applications deadline'),
			'#default_value' => populate($timeline, 'org_review_student_applications_date'),
	);

	$form['students_matched_to_mentors_deadline_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Students matched to mentors deadline'),
			'#default_value' => populate($timeline, 'students_matched_to_mentors_deadline_date'),
	);

	$form['accepted_students_announced_deadline_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Accepted students announced deadline'),
			'#default_value' => populate($timeline, 'accepted_students_announced_deadline_date'),
	);

	$form['coding_start_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Coding start date'),
			'#default_value' => populate($timeline, 'coding_start_date'),
	);

	$form['suggested_coding_deaadline_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Suggested coding deadline'),
			'#default_value' => populate($timeline, 'suggested_coding_deaadline_date'),
	);

	$form['coding_end_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Coding end date'),
			'#default_value' => populate($timeline, 'coding_end_date'),
	);

	$form['program_end_date'] = array(
			'#type' => 'date_popup',
			'#date_format' => 'Y-m-d H:i',
			'#title' => t('Program end date'),
			'#default_value' => populate($timeline, 'program_end_date'),
	);
	$form['submit'] = array(
			'#type' => 'submit',
			'#value' => t('Save'),
			'#id' => 'vals_soc_timeline_edit_submit_me',
			'#ajax' => array(
					'callback' => 'vals_soc_timeline_form_callback',
					'wrapper' => 'vals_soc_entity_form_wrapper',
			),
	);
	$form['cancel'] = array(
			'#type' => 'button',
			'#value' => t('Cancel'),
			'#prefix' => '&nbsp; &nbsp; &nbsp;',
			'#attributes' => array('onClick' => 'location.reload(); return true;'),
			'#post_render' => array('vals_soc_entity_form_cancel'),
	);
	$form['#attached']['js'] = array(
			array(
					'type'  => 'file',
					'data' => drupal_get_path('module', 'vals_soc') .'/includes/js/drupal_ajax_functions.js',
			),
	);
	return $form;
}

function vals_soc_timeline_form_callback($form, &$form_state){
	if (form_get_errors()) {
		$form_state['rebuild'] = TRUE;
		return $form;
	}
	else{
		$commands = array();
		$commands[] = ajax_command_html('#vals_soc_entity_form_wrapper', render($form));
		$commands[] = array(
				'command' => 'timelineSaveCallback',
				'key' => $form_state['values']['key'],
				'tabTarget' => $form_state['values']['tab_target'],
				'entityType' => $form_state['values']['entity_type'],
		);
		return array('#type' => 'ajax', '#commands' => $commands);
	}
}