<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
include(_VALS_SOC_ROOT.'/includes/classes/Institutes.php');
include(_VALS_SOC_ROOT.'/includes/classes/Organisations.php');
include(_VALS_SOC_ROOT.'/includes/classes/Proposal.php');
include(_VALS_SOC_ROOT.'/includes/classes/Project.php');
module_load_include('php', 'vals_soc', 'includes/classes/ThreadedComments');
module_load_include('php', 'vals_soc', 'includes/functions/proposals');

$apply_proposals = vals_soc_access_check('dashboard/projects/apply') ? 1 : 0;
$browse_proposals = vals_soc_access_check('dashboard/proposals/browse') ? 1 : 0;
$is_student = (Users::isOfType(_STUDENT_TYPE));

switch ($_GET['action']){
	case 'proposal_page':
		initBrowseProposalsLayout();
	break;
	case 'myproposal_page':
		echo "<div id='admin_container' class='tabs_container'>";
		echo showMyProposalPage();
		echo "</div>";
		// showMyProposalPage();
	break;
	case 'render_proposals_for_id':
		if(isset($_POST['id']) && $_POST['id']){
			echo showProposalsForProject($_POST['id']);
		}else{
			echo "Unable to find proposals without project identifier";
		}
	break;
	case 'list_proposals':
		try{
			$student=null;
			if(isset($_POST['student']) && $_POST['student']){
				$student = $_POST['student'];
				
				if ($is_student && $student != Users::getMyId()){
					throw new Exception((t('You can only see your own proposals!')));
				}
			} else {
				if ($is_student){
					throw new Exception((t('You can only see your own proposals!')));
				}
			}
			$institute=null;
			if(isset($_POST['institute']) && $_POST['institute']){
				$institute = $_POST['institute'];
			}
			$organisation=null;
			if(isset($_POST['organisation']) && $_POST['organisation']){
				$organisation = $_POST['organisation'];
			}
			$project=null;
			if(isset($_POST['project']) && $_POST['project']){
				$project = $_POST['project'];
			}
			//Return result to jTable
			$cnt = Proposal::getInstance()->getProposalsRowCountBySearchCriteria(
					$student, $institute, $organisation, $project);
			$recs = $cnt ? 
						Proposal::getInstance()->getProposalsBySearchCriteria(
							$student, $institute, $organisation, $project, $_GET["jtSorting"], $_GET["jtStartIndex"],
							$_GET["jtPageSize"]) :
						array();
			
			jsonGoodResultJT($recs, $cnt);
		}
		catch(Exception $ex){
			//Return error message
			jsonBadResultJT($ex->getMessage());
		}
	break;
	case 'proposal_detail':
		$proposal_id = getRequestVar('proposal_id', null);
		//TODO bepaal hier 
		if ($proposal_id){
			if (! ($browse_proposals || Groups::isOwner('proposal', $proposal_id) )){
				jsonBadResult(t('You can only see your own proposals!'));
			} else {
				$proposal = Proposal::getInstance()->getProposalById($proposal_id, true);
				jsonGoodResult($proposal);
			}
		} else {
			jsonBadResult(t('No proposal identifier submitted!'));
		}
	break;
	case 'edit':
		$proposal_id = getRequestVar('proposal_id', null, 'post');
		$result_format = getRequestVar('format', 'json', 'post');
		if($proposal_id){
			if (! ($browse_proposals || Groups::isOwner('proposal', $proposal_id) )){
				jsonBadResult(t('You can only see your own proposals!'));
			} else {
				$target = altSubValue($_POST, 'target');
				$proposal = Proposal::getInstance()->getProposalById($proposal_id, true);
				
				$form = drupal_get_form('vals_soc_proposal_form', $proposal, $target);
				if ($form){
					if ($result_format == 'json') {
						jsonGoodResult(renderForm($form, $target, true));//, $proposal, $target
					} else {
						renderForm($form, $target);
					}
				} else {
					if ($result_format == 'json') {
						jsonBadResult();
					} else {
						echo errorDiv(getDrupalMessages('error', true));
					}
				}
			}
		} else{
			jsonBadResult(t('No proposal identifier submitted!'));
		}
	break;
	case 'delete':
		$proposal_id = getRequestVar('proposal_id', 'post', null);
		$target = getRequestVar('target', 'post', 'content');
		if($proposal_id){
			$is_modal = ($target !== 'content');
			//this is the case where the result is bad and we show an error msg
			$container =  $is_modal ? 'modal-content' : 'content';
			$before = 'toc' ;
			$args = array('id' => $proposal_id, 'before'=> $before, 'target'=> $container, 'replace_target'=> true);
			$proposal_nr = Proposal::getInstance()->getProposalById($proposal_id);
			if (!$proposal_nr){
				jsonBadResult(t('This proposal was already deleted!'), $args);
				return;
			}
			$title = altPropertyValue($proposal_nr, 'title');
			if (! Groups::isOwner('proposal', $proposal_id)){
				jsonBadResult(t('You can only delete your own proposals!'), $args);
			} else {
				$num_deleted = db_delete(tableName('proposal'))
					->condition(AbstractEntity::keyField('proposal'), $proposal_id)
					->execute();
				if ($num_deleted){
					// junk the proposal comments too
					ThreadedComments::getInstance()->removethreadsForEntity($proposal_id, 'proposal');
					$args['before'] = '';
					jsonGoodResult(TRUE, tt('You have removed the proposal %1$s', $title), $args);
				} else {
					jsonBadResult(t('We could not remove your proposal'), $args);
				}
			}
		} else{
			jsonBadResult(t('No proposal identifier submitted!'), $args);
		}
	break;
	case 'view':
		$proposal_id = getRequestVar('id', 'post', 0);
		$target = getRequestVar('target', 'post', 'admin_content');
		if($proposal_id){
			//$is_modal = ($target !== 'content');
			//this is the case where the result is bad and we show an error msg
			//$container =  $is_modal ? 'modal-content' : 'content';
			//$before = 'toc' ;
			//$args = array('id' => $proposal_id, 'before'=> $before, 'target'=> $container, 'replace_target'=> true);
			$proposal = Proposal::getInstance()->getProposalById($proposal_id, TRUE);
			if (!$proposal){
				echo errorDiv(t('This proposal does not seem to exist!'));
				return;
			}
			if (Users::isStudent() && ! Groups::isOwner('proposal', $proposal_id)){
				echo errorDiv(t('You can only view your own proposals!'));
			} else {
				//TODO: find out whether we use the proposal view only in the my proposals and if not whether this 
				//matters: non owners have no right to delete for example and so no reason to do a followup action
				echo renderProposal($proposal, $target, 'myproposal_page');
			}
		} else {
			echo errorDiv(t('No proposal identifier submitted!'));
		}
	break;
	case 'show':
		// THIS IS A PLACEHOLDER
	break;
	default: echo "No such action: ".$_GET['action'];
}