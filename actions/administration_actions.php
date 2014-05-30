<?php
define('DRUPAL_ROOT', realpath(getcwd().'/../../../../..'));
define('_VALS_SOC_ROOT', DRUPAL_ROOT.'/sites/all/modules/vals_soc');
$base_url = $_SERVER['REQUEST_SCHEME']. '://'.$_SERVER['HTTP_HOST'].'/vals'; //This seems to be necessary to get to the user object: see
//http://drupal.stackexchange.com/questions/76995/cant-access-global-user-object-after-drupal-bootstrap, May 2014
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);//Used to be DRUPAL_BOOTSTRAP_SESSION

//This file is included as part of the bootstrap process as the handle_forms file includes it which is included itself
//automatically
module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
include(_VALS_SOC_ROOT.'/includes/vals_soc.helper.inc');
include(_VALS_SOC_ROOT.'/includes/classes/Participants.php');
include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');
include(_VALS_SOC_ROOT.'/includes/functions/administration.php');

function jsonResult($result, $type, $show_always=FALSE){
	$msgs = drupal_get_messages($type);
	if ($msgs){
		if ($type){
			$msg = implode('<br/>', $msgs[$type]);
		} else {
			$msg = '';
			foreach ($msgs as $cat => $msg_arr){
				$msg .= "$cat:".implode('<br/>', $msg_arr);
			}
		}
	} else {
		$msg = (_DEBUG && $show_always ? sprintf(t(' No %1$s message available'), $type): '');
	}
	$struct = array('result'=> $result);
	if (empty($result) || ($result === 'error')) {
		$struct['error'] = $msg;
	} else {
		$struct['msg'] = $msg;
	}
	echo json_encode($struct);
}

function jsonBadResult($result='error', $type='error'){
	jsonResult($result, $type, TRUE);
}

function jsonGooResult($result=TRUE, $type='status'){
	jsonResult($result, $type);
}

function isValidOrganisationType($type){
	return in_array($type, array('organisation', 'institute', 'group'));
}

function showDrupalMessages($category='status', $echo=FALSE){
	if (empty($category)){
		$s = '';
		$msgs = drupal_get_messages();
		foreach ($msgs as $type =>$msgs1){
			$s .= "<br/>$type :<br/>";
			$s.= implode('<br/>', $msgs1);
		}
	} else {
		$msgs = drupal_get_messages($category);
		$s = $msgs[$category] ? "<br/>$category:<br/>".implode('<br/>', $msgs[$category]) : '';
	}
	
	if ($echo) echo $s;
	return $s;
}

function showError($msg='') {
	$msg .= showDrupalMessages('error');
	if ($msg){
		echo "<div class='messages error'>'$msg'</div>";
	}
}

//return result depending on action parameter
switch ($_GET['action']){
	case 'list':
		$type = altSubValue($_POST, 'type');
		switch ($type){
			case 'institute': 
			case 'organisation':  
			case 'group': echo renderOrganisations($type, '', 'all', $_POST['target']);break;
			case 'supervisor': 
			case 'student':
			case 'mentor':
			case 'organisation_admin': 
			case 'institute_admin': 
			case 'administer': echo renderParticipants($type, '', 'all');break;
			default: 
				echo tt('No such type: %1$s', $type);
// 				showError(tt('No such type: %1$s', $type));
		}
		//echo jsonResult($result);
	break;
	case 'addgroup':
		$target = altSubValue( $_GET, 'target');
		echo '<h2>'.t('Add a group to your list of groups').'</h2>';
		$f2 = drupal_get_form('vals_soc_group_form', null, $target);
		print drupal_render($f2);
	break;
	case 'add':
		$target = altSubValue($_POST, 'target');
		$type = altSubValue($_POST, 'type');
		echo 
		'<h2>'.
			(($type == 'group') ? t('Add a group to your list of groups') :
			sprintf(t('Add your %1$s'), t($type))).
		'</h2>';
		echo "<div id='msg_$target'></div>";
		$f2 = drupal_get_form("vals_soc_${type}_form", null, $target);
		print drupal_render($f2);
	break;
    case 'showmembers':
    	if ($_POST['type'] == 'group'){
            echo renderParticipants('student', '', $_POST['group_id'], $_POST['type']);
            //echo renderStudents($_POST['group_id']);
        } elseif ($_POST['type'] == 'institute'){
            $type = altSubValue($_GET, 'subtype', 'all');
            if ($type == 'student'){
                $students = Participants::getAllStudents($_POST['id']);
                echo renderStudents('', $students);
            } elseif ($type == 'supervisor'){
                $teachers = Participants::getSupervisors($_POST['id']);
                echo renderSupervisors('', $teachers);
            } else {
            	echo tt('No such type %1$s', $type);
            }
                
        } elseif ($_POST['type'] == 'organisation'){
           $organisation_id = altSubValue($_POST, 'id', '');
           echo renderParticipants('mentor', '', $organisation_id, 'organisation');
        }
     break;
    case 'show':
    	showRoleDependentAdminPage(getRole());
    break;
    case 'view':
    	$type = altSubValue($_POST, 'type');
    	$id = altSubValue($_POST, 'id');
    	$target = altSubValue($_POST, 'target', '');
    	$organisation = Participants::getOrganisation($type, $id);
						    	if (Participants::isOwner($type, $id)){
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
    		$result = Participants::deleteOrganisation($type, $id);
    		echo $result ? jsonGooResult() : jsonBadResult();
    	}
    break;
    case 'edit':
        $type = altSubValue($_POST, 'type', '');
        $id = altSubValue($_POST, 'id', '');
        $target = altSubValue($_POST, 'target', '');
        if (! isValidOrganisationType($type)) {
        	echo t('There is no such type to edit');
        } else {
        	$obj = Participants::getOrganisation($type, $id);
        	$f = drupal_get_form("vals_soc_${type}_form", $obj, $target);
        	print drupal_render($f);
        }        
    break;
    case 'save':
        $type = altSubValue($_POST, 'type', '');
        $id = altSubValue($_POST, 'id', '');

        //TODO do some checks here
        if(! isValidOrganisationType($type)){
        	$result = NULL;
        	drupal_set_message(sprintf(t('This is not a valid type: %s'), $type), 'error');
        	echo jsonBadResult();
        	return;
        }
        
        $properties = Participants::filterPost($type, $_POST);
        if (!$id){
        	$result = ($type == 'group') ? Participants::insertGroup($properties) :
        		Participants::insertOrganisation($properties, $type);
        } else {
        	$result = Participants::updateOrganisation($type, $properties, $id);
        }	

		if ($result){
            echo json_encode(array(
            		'result'=>TRUE,
            		'id' => $id,
            		'type'=> $type,
            		'msg'=>
            		($id ? sprintf(t('You succesfully changed the data of your %1$s'), t($type)):
            			   sprintf(t('You succesfully added your %1$s'), t($type))).
            		(_DEBUG ? showDrupalMessages(): '') 
            		));
        } else {
        	echo jsonBadResult();
        }

        
    break;
    default: echo "No such action: ".$_GET['action'];
}