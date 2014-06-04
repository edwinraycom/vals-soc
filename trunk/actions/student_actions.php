<?php
include('include.php');
// print_r(get_included_files());die('dat waren ze');
//This file is included as part of the bootstrap process as the handle_forms file includes it which is included itself
//automatically
// module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
// include(_VALS_SOC_ROOT.'/includes/vals_soc.helper.inc');

include(_VALS_SOC_ROOT.'/includes/classes/Users.php');
include(_VALS_SOC_ROOT.'/includes/classes/Groups.php');
include(_VALS_SOC_ROOT.'/includes/classes/Project.php');//action:proposal,...
include(_VALS_SOC_ROOT.'/includes/classes/Proposal.php');//action:proposal,...

switch ($_GET['action']){
	case 'proposal':
		$target = altSubValue( $_POST, 'target');
		$project_id = altSubValue( $_POST, 'id');
		$proposal_id = altSubValue( $_POST, 'proposalid');
		if (! Users::isOfType('student', $GLOBALS['user']->uid)){
			echo "Since you are an admin, you can test a bit";
			$owner_id = 31;
		} else {
			$owner_id = $GLOBALS['user']->uid;
		}
		$project = Project::getInstance()->getProjectById($project_id);
		echo "<h2>".t('Solution proposal for '.$project['title'])."</h2>";
		$student_details = Users::getStudentDetails($owner_id);
		$proposal = $proposal_id ? Proposal::getInstance()->getProposalById($proposal_id): null;
		echo '<h3>'.t('Student details').'</h3>';
		echo "<div id='student_details'>Institute: ".$student_details->name."<br/>Supervisor: ".$student_details->supervisor."</div>";
		$f = drupal_get_form('vals_soc_proposal_form', $proposal, $target, $project_id);
		print drupal_render($f);
		break;
	
/* 	case 'add':
		$target = altSubValue($_POST, 'target');
		$type = altSubValue($_POST, 'type');
		echo
		'<h2>'.
		(($type == 'studentgroup') ? t('Add a group to your list of groups') :
				sprintf(t('Add your %1$s'), t($type))).
				'</h2>';
		echo "<div id='msg_$target'></div>";
		$f2 = drupal_get_form("vals_soc_${type}_form", null, $target);
		print drupal_render($f2);
		break;
case 'showmembers':
	if ($_POST['type'] == 'studentgroup'){
		echo renderUsers('student', '', $_POST['studentgroup_id'], $_POST['type']);
		//echo renderStudents($_POST['studentgroup_id']);
	} elseif ($_POST['type'] == 'institute'){
		$type = altSubValue($_GET, 'subtype', 'all');
		if ($type == 'student'){
			$students = Users::getAllStudents($_POST['id']);
			echo renderStudents('', $students);
		} elseif ($type == 'supervisor'){
			$teachers = Users::getSupervisors($_POST['id']);
			echo renderSupervisors('', $teachers);
		} else {
			echo tt('No such type %1$s', $type);
		}

	} elseif ($_POST['type'] == 'organisation'){
		$organisation_id = altSubValue($_POST, 'id', '');
		echo renderUsers('mentor', '', $organisation_id, 'organisation');
	}
	break;
case 'show':
	showRoleDependentAdminPage(getRole());
	break; */
/* 	case 'view':
		$type = altSubValue($_POST, 'type');
		$id = altSubValue($_POST, 'id');
		$target = altSubValue($_POST, 'target', '');
		$organisation = Groups::getGroup($type, $id);
		if (Groups::isOwner($type, $id)){
			echo "IK BEN DE OWNER";
		} else {
			echo "IK BEN NIET DE EIGENAAR";
		}
		if (! $organisation){
			echo sprintf(t('You have no %1$s yet registered'), t($type));
		} else {
			echo sprintf('<h3>%1$s</h3>', sprintf(t('Your %1$s'), t($type)));
			echo "<div id='msg_$target'></div>";
			echo renderOrganisation($type, $organisation, null, $target);
		}
	break;
	case 'delete':
		$type = altSubValue($_POST, 'type', '');
		$id = altSubValue($_POST, 'id', '');
		if (! isValidOrganisationType($type)) {
		echo t('There is no such type we can delete');
		} else {
		$result = Groups::removeGroup($type, $id);
		echo $result ? jsonGooResult() : jsonBadResult();
		}
	break; */
// 	case 'edit':
// 		$id = altSubValue($_POST, 'id', '');
// 		$obj = Proposal::getProposal($id);
// 		$f = drupal_get_form("vals_soc_proposal_form", $obj, $target);
// 		print drupal_render($f);
// 	break;
	case 'save':
		$id = altSubValue($_POST, 'id', '');
		$project_id = altSubValue($_POST, 'project_id', '');
		$project = Project::getInstance()->getProjectById($project_id);
		$properties = Proposal::filterPost($_POST);
		if (!$id){
			$result = Proposal::insertProposal($properties, $project_id);
		} else {
			if (!Groups::isOwner('proposal', $id)){
				drupal_set_message(t('You are not the owner of this proposal'));
				$result = null;
			} else {
				$result = Proposal::updateProposal($properties, $id, $project_id);
			}
		}
	
		if ($result){
			echo json_encode(array(
					'result'=>TRUE,
					'id' => $id,
					'type'=> $type,
					'msg'=>
					($id ? tt('You succesfully changed your proposal for %$1s', $project['title']):
					tt('You succesfully added your proposal for %1$s', $project['title'])).
				(_DEBUG ? showDrupalMessages(): '')
				));
		} else {
			echo jsonBadResult();
		}


	break;
	default: echo "No such action: ".$_GET['action'];
	}