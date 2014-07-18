<?php 
module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.access');


/**
 * Callback for the Program settings page
 * @return string
 */
function vals_soc_admin_program_settings_page() {
    ob_start();
    module_load_include('inc', 'vals_soc', 'includes/module/ui/administer/program_settings');
    return ob_get_clean();
}

function vals_soc_administer_page(){
    ob_start();
    module_load_include('inc', 'vals_soc', 'includes/module/ui/administer/blocklayout');
    return ob_get_clean();
}

function vals_soc_administer_entity_page(){
	ob_start();
	$action = 'administer';
	include(_VALS_SOC_ROOT. '/includes/module/ui/administer/blocklayout.inc');
	return ob_get_clean();
}

function vals_soc_membersof_page(){
	ob_start();
	$action = 'membersof';
	include(_VALS_SOC_ROOT. '/includes/module/ui/administer/blocklayout.inc');
	return ob_get_clean();
}

function vals_soc_administer_institute_page(){
	ob_start();
	$action = 'administer';
	include(_VALS_SOC_ROOT. '/includes/module/ui/administer/blocklayout.inc');
	return ob_get_clean();
}

function vals_soc_members_page(){
	ob_start();
	$action = 'members';
	include(_VALS_SOC_ROOT. '/includes/module/ui/administer/blocklayout.inc');
	return ob_get_clean();
}

function vals_soc_administer_groupspage(){
	ob_start();
	$action = 'groups';
	include(_VALS_SOC_ROOT. '/includes/module/ui/administer/blocklayout.inc');
	//This does not work with variables being defined in this scope like action
// 	module_load_include('inc', 'vals_soc', 'includes/module/ui/administer/blocklayout');
	return ob_get_clean();
}

function vals_soc_administer_projects_page(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/administer/project');
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
		$includeFile = 'includes/module/ui/dashboard/instadminlayout';
	}
	elseif(hasRole(array('supervisor'))){  // may need to update this to separate page
		$includeFile = 'includes/module/ui/dashboard/supervisorlayout';
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

function vals_soc_browse_proposals(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/proposals_browse');
	return ob_get_clean();
}

function vals_soc_browse_my_proposals(){
	// vals_soc_apply_projects - this function I removed as it pointed to a n non existent .inc file.
	
	// - there were 2 links in vals_soc.menus"; which pointed to the same function
	/// 'vals_soc_browse_proposals()' Need to implement it or delete this & entry from vals_soc.menus
	return "TODO";
}

