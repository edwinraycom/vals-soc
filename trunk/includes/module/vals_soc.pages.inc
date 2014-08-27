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

function vals_soc_administer_entity_page($action='administer'){
	ob_start();
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
	if(hasRole(array(_ADMINISTRATOR_TYPE))){
		$includeFile = 'includes/module/ui/dashboard/adminlayout';
	}
	elseif(hasRole(array(_ORGADMIN_TYPE))){
		$includeFile = 'includes/module/ui/dashboard/orgadminlayout';
	}
	elseif(hasRole(array(_MENTOR_TYPE))){
		$includeFile = 'includes/module/ui/dashboard/mentorlayout';
	}
	elseif(hasRole(array(_INSTADMIN_TYPE))){ // may need to update this to separate page
		$includeFile = 'includes/module/ui/dashboard/instadminlayout';
	}
	elseif(hasRole(array(_SUPERVISOR_TYPE))){  // may need to update this to separate page
		$includeFile = 'includes/module/ui/dashboard/supervisorlayout';
	}
	elseif(hasRole(array(_STUDENT_TYPE))){
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

function vals_soc_browse_institutes(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/institutes');
	return ob_get_clean();
}

function vals_soc_browse_proposals(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/proposals_browse');
	return ob_get_clean();
}

function vals_soc_browse_my_proposals(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/administer/proposals');
	return ob_get_clean();
}