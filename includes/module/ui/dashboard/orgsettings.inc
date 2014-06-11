<?php
drupal_add_css(_VALS_SOC_PATH .'/includes/module/ui/tabs/tabs.css');
drupal_add_js (_VALS_SOC_PATH .'/includes/module/ui/tabs/activatetabs.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/settings.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/ajax.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/target_functions.js');
drupal_add_js (_VALS_SOC_PATH .'/includes/js/jquery-migrate-1.2.1.js');

include(_VALS_SOC_ROOT.'/includes/functions/administration.php');
// include(_VALS_SOC_ROOT.'/includes/classes/AbstractEntity.php');
include(_VALS_SOC_ROOT.'/includes/classes/Users.php');
// include(_VALS_SOC_ROOT.'/includes/classes/Groups.php');
include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');
function renderOrgSettings(){
	echo "<div id='admin_container' class='tabs_container'>";
	showOrganisationPage();
	echo "</div>";//end of admin_container
}
renderOrgSettings();