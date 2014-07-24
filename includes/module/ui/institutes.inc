<?php
// module_load_include('php', 'vals_soc', 'includes/classes/AbstractEntity');
// module_load_include('php', 'vals_soc', 'includes/classes/Groups');
module_load_include('php', 'vals_soc', 'includes/classes/Institutes');
module_load_include('php', 'vals_soc', 'includes/functions/institutes');
drupal_add_library('system','ui.widget');
drupal_add_library('system','ui.dialog');
// drupal_add_js(_VALS_SOC_PATH .'/includes/js/settings.js');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/ajax.js');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/target_functions.js');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/render_functions.js');
// drupal_add_js(_VALS_SOC_PATH .'/includes/js/jquery-migrate-1.2.1.js');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/jtable/jquery.jtable.js');
drupal_add_css(_VALS_SOC_PATH . '/includes/js/jtable/jtable.css');
ctools_include('modal');
ctools_modal_add_js();
$inst_id = getRequestVar('instid', '', 'get');
initBrowseInstitutesLayout($inst_id);