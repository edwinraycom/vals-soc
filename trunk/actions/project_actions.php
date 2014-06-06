<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
module_load_include('php', 'vals_soc', 'includes/classes/AbstractEntity');
module_load_include('php', 'vals_soc', 'includes/classes/Groups');
module_load_include('php', 'vals_soc', 'includes/classes/Project');
module_load_include('php', 'vals_soc', 'includes/functions/projects');
module_load_include('php', 'vals_soc', 'includes/functions/ajax_functions');

switch ($_GET['action']){
	case 'project_page':
		module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
		module_load_include('php', 'vals_soc', 'includes/functions/projects');
		initBrowseProjectLayout();
	break;
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
			try {
				$project = Project::getInstance()->getProjectById($_GET['project_id']);
				jsonGoodResult($project);
			} catch (Exception $e){
				jsonBadResult($e->getMessage());
			}
		}
		else{
			jsonBadResult( t("No Project identifier submitted!"));
		}
	break;
	default: echo "No such action: ".$_GET['action'];
}