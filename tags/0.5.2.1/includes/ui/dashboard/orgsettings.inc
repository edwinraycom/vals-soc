<?php
drupal_add_css(_VALS_SOC_PATH .'/includes/ui/tabs/tabs.css', array(
      'type' => 'file',
      'group' => CSS_THEME
    ));
drupal_add_js (_VALS_SOC_PATH .'/includes/ui/tabs/activatetabs.js');
// drupal_add_js (_VALS_SOC_PATH .'/includes/js/settings.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/ajax.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/target_functions.js');
// drupal_add_js (_VALS_SOC_PATH .'/includes/js/jquery-migrate-1.2.1.js');

include(_VALS_SOC_ROOT.'/includes/functions/administration.php');
include(_VALS_SOC_ROOT.'/includes/functions/render_functions.php');
function renderOrgSettings(){
	echo "<div id='admin_container' class='tabs_container'>";
	showOrganisationPage();
	echo "</div>";//end of admin_container
}
renderOrgSettings();