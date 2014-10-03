<?php 
/*
 * We use this function to populate fields in the form of an entity if it already exists
 */
function populate($obj, $prop, $default=''){
	return ($obj && isset($obj->$prop)) ? $obj->$prop : $default;
}

function entityCall($type, $obj, $target, $show_action='administer', $action_handler='administration', $entity_action='save'){
	$key = AbstractEntity::keyField($type);
	$id = $obj && $obj->$key ? $obj->$key : 0;
    $post_function = $id ? 'refreshSingleTab' : 'refreshTabs';
	$call = "ajaxFormCall('vals_soc_entity_form_wrapper_$target form', '$action_handler', '$entity_action', ".
		"{type: '$type',id: $id, show_action:'$show_action', target: '$target'},".
		"'$post_function','json',['$type', '$target', '$action_handler']);";
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
    if ($is_admin = ($role  === _ADMINISTRATOR_TYPE)){
    	$roles = $form['#user']->roles;
    	$role = getUserRoleName($roles);
    	$uid = $form['#user']->uid;
    } else {
    	$uid = $user->uid;
    }
   
    //Find out the name of this user (if set)
    $name_rec = db_select('soc_names', 'name')->fields('name')->condition('names_uid', $uid)->
    	condition('type', $role)->execute()->fetchAssoc();
    $name = $name_rec ? $name_rec['name'] : $form['#user']->name;
    $form['account']['fullname'] = array(
    		"#type" => "textfield",
    		"#title" => t('You can add your full name here'),
    		//"#options" => $options_institutes,
    		"#description" => t("(If you leave this empty, we will use your account name instead)"),
    		"#default_value" => $name,
    );
    if (in_array($role, array(_SUPERVISOR_TYPE, _INSTADMIN_TYPE, _STUDENT_TYPE))){
        $member_inst_rec = db_select('soc_user_membership', 'mem')->fields('mem')->condition('uid', $uid)->
            condition('type', _INSTITUTE_GROUP)->execute()->fetchAssoc();
        $member_inst = $member_inst_rec ? $member_inst_rec['group_id'] : 0;
       
        if ((! $member_inst && (($role == _INSTADMIN_TYPE) || $role  == _SUPERVISOR_TYPE))  || $is_admin){
	        //So we know the user is not in an institute yet
        	//He/she can choose between existing institutes (or leave the institute option as 'Fill in later' 
        	//and enter an own institute)
	        $institutes = db_select('soc_institutes', 'i')->fields('i', array('inst_id', 'name'))->execute()->fetchAll(PDO::FETCH_ASSOC);
	        $options_institutes = array(0 => t('Fill in later'));
	        foreach ($institutes as $ins){
	            $options_institutes[$ins['inst_id']] = $ins['name'];
	        }
	       
	        $form['account']['institute'] = array(
	                "#type" => "select",
	                "#title" => t("Select the institute you are in"),
	                "#options" => $options_institutes,
	                "#description" => t("(For students and supervisors or institute administrators only)"),
	                "#default_value" => $member_inst,
	        		"#attributes"=> array('onchange'=>"if (this.value != 0){makeVisible('account_key_wrap')} else {makeInvisible('account_key_wrap');}")
	        );
	        if (!$is_admin){
	        	$show_account_key = (isset($form_state['input']['institute'])) && $form_state['input']['institute'];
		        $form['account']['account_key'] = array(
		        		'#prefix' => '<div  id="account_key_wrap" class="'.($show_account_key ? '' : 'invisible').'">',
		        		'#suffix' => '</div>',
		        		"#type" => "textfield",
		        		"#title" => t("Type the key you got from your colleague in the institute "),
		        		"#size" => 10,
		        		"#description" => t("The code is for this institute only."),
		        );
	        }
        }
     } elseif (in_array($role, array(_ORGADMIN_TYPE, _MENTOR_TYPE))) {
        $member_org_rec = db_select('soc_user_membership', 'mem')->fields('mem')->condition('uid', $uid)->
            condition('type', _ORGANISATION_GROUP)->execute()->fetchAssoc();
        $member_org = $member_org_rec ? $member_org_rec['group_id'] : 0;
    	if ((! $member_org )  || $is_admin){
	        //So we know the user is not in an organisation yet
    		//He/she can choose between existing organisations (or leave the organisation option as 
    		//'Fill in later'and enter an own organisation)
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
	        		"#attributes"=> array('onchange'=>"if (this.value != 0){makeVisible('account_key_wrap')} else {makeInvisible('account_key_wrap');}")
	        );
	        //when rebuilding this form there might be an organisation set. We need to know whether to make the account_key visible
	        
	        if (!$is_admin){
	        	$show_account_key = (isset($form_state['input']['organisation'])) && $form_state['input']['organisation'];
		        $form['account']['account_key'] = array(
		        		'#prefix' => '<div  id="account_key_wrap" class="'.($show_account_key ? '' : 'invisible').'">',
		        		'#suffix' => '</div>',
		        		"#type" => "textfield",
		        		"#title" => t("Type the key you got from your colleague in the organisation "),
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
	
	$q = explode("/",$_GET['q']);
	$code = (isset($q[2]))?$q[2]:'';
	
	$form['account']['fullname'] = array(
			"#type" => "textfield",
			"#title" => t("What is your full name?"),
			//"#options" => $options_institutes,
			"#description" => t("(If you leave this empty, we will use your account name instead)"),
			//"#default_value" => '',
	);
	$current_role = getRole();
	$no_administrator = ($current_role !== _ADMINISTRATOR_TYPE);
	
	if (! $no_administrator){
		//If the admin adds users directly we do not want the following fields as these are already
		//provided in one or another way
	
		$form['account']['account_type'] = array(
				"#type" => "select",
				"#title" => t("What is your role"),
				"#options" => array(
						_STUDENT_TYPE => t("Student"),
						_SUPERVISOR_TYPE => t("Supervisor"),
						_MENTOR_TYPE => t("Mentor"),
						_ORGADMIN_TYPE => t('Organisation Administrator'),
						_INSTADMIN_TYPE => t('Institute Administrator'),
						_SOC_TYPE => t('Virtual Alliances Consortium member'),
				),
				"#description" => t("Select your role in Semester of Code."),
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
	}
    $url_set_code =   $code;//getRequestVar('c', '', 'GET');
    if ($no_administrator){
        //If the admin adds users directly we do not want the following fields as these are already 
        //provided in one or another way
        $form['account']['account_key'] = array(
                "#type" => "textfield",
                "#title" => t("Type the key you got in the invitation"),
                "#size" => 10,
                "#description" => t("This code is different per role."),
        		"#default_value" => $url_set_code,
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

function vals_soc_organisation_form($form, &$form_state, $org='', $target='', $show_action='administer'){
	$form = array(
			'#prefix' => "<div id='vals_soc_entity_form_wrapper_$target'>",
			'#suffix' => '</div>',
	);
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
			'#attributes' => array('onclick'=> entityCall(_ORGANISATION_GROUP, $org, $target, $show_action)),
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
	$form['#vals_soc_attached']['js'] = array(
		array(
			'type'  => 'file',
			'data' => '/includes/js/test_functions.js',
		),
	);
	return $form;
}

function vals_soc_institute_form($form, &$form_state, $institute='', $target='', $show_action='administer'){
	$form = array(
			'#prefix' => "<div id='vals_soc_entity_form_wrapper_$target'>",
			'#suffix' => '</div>',
	);
	$form['name'] = array(
		"#type" => "textfield",
		'#title' => t('What is the name of your institute?'),
		"#size" => 60,
		"#default_value" => populate($institute, 'name'),
		'#required' => '1'
 	);
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
		'#attributes' => array('onclick'=> entityCall(_INSTITUTE_GROUP, $institute, $target, $show_action)),
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
	$form['#vals_soc_attached']['js'] = array(
		array(
			'type'  => 'file',
			'data' => '/includes/js/test_functions.js',
		),
	);
	return $form;
}

function vals_soc_studentgroup_form($form, &$form_state, $group='', $target='', $show_action='administer'){
	$form = array(
			'#prefix' => "<div id='vals_soc_entity_form_wrapper_$target'>",
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
		"#default_value" => _STUDENT_GROUP,
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
			'#attributes' => array('onclick'=> entityCall(_STUDENT_GROUP, $group, $target, $show_action)),
			'#value' => t('Save'),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$form['cancel'] = array(
		'#type' => 'button',
		'#value' => t('Cancel'),
		'#prefix' => '&nbsp; &nbsp; &nbsp;',
		'#attributes' => array('onClick' => 'location.reload(); return true;'),//TODO Handle this without page reload
		'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$form['#vals_soc_attached']['js'] = array(
		array(
			'type'  => 'file',
			'data' => '/includes/js/test_functions.js',
		),
	);
	return $form;
}

function vals_soc_project_form($form, &$form_state, $project='', $target='', $org=''){
	$form = array(
			'#prefix' => "<div id='vals_soc_entity_form_wrapper_$target'>",
			'#suffix' => '</div>',
	);
	$my_organisations = Groups::getGroups(_ORGANISATION_GROUP);
	$number_of_organisations = $my_organisations->rowCount();
	// now turn results into array
	$organisations = objectToArray($my_organisations);
	$select_values = array(); // to store the result
	//loop around setting the name/value parts to be values in the drop down select box
	foreach($organisations as $organisation){
		$select_values[$organisation['org_id']] = $organisation['name'];
	}
	
	if ($project || ($number_of_organisations < 2)){//|| $org
		$org_id = $project ? $project->org_id : ($number_of_organisations ? $organisations[0]['org_id']: 0);
		$form['org_id'] = array(
				'#type' => 'hidden',
				"#default_value" => $org_id,
		);
	}  else {
		$form['org_id'] = array(
			"#type" => "select",
			'#title' => t('For which organisation will this project be?'),
			"#options" => $select_values,
			'#required' => '1',
			"#default_value" => populate($project, 'org_id', ($org ?: ''))
		);
	};
	$form['title'] = array(
			"#type" => "textfield",
			'#title' => t('The name of the project'),
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
	$form['url_description_text'] = array(
			'#markup' => '<p><i>' . 'At a minimum, please include the expected outcome of the project, a potential mentor, the skills and/or 
				languages required to complete the project, and a general "difficulty" level. The project should take about 3 months to complete. 
				Please bear in mind that it\'s better to start with a smaller project that can be extended if your student proves to be capable 
				rather than have an over-ambitious idea which can\'t be completed in time.'. '</i></p>'
	);
	$form['url'] = array(
			"#type" => "textfield",
			'#title' => t('The url of this project'),
			"#size" => 60,
			'#required' => '1',
			"#default_value" => populate($project, 'url')
	);
	$form['url_help_text'] = array(
			'#markup' => '<p><i>' . 'This could be for example a link to a bug tracker issue or google doc that describes the project idea.'. '</i></p>'
	);
	$form['tags'] = array(
			"#type" => "textfield",
			'#title' => t('Some tags to facilitate searching (Delimited by comma i.e. php, javascript, html)'),
			"#size" => 60,
			//'#required' => '1',
			"#default_value" => populate($project, 'tags')
	);
	if ($project){
		$orgadmin_users = Users::getUsers(_ORGADMIN_TYPE, _ORGANISATION_GROUP, $project->org_id);
		$mentor_users = Users::getUsers(_MENTOR_TYPE, _ORGANISATION_GROUP, $project->org_id);
		$o_users = objectToArray($orgadmin_users);
		$m_users = objectToArray($mentor_users);
		$users = array_merge($o_users, $m_users);
		$tselect_values = array(); // to store the result
		//loop around setting the name/value parts to be values in the drop down select box
		foreach($users as $user){
			$tselect_values[$user['uid']] = $user['name'] . ' ('.$user['mail'].')';
		}
	
		$form['mentor_id'] = array(
				"#type" => "select",
				'#title' => t('Optionally, set the mentor to another user'),
				"#options" => $tselect_values,
				'#required' => '0',
				"#default_value" => populate($project, 'mentor_id', ($org ?: ''))
		);
	}
	$form['draft'] = array(
			"#type" => "checkbox",
			'#title' => t('If you want you can save the project as draft, making it visible only to members of your organisation)'),
			"#size" => 60,
			//'#required' => '1',
			"#default_value" => (populate($project, 'state', 'pending') == 'draft' ? TRUE : FALSE)
	);
	$form['submit'] = array(
			'#type' => 'submit',
			'#attributes' => array('onclick'=> entityCall(_PROJECT_OBJ, $project, $target,
				'does_not_matter', _PROJECT_OBJ)),
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
	$form['#vals_soc_attached']['js'] = array(
		array(
			'type'  => 'file',
			'data' => '/includes/js/test_functions.js',
		),
	);
	return $form;
}

function vals_soc_proposal_form($form, &$form_state, $proposal='', $target='', $project_id='', $possible_supervisors=''){
	$project_id = $proposal ? $proposal->pid : $project_id;
	if (! $project_id){
		drupal_set_message(
			t('The project id has not been passed on well. Cannot continue. Please inform the system administrator'),
			'error');
		return FALSE;
	}
	$form = array(
			'#prefix' => "<div id='vals_soc_entity_form_wrapper_$target'>",
			'#suffix' => '</div>',
	);
	$form['title'] = array(
		"#type" => "textfield",
		'#title' => t('Give a title to your proposal'),
		"#size" => 60,
		'#required' => '1',
		"#default_value" => populate($proposal, 'title'),
		"#description" => t("For easy communication, it is nice to have a title for your proposal"),
	);
	$options = array(0 => t('Choose later'));
	if ($possible_supervisors){
		foreach ($possible_supervisors as $super_obj) {
			$options[$super_obj->uid] = ($super_obj->full_name ?: $super_obj->name);
		}
	}
	$form['supervisor_id'] = array(
			"#type" => "select",
			'#title' => t('Choose a supervisor for this project'),
			"#options" => $options,
			'#required' => '0',
			"#default_value" => populate($proposal, 'supervisor_id'),
			"#description" => t("You can also choose a supervisor later. The supervisors in this list have voted that they are willing to be supervisor for this project"),
	);
	
	$form['solution_short'] = array(
		"#type" => "textarea",
		'#title' => t('Give a summary of your solution'),
		'#cols' => 50,//"#size" => 1024,
		"#default_value" => populate($proposal, 'solution_short')
	);
	$form['solution_short_text'] = array(
			'#markup' => '<p><i>' . 'In this section please add a brief outline of how you plan tackle the project idea. '
				.' You will be given an opportunity to provide more detailed information below.'. '</i></p><br/>'
	);
	$form['solution_long'] = array(
		"#type" => "textarea",
		'#title' => t('Give a more detailed description of your solution'),
		'#cols' => 50,
		//"#size" => 750,
		"#default_value" => populate($proposal, 'solution_long')
	);
	$form['solution_long_text'] = array(
			'#markup' => '<p><i>' . 'In this section please add more detail to your proposal. '
			.' You may want to add details of your design and intended strategy to tackle the problem. '
			.' Additionally you may want to describe any third party modules you intend to utilise as part of your solution.'.
			'</i></p><br/>'
	);
	/*
	$form['modules'] = array(
		"#type" => "textarea",
		'#title' => t('Which extra modules and libraries will you use, if any?'),
		'#cols' => 50,//"#size" => 1024,
		"#default_value" => populate($proposal, 'modules'),
		"#description" => t("Write the name of the library, it's url and its purpose."),
		
	);
	*/
// 	$form['cv'] = array(
// 		"#type" => "textarea",
// 		'#title' => t('Describe your Curriculum Vitae'),
// 		'#cols' => 50,//"#size" => 1024,
// 		"#default_value" => populate($proposal, 'cv'),
// 		"#description" => t("You might want to discuss with your supervisor which courses you should put on your cv."),
// 	);
	//If we never need a stand alone form and only use ajax, remove the commented code
	//and get common code outside
	$form['cancel'] = array(
			'#type' => 'button',
			'#button_type' => 'button',
			'#value' => t('Cancel'),
			//'#theme' => 'garland_button',
			'#attributes' => array(
					'onclick' => 'if (confirm("'.t('Do you want to leave the proposal for now?').'")){'.
						(($target != 'our_content') ? 
							(($target == 'admin_container') ?  'ajaxCall("proposal", "myproposal_page", "", "admin_container");}'
									:'Drupal.CTools.Modal.dismiss();}'):
 						'ajaxCall("project", "project_page", "", "our_content");}')),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$proposal_id = ((!$proposal) ? 0 : $proposal->proposal_id);
	unset($form['#submit']);		
	$form['save'] = array(
			'#type' => 'button',
			'#button_type' => 'button',
			'#value' => t('Save draft'),
			//'#theme' => 'garland_button',
			'#attributes' => array(
					'onclick' => "ajaxFormCall('vals-soc-proposal-form', _PROPOSAL_OBJ, 'save',".
					" {id: $proposal_id, project_id: $project_id,  target: '$target' }, 'handleSaveResult', 'json', ['$target']);")
			,'#post_render' => array('vals_soc_fix_submit_button')
	);
	$form['sumbit'] = array(
			'#type' => 'button',
			'#button_type' => 'button',
			'#value' => t('Submit proposal'),
			//'#theme' => 'garland_button',
			'#attributes' => array(
					'onclick' => "if (confirm('".t('Are you sure you want to submit your proposal now? Once submitted, you cannot edit it anymore.')."')){".
					"ajaxFormCall('vals-soc-proposal-form', _PROPOSAL_OBJ, 'submit',".
					" {id: $proposal_id, project_id: $project_id,  target: '$target' }, 'handleSubmitResult', ".
					"'json', ['$target']);}")
			,'#post_render' => array('vals_soc_fix_submit_button')
	);
	$form['#vals_soc_attached']['js'] = array(
		array(
			'type'  => 'file',
			'data' => '/includes/js/test_functions.js',
		),
	);

	return $form;	
}

function vals_soc_invite_form($form, &$form_state, $org='', $target='', $show_action='administer', $type, $subtype=''){
	include_once(_VALS_SOC_ROOT. '/includes/module/vals_soc.mail_messages.inc');
	$message = get_invite_email_body($org, $subtype, ($subtype== _STUDENT_TYPE)? t('your institute'): '');
	
	$form = array(
			'#prefix' => "<div id='vals_soc_entity_form_wrapper_$target'>",
			'#suffix' => '</div>',
	);
	$form['email_contact_header'] = array(
			'#markup' => '<h2>' . tt('Invite new %1$s', $subtype). '</h2><br/>'
	);
	$form['subject'] = array(
			'#type' => 'hidden',
			"#default_value" => t('Invitation to join the VALS Semester of code'),
	);
	$form['contact_email'] = array(
			"#type" => "textfield",
			'#title' => t('Email of the person to invite'),
			'#description' => t('This can be a comma-separated list of addresses'),
			"#size" => 100,
			'#required' => '1',
			"#default_value" => '',
	);
	$form['description'] = array(
			"#type" => "textarea",
			'#title' => t('Message'),
			//"#size" => 1024,
			"#default_value" => $message,
		
	);
	$form['submit'] = array(
			'#type' => 'submit',
			'#attributes' => array('onclick'=> entityCall($type, $org, $target, $show_action, 'administration', 'send_invite_email')),
			'#value' => t('Send'),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$form['cancel'] = array(
			'#type' => 'button',
			'#value' => t('Cancel'),
			'#prefix' => '&nbsp; &nbsp; &nbsp;',
			'#attributes' => array('onClick' => 'location.reload(); return true;'),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$form['#vals_soc_attached']['js'] = array(
			array(
					'type'  => 'file',
					'data' => '/includes/js/test_functions.js',
			),
	);
	return $form;
}

function vals_soc_comment_form($form, &$form_state, $comment='', $target='', $entity_id='', $entity_type){
	$form = array(
			//'#prefix' => "<div id='vals_soc_entity_form_wrapper_$comment'>",
			'#prefix' => "<div id='vals_soc_entity_form_wrapper_".$comment."_".$entity_id."_".$entity_type."'>",
			'#suffix' => '</div>',
	);
	$form['description'] = array(
			"#type" => "textarea",
			'#title' => t('Message'),
			"#default_value" => '',
			'#attributes' => array(
					'class' => array('comment-textarea'),
			),
	);
	$form['submit'] = array(
			'#type' => 'submit',
			'#attributes' => array('onclick'=> "ajaxFormCall('vals_soc_entity_form_wrapper_".$comment."_".$entity_id."_".$entity_type." form', 'comment', 'save', ".
					"{parent_id: '$comment', entity_type: '$entity_type', entity_id: $entity_id, show_action:''},".
					"'refreshSingleComment','json',['comment', '$target', 'comment', '$comment']);"),
			'#value' => t('Post'),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$form['cancel'] = array(
			'#type' => 'button',
			'#value' => t('Cancel'),
			'#prefix' => '&nbsp; &nbsp; &nbsp;',
			'#attributes' => array('onClick' => 'console.log(\'doet een reset\');this.form.reset(); return false;'),
			'#post_render' => array('vals_soc_fix_submit_button'),
	);
	$form['#vals_soc_attached']['js'] = array(
			array(
				'type'  => 'file',
				'data' => '/includes/js/test_functions.js',
			),
	);
	return $form;
}
//
/*
* Drupal has a nasty habit of making buttons into "submit" buttons
* when you dont want it to.  This function sets the cancel button
* back to type "button"
*/
function vals_soc_fix_submit_button($markup, $element) {
	$markup = str_replace('type="submit', 'type="button', $markup);
	return $markup;
}

// function vals_soc_entity_form_callback($form, &$form_state){
// 	// if there are errors, return the form to display the error messages
// 	$target='';
// 	if (form_get_errors()) {
// 		$form_state['rebuild'] = TRUE;
// 		return $form;
// 	}
// 	else{
// 		$commands = array();
// 		$commands[] = ajax_command_html('#vals_soc_entity_form_wrapper', render($form));
// 		$commands[] = array(
// 			'command' => 'jsEntitySaveCallback',
// 			'key' => $form_state['values']['key'],
// 			'tabTarget' => $form_state['values']['tab_target'],
// 			'entityType' => $form_state['values']['entity_type'],
// 		);
// 		return array('#type' => 'ajax', '#commands' => $commands);
// 	}
// }