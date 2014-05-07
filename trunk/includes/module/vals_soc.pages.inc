<?php 
module_load_include('inc', 'vals_soc', 'includes/vals_soc.helper');

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
    include('admin.php');
    return ob_get_clean();
}

function vals_soc_browse_projects(){
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/projects');
	return ob_get_clean();
}
