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
	$items['dashboard/members'] = getMembersLink();
	$items['dashboard/institute/members'] = getMembersLink2();
	$items['dashboard/institute/administer'] = getAdministerLink();
	$items['dashboard/institute/group/administer'] = getGroupAdministerLink();
	$items['dashboard/organisations/browse'] = getOrganisationBrowseLink('dashboard/organisations/browse');
	$items['dashboard/projects/browse'] = getProjectsBrowseLink('dashboard/projects/browse');
	$items['dashboard/projects/administer'] = getProjectsAdministerLink('dashboard/projects/administer');
	$items['dashboard/proposals/mine'] = getMyProposalBrowseLink('dashboard/projects/apply');
	
	//########################## TOP LEVEL LINKS ##############################
	// These are usually used when we want unauthenticated users to view something not via the dashboard  
	$items['organisations/browse'] = getOrganisationBrowseLink('organisations/browse');
	// Allows user to browse projects registered in the system
	$items['projects/browse'] = getProjectsBrowseLink('projects/browse');
	$items['proposals/browse'] = getProposalsBrowseLink();

//	$items['projects/apply'] = array(
//			'title' => t('Apply for Projects'),
//			'type' => MENU_NORMAL_ITEM,
//			'page callback' => 'vals_soc_apply_projects',
//			'access callback' => 'vals_soc_access_check',
//			'access arguments' =>  array('projects/apply'),
//			'description' => 'Proposal page',
//			'file' => 'vals_soc.pages.inc',
//			'file path' => _VALS_SOC_PATH . '/includes/module/',
//	);
	return $items;
}

function getOrganisationBrowseLink($path){
	return  array(
			'title' => t('Organisations'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_browse_organisations',
			'access callback' => 'vals_soc_access_check',
			'access arguments' =>  array($path),
			'description' => t('Organisations page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getMyProposalBrowseLink($path=''){
	return  array(
			'title' => t('My Proposals'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_browse_proposals',
			'access callback' => 'vals_soc_access_check',
			'access arguments' =>  array($path),
			'description' => t('My Proposal page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getProjectsBrowseLink($path){
	return array(
			'title' => t('Projects'),
			'type' => MENU_CALLBACK, // register link, but dont put it in the menu
			'page callback' => 'vals_soc_browse_projects',
			'access callback' => 'vals_soc_access_check',
			'access arguments' => array($path),
			'description' => t('Projects page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getEditMessagesLink($path, $menu_type){
	return array(
			'title' => t('Edit Messages'),
			'type' => $menu_type,
			'page callback' => 'drupal_get_form',
			'page arguments' => array('vals_soc_admin_messages_form'),
			'access arguments' => array($path),
			'description' => t('Edit the email messages that are sent to users.'),
			'file' => 'vals_soc.forms.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}
	
function getEditTimelineLink($path, $menu_type){
	return array(
			'title' => t('Edit Timeline'),
			'type' => $menu_type,
			'page callback' => 'drupal_get_form',
			'page arguments' => array('vals_soc_admin_timeline_form'),
			'access arguments' => array($path),
			'description' => t('Edit the timeline and other settings.'),
			'file' => 'vals_soc.forms.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getProgramSettingsLink($path, $title, $menu_type){
	return array(
			'title' => $title,
			'type' => $menu_type,
			'page callback' => 'vals_soc_admin_program_settings_page',
			'access arguments' => array($path),
			'description' => t('Allow Administration on the summer of code module.'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getDashboardLink(){
	return array(
			'title' => t('Dashboard'),
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_dashboard_overview_page',
			'access arguments' => array('vals access dashboard'),
			'description' => t('Dashboard'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getMembersLink(){
	return array(
			'title' => t('Administer'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_page',
			'access arguments' => array('vals admin register'), // This needs to be revisited
			'description' => t('Administer page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getMembersLink2($type='institute'){
	return array(
			'title' => tt('Overview of the members of your %1$s', t($type)),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_members_page',
			'access arguments' => array('vals admin register'), // This needs to be revisited
			'description' => t('Administer page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getAdministerLink(){//$items['dashboard/institute/administer']
	return array(
			'title' => t('Administer your institute'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_institute_page',
			'access arguments' => array('vals admin register'), // This needs to be revisited
			'description' => t('Administer page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}
function getGroupAdministerLink(){//$items['dashboard/institute/group/administer']
	return array(
			'title' => t('Administer your groups'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_groupspage',
			'access arguments' => array('vals admin register'), // This needs to be revisited
			'description' => t('Administer groups in your institute page'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}


function getProjectsAdministerLink(){
	return array(
			'title' => t('Administer'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_projects_page',
			'access arguments' => array('vals admin register'), // This needs to be revisited
			'description' => t('Administer page for projects'),
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getProposalsBrowseLink(){
	return array(
			'title' => t('Proposals'),
			'type' => MENU_NORMAL_ITEM, //MENU_CALLBACK,
			'page callback' => 'vals_soc_browse_proposals',
			'access arguments' => array('access content'),//array('vals apply projects'), // Voor alle gebruikers (incl guests: 'use text format filtered_html'
			// uncomment the 2 lines below to restrict access to menu items based on role
			//'access callback' => 'vals_soc_user_role_check',
			// 			'access arguments' => array(1, array('Organisation Administrator','Student')),
			'description' => 'Proposal page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}
