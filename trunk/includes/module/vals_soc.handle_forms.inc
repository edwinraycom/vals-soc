<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function vals_soc_form_user_register_form_validate_handler(&$form, &$form_state){
    $current_role = getRole();
    
    if ($current_role == 'administrator'){
        return;
    }
    
    if (! (isset($form_state['values']['account_key']))){
        form_set_error('account_key', t('There is no key entered. Ask the Semester of Code Admin.'));
        return;
    }
    $code_entered = $form_state['values']['account_key'];
    $requested_user_type = isset($form_state['values']['account_type']) ? $form_state['values']['account_type'] : 'none';
     
    //Check the student belonging to a valid group
    if ($requested_user_type == 'student'){
        //if (! isset($form_state['values']['institute']) || $form_state['values']['institute']  == 0){
          //  form_set_error('institute', t('You should select your university or institute.'));
        //}
        $db_and = db_and();
        $db_and->condition('code', $code_entered);
        //$db_and->condition('type', 'group');//Since we do not want a long list of groups during registration
        //we use the institute instead and so we select not on type student, but on group
        $db_and->condition('org', $form_state['values']['institute']);
        $valid_groups_cnt = db_select('soc_codes')->fields('soc_codes')->condition($db_and)->execute()->rowCount();
        if ($valid_groups_cnt == 0){
            form_set_error('account_key', t('This is no valid key you entered. Ask your tutor.'));
        }
        return;
    }
    $org = 0;
    if (($requested_user_type == 'mentor' ) && $form_state['values']['organisation']){
        $org = $form_state['values']['organisation'];
    }
    if (($requested_user_type == 'supervisor' ) && $form_state['values']['institute']){
        $org = $form_state['values']['institute'];
    }
    $db_and2 = db_and();
    $db_and2->condition('type', $requested_user_type);
    $db_and2->condition('code', $code_entered);
    if ((! in_array($requested_user_type, array('institute_admin', 'organisation_admin', 'soc', 'administrator')))&&
        $org){
        $db_and2->condition('org', $org);
    }
    $found_codes = db_select('soc_codes')->fields('soc_codes')->condition($db_and2)->execute()->rowCount();
    $possible_code_rec = db_select('soc_codes')->fields('soc_codes')->
        condition('type', $requested_user_type)->
        condition('org', $org)->
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
    
    //First get the current user
    $user = db_select('users', 'r')->fields('r')->condition('mail', $email)->execute()->fetchAssoc();
    if ($user){
        $uid = $user['uid'];
    } else {
        throw new Exception(t('An internal program error occured.'));
    } 
    $code_entered = $form_state['values']['account_key'];
    $current_role = getRole();
    if ($current_role == 'administrator'){
        $rid = getUserRoleId($form_state['values']['roles']);
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
    
    
    if ((($type == 'student') || ($type == 'supervisor')) && ($form_state['values']['institute'])){
        db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'institute', 
            'oid'=>$form_state['values']['institute']
            ))->execute();
        
        if (($type == 'student')){
           $group_ids = db_select('soc_codes')->fields('soc_codes', array('org', 'group_id'))->condition('code',
           $code_entered)->condition('type', 'group')->condition('org', $form_state['values']['institute'])->execute()->fetchAssoc();
            db_insert('soc_user_membership')->fields( array(
                'uid'=>$uid, 
                'type' => 'group', 
                'oid'=>$group_ids['group_id']
                ))->execute();
        }
    }
    
    if ((($type == 'organisation_admin') || ($type == 'mentor')) && ($form_state['values']['organisation'])){
        db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'organisation', 
            'oid'=>$form_state['values']['organisation']
            ))->execute();
    }
}

