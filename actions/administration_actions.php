<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('DRUPAL_ROOT', realpath(getcwd().'/../../../../..'));
define('_VALS_SOC_ROOT', DRUPAL_ROOT.'/sites/all/modules/vals_soc');
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);//Used to be DRUPAL_BOOTSTRAP_SESSION
include(_VALS_SOC_ROOT.'/includes/vals_soc.helper.inc');

function jsonWrongResult($type='status'){
	$errors = drupal_get_messages($type);
	echo json_encode(array('result'=> 0, 'msg'=> t('Something went wrong').
			($errors ? '<br/>'.implode('<br/>', $errors[$type]): (_DEBUG ? t(' No error message available'): '')
					)));
}

//return result depending on action parameter
switch ($_GET['action']){
	case 'addgroup':
		$target = altSubValue( $_GET, 'target');
		echo '<h2>'.t('Add a group to your list of groups').'</h2>';
		$f2 = drupal_get_form('vals_soc_group_form', null, $target);
		print drupal_render($f2);
	break;	
    case 'showmembers':
        include(_VALS_SOC_ROOT.'/includes/classes/Participants.php');
        include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');     
        if ($_POST['type'] == 'group'){
            renderParticipants('student', '', $_POST['group_id'], $_POST['type']);
            //renderStudents($_POST['group_id']);
        } elseif ($_POST['type'] == 'institute'){
            $type = altSubValue($_POST, 'subtype', 'all');
            if ($type == 'student'){
                $participants = new Participants();
                $students = $participants->getAllStudents($_POST['institute_id']);
                renderStudents('', $students);
            } elseif ($type == 'supervisor'){
                $participants = new Participants();
                $teachers = $participants->getSupervisors($_POST['institute_id']);
                renderSupervisors('', $teachers);
            }
                
        } elseif ($_POST['type'] == 'organisation'){
           $type = altSubValue($_POST, 'subtype', 'all');
           $organisation_id = altSubValue($_POST, 'organisation_id', '');
           renderParticipants($type, '', $organisation_id);
        }
     break;
    case 'view':
    	include(_VALS_SOC_ROOT.'/includes/classes/Participants.php');
    	include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');
    	$type = altSubValue($_POST, 'type');
    	$id = altSubValue($_POST, 'id');
    	$organisation = Participants::getOrganisation($type, $id);
    	if (! $organisation){
    		echo t('You have no organisation yet registered');
//     		echo '<h2>'.t('Add your organisation').'</h2>';
//     		$f3 = drupal_get_form('vals_soc_organisation_form');
//     		print drupal_render($f3);
    	} else {
    		 echo sprintf('<h3>%1$s</h3>', sprintf(t('Your %1$s'), t($type)));
    		 renderOrganisation($type, $organisation);
    	}
    break;
    case 'edit':
        include(_VALS_SOC_ROOT.'/includes/classes/Participants.php');
        include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');
        $type = altSubValue($_POST, 'type', '');
        switch ($type) {
        	case 'group':
        		$id = $_POST['id'];
        		$group = Participants::getOrganisation('group', $id);
        		$f1 = drupal_get_form('vals_soc_group_form', $group);
        		print drupal_render($f1);        	
            break;
            case 'institute':
            	$inst_id = $_POST['id'];
	            $inst = Participants::getOrganisation('institute', $inst_id);
	            $f1 = drupal_get_form('vals_soc_institute_form', $inst);
	            print drupal_render($f1);     
            break;
			case 'organisation':
				$organisation_id = altSubValue($_POST, 'id', '');
				$org = Participants::getOrganisation('organisation', $organisation_id);
				 
				$f1 = drupal_get_form('vals_soc_organisation_form', $org);
				print drupal_render($f1); 
			break;
        	default: echo t('There is no such type to edit');
        }
        
    break;
    case 'save':
        include(_VALS_SOC_ROOT.'/includes/classes/Participants.php');
        //include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');
        $type = altSubValue($_POST, 'type', '');
        $id = altSubValue($_POST, 'id', '');
        //TODO do some checks here
        $properties = Participants::filterPost($type, $_POST);
        if (in_array($type, array('organisation', 'institute'))){
        	$result = Participants::updateOrganisation($type, $properties, $id);
        } elseif ($type == 'group') {
        	if ($id){
        		$result = Participants::updateOrganisation($type, $properties, $id);
        	} else {
        		drupal_set_message(" insert geval");
        		$result = Participants::insertGroup($properties);
        	}
        } else {
        	$result = NULL;
//         	$result = Participants::updateParticipant($type, $properties, $id);
		}
		if ($result){
            echo json_encode(array('result'=>TRUE, 'msg'=> 
            		sprintf(t('You succesfully changed the data of your %1$s'), t($type))));
        } else {
        	drupal_set_message(" het type is $type en $id");
        	 
        	drupal_set_message('Het ging mis met deze data '.print_r($properties, 1));
        	echo jsonWrongResult();
        }

        
    break;
    default: echo "No such action: ".$_GET['action'];
}