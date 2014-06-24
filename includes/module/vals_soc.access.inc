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

/*
 * The access strings take the form of relative path being requested a la
 * 'dashboard/organisations/browse' where the actual url was
 * http://localhost/vals/dashboard/organisations/browse. 
 * (although in some cases there is no real url eg 'dashboard/projects/apply')
 * The default case will also check the permission...
 * 		hasPermission($access_string);
 * so you can also pass the permission a la
 * 	   'vals browse proposals'
 */
function page_access_check($access_string) {
	//drupal_set_message("test:".$access_data);
	global $user;
	switch ($access_string) {
		case 'dashboard/proposals/browse':
			return hasPermission('vals browse proposals');
			break;
		case 'dashboard/projects/apply': // this actual url path does not exist
			return hasPermission('vals apply projects');
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
		case 'projects/browse':
		case 'dashboard/projects/browse':
			return TRUE;
			// any role can access projects/browse - but only after the organisations have been announced
			if(hasPermission('vals browse projects')&& Timeline::getInstance()->isAfterOrgsAnnouncedDate()){
				return TRUE;
			}
			else{
				return FALSE;
			}
			break;
		
		case 'organisations/browse':
		case 'dashboard/organisations/browse':
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
			return hasPermission($access_string);
	}
}