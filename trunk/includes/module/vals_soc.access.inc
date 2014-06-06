<?php
module_load_include('php', 'vals_soc', 'includes/classes/Timeline');

function hasRole($roles){
	global $user;
	$check = array_intersect($roles, array_values($user->roles));
	return empty($check) ? FALSE : TRUE;
}

function hasPermission($permission){
	return user_access($permission);	
}

function page_access_check($access_data) {
	//drupal_set_message("test");
	global $user;
	switch ($access_data) {
		case 'projects/browse':
			return TRUE;
			// any role can access projects/browse - but only after the organisations have been announced
			if(hasPermission('vals browse projects')&& Timeline::getInstance()->isAfterOrgsAnnouncedDate()){
				return TRUE;
			}
			else{
				return FALSE;
			}
			break;
		case 'projects/apply':
			// only students with 'vals apply projects' permission can apply for a project AND only during the student signup period
			if(hasRole(array('student')) && hasPermission('vals apply projects') && Timeline::getInstance()->isStudentsSignupPeriod()){
				return TRUE;
			}
			else{
				return FALSE;
			}
			break;
		case 'organisations/browse':
			// any role can access organisations/browse - but only after the organisations have been announced
			// note - reusing the browse projects permission here *maybe add another new one*
			if(hasPermission('vals browse projects')&& Timeline::getInstance()->isAfterOrgsAnnouncedDate()){
				return TRUE;
			}
			else{
				return FALSE;
			}
			break;	
		default:
			return FALSE;
	}
}