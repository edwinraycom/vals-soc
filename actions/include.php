<?php
define('DRUPAL_ROOT', realpath(getcwd().'/../../../../..'));
define('_VALS_SOC_ROOT', DRUPAL_ROOT.'/sites/all/modules/vals_soc');
$base_url = $_SERVER['REQUEST_SCHEME']. '://'.$_SERVER['HTTP_HOST'].'/vals'; //This seems to be necessary to get to the user object: see
//http://drupal.stackexchange.com/questions/76995/cant-access-global-user-object-after-drupal-bootstrap, May 2014
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);//Used to be DRUPAL_BOOTSTRAP_SESSION
include(_VALS_SOC_ROOT.'/includes/functions/ajax_functions.php');