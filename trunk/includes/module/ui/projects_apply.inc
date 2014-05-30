<?php
module_load_include('php', 'vals_soc', 'includes/classes/Project');
module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
drupal_add_library('system','ui.widget');
// drupal_add_library('system','ui.dialog');
//die('ff checken '.drupal_get_path('module', 'vals_soc'). ' en '.DRUPAL_ROOT);
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/settings.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/ajax.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/target_functions.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jquery-migrate-1.2.1.js');

//Find out what really necessary and what not
drupal_add_css(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/tabs.css');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/activatetabs.js');

// module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
// module_load_include('inc', 'vals_soc', 'includes/vals_soc.helper');
module_load_include('php', 'vals_soc', 'includes/functions/administration');
module_load_include('php', 'vals_soc', 'includes/classes/Participants');
module_load_include('inc', 'vals_soc', 'includes/module/ui/participant');
// drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jtable/jquery.jtable.js');
// drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/js/jtable/jtable.css');
// ctools_include('modal');
// ctools_modal_add_js();

$projects = Project::getInstance()->getProjectsBySearchCriteria('', 0, 'title', 1, 10);
$i = 0;
echo "<div id='testing'>";
foreach ($projects as $project){
	if ($i > 5) break;
	echo t('Apply for this project?')."<button onclick=\"ajaxCall('student', 'proposal', {id: ".
	$project->pid."}, 'testing');\">Write Proposal</button>";
	echo($project->title);echo "<BR/>";
	$i++;
}
echo "</div>";