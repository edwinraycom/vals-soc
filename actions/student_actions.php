<?php
include('include.php');
// print_r(get_included_files());die('dat waren ze');
//This file is included as part of the bootstrap process as the handle_forms file includes it which is included itself
//automatically
// module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
// include(_VALS_SOC_ROOT.'/includes/vals_soc.helper.inc');
//include(_VALS_SOC_ROOT.'/includes/classes/AbstractEntity.php');
include(_VALS_SOC_ROOT.'/includes/classes/Users.php');
// include(_VALS_SOC_ROOT.'/includes/classes/Groups.php');
include(_VALS_SOC_ROOT.'/includes/classes/Project.php');//action:proposal,...
include(_VALS_SOC_ROOT.'/includes/classes/Proposal.php');//action:proposal,...

switch ($_GET['action']){
	case 'proposal':
		if (!vals_soc_access_check('dashboard/projects/apply')) {
			echo errorDiv(t('You cannot apply for projects'));
			break;
		}
		$target = altSubValue( $_POST, 'target');
		$project_id = altSubValue( $_POST, 'id');
		$proposal_id = altSubValue( $_POST, 'proposalid', 0);
		if (! Users::isOfType('student', $GLOBALS['user']->uid)){
			if (_VALS_TEST_UI_ONLY){
				echo "!! Since you are an admin, you can test a bit. We test with user 31 under the condition that _VALS_TEST_UI_ONLY is true.";
				$owner_id = 31;
			} else {
				echo errorDiv(t('Only students can submit proposals'));
				return;
			}
		} else {
			$owner_id = $GLOBALS['user']->uid;
		}
		$project = Project::getInstance()->getProjectById($project_id);
		echo "<h2>".t('Solution proposal for '.$project['title'])."</h2>";
		$student_details = Users::getStudentDetails($owner_id);
		if ($student_details){
			$proposal = $proposal_id ? Proposal::getInstance()->getProposalById($proposal_id): null;
			echo '<h3>'.t('Student details').'</h3>';
			echo "<div id='student_details'>".
			sprintf('%1$s: %2$s<br/>%3$s: %4$s<br/>%5$s: %6$s<br/>', t('Name'), $student_details->student_name,
					t('Email'), $student_details->student_mail, t('Institute'), $student_details->institute_name).
			"<br/>Group: ".$student_details->group_name.
			"<br/>First Supervisor: ".$student_details->supervisor_name.
			"</div>";
			$form = drupal_get_form('vals_soc_proposal_form', $proposal, $target, $project_id);
			renderForm($form, $target);
		} else {
			echo errorDiv(t('Not all details could be retrieved for you. You might not have been put in a student group. Contact your lecturer please.'));
		}
		break;
	case 'save':
		$id = altSubValue($_POST, 'id', '');
		$project_id = altSubValue($_POST, 'project_id', '');
		$project = Project::getInstance()->getProjectById($project_id);
		$properties = Proposal::filterPost($_POST);
		if (!$id){
			$id = $result = Proposal::insertProposal($properties, $project_id);
		} else {
			if (!Groups::isOwner('proposal', $id) && !Users::isAdmin()){
				drupal_set_message(t('You are not the owner of this proposal'), 'error');
				$result = null;
			} else {
				$result = Proposal::updateProposal($properties, $id);
			}
		}

		if ($result){
			echo json_encode(array(
					'result'=>'OK',
					'id' => $id,
					//'type'=> $type,
					'msg'=> 
						($id ? 
							tt('You succesfully changed your proposal for %1$s', $project['title']):
							tt('You succesfully added your proposal for %1$s', $project['title'])).
						(_DEBUG ? showDrupalMessages() : '')
				));
		} else {
			echo jsonBadResult();
		}
	break;
	case 'submit':
		$id = altSubValue($_POST, 'id', '');
		$project_id = altSubValue($_POST, 'project_id', '');
		$project = Project::getInstance()->getProjectById($project_id);
		$properties = Proposal::filterPost($_POST);
		$properties['state'] = 'published';
		if (!$id){
			$result = Proposal::insertProposal($properties, $project_id);
		} else {
			if (!Groups::isOwner('proposal', $id)&& !Users::isAdmin()){
				drupal_set_message(t('You are not the owner of this proposal'), 'error');
				$result = null;
			} else {
				$result = Proposal::updateProposal($properties, $id);
			}
		}
		
		if ($result){
			//TODO: notify mentor, supervisor
			echo json_encode(array(
					'result'=>'OK',
					'id' => $id,
					'msg'=>tt('You succesfully submitted your proposal for %1$s', $project['title']).
						(_DEBUG ? showDrupalMessages() : '')
			));
		} else {
			echo jsonBadResult();
		}
	
	
		break;
	default: echo "No such action: ".$_GET['action'];
	}