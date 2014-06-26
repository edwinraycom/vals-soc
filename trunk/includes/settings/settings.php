<?php
define('_DEBUG', TRUE);
define('_VALS_SOC_PATH', drupal_get_path('module', 'vals_soc'));
define('_VALS_SOC_ROOT', DRUPAL_ROOT.'/'._VALS_SOC_PATH);
variable_set('configurable_timezones', 0);
variable_set('user_pictures', 0); //omit the user avatar picture
include_once(DRUPAL_ROOT.'/initial.php');