function vals_soc_form_user_profile_form_submit_handler(&$form, &$form_state) {
    //First get the current user
    global $user;
    $uid = $user->uid;
    $current_role = getRole();    
    
    if ($current_role == 'administrator'){
        $rid = getUserRoleId($form_state['values']['roles']);
        $role = db_select('role', 'r')->fields('r')->condition('rid', $rid)->execute()->fetchAssoc();
        if ($role){
            $type = $role['name'];
        } else {
            throw new Exception(t('An internal program error occured.'));
        }
    } else {
        $type = $current_role;
    }

    
    if ((($type == 'student') || ($type == 'supervisor') || ($type == 'organisation_admin')) && ($form_state['values']['institute'])){
        db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'institute', 
            'oid'=>$form_state['values']['institute']
            ))->execute();
    }
    
    if ((($type == 'organisation_admin') || ($type == 'mentor')) && ($form_state['values']['organisation'])){
        db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'organisation', 
            'oid'=>$form_state['values']['organisation']
            ))->execute();
    }
}
//function testObligatedFields($form_id, $values, $stop_on_error= TRUE, $show_field_error_detail=FALSE){
//    $obligated_fields = array(
//        'vals_soc_organisation_form' => array(
//            'name', 'url', 'contact_name', 'contact_email',
//        ),
//        
//    );
//    $ok = TRUE;
//    foreach ($obligated_fields[$form_id] as $f){
//        if (!isset($values[$f]) || $values[$f] == ''){
//           if ($show_field_error_detail){
//               form_set_error($f, sprintf(t('You have to fill in the field: %1$s'), $f));
//           } else {
//               
//           }
//           $ok = FALSE;
//           if ($stop_on_error){
//               break;
//           }
//        }
//    }
//    return $ok;
//}

function vals_soc_organisation_form_validate_handler(&$form, &$form_state)
{
     $current_role = getRole();
     if ($current_role == 'administrator' || $current_role == 'organisation_admin'){
         return;
//         $all_fields = testObligatedFields('vals_soc_organisation_form', $form_state['values'], 0);
//         if (!$all_fields){
//             form_set_error($f, t('You have to fill in all the marked fields'));
//         }
//         return $all_fields;
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
      $oid = db_insert('soc_organisations')->fields(array(
            'name'=>$form_state['values']['name'], 
            'contact_name' => $form_state['values']['contact_name'], 
            'contact_email' => $form_state['values']['contact_email'],
            'url' => $form_state['values']['url'],
            'description' => ($form_state['values']['description'] ?: ''), 
            ))->execute();

      $result = $oid && db_insert('soc_user_membership')->fields( array(
            'uid'=>$uid, 
            'type' => 'organisation', 
            'oid'=>$oid,
            ))->execute();
      $result = $result && db_insert('soc_codes')->fields( array(
          'type'=>'mentor',
          'code' => createRandomCode(), 
          'org'=> $oid))->execute();
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

function createRandomCode(){
    return 'AA';//TODO: echte code maken
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
            'oid'=>$inst_id,
            ))->execute();
      $result = $result && db_insert('soc_codes')->fields( array(
          'type'=>'supervisor',
          'code' => createRandomCode(), 
          'org'=> $inst_id))->execute();
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


function vals_soc_group_form_validate_handler(&$form, &$form_state)
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

function vals_soc_group_form_submit_handler(&$form, &$form_state)
{
    global $user;
    
    $uid = $user->uid;
    $inst = db_select('soc_user_membership')->fields('soc_user_membership')->
        condition('uid', $uid)->
        condition('type', 'institute')->
        execute()->fetchAssoc();
    $inst_id = $inst['oid'];
    $txn = db_transaction();
    try {
        
        $group_id = db_insert('soc_groups')->fields(array(
                'inst_id' => $inst_id,
                'name' => $form_state['values']['name'],
                'description' => $form_state['values']['description'],
                'supervisor_id' => $uid,
            ))->execute();

        $result = $group_id && db_insert('soc_user_membership')->fields(array(
                'uid' => $uid,
                'type' => 'group',
                'oid' => $group_id,
            ))->execute();
        $result = $result && db_insert('soc_codes')->fields( array(
            'type'=>'group',
            'code' => createRandomCode(), 
            'org'=> $inst_id,
            'group_id' =>$group_id
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