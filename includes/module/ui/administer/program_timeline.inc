<?php
drupal_add_css(_VALS_SOC_PATH .'/includes/module/ui/tabs/tabs.css');
drupal_add_js (_VALS_SOC_PATH .'/includes/module/ui/tabs/activatetabs.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/settings.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/ajax.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/target_functions.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/jquery-migrate-1.2.1.js');

include(_VALS_SOC_ROOT.'/includes/classes/ProgramTimeline.php');
include(_VALS_SOC_ROOT.'/includes/functions/timeline_functions.php');

$role = getRole();
?>
<div id='ajax_error'></div>

<div id='admin_container' class='tabs_container'>
	<?php showProgramTimelinePage($role);?>
</div>
