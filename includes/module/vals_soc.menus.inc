<?php 

function vals_soc_menu_handler(){
	$items['projects/browse'] = array(
			'title' => 'Projects',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_browse_projects',
			'access arguments' => array('authenticated user'), // This needs to be revisited
			'description' => 'Projects page',
			'file' => 'vals_soc.pages.inc',
			'file path' => drupal_get_path('module', 'vals_soc') . '/includes/module/',
	);	
	
	$items['admin/config/vals_soc'] = array(
			'title' => 'VALS Summer of code',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_timeline_admin_overview_page',
			'access arguments' => array('access administration pages'),
			'description' => 'Allow Administration on the summer of code module',
			'file' => 'vals_soc.pages.inc',
			'file path' => drupal_get_path('module', 'vals_soc') . '/includes/module/',
	);
	
	$items['admin/config/vals_soc/program_settings'] = array(
			'title' => 'Program Settings',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_timeline_admin_program_page',
			'access arguments' => array('access administration pages'),
			'description' => 'Allow Administration on the summer of code module',
			'file' => 'vals_soc.pages.inc',
			'file path' => drupal_get_path('module', 'vals_soc') . '/includes/module/',
	);
	
	$items['admin/config/vals_soc/program_settings/timeline'] = array(
			'title' => 'Edit Timeline',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'drupal_get_form',
			'page arguments' => array('vals_soc_admin_timeline_form'),
			'access arguments' => array('access administration pages'),
			'description' => 'Edit the timeline and other settings.',
			'file' => 'vals_soc.forms.inc',
			'file path' => drupal_get_path('module', 'vals_soc') . '/includes/module/',
	);
	
	$items['admin/config/vals_soc/program_settings/messages'] = array(
			'title' => 'Edit Messages',
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'drupal_get_form',
			'page arguments' => array('vals_soc_admin_messages_form'),
			'access arguments' => array('access administration pages'),
			'description' => 'Edit the email messages that are sent to users.',
			'file' => 'vals_soc.forms.inc',
			'file path' => drupal_get_path('module', 'vals_soc') . '/includes/module/',
	);
    $items['admin/config/vals_soc/administer'] = array(
			'title' => t('Add an organisation, institute or group'),
			'type' => MENU_NORMAL_ITEM,
			'page callback' => 'vals_soc_administer_page',
			'access arguments' => array('access administration pages'),
			'description' => 'Allow Administration on institute etc.',
			'file' => 'vals_soc.pages.inc',
			'file path' => drupal_get_path('module', 'vals_soc') . '/includes/module/',
	);
//	$items['admin/config/vals_soc/administer/add/organisation'] = array(
//            'title' => t('Add your organisation'),
//            'page callback' => 'drupal_get_form',
//            'page arguments' => array('vals_soc_organisation_form'),
//            'access arguments' => array('access content'),
//            'description' => t('Add your organisation'),
//            'type' => MENU_CALLBACK,
//    );
	return $items;
}