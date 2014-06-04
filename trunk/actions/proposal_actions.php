<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
module_load_include('php', 'vals_soc', 'includes/classes/Groups');
module_load_include('php', 'vals_soc', 'includes/classes/Proposal');

switch ($_GET['action']){
	case 'proposal_page':
		//module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
		module_load_include('php', 'vals_soc', 'includes/functions/proposals');
		initBrowseProposalsLayout();
	break;
	case 'list_proposals':
		try{
		$student=null;
			if(isset($_POST['student'])){
				$student = $_POST['student'];
			}
			$institute=null;
			if(isset($_POST['institute'])){
				$institute = $_POST['institute'];
			}
			$organisation=null;
			if(isset($_POST['organisation'])){
				$organisation = $_POST['organisation'];
			}
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = Proposal::getInstance()->getProposalsRowCountBySearchCriteria(
					$student, $institute, $organisation);
			$jTableResult['Records'] = Proposal::getInstance()->getProposalsBySearchCriteria(
					$student, $institute, $organisation, $_GET["jtSorting"], $_GET["jtStartIndex"],
					$_GET["jtPageSize"]);
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
			$results = Proposal::getInstance()->getProposalById($_GET['proposal_id']);
			die(gettype($results).print_r($results,1));
			$proposalDetail = array();
			$proposalDetail['title'] = $results->title;
			$proposalDetail['description'] = $results->description;
			print json_encode($results);
		}
		else{
			echo json_encode(array('result' =>'error', 'error' => t('No Project identifier submitted!')));
		}
	break;
	
	default: echo "No such action: ".$_GET['action'];
}