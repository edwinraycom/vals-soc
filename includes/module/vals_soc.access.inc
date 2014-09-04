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
function pageAccessCheck($access_string) {
	global $user;
	
	if (_VALS_TEST_UI_ONLY && Users::isAdmin()){
		return TRUE;
	}
	switch ($access_string) {
		case 'dashboard/members':
			return hasPermission('vals admin register') && Timeline::getInstance()->organisationSignupPeriodOpened();
			break;
		case 'dashboard/organisation/view':
			return hasPermission('vals admin view') && Timeline::getInstance()->organisationSignupPeriodOpened();
			break;
		case 'dashboard/organisation/members':
			return (hasPermission('vals admin register') || hasPermission('vals admin view')) && Timeline::getInstance()->organisationSignupPeriodOpened();
			break;
		case 'dashboard/organisation/administer':
		case 'dashboard/institute/administer':
		case 'dashboard/institute/members':
		case 'dashboard/institute/view':
		case 'dashboard/institute/group/administer':
			return hasPermission('vals admin register') && Timeline::getInstance()->organisationSignupPeriodOpened();
			break;
		case 'dashboard/projects/administer':
			return hasPermission('vals admin projects') && Timeline::getInstance()->organisationSignupPeriodOpened();
			break;
		case 'projects/browse':
		case 'dashboard/projects/browse':
			// any user must have 'val browse projects' permission
			if (hasPermission('vals browse projects')) {
				// students can only see the projects after the organisations have been announced
				if(hasRole(array(_STUDENT_TYPE)) && 
					Timeline::getInstance()->isAfterOrgsAnnouncedDate() ){
					return TRUE;
				} else {
					// everyone else with 'vals browse projects' permission can see projects once org applications open
				 	return (Timeline::getInstance()->organisationSignupPeriodOpened());
				}
			} else {
				return FALSE;
			}
			break;
		case 'projects/apply':
		case 'dashboard/projects/apply':
			// only students with 'vals apply projects' permission can apply for a project AND only during the student signup period
			if(hasRole(array(_STUDENT_TYPE)) && hasPermission('vals apply projects') && Timeline::getInstance()->isStudentsSignupPeriod()){
				return TRUE;
			}
			else{
				return FALSE;
			}
			break;
		case 'organisations/browse':
		case 'dashboard/organisations/browse':
			// any user must have 'val browse organisations' permission
			if(hasPermission('vals browse organisations')){
				// students AND unauthenticated users can only see the orgs after the organisations have been announced
				if( (hasRole(array(_STUDENT_TYPE)) || hasRole(array(_ANONYMOUS_TYPE))) && 
					Timeline::getInstance()->isAfterOrgsAnnouncedDate() ){
					return TRUE;
				} else {
					return (Timeline::getInstance()->organisationSignupPeriodOpened());
				}
			} else{
				return FALSE;
			}
			break;
		case 'dashboard/proposals/browse': 
			return (! hasRole(array(_ANONYMOUS_TYPE))) && (!hasRole(array(_STUDENT_TYPE)));
			break;
		case 'dashboard/proposals/mine':
			return  hasRole(array(_STUDENT_TYPE));
			break;		
		case 'institutes/browse':
			 if( (hasRole(array(_STUDENT_TYPE)) || hasRole(array(_ANONYMOUS_TYPE))) && 
					Timeline::getInstance()->isAfterOrgsAnnouncedDate() ){
					return TRUE;
				} else {
					// everyone else with 'vals browse projects' permission can see projects once org applications open
				 	return (Timeline::getInstance()->organisationSignupPeriodOpened());
				};
	
		default:
			return hasPermission($access_string);
	}
}
