<?php 
module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.access');

/**
 * Callback for the admin overview page
 * @return string
 */
function vals_soc_timeline_admin_overview_page() {
	return '<p><a href="./vals_soc/program_settings">'.t('Program settings').'</a></p>';
}

/**
 * Callback for the Program settings page
 * @return string
 */
function vals_soc_timeline_admin_program_page() {
    $version = get_vals_version();
	$txt = '<a href="./program_settings/timeline">'.t('Edit Timeline').'</a></p>';
	$txt .= '<a href="./program_settings/messages">'.t('Edit Messages').'</a></p>';
	return $txt;
}

function vals_soc_administer_page(){
    ob_start();
    module_load_include('inc', 'vals_soc', 'includes/module/ui/administer/blocklayout');
    return ob_get_clean();
}

function vals_soc_dashboard_overview_page(){
	$includeFile = null;
	if(hasRole(array('administrator'))){
		$includeFile = 'includes/module/ui/dashboard/adminlayout';
	}
	elseif(hasRole(array('organisation_admin'))){
		$includeFile = 'includes/module/ui/dashboard/orgadminlayout';
	}
	elseif(hasRole(array('mentor'))){
		$includeFile = 'includes/module/ui/dashboard/mentorlayout';
	}
	elseif(hasRole(array('institute_admin'))){ // may need to update this to separate page
		$includeFile = 'includes/module/ui/dashboard/orgadminlayout';
	}
	elseif(hasRole(array('supervisor'))){  // may need to update this to separate page
		$includeFile = 'includes/module/ui/dashboard/mentorlayout';
	}
	elseif(hasRole(array('student'))){
		$includeFile = 'includes/module/ui/dashboard/studentlayout';
	}
	ob_start();	
	module_load_include('inc', 'vals_soc', $includeFile);
	return ob_get_clean();
}

function vals_soc_orgsettings_page(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/dashboard/orgsettings');
	return ob_get_clean();	
}

function vals_soc_browse_projects(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/projects');
	return ob_get_clean();
}

function vals_soc_browse_organisations(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/organisations');
	return ob_get_clean();
}

function vals_soc_apply_projects(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/projects_apply');
	return ob_get_clean();
}

function vals_soc_browse_proposals(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/proposals_browse');
	return ob_get_clean();
}

