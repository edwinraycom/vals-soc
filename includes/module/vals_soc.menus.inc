<?php 

function vals_soc_menu_handler(){
	$items['projects/browse'] = array(
			'title' => 'Projects',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_browse_projects',
			'access arguments' => array('use text format filtered_html'), // Voor alle gebruikers (incl guests: 'use text format filtered_html'
			// uncomment the 2 lines below to restrict access to menu items based on role
			//'access callback' => 'vals_soc_user_role_check',
			//'access arguments' => array(1, array('Organisation Administrator','Student')),
			'description' => 'Projects page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
	$items['organisations/browse'] = array(
			'title' => 'Organisations',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_browse_organisations',
			'access arguments' => array('access content'), // was 'access_contents'
			'description' => 'Organisations page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
	$items['administer/members'] = array(
			'title' => 'Administer',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_administer_page',
			'access arguments' => array('vals admin register'), // This needs to be revisited
			'description' => 'Administer page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
// 	$items['projects/apply'] = array(
// 			'title' => t('Apply for Projects'),
// 			'type' => MENU_NORMAL_ITEM,
// 			'page callback' => 'vals_soc_apply_projects',
// 			'access arguments' => array('vals apply projects'), // Voor alle gebruikers (incl guests: 'use text format filtered_html'
// 			// uncomment the 2 lines below to restrict access to menu items based on role
// 			//'access callback' => 'vals_soc_user_role_check',
// // 			'access arguments' => array(1, array('Organisation Administrator','Student')),
// 			'description' => 'Proposal page',
// 			'file' => 'vals_soc.pages.inc',
// 			'file path' => _VALS_SOC_PATH . '/includes/module/',
// 	);
	$items['proposals/browse'] = array(
			'title' => t('Proposals overview'),
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_browse_proposals',
			'access arguments' => array('vals apply projects'), // Voor alle gebruikers (incl guests: 'use text format filtered_html'
			// uncomment the 2 lines below to restrict access to menu items based on role
			//'access callback' => 'vals_soc_user_role_check',
					// 			'access arguments' => array(1, array('Organisation Administrator','Student')),
			'description' => 'Proposal page',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
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
    $items['admin/config/vals_soc/administer'] = array(
			'title' => t('Add an organisation, institute or group'),
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_administer_page',
			'access arguments' => array('access administration pages'),
			'description' => 'Allow Administration on institute etc.',
			'file' => 'vals_soc.pages.inc',
			'file path' => _VALS_SOC_PATH . '/includes/module/',
	);
	return $items;
}