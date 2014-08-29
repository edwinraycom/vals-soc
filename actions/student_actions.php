<?php
include('include.php');
include(_VALS_SOC_ROOT.'/includes/classes/Project.php');//action:proposal,...
include(_VALS_SOC_ROOT.'/includes/classes/Institutes.php');
include(_VALS_SOC_ROOT.'/includes/classes/Organisations.php');
include(_VALS_SOC_ROOT.'/includes/classes/Proposal.php');

switch ($_GET['action']){
	//creates a first proposal for a project
	case 'proposal':
		if (!vals_soc_access_check('dashboard/projects/apply')) {
			echo errorDiv(t('You cannot apply for projects'));
			break;
		}
		$target = altSubValue( $_POST, 'target');
		$project_id = altSubValue( $_POST, 'id');
		$proposal_id = altSubValue( $_POST, 'proposalid', 0);
		if (! Users::isOfType(_STUDENT_TYPE, $GLOBALS['user']->uid)){
			if (_VALS_TEST_UI_ONLY){
				//TODO: this kind of testing should go soon
				echo "!! Since you are an admin, you can test a bit. We test with user 31 under the condition that _VALS_TEST_UI_ONLY is true.";
				$owner_id = 31;
			} else {
				echo errorDiv(t('Only students can submit proposals'));
				return;
			}
		} else {
			$owner_id = $GLOBALS['user']->uid;
		}
		$project = Project::getProjectById($project_id);
		$student_details = Users::getStudentDetails($owner_id);
		
		
		if ($student_details){
			$proposal = $proposal_id ? Proposal::getInstance()->getProposalById($proposal_id): null;
			echo "<div id='edit_proposal' class='edit_proposal' style='border-style: solid; border-color:	rgb(153,​ 217,​ 234);padding:10px;'>
					<h2>".tt('Create proposal for :"%1$s"',$project['title'])."</h2>";
			echo '<h3>'.t('Student details').'</h3>';
			echo "<div id='student_details' style='color:blue'>".
			sprintf('%1$s: %2$s<br/>%3$s: %4$s<br/>%5$s: %6$s<br/>%7$s: %8$s', t('Name'), $student_details->student_name,
					t('Email'), $student_details->student_mail, t('Institute'), $student_details->institute_name,
					t('First supervisor'), $student_details->supervisor_name)
			//"<br/>Group: ".$student_details->group_name.
			."</div><hr>";
			$form = drupal_get_form('vals_soc_proposal_form', $proposal, $target, $project_id);
			renderForm($form, $target);
			echo "</div>";
		} else {
			echo errorDiv(t('Not all details could be retrieved for you. You might not have been put in a student group. Contact your lecturer please.'));
		}
		break;
	case 'save':
		$id = altSubValue($_POST, 'id', '');
		$project_id = altSubValue($_POST, 'project_id', '');
		$project = Project::getProjectById($project_id);
		$properties = Proposal::filterPost($_POST);
		if (!$id){
			$id = $result = Proposal::insertProposal($properties, $project_id);
		} else {
			if (!Groups::isOwner('proposal', $id)){
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
		$project = Project::getProjectById($project_id);
		$properties = Proposal::filterPost($_POST);
		$properties['state'] = 'published';
		if (!$id){
			$result = Proposal::insertProposal($properties, $project_id);
		} else {
			if (!Groups::isOwner('proposal', $id)){
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