<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
//This file is included as part of the bootstrap process as the handle_forms file includes it which is included itself
//automatically
// module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
// include(_VALS_SOC_ROOT.'/includes/vals_soc.helper.inc');
include(_VALS_SOC_ROOT.'/includes/classes/Participants.php');
include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');
include(_VALS_SOC_ROOT.'/includes/functions/administration.php');

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
			tt('Add your %1$s', t($type))).
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
    		echo tt('You have no %1$s yet registered', t($type));
    	} else {
    		 echo sprintf('<h3>%1$s</h3>', tt('Your %1$s', t($type)));
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
        	drupal_set_message(tt('This is not a valid type: %s', $type), 'error');
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
            		($id ? tt('You succesfully changed the data of your %1$s', t($type)):
            			   tt('You succesfully added your %1$s', t($type))).
            		(_DEBUG ? showDrupalMessages(): '') 
            		));
        } else {
        	echo jsonBadResult();
        }

        
    break;
    default: echo "No such action: ".$_GET['action'];
}