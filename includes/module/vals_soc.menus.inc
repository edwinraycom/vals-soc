<?php 

function vals_soc_menu_handler(){
	/*
	 *  DASHBOARD MENU OPTIONs
	 */
	$items['dashboard'] = array(
			'title' => 'Dashboard',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_dashboard_overview_page',
			'access arguments' => array('vals access dashboard'),
			'description' => 'Administer page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
	// allows user to define create organisations, institutions etc.
	$items['dashboard/members'] = array(
			'title' => 'Administer',
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_administer_page',
			'access arguments' => array('vals admin register'), // This needs to be revisited
			'description' => 'Administer page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
	// Allows user to browse organisations registered in the system
	$items['organisations/browse'] = getOrganisationBrowseLink('organisations/browse');
	$items['dashboard/organisations/browse'] = getOrganisationBrowseLink('dashboard/organisations/browse');
	// Allows user to browse projects registered in the system
	$items['projects/browse'] = getProjectsBrowseLink('projects/browse');
	$items['dashboard/projects/browse'] = getProjectsBrowseLink('dashboard/projects/browse');
	
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

	$items['dasboard/proposals/browse'] = array(
			'title' => t('Proposals overview'),
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_browse_proposals',
			'access arguments' => array('access content'),//array('vals apply projects'), // Voor alle gebruikers (incl guests: 'use text format filtered_html'
			// uncomment the 2 lines below to restrict access to menu items based on role
			//'access callback' => 'vals_soc_user_role_check',
					// 			'access arguments' => array(1, array('Organisation Administrator','Student')),
			'description' => 'Proposal page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);

	//##### PROGRAM SETTINGS PAGES #######################
	$items['admin/config/vals_soc'] = array(
			'title' => 'VALS Summer of code',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_timeline_admin_overview_page',
			'access arguments' => array('access administration pages'),
			'description' => 'Allow Administration on the summer of code module',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
	$items['admin/config/vals_soc/program_settings'] = array(
			'title' => 'Program Settings',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_timeline_admin_program_page',
			'access arguments' => array('access administration pages'),
			'description' => 'Allow Administration on the summer of code module',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
	$items['admin/config/vals_soc/program_settings/timeline'] = array(
			'title' => 'Edit Timeline',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'drupal_get_form',
			'page arguments' => array('vals_soc_admin_timeline_form'),
			'access arguments' => array('access administration pages'),
			'description' => 'Edit the timeline and other settings.',
			'file' => 'vals_soc.forms.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
	$items['admin/config/vals_soc/program_settings/messages'] = array(
			'title' => 'Edit Messages',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'drupal_get_form',
			'page arguments' => array('vals_soc_admin_messages_form'),
			'access arguments' => array('access administration pages'),
			'description' => 'Edit the email messages that are sent to users.',
			'file' => 'vals_soc.forms.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
	return $items;
}

function getOrganisationBrowseLink($path){
	return  array(
			'title' => 'Organisations',
			'type' => MENU_CALLBACK,
			'page callback' => 'vals_soc_browse_organisations',
			'access callback' => 'vals_soc_access_check',
			'access arguments' =>  array($path),
			'description' => 'Organisations page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}

function getProjectsBrowseLink($path){
	return array(
			'title' => 'Projects',
			'type' => MENU_CALLBACK, // register link, but dont put it in the menu
			'page callback' => 'vals_soc_browse_projects',
			'access callback' => 'vals_soc_access_check',
			'access arguments' => array($path),
			'description' => 'Projects page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
}