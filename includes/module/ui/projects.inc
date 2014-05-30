<?php
module_load_include('php', 'vals_soc', 'includes/classes/Project');
module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
module_load_include('php', 'vals_soc', 'includes/functions/projects');
drupal_add_library('system','ui.widget');
drupal_add_library('system','ui.dialog');
drupal_add_library('system','ui.icon');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/settings.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/ajax.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/target_functions.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jquery-migrate-1.2.1.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jtable/jquery.jtable.js');
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/js/jtable/jtable.css');
ctools_include('modal');
ctools_modal_add_js();
initBrowseProjectLayout('content');