<?php
drupal_add_css(_VALS_SOC_PATH .'/includes/module/ui/tabs/tabs.css');
drupal_add_js (_VALS_SOC_PATH .'/includes/module/ui/tabs/activatetabs.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/ajax.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/target_functions.js');

include(_VALS_SOC_ROOT.'/includes/functions/projects.php');
include(_VALS_SOC_ROOT.'/includes/classes/Project.php');
include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');

?>
<div id='ajax_msg'></div>

<div id='admin_container' class='tabs_container'>
	<?php echo "hoera";//showProjectPage();?>
</div>