<?php
drupal_add_css(_VALS_SOC_PATH .'/includes/ui/tabs/tabs.css', array(
      'type' => 'file',
      'group' => CSS_THEME
    ));
drupal_add_js (_VALS_SOC_PATH .'/includes/ui/tabs/activatetabs.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/ajax.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/target_functions.js');
drupal_add_library('system','ui');

include(_VALS_SOC_ROOT.'/includes/functions/administration.php');
include(_VALS_SOC_ROOT.'/includes/functions/render_functions.php');
//To test we switch here the user
//31 student stuutje, 30 tutor zelfstandig, 27 salamanca (org inst), 25 orgadmin, 1 admin
//$GLOBALS['user'] = user_load(30,  TRUE);
$role = getRole();
$action = isset($action) ? $action : 'administer';?>
<div id='ajax_msg'></div>

<div id='admin_container' class='tabs_container'>
	<?php showRoleDependentAdminPage($role, $action, false);?>
</div>