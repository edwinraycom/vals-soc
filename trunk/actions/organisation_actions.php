<?php
define('DRUPAL_ROOT', realpath(getcwd().'/../../../../..'));
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);//Used to be DRUPAL_BOOTSTRAP_SESSION
module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
drupal_set_message(t('1 '));
switch ($_GET['action']){
	case 'list_organisations':
		try{
			$orgName=null;
			if(isset($_POST['oname'])){
				$orgName = $_POST['oname'];
			}

			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = Organisations::getInstance()->getOrganisationsRowCountBySearchCriteria($orgName);
			$jTableResult['Records'] = Organisations::getInstance()->getOrganisationsBySearchCriteria($orgName,
					 $_GET["jtSorting"], $_GET["jtStartIndex"], $_GET["jtPageSize"]);
			print json_encode($jTableResult);
		}
		catch(Exception $ex){
			//Return error message
			$jTableResult = array();
			$jTableResult['Result'] = "ERROR";
			$jTableResult['Message'] = $ex->getMessage();
			print json_encode($jTableResult);
		}
	break;
	default: echo "No such action: ".$_GET['action'];
}