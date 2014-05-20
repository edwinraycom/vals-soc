<?php
define('DRUPAL_ROOT', realpath(getcwd().'/../../../../..'));
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);//Used to be DRUPAL_BOOTSTRAP_SESSION
module_load_include('php', 'vals_soc', 'includes/classes/Project');

switch ($_GET['action']){
	case 'list_projects':
		try{
			$tags=null;
			if(isset($_POST['tags'])){
				$tags = $_POST['tags'];
			}
			$organisation=null;
			if(isset($_POST['organisation'])){
				$organisation = $_POST['organisation'];
			}
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = Project::getInstance()->getProjectsRowCountBySearchCriteria(
					$tags, $organisation);
			$jTableResult['Records'] = Project::getInstance()->getProjectsBySearchCriteria($tags,
					$organisation, $_GET["jtSorting"], $_GET["jtStartIndex"], $_GET["jtPageSize"]);
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
	case 'project_detail':
		$project_id=null;
		if(isset($_GET['project_id'])){
			$results = Project::getInstance()->getProjectById($_GET['project_id']);
			$projectDetail = array();
			$projectDetail['title'] = $results->title;
			$projectDetail['description'] = $results->description;
			print json_encode($results);
		}
		else{
			echo "No Project identifier submitted!";
		}
	break;
	
	default: echo "No such action: ".$_GET['action'];
}