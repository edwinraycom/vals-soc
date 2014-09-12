<?php 
/*
 * Note: changing type of MENU_NORMAL_ITEM to MENU_CALLBACK and refreshing the cache
 * does NOT make the menu item disappear.  (its still cached. you have to manually do it
 * at http://localhost/vals/admin/structure/menu/manage/navigation)
 * 
 */
function vals_soc_menu_handler(){

	//########################## PROGRAM SETTINGS LINKS #######################
	$progSettings = t('Program Settings');
	$items = array();
	$items[_VALS_SOC_FULL_URL]   = array(
		'title' => t('Home'),
		'type' => MENU_NORMAL_ITEM,
		'access callback' => TRUE,
		'page callback' => 'drupal_goto',
		'page arguments' => array(_VALS_SOC_FULL_URL),
	);
	$items['admin/config/vals-soc']  = getProgramSettingsLink('access administration pages', t('VALS Summer of code'), MENU_NORMAL_ITEM);
	$items['admin/config/vals-soc/program_settings'] = getProgramSettingsLink('access administration pages', $progSettings, MENU_NORMAL_ITEM);
	$items['admin/config/vals-soc/program_settings/messages'] = getEditMessagesLink('access administration pages', MENU_NORMAL_ITEM);
	$items['admin/config/vals-soc/program_settings/timeline'] = getEditTimelineLink('access administration pages', MENU_NORMAL_ITEM);
	
	//########################## DASHBOARD LINKS ##############################
	$items['dashboard'] = getDashboardLink(); // main dashboard url
	# admin pages (as above) also accessible from the dashboard (only for admin user)
	$items['dashboard/program_settings']  = getProgramSettingsLink('access administration pages', $progSettings, MENU_CALLBACK);
	$items['dashboard/program_settings/messages'] = getEditMessagesLink('access administration pages', MENU_CALLBACK);
	$items['dashboard/program_settings/timeline'] = getEditTimelineLink('access administration pages', MENU_CALLBACK);

	// This is the organisation, institution and studentgroup admin form link
	$items['dashboard/members'] = getMembersLink('dashboard/members');
	
	//allow institutes to add/edit their institute details
	$items['dashboard/institute/administer'] = getAdministerLink('dashboard/institute/administer', _INSTITUTE_GROUP, false);
	$items['dashboard/institute/view'] = getAdministerLink('dashboard/institute/view', _INSTITUTE_GROUP, false);
	// allow institutes to view supervisors/other instadmins
	$items['dashboard/institute/members'] = getEntityMembersLink('dashboard/institute/members', _INSTITUTE_GROUP, false);
	// used just for institutes to allow creation of student groups
	$items['dashboard/institute/group/administer'] = getGroupAdministerLink('dashboard/institute/group/administer');
	
	//allow organisations to add/edit their organisation details
	$items['dashboard/organisation/administer'] = getAdministerLink('dashboard/organisation/administer', _ORGANISATION_GROUP);
	// allow organisations to view mentors/other orgadmins
	$items['dashboard/organisation/members'] = getEntityMembersLink('dashboard/organisation/members', _ORGANISATION_GROUP);
	// allow mentors to VIEW the admin details but not add new orgs or edit like org admin can do
	$items['dashboard/organisation/view'] = getAdministerLink('dashboard/organisation/view', _ORGANISATION_GROUP);
	
	// organisation actions - let users see all the announced organisations
	//$items['dashboard/organisations/browse'] = getOrganisationBrowseLink('dashboard/organisations/browse');
	
	// project actions
	$items['dashboard/projects/administer'] = getProjectsAdministerLink('dashboard/projects/administer');
	$items['dashboard/projects/administer/mine'] = getMyProjectsAdministerLink('dashboard/projects/administer/mine');
	$items['dashboard/projects/browse'] = getProjectsBrowseLink('dashboard/projects/browse');

	// proposal actions
	$items['dashboard/proposals/browse'] = getProposalsBrowseLink('dashboard/proposals/browse'); // browse all proposals
	$items['dashboard/proposals/mine'] = getMyProposalBrowseLink('dashboard/proposals/mine'); //browse only my proposals
	$items['dashboard/proposals/browsebytype'] = getProposalsBrowseByTypeLink('dashboard/proposals/browsebytype'); // browse all proposals
	
	//########################## TOP LEVEL LINKS ##############################
	// These are usually used when we want unauthenticated users to view something not via the dashboard
	$items['institutes/browse'] = getInstituteBrowseLink('institutes/browse');
	$items['organisations/browse'] = getOrganisationBrowseLink('organisations/browse');
	// Allows user to browse projects registered in the system
	$items['projects/browse'] = getProjectsBrowseLink('projects/browse');
	
	$items['http://semesterofcode.com/'] = array(
		'title' => t('About Vals'),
		'type' => MENU_NORMAL_ITEM,
		'access callback' => TRUE, 
		'page callback' => 'drupal_goto',
		'page arguments' => array('http://semesterofcode.com/')
			
	);
	// open this in a new window
	$items['http://semesterofcode.com/']['options']['attributes']['target'] = '_blank';
	
	
	$items[_VALS_SOC_FULL_URL]['weight'] = 0;
	$items['dashboard']['weight'] = 1;
	$items['institutes/browse']['weight'] = 4;
	$items['organisations/browse']['weight'] = 5;
	$items['projects/browse']['weight'] = 6;
	$items['http://semesterofcode.com/']['weight'] = 8;
	
	
	
	return $items;
}


/*********************************************************************************************
 * 	 PLACE FUNCTIONS BELOW THAT ARE ONLY ACCESSIBLE TO THE ADMIN USER
* *******************************************************************************************
*/

/**
 * ADMIN ONLY LINK
 * @param unknown $path
 * @param unknown $title
 * @param unknown $menu_type
 * @return multitype:string unknown multitype:unknown  NULL
 */
function getProgramSettingsLink($path, $title, $menu_type){
	return array(
			'title' => $title,
			'type' => $menu_type,
			'page callback' => 'vals_soc_admin_program_settings_page',
			'access arguments' => array($path),
			'description' => t('Allow Administration on the summer of code module.'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

/**
 * ADMIN ONLY LINK
 * @param unknown $path
 * @param unknown $menu_type
 * @return multitype:string unknown multitype:string  NULL multitype:unknown
 */
function getEditMessagesLink($path, $menu_type){
	return array(
			'title' => t('Edit Messages'),
			'type' => $menu_type,
			'page callback' => 'drupal_get_form',
			'page arguments' => array('vals_soc_admin_messages_form'),
			'access arguments' => array($path),
			'description' => t('Edit the email messages that are sent to users.'),
			'file' => 'vals_soc.forms.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

/**
 * ADMIN ONLY LINK
 * @param unknown $path
 * @param unknown $menu_type
 * @return multitype:string unknown multitype:string  NULL multitype:unknown
 */
function getEditTimelineLink($path, $menu_type){
	return array(
			'title' => t('Edit Timeline'),
			'type' => $menu_type,
			'page callback' => 'drupal_get_form',
			'page arguments' => array('vals_soc_admin_timeline_form'),
			'access arguments' => array($path),
			'description' => t('Edit the timeline and other settings.'),
			'file' => 'vals_soc.forms.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

/**
 * ALL USERS REGISTERED WITH A 2ND ROLE HAVE SOME SORT OF DASHBOARD
 * 
 * USES ROLE_PERMISSION 'vals access dashboard' 
 * 
 * All users have this and doesnt need to go through the access check
 * @return multitype:string multitype:string  NULL
 */
function getDashboardLink(){
	return array(
			'title' => t('Dashboard'),
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_dashboard_overview_page',
			'access arguments' => array('vals access dashboard'),
			'description' => t('Dashboard'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

/*********************************************************************************************
 * 	 PLACE FUNCTIONS BELOW THAT ARE TIMELINE DEPENDANT AND MUST GO THROUGH THE ACCESS CHECK
 * *******************************************************************************************
 */

/**
 * TODO - DEPRICATE TIHS FUNCTION
 * HOWS A DIFFERENT SET OF TABS DEPENDING ON ROLE TYPE
 * - ORG ADMIN CAN ENTER/EDIT/DELETE ORGANSATIONS
 * - INST ADMIN CAN ENTER/EDIT/DELETE INSTITUTES
 * - SUPERVISORS CAN ENTER/EDIT/DELETE STUDENTGROUPS
 * USES ROLE_PERMISSION 'vals admin register'
 * 
 * IS PASSED 'dashboard/members'
 * @return multitype:string multitype:string  NULL
 */
function getMembersLink($path){ //dashboard/members
	return array(
			'title' => t('Administer'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_page',
			'access callback' => 'vals_soc_access_check',
			'access arguments' => array($path),
			'description' => t('Administer page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

function getAdministerLink($path, $type, $plural=true){//$items['dashboard/institute/administer']
	$path_arr = explode('/', $path);
	$action = array_pop($path_arr);
	$plural_str = $plural ? '(s)':'';
	 
	return array(
			'title' => ($action == 'view') ? tt('View your %1$s'.$plural_str, t($type)) : tt('%1$s your %2$s'.$plural_str, 
					ucfirst(t($action)), t($type)),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_entity_page',
			'page arguments' => array($action),
			'access callback' => 'vals_soc_access_check',
			'access arguments' => array($path),
			'description' => t('Administer page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

function getEntityMembersLink($path, $type, $plural=true){ //'dashboard/institute/members'
	$plural_str = $plural ? '(s)':'';
	return array(
			'title' => tt('Overview of the members of your %1$s', t($type)).$plural_str,
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_members_page',
			'access callback' => 'vals_soc_access_check',
			'access arguments' => array($path),
			'description' => t('Administer page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

function getGroupAdministerLink($path){//$items['dashboard/institute/group/administer']
	return array(
			'title' => t('Administer your groups'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_groupspage',
			'access callback' => 'vals_soc_access_check',
			'access arguments' => array($path),
			'description' => t('Administer groups in your institute page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

/**
 * ALLOWS ORG ADMINS AND MENTORS TO ADD/EDIT/REMOVE PROJECTS
 * USES ROLE_PERMISSION 'vals admin projects'
 * 
 * IS PASSED 'dashboard/projects/administer'
 * @return multitype:string multitype:string  NULL
 */
function getProjectsAdministerLink($path){ //dashboard/projects/administer
	return array(
			'title' => t('Administer your projects'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_projects_page',
			'access callback' => 'vals_soc_access_check',
			'access arguments' => array($path),
			'description' => t('Administer page for projects'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

function getMyProjectsAdministerLink($path){ //dashboard/projects/administer/mine
	return array(
			'title' => t('Administer your own projects'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_my_projects_page',
			'access callback' => 'vals_soc_access_check',
			'access arguments' => array($path),
			'description' => t('Administer page for projects'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

/**
 * ALLOWS USERS TO VIEW THE PROJECT LIST
 * 
 * Should only be available to ALL once projects are announced
 * otherwise just org/admins/mentors/supervisors during org signup period
 * (LOGIC FOR THIS GOES IN vals_soc.access)
 * 
 * USES ROLE_PERMISSION 'vals browse projects' - all authenticated users have this.
 * 
 * IS PASSED either 'dashboard/projects/browse' or 'projects/browse'
 * @param unknown $path
 * @return multitype:string NULL multitype:unknown
 */
function getProjectsBrowseLink($path){// dashboard/projects/browse or projects/browse
	return array(
			'title' => t('Projects'),
			'type' => MENU_CALLBACK, // register link, but dont put it in the menu
			'page callback' => 'vals_soc_browse_projects',
			'access callback' => 'vals_soc_access_check',
			'access arguments' => array($path),
			'description' => t('Projects page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

/**
 * ALLOWS USERS TO VIEW THE ORGANISAITONS IN THE SYSTEM
 * 
 * USES ROLE_PERMISSION - 'vals browse organisations'
 * 
 * IS PASSED either 'dashboard/organisations/browse' or 'organisations/browse'
 * @param unknown $path
 * @return multitype:string NULL multitype:unknown
 */
function getOrganisationBrowseLink($path){ // dashboard/organisations/browse or organisations/browse
	return  array(
			'title' => t('Organisations'),
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_browse_organisations',
			'access callback' => 'vals_soc_access_check',
			'access arguments' =>  array($path),
			'description' => t('Organisations page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

/**
 * ALLOWS USERS TO VIEW THE ORGANISAITONS IN THE SYSTEM
 *
 * USES ROLE_PERMISSION - 'vals browse organisations'
 *
 * IS PASSED either 'dashboard/organisations/browse' or 'organisations/browse'
 * @param unknown $path
 * @return multitype:string NULL multitype:unknown
 */
function getInstituteBrowseLink($path){ // dashboard/institutes/browse or institutes/browse
	return  array(
			'title' => t('Institutes'),
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_browse_institutes',
			'access callback' => 'vals_soc_access_check',
			'access arguments' =>  array($path),
			'description' => t('Institutions page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

// EDWIN - i'M NOT SURE OF THE FUNCTIONALITY OF THE BOTTOM TWO MENU LINKS BELOW...

/**
 * ALLOW USERS TO BROWSE PROPOSALS
 *
 * USES ROLE_PERMISSION - none at present
 *
 * is passed 'dashboard/proposals/browse'
 * @return multitype:string multitype:string  NULL
 */
function getProposalsBrowseLink($path){ //dashboard/proposals/browse
	return array(
			'title' => t('Proposals'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_browse_proposals',
			'access callback' => 'vals_soc_access_check',
			'access arguments' =>  array($path),
			'description' => 'Proposal page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

function getProposalsBrowseByTypeLink($path){ //dashboard/proposals/browsebytype
	return array(
			'title' => t('Proposals'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_browse_proposals_by_type',
			'access callback' => 'vals_soc_access_check',
			'access arguments' =>  array($path),
			'description' => 'Proposal page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}

/**
 * ALLOWS A USER TO BROWSE HIS/HER OWN PROPOSAL
 *  
 * USES ROLE_PERMISSION - none at present
 * 
 * is passed 'dashboard/proposals/mine'
 * @param string $path
 * @return multitype:string NULL multitype:string
 */
function getMyProposalBrowseLink($path){ //dashboard/proposals/mine
	return  array(
			'title' => t('My Proposals'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_browse_my_proposals',
			'access callback' => 'vals_soc_access_check',
			'access arguments' =>  array($path),
			'description' => t('My Proposal page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module',
	);
}
