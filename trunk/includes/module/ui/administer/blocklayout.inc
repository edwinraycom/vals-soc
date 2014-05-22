<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/tabs.css');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/activatetabs.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/ajax.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/target_functions.js');
module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
module_load_include('inc', 'vals_soc', 'includes/vals_soc.helper');
module_load_include('php', 'vals_soc', 'includes/functions/administration');
module_load_include('php', 'vals_soc', 'includes/classes/Participants');
module_load_include('inc', 'vals_soc', 'includes/module/ui/participant');

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