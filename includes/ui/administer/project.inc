<?php
drupal_add_css(_VALS_SOC_PATH .'/includes/ui/tabs/tabs.css', array(
      'type' => 'file',
      'group' => CSS_THEME
    ));
drupal_add_js (_VALS_SOC_PATH .'/includes/ui/tabs/activatetabs.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/ajax.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/target_functions.js');

include(_VALS_SOC_ROOT.'/includes/functions/projects.php');
include(_VALS_SOC_ROOT.'/includes/classes/Project.php');
include(_VALS_SOC_ROOT.'/includes/functions/render_functions.php');

$owner_only = isset($owner_only) ? $owner_only : NULL;
?>
<div id='ajax_msg'></div>

<div id='admin_container' class='tabs_container'>
	<?php showProjectPage(FALSE, $owner_only);?>
</div>