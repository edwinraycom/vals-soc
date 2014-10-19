<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
include(_VALS_SOC_ROOT.'/includes/classes/Agreement.php');
include(_VALS_SOC_ROOT.'/includes/functions/render_functions.php');
//include(_VALS_SOC_ROOT.'/includes/pages/projects.php');
//include(_VALS_SOC_ROOT.'/includes/pages/administration.php');

//return result depending on action parameter
switch ($_GET['action']){
case 'edit':
	//$type = altSubValue($_POST, 'type', '');
	$id = altSubValue($_POST, 'id', '');
	$target = altSubValue($_POST, 'target', '');
	$agreement = Agreement::getInstance()->getSingleStudentsAgreement($id);
	$originalPath = false;
	if(isset($_POST['path'])){
		$originalPath = $_POST['path'];
	}
	unset($_POST);
	$form = drupal_get_form("vals_soc_agreement_form", $agreement, $target);
	if($originalPath){
		$form['#action'] = url($originalPath);
	}
	renderForm($form, $target);
break;
case 'save':
	$type = altSubValue($_POST, 'type', '');
	$id = altSubValue($_POST, 'id', '');
	$show_action = altSubValue($_POST, 'show_action', '');

	$props = Agreement::getInstance()->filterPostLite(Agreement::getInstance()->getKeylessFields(), $_POST);
	
	if(isset($_POST['student_signed_already'])){
		$props['student_signed'] = 1;
	}
	if(isset($_POST['supervisor_signed_already'])){
		$props['supervisor_signed'] = 1;
	}
	if(isset($_POST['mentor_signed_already'])){
		$props['mentor_signed'] = 1;
	}
	$props['agreement_id'] = $id;
	$result = Agreement::getInstance()->updateAgreement($props);
	if ($result){
		echo json_encode(array(
				'result'=>TRUE,
				'id' => $id,
				'type'=> $type,
				'new_tab' => !$id ? $result : 0,
				'show_action' => $show_action,
				'msg'=>
				($id ? tt('You succesfully changed the data of your %1$s', t_type($type)):
						tt('You succesfully added your %1$s', t_type($type))).
				(_DEBUG ? showDrupalMessages(): '')
		));
	} else {
		echo jsonBadResult();
	}
	break;
	case 'view':
		$type = altSubValue($_POST, 'type');
		$id = altSubValue($_POST, 'id');
		$target = altSubValue($_POST, 'target', '');
		$buttons = altSubValue($_GET, 'buttons', true);
		if (! ($id && $type && $target)){
			die(t('There are missing arguments. Please inform the administrator of this mistake.'));
		}
		$agreement = Agreement::getInstance()->getProjectAgreements($id, '', '', '', '', true)->fetchObject();
		echo "<div id='msg_$target'></div>";
		echo renderAgreement($type, $agreement, '',$target, $buttons);
	break;
default: echo "No such action: ".$_GET['action'];
}