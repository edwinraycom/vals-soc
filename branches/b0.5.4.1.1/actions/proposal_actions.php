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
	case 'proposal_form':
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
	
		if (!$project){
			echo errorDiv(t('This project could not be found'));
			return;
		}
		if ($student_details){
			if (!$proposal_id){
				$proposals = Proposal::getInstance()->getProposalsPerProject($project_id, Users::getMyId());
				if (count($proposals) > 1){
					//This case should not occur or very little, once we catch the case of having already a version
					echo '<span style="color:orange;">'.
							t('Be aware that you have more than one proposal for this project. You better delete one of them.').
							'</span>';
				}
				$proposal = $proposals ? $proposals[0] : null;
			} else {
				$proposal = $proposal_id ? Proposal::getInstance()->getProposalById($proposal_id): null;
			}
	
				
			echo "<div id='edit_proposal' class='edit_proposal' style='border-style: solid;border-width: 1px; border-color:	rgb(153,​ 217,​ 234);padding:10px;'>
			<h2>".tt('Create proposal for :"%1$s"',$project['title'])."</h2>";
			echo '<h3>'.t('Student details').'</h3>';
			echo "<div id='student_details' style='color:blue'>".
					sprintf('%1$s: %2$s<br/>%3$s: %4$s<br/>%5$s: %6$s<br/>%7$s: %8$s', t('Name'), $student_details->student_name,
							t('Email'), $student_details->student_mail, t('Institute'), $student_details->institute_name,
							t('First supervisor'), $student_details->supervisor_name)
							//"<br/>Group: ".$student_details->group_name.
			."</div><hr>";
			$possible_supervisors = Project::getInterestedSupervisors($project_id);
			$form = drupal_get_form('vals_soc_proposal_form', $proposal, $target, $project_id, $possible_supervisors);
			renderForm($form, $target);
			echo "</div>";
		} else {
			echo errorDiv(t('Not all details could be retrieved for you. You might not have been put in a student group. Contact your lecturer please.'));
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
		global $user;
		$proposal_id = getRequestVar('proposal_id', null);
		//TODO bepaal hier 
		if ($proposal_id){
			if (! ($browse_proposals || Groups::isOwner(_PROPOSAL_OBJ, $proposal_id) )){
				jsonBadResult(t('You can only see your own proposals!'));
			} else {
				$proposal = Proposal::getInstance()->getProposalById($proposal_id, true);
				$project_id = $proposal->pid;
				// is this person the project owner?
				$is_project_owner = Groups::isOwner(_PROJECT_OBJ, $project_id);
				$proposal->is_project_owner = $is_project_owner;
				$proposal->is_project_mentor = true;
				if($user->uid != $proposal->mentor_id){
					$proposal->is_project_mentor = false;
				}
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
			if (! ($browse_proposals || Groups::isOwner(_PROPOSAL_OBJ, $proposal_id) )){
				jsonBadResult(t('You can only see your own proposals!'));
			} else {
				$target = altSubValue($_POST, 'target');
				$proposal = Proposal::getInstance()->getProposalById($proposal_id, true);
				$project_id = $proposal->pid;
				$project = Project::getProjectById($project_id);
				$possible_supervisors = Project::getInterestedSupervisors($project_id);
				$form = drupal_get_form('vals_soc_proposal_form', $proposal, $target, $project_id, $possible_supervisors);
				if ($form){
					$prefix_form = "<div>".tt('<b>Project</b> <i>%1$s</i>',$project['title'])."</div>";
					if ($result_format == 'json') {
						jsonGoodResult($prefix_form.renderForm($form, $target, true));
					} else {
						echo $prefix_form;
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
		$target = getRequestVar('target', 'post', 'our_content');
		if($proposal_id){
			$is_modal = ($target !== 'our_content');
			//we need the container where the result is bad and we show an error msg
			$container =  $is_modal ? 'admin_container' : 'our_content';
			$before = 'toc' ;
			$args = array('id' => $proposal_id, 'before'=> $before, 'target'=> $container, 'replace_target'=> true);
			$proposal_nr = Proposal::getInstance()->getProposalById($proposal_id);
			if (!$proposal_nr){
				jsonBadResult(t('This proposal was already deleted!'), $args);
				return;
			}
			$title = altPropertyValue($proposal_nr, 'title');
			$state = altPropertyValue($proposal_nr, 'state');
			if (! Groups::isOwner(_PROPOSAL_OBJ, $proposal_id)){
				jsonBadResult(t('You can only delete your own proposals!'), $args);
			} elseif ($state == 'published') {
				jsonBadResult(t('We could not remove your proposal: It has already been published.'), $args);
			} else {
				$num_deleted = db_delete(tableName(_PROPOSAL_OBJ))
					->condition(AbstractEntity::keyField(_PROPOSAL_OBJ), $proposal_id)
					->execute();
				if ($num_deleted){
					// junk the proposal comments too
					ThreadedComments::getInstance()->removethreadsForEntity($proposal_id, _PROPOSAL_OBJ);
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
	case 'save':
		$id = altSubValue($_POST, 'id', '');
		$project_id = altSubValue($_POST, 'project_id', '');
		$project = Project::getProjectById($project_id);
		$properties = Proposal::filterPost($_POST);
		if (!$id){
			$new = TRUE;
			$id = $result = Proposal::insertProposal($properties, $project_id);
		} else {
			$new = FALSE;
			if (!Groups::isOwner(_PROPOSAL_OBJ, $id)){
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
					($new ?
							tt('You succesfully saved a draft of your proposal for %1$s', $project['title']):
							tt('You succesfully changed the draft of your proposal for %1$s', $project['title'])
					).
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
		$target = altSubValue($_POST, 'target', '');
		$properties = Proposal::filterPost($_POST);
		$properties['state'] = 'published';
		//If there was no supervisor chosen, at least maintain the orginal one, rather than leave it orphaned
		$original_supervisor = altSubValue($_POST, 'original_supervisor_id', '');
		if($properties['supervisor_id'] == 0 && isset($original_supervisor)){
			$properties['supervisor_id'] = $original_supervisor;
		}
		
		if (!$id){
			$result = $id = Proposal::insertProposal($properties, $project_id);
		} else {
			if (!Groups::isOwner(_PROPOSAL_OBJ, $id)){
				drupal_set_message(t('You are not the owner of this proposal'), 'error');
				$result = null;
			} else {
				$result = Proposal::updateProposal($properties, $id);
			}
		}
		if ($result){
			// uncomment below to send out emails to mentor/supervisor once new proposal published
			// get either the existing proposal key
			// or the newly inserted proposal key
			if(is_bool($result)){
				//already existed
				$existed = true;
				$key = $id;
			}
			else{
				/// newly inserted
				$existed = false;
				$key = $result;
			}
			try {
				$props = Proposal::getInstance()->getProposalById($key, true);
				module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');
				notify_mentor_and_supervisor_of_proposal_update($props, $existed);
			} catch (Exception $e) {
				// Logged higher up or log this here somehow? TODO
			}
			echo json_encode(array(
					'result'=>'OK',
					'id' => $id,
					'target' => $target,
					'msg'=>tt('You succesfully submitted your proposal for %1$s', $project['title']).
					(_DEBUG ? showDrupalMessages() : '')
			));
		} else {
			echo jsonBadResult();
		}
	
	
		break;
	case 'view':
		$proposal_id = getRequestVar('id', 'post', 0);
		$target = getRequestVar('target', 'post', 'admin_container');
		if($proposal_id){
			//$is_modal = ($target !== 'our_content');
			//this is the case where the result is bad and we show an error msg
			//$container =  $is_modal ? 'modal-content' : 'our_content';
			//$before = 'toc' ;
			//$args = array('id' => $proposal_id, 'before'=> $before, 'target'=> $container, 'replace_target'=> true);
			$proposal = Proposal::getInstance()->getProposalById($proposal_id, TRUE);
			if (!$proposal){
				echo errorDiv(t('This proposal does not seem to exist!'));
				return;
			}
			if (Users::isStudent() && ! Groups::isOwner(_PROPOSAL_OBJ, $proposal_id)){
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