<?php

/**
 * @file
 * The PHP page that serves all page requests on a Drupal installation.
 *
 * The routines here dispatch control to the appropriate handler, which then
 * prints the appropriate page.
 *
 * All Drupal code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 */

/**
 * Root directory of Drupal installation.
 */
define('DRUPAL_ROOT', getcwd());
/*For some reason the server could not derive well the scheme of the url and returned something like ://<host>
 * in Ubuntu, giving such a malformed base url and resulting in an identical path to the base_url and thereby
 * an empty base_root. It is not sure whether this exists also in non-ajax calls, but it seemed better to derive the
 * very basic globals the same for both ajax and non-ajax. So we derive the scheme based on the HTTPS server var and
 * our own path derivation in initial.php.
 * 
 *  COPY THIS FILE TO THE ROOT OF THE INSTALLATION, REPLACING THE DRUPAL INDEX!
 */
include(DRUPAL_ROOT.'/initial.php');//Needed to derive the _VALS_SOC_URL which will be '' or '/vals'
$scheme = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https': 'http');
$base_url = $scheme. '://'.$_SERVER['HTTP_HOST']._VALS_SOC_URL;
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$vals_soc_pretend_possible = defined('_DEBUG') && _DEBUG && (Users::isAdmin() || defined('_VALS_SOC_TEST_ENV') && _VALS_SOC_TEST_ENV);
if (Users::isAdmin() || $vals_soc_pretend_possible){
	list($u, $o_state) = pretendUser();
}
menu_execute_active_handler();
if ($vals_soc_pretend_possible){
	restoreUser($u, $o_state);
}