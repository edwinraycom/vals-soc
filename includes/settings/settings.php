<?php
define('_DEBUG', TRUE);
define('_VALS_SOC_PATH', drupal_get_path('module', 'vals_soc'));
define('_VALS_SOC_ROOT', DRUPAL_ROOT.'/'._VALS_SOC_PATH);
variable_set('configurable_timezones', 0);
variable_set('user_pictures', 0); //omit the user avatar picture
include_once(DRUPAL_ROOT.'/initial.php');
define('_VALS_SOC_FULL_URL', _VALS_SOC_URL.'/'._VALS_SOC_PATH);
define('_VALS_TEST_UI_ONLY', TRUE && _DEBUG);
define('_FULL_NAME_LENGTH', 50);

//Some convenient constants
//user types
define('_ADMINISTRATOR_TYPE', 'administrator');
define('_ORGADMIN_TYPE', 'organisation_admin');
define('_INSTADMIN_TYPE', 'institute_admin');
define('_SOC_TYPE', 'soc');
define('_STUDENT_TYPE', 'student');
define('_SUPERVISOR_TYPE', 'supervisor');
define('_MENTOR_TYPE', 'mentor');
define('_ANONYMOUS_TYPE', 'anonymous user');
define('_USER_TYPE', 'authenticated user');
//Grouping types
define('_STUDENT_GROUP', 'studentgroup');
define('_ORGANISATION_GROUP', 'organisation');
define('_INSTITUTE_GROUP', 'institute');