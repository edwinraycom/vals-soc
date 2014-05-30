<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
module_load_include('php', 'vals_soc', 'includes/classes/Proposal');

switch ($_GET['action']){
	case 'proposal_page':
		//module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
		module_load_include('php', 'vals_soc', 'includes/functions/proposals');
		initBrowseProposalLayout();
	break;
	case 'list_proposals':
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
	case 'proposal_detail':
		$proposal_id=null;
		if(isset($_GET['proposal_id'])){
			$results = Project::getInstance()->getProjectById($_GET['proposal_id']);
			
			$proposalDetail = array();
			$proposalDetail['title'] = $results->title;
			$proposalDetail['description'] = $results->description;
			print json_encode($results);
		}
		else{
			echo "No Project identifier submitted!";
		}
	break;
	
	default: echo "No such action: ".$_GET['action'];
}