<?php
function vals_soc_form_user_register_form_validate_handler(&$form, &$form_state){
    $current_role = getRole();
    
    if (isset($form_state['values']['fullname']) && (strlen($form_state['values']['fullname']) > _FULL_NAME_LENGTH)){
    	form_set_error('fullname', tt('The name you entered is a bit too long, Please abbreviate to a max of %1d chars', _FULL_NAME_LENGTH));
    	return;
    }
    
    //If no  errors are encountered, just return, otherwise call form_set_error
    if ($current_role == 'administrator'){
        return;
    }
    $super = t('Semester of Code Admin');
    $requested_user_type = isset($form_state['values']['account_type']) ? $form_state['values']['account_type'] : 'none';
    if ($requested_user_type == 'none'){
    	form_set_error('account_key', t('There is no type of the account entered.'));
    	return;
    }
    
    if (! (isset($form_state['values']['account_key']))){
        form_set_error('account_key', tt('There is no key entered. %1$s.'), $super);
        return;
    }
    
    $code_entered = $form_state['values']['account_key'];
    
    //Check the student belonging to a valid group
    if ($requested_user_type == 'student'){
        if (! isset($form_state['values']['institute']) || $form_state['values']['institute']  == 0){
           form_set_error('institute', t('You should select your university or institute.'));
        }
        $db_and = db_and();
        $db_and->condition('code', $code_entered);
        $db_and->condition('type', 'studentgroup');//Since we do not want a long list of groups during registration
        //we use the institute instead and so we select not on type student, but on group
        $db_and->condition('entity_id', $form_state['values']['institute']);
        $valid_groups_cnt = db_select('soc_codes')->fields('soc_codes')->condition($db_and)->execute()->rowCount();
        if ($valid_groups_cnt == 0){
            form_set_error('account_key', t('This is no valid key you entered. Ask your tutor.'));
        }

        return;
    }
   	$becomes_some_admin = (in_array($requested_user_type, array('institute_admin', 'organisation_admin', 'soc') ));
    	
    $org = 0;
	if (in_array($requested_user_type, array('mentor', 'organisation_admin') ) && $form_state['values']['organisation']){
		$org = $form_state['values']['organisation'];
	}
	if (in_array($requested_user_type, array('supervisor', 'institute_admin')) && $form_state['values']['institute']){
		$org = $form_state['values']['institute'];
	}
	if (!$org && !$becomes_some_admin){
		//Make distinction for choosing error message
		if ($requested_user_type == ' mentor'){
			form_set_error('organisation', t('You should select your organisation.'));
		} else {
			form_set_error('institute', t('You should select your university or institute.'));
		}
		return;
	}
	$db_and2 = db_and();
    $db_and2->condition('type', $requested_user_type);
    $db_and2->condition('code', $code_entered);
    $db_and2->condition('entity_id', $org);
    $found_codes = db_select('soc_codes')->fields('soc_codes')->condition($db_and2)->execute()->rowCount();
    $possible_code_rec = db_select('soc_codes')->fields('soc_codes')->
        condition('type', $requested_user_type)->
        condition('entity_id', $org)->
        execute()->fetchAssoc();
        //->fetchAllAssoc('type', PDO::FETCH_ASSOC); returns [student=>[type=>student, code=><student_code>,....]
    if (!$found_codes){
       form_set_error('account_key', t('The key you typed is not correct. Ask the Semester of Code Admin.'.
            (_DEBUG ? '<BR>'.t('In this phase, we just give you the code: ').$possible_code_rec['code']: '')));
    } else {
        return;
    }
    
}

function vals_soc_form_user_register_form_submit_handler(&$form, &$form_state) {
    $email = isset($form_state['values']['mail']) ? $form_state['values']['mail'] : ''; 
   //First get the current user. As the user gets at this hook, the user is already entered in the system
    $user = db_select('users', 'r')->fields('r')->condition('mail', $email)->execute()->fetchAssoc();
    if ($user){
        $uid = $user['uid'];
    } else {
        throw new Exception(t('An internal program error occured.'));
    } 
    $code_entered = $form_state['values']['account_key'];
    $current_role = getRole();
    if ($current_role == 'administrator'){
        $rid = $form_state['values']['roles'];//The standard Drupal form for admins to add users submits an int id
        $role = db_select('role', 'r')->fields('r')->condition('rid', $rid)->execute()->fetchAssoc();
        if ($role){
            $type = $role['name'];
        } else {
            throw new Exception(t('An internal program error occured.'));
        }
    } else {
        $type = $form_state['values']['account_type'];
        $role = db_select('role', 'r')->fields('r')->condition('name', $type)->execute()->fetchAssoc();
        if ($role){
            $rid = $role['rid'];
            db_insert('users_roles')->fields( array('uid'=>$uid, 'rid'=>$rid))->execute();
        } else {
            throw new Exception(t('An internal program error occured.'));
        }
    }
  
    //Insert user role and its membership to some organisation/institute
    //
    //Drupal expects a language input element in the account form. If we are here, the chosen custom language 
    //fiels has already been inserted in the user record for the new user (so we have to do nothing to achieve that).
     //The user table always contained a language field and is ready to accept that from the
    //register form. If no language field is submitted, the default language will be inserted
//    $requested_lang = trim($form_state['values']['language']);
//    if ($user['language'] != $requested_lang){
//        //db_update('users')->fields(array('language' => $requested_lang))->condition('uid', $uid)->execute();      
//    }
    
    
    if ((($type == 'student') || ($type == 'supervisor') || ($type == 'institute_admin')) && ($form_state['values']['institute'])){
        db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'institute', 
            'group_id'=>$form_state['values']['institute']
            ))->execute();
        
        if (($type == 'student')){
           $studentgroup_ids = db_select('soc_codes')->fields('soc_codes', array('entity_id', 'studentgroup_id'))->condition('code',
           $code_entered)->condition('type', 'studentgroup')->condition('entity_id', $form_state['values']['institute'])->execute()->fetchAssoc();
            db_insert('soc_user_membership')->fields( array(
                'uid'=>$uid, 
                'type' => 'studentgroup', 
                'group_id'=>$studentgroup_ids['studentgroup_id']
                ))->execute();
        }
    }
    
    if ((($type == 'organisation_admin') || ($type == 'mentor')) && ($form_state['values']['organisation'])){
        db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'organisation', 
            'group_id'=>$form_state['values']['organisation']
            ))->execute();
    }
    
	if (isset( $form_state['values']['fullname'])){
    	$txn = db_transaction();
		try {
			$del_query = "DELETE FROM soc_names WHERE names_uid = $uid AND type= '$type'";
			$ins_query = "INSERT INTO soc_names VALUES ($uid, '$type', '".$form_state['values']['fullname']."')";
				
	    	$updated1 = db_query($del_query);
	    	$updated2 = db_query($ins_query);
		}  catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not update the profile.'). (_DEBUG?  $ex->getMessage().': '.
					(isset($updated1)? $ins_query: $del_query): ''), 'error');
		}//$updated2->getQueryString() only works if the syntax was correct, otherwise updated2 == NULL
    }
}

function vals_soc_form_user_profile_form_validate_handler(&$form, &$form_state){
	$current_role = getRole();
	
	
	$super = t('Semester of Code Admin');
	//If no  errors are encountered, just return, otherwise call form_set_error
	if (isset($form_state['values']['fullname']) && (strlen($form_state['values']['fullname']) > _FULL_NAME_LENGTH)){
		form_set_error('fullname', tt('The name you entered is a bit too long, Please abbreviate to a max of %1d chars', _FULL_NAME_LENGTH));
		return;
	}
	if ($current_role == 'administrator'){
		return;
	}
	$group_type = in_array($current_role, array('supervisor', 'institute_admin', 'student')) ? 'institute': 'organisation';
	
	$my_groups = Groups::getGroups($group_type);
	if ($my_groups->rowCount()){
		//The user is already in an institute or organisation. Do not expect an account key or do anything with the 
		//organisation/institute. So the form validates as the other Drupal tests already passed if we are here.
		return;
	}
	

	$code_entered = $form_state['values']['account_key'];

	//Check the student belonging to a valid group
	if ($current_role == 'student'){
		$super = t('supervisor');
		if (! isset($form_state['values']['institute']) || $form_state['values']['institute']  == 0){
			form_set_error('institute', t('You should select your university or institute.'));
			return;
		}
		//If some organisation or institute is chosen, the key should be present
		if ((! (isset($form_state['values']['account_key']) && $form_state['values']['account_key']))){
			form_set_error('account_key', tt('There is no key entered. Ask your %1$s'), $super);
			return;
		}
		$db_and = db_and();
		$db_and->condition('code', $code_entered);
		$db_and->condition('type', 'studentgroup');//Since we do not want a long list of groups during registration
		//we use the institute instead and so we select not on type student, but on group
		$db_and->condition('entity_id', $form_state['values']['institute']);
		$valid_groups_cnt = db_select('soc_codes')->fields('soc_codes')->condition($db_and)->execute()->rowCount();
		if ($valid_groups_cnt == 0){
			form_set_error('account_key', t('This is no valid key you entered. Ask your supervisor.'));
		}

		return;
	} else {
		$org = 0;
		if (in_array($current_role, array('mentor', 'organisation_admin') ) && $form_state['values']['organisation']){
			$org = $form_state['values']['organisation'];
			$super = t('Organisation administrator');
		}
		if (in_array($current_role, array('supervisor', 'institute_admin')) && $form_state['values']['institute']){
			$org = $form_state['values']['institute'];
			$super = t('Institution administrator');
		}
		if ($org){
			$db_and2 = db_and();
			$db_and2->condition('type', $current_role);
			$db_and2->condition('code', $code_entered);
			if ((! in_array($current_role, array('institute_admin', 'organisation_admin', 'soc', 'administrator')))&&
					$org){
				$db_and2->condition('entity_id', $org);
			}
			$found_codes = db_select('soc_codes')->fields('soc_codes')->condition($db_and2)->execute()->rowCount();
			$possible_code_rec = db_select('soc_codes')->fields('soc_codes')->
				condition('type', $current_role)->
				condition('entity_id', $org)->
				execute()->fetchAssoc();
			//->fetchAllAssoc('type', PDO::FETCH_ASSOC); returns [student=>[type=>student, code=><student_code>,....]
			if (!$found_codes){
				form_set_error('account_key', tt('The key you typed is not correct. Ask your %1$s.', $super).
						(_DEBUG ? '<BR>'.t('In this phase, we just give you the code: ').$possible_code_rec['code']: ''));
			} else {
				return;
			}
		} else {
			//user changes details without connecting to an organisation or institute
			return;
		}
	}

}

function vals_soc_form_user_profile_form_submit_handler(&$form, &$form_state) {
    
    /* As we are no longer performing the code labeled here under with 'FROM HERE', it makes no sense to perform these
     * lines either.
     */
    global $user;
    //First get the current user
    $uid = $user->uid;
    $current_role = getRole();    
    
    if ($current_role == 'administrator'){
    	$is_admin = TRUE;
        $rid = getUserRoleId($form_state['values']['roles']);
        $role = db_select('role', 'r')->fields('r')->condition('rid', $rid)->execute()->fetchAssoc();
        if ($role){
            $type = $role['name'];
        } else {
            throw new Exception(t('An internal program error occured.'));
        }
    } else {
    	$is_admin = FALSE;
        $type = $current_role;
    }

    if (isset( $form_state['values']['fullname'])){
    	$txn = db_transaction();
		try {
			$del_query = "DELETE FROM soc_names WHERE names_uid = $uid AND type= '$type'";
			$ins_query = "INSERT INTO soc_names VALUES ($uid, '$type', '".$form_state['values']['fullname']."')";
				
	    	$updated1 = db_query($del_query);
	    	$updated2 = db_query($ins_query);
		}  catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not update the profile.'). (_DEBUG?  $ex->getMessage().': '.
					(isset($updated1)? $ins_query: $del_query): ''), 'error');
		}//$updated2->getQueryString() only works if the syntax was correct, otherwise updated2 == NULL
    }
	/*   
	 *  We assume that the admins create their organisation/institute and all the others register with a 
     * certain organisation. There seems no reason to change that later. If an organisation has two admins 
	 * they can add themselves to an organisation/institute */
     if ((isset($form_state['values']['institute']) && $form_state['values']['institute']) &&
      		(($type == 'institute_admin') || ($is_admin && (($type == 'student') || ($type == 'supervisor'))))
      		
    		){
      	db_query("DELETE FROM soc_user_membership WHERE uid = $uid AND type= 'institute'");
        db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'institute', 
            'group_id'=>$form_state['values']['institute']
            ))->execute();
    }
    
    //Mentors might want to change from organisation
    if ((($type == 'organisation_admin') || ($type == 'mentor')) && 
    		(isset($form_state['values']['organisation']) && $form_state['values']['organisation'])){
    	db_query("DELETE FROM soc_user_membership WHERE uid = $uid AND type= 'organisation'");
        db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'organisation', 
            'group_id'=>$form_state['values']['organisation']
            ))->execute();
    }
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
		module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');
		// A test here to email via queues and cron
		if ($form_state['values']['vals_messages_test_cron_email']==1){
			drupal_set_message(t('Sending queued test emails to ').  $form_state['values']['vals_messages_test_email'] ."");
			vals_soc_handle_cron_test_emails($form_state['values']['vals_messages_test_email']);
		}
		else{
			drupal_set_message(t('Sending NON queued test emails to ').  $form_state['values']['vals_messages_test_email'] ."");
			vals_soc_handle_test_emails($form_state['values']['vals_messages_test_email']);
		}
	}
}

function vals_soc_organisation_form_validate_handler(&$form, &$form_state)
{
     $current_role = getRole();
     if ($current_role == 'administrator' || $current_role == 'organisation_admin'){
         return;
     } else {
         form_set_error('', t('You have not sufficient rights to submit this form'));
     }
}

function vals_soc_organisation_form_submit_handler(&$form, &$form_state)
{
  global $user;
  
  $txn = db_transaction();
  try {
      $uid = $user->uid;
      $org_id = db_insert('soc_organisations')->fields(array(
            'name'=>$form_state['values']['name'], 
            'contact_name' => $form_state['values']['contact_name'], 
            'contact_email' => $form_state['values']['contact_email'],
            'url' => $form_state['values']['url'],
            'description' => ($form_state['values']['description'] ?: ''), 
            ))->execute();

      $result = $org_id && db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'organisation', 
            'group_id'=>$org_id,
            ))->execute();
      $result = $result && db_insert('soc_codes')->fields( array(
          'type'=>'mentor',
          'code' => createRandomCode('mentor', $org_id), 
          'entity_id'=> $org_id))->execute();
      if ($result) {
          drupal_set_message(t('You have succesfully added your organisation to the Semester of Code.'));
      } else {
            drupal_set_message(t('We could not add your organisation.'), 'error');
      }
  } catch (Exception $ex) {
      $txn->rollback();
      drupal_set_message(t('We could not add your organisation.'). (_DEBUG? $ex->__toString(): ''), 'error');
  }
    
}

function vals_soc_institute_form_validate_handler(&$form, &$form_state)
{
 
}

function vals_soc_institute_form_submit_handler(&$form, &$form_state)
{
	global $user;
  	
	$txn = db_transaction();
  try {
      $uid = $user->uid;
      $inst_id = db_insert('soc_institutes')->fields(array(
            'name'=>$form_state['values']['name'], 
            'contact_name' => $form_state['values']['contact_name'], 
            'contact_email' => $form_state['values']['contact_email'], 
            ))->execute();

      $result = $inst_id && db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'institute', 
            'group_id'=>$inst_id,
            ))->execute();
      $result = $result && db_insert('soc_codes')->fields( array(
          'type'=>'supervisor',
          'code' => createRandomCode('supervisor', $inst_id), 
          'entity_id'=> $inst_id))->execute();
      if ($result) {
          drupal_set_message(t('You have succesfully added your institute to the Semester of Code.'));
      } else {
          $txn->rollback();
          drupal_set_message(t('We could not add your institute.'), 'error');
      }
  } catch (Exception $ex) {
      $txn->rollback();
      drupal_set_message(t('We could not add your institute.'). (_DEBUG? $ex->__toString(): ''), 'error');
  }
    
}

function vals_soc_studentgroup_form_validate_handler(&$form, &$form_state)
{
 global $user, $language, $base_url;
    
    $uid = $user->uid;
    $inst_nr = db_select('soc_user_membership')->fields('soc_user_membership')->
        condition('uid', $uid)->
        condition('type', 'institute')->
        execute()->rowCount();
    if (!$inst_nr){
        $language_code = $language->prefix;//'en';
        //print_r(get_object_vars($language));die('dus hoe zit het nu');
        form_set_error('', t('You have not registered yourself for a specific institute. ').
            t('You can change that in your ')."<a href='$base_url/$language_code/user/$uid/edit'>".
            t('profile')."</a>");
    }
}

function vals_soc_studentgroup_form_submit_handler(&$form, &$form_state)
{
    global $user;
    
    $uid = $user->uid;
    $inst = db_select('soc_user_membership')->fields('soc_user_membership')->
        condition('uid', $uid)->
        condition('type', 'institute')->
        execute()->fetchAssoc();
    $inst_id = $inst['group_id'];
    $txn = db_transaction();
    try {
        
        $studentgroup_id = db_insert('soc_studentgroups')->fields(array(
                'inst_id' => $inst_id,
                'owner_id' => $uid,
        		'name' => $form_state['values']['name'],
                'description' => $form_state['values']['description'],
               
            ))->execute();

        $result = $studentgroup_id && db_insert('soc_user_membership')->fields(array(
                'uid' => $uid,
                'type' => 'studentgroup',
                'group_id' => $studentgroup_id,
            ))->execute();
        $result = $result && db_insert('soc_codes')->fields( array(
	            'type'=>'studentgroup',
	            'code' => createRandomCode('studentgroup', $studentgroup_id), 
	            'entity_id'=> $inst_id,
	            'studentgroup_id' =>$studentgroup_id
            ))->execute();
        if ($result) {
            drupal_set_message(t('You have succesfully added your group to the Semester of Code.'));
        } else {
            drupal_set_message(t('We could not add your group.'), 'error');
        }
    } catch (Exception $ex) {
        $txn->rollback();
        drupal_set_message(t('We could not add your group.') . (_DEBUG ? $ex->__toString() : ''), 'error');
    }
}

function vals_soc_project_form_validate_handler(&$form, &$form_state)
{
 
}

function vals_soc_project_form_submit_handler(&$form, &$form_state)
{

    
}