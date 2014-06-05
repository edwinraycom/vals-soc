<?php
// drupal_add_css(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/tabs.css');
// drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/activatetabs.js');
// drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/settings.js');
// drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/ajax.js');
// drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/target_functions.js');
// drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jquery-migrate-1.2.1.js');
// module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
// module_load_include('inc', 'vals_soc', 'includes/vals_soc.helper');
// module_load_include('php', 'vals_soc', 'includes/functions/administration');
// module_load_include('php', 'vals_soc', 'includes/classes/Users');
// module_load_include('inc', 'vals_soc', 'includes/module/ui/participant');
drupal_add_css(_VALS_SOC_PATH .'/includes/module/ui/tabs/tabs.css');
drupal_add_js (_VALS_SOC_PATH .'/includes/module/ui/tabs/activatetabs.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/settings.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/ajax.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/target_functions.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/jquery-migrate-1.2.1.js');
//print_r(get_included_files());
//include(_VALS_SOC_ROOT.'/includes/install/vals_soc.roles.inc');
include(_VALS_SOC_ROOT.'/includes/functions/administration.php');
include(_VALS_SOC_ROOT.'/includes/classes/AbstractEntity.php');
include(_VALS_SOC_ROOT.'/includes/classes/Users.php');
include(_VALS_SOC_ROOT.'/includes/classes/Groups.php');
include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');
//To test we switch here the user
//31 student stuutje, 30 tutor zelfstandig, 27 salamanca (org inst), 25 orgadmin, 1 admin
//$GLOBALS['user'] = user_load(30,  TRUE);
$role = getRole();
echo '<BR>In administer/blocklayout.inc: I am a '.$role. ' and my user is '. $GLOBALS['user']->uid;
echo "<div id='ajax_error'></div>";
echo "<div id='admin_container' class='tabs_container'>";

//     create_permissions();
showRoleDependentAdminPage($role);
echo "</div>";//end of admin_container