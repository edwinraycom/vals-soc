<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
include(_VALS_SOC_ROOT.'/includes/classes/Project.php');
include(_VALS_SOC_ROOT.'/includes/functions/render_functions.php');
include(_VALS_SOC_ROOT.'/includes/functions/projects.php');
include(_VALS_SOC_ROOT.'/includes/functions/administration.php');

//return result depending on action parameter
switch ($_GET['action']){
	case 'list':
		$type = altSubValue($_POST, 'type');
		switch ($type){
			case _INSTITUTE_GROUP:
			case _ORGANISATION_GROUP:
			case 'project':
			case _STUDENT_GROUP: echo renderOrganisations($type, '', 'all', $_POST['target']);break;
			case _SUPERVISOR_TYPE:
			case _STUDENT_TYPE:
			case _MENTOR_TYPE:
			case _ORGADMIN_TYPE:
			case _INSTADMIN_TYPE:
			case 'administer': echo renderUsers($type, '', 'all', '', TRUE);break;
			default:
				//echo tt('No such type: %1$s', $type);
				showError(tt('No such type: %1$s', $type));
		}
	break;
	case 'add':
		$target = altSubValue($_POST, 'target');
		$type = altSubValue($_POST, 'type');
		$show_action = getRequestVar('show_action', 'administer');//altSubValue($_GET, 'show_action', 'administer');
		echo
		'<h2>'.
			(($type == _STUDENT_GROUP) ? t('Add a group to your list of student groups') :
			tt('Add your %1$s', t($type))).
		'</h2>';

		$form = drupal_get_form("vals_soc_${type}_form", null, $target, $show_action);
		//TODO  Should this stay? $form['#action'] = url('dashboard/administer/members');
		// Process the submit button which uses ajax
		$form['submit'] = ajax_pre_render_element($form['submit']);
		// Build renderable array
// 		$build = array(
// 			'form' => $form,
// 			'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
// 		);
		// Print $form
		renderForm($form, $target);
// 		print drupal_render($form);
//         print valssoc_form_get_js($form);// Print JS
		//print drupal_get_js();
	break;
    case 'showmembers':
    	if ($_POST['type'] == _STUDENT_GROUP){
            echo renderUsers(_STUDENT_TYPE, '', $_POST['id'], $_POST['type']);
        } elseif ($_POST['type'] == _INSTITUTE_GROUP){
            $type = altSubValue($_GET, 'subtype', 'all');
            if ($type == _STUDENT_TYPE){
                echo renderStudents($_POST['id']);
            } elseif ($type == _SUPERVISOR_TYPE){
                echo renderSupervisors($_POST['id']);
            } elseif ($type == _INSTADMIN_TYPE){
                echo renderUsers(_INSTADMIN_TYPE, '', $_POST['id'], _INSTITUTE_GROUP);
            } elseif ($type == 'staff'){
                $inst_id = $_POST['id'];
                echo renderUsers(_INSTADMIN_TYPE, '', $inst_id, _INSTITUTE_GROUP, TRUE);
	    		echo renderUsers(_SUPERVISOR_TYPE, '', $inst_id, _INSTITUTE_GROUP, TRUE);
	    		
            } else {
            	echo tt('No such type %1$s', $type);
            }

        } elseif ($_POST['type'] == _ORGANISATION_GROUP){
           $organisation_id = altSubValue($_POST, 'id', '');
           if($organisation_id == 0){
           	$organisation_id = 'all';
           }
           echo 
			renderUsers(_ORGADMIN_TYPE, '', $organisation_id, _ORGANISATION_GROUP, TRUE). 
			renderUsers(_MENTOR_TYPE, '', $organisation_id, _ORGANISATION_GROUP, TRUE);
        }
     break;
    case 'show':
    	$type = altSubValue($_POST, 'type', '');
    	$show_action = altSubValue($_POST, 'show_action', 'administer');
    	if ($type && ($type == 'student_group')){
    		$show_action = 'groups';
    	}
    	//$subject = getRequestVar('subject', 'no_subject');
    	$show_last = altSubValue($_POST, 'new_tab', false);
    	showRoleDependentAdminPage(getRole(), $show_action, $show_last);
    break;
    case 'view':
    	$type = altSubValue($_POST, 'type');
    	$id = altSubValue($_POST, 'id');
    	$target = altSubValue($_POST, 'target', '');
    	$buttons = altSubValue($_GET, 'buttons', true);
    	if (! ($id && $type && $target)){
    		die(t('There are missing arguments. Please inform the administrator of this mistake.'));
    	}
    	$organisation = Groups::getGroup($type, $id);
    	if (! $organisation){
    		echo tt('The %1$s cannot be found', t($type));
    	} else {
    		echo "<div id='msg_$target'></div>";
    		echo renderOrganisation($type, $organisation, null, $target, $buttons);
    	}
    	break;
    case 'delete':
    	$type = altSubValue($_POST, 'type', '');
    	$id = altSubValue($_POST, 'id', '');
    	$target = altSubValue($_POST, 'target', '');
    	extract(deriveTypeAndAction(empty($type)), EXTR_OVERWRITE);
    	if (! isValidOrganisationType($type)) {
    		echo jsonBadResult(t('There is no such type we can delete'));
    	} else {
    		$result = Groups::removeGroup($type, $id);
    		echo $result ? jsonGoodResult() : jsonBadResult('', 'error', array('target'=>$target));
    	}
    break;
    case 'send_invite_email':
    	module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');	
    	$type = altSubValue($_POST, 'type', '');
    	$email = altSubValue($_POST, 'contact_email', '');
    	$subject = altSubValue($_POST, 'subject', '');
    	$body = altSubValue($_POST, 'description', '');
		$result = vals_soc_send_email('vals_soc_invite_new_user', $email, NULL, $subject, $body);
    	$id = altSubValue($_POST, 'id', '');
    	$show_action = altSubValue($_POST, 'show_action', '');
    	
		if ($result){
            echo json_encode(array(
            		'result'=>TRUE,
            		'id' => $id,
            		'type'=> $type,
            		'show_action' => $show_action,
            		'msg'=> t('Email successfully sent') .
            		(_DEBUG ? showDrupalMessages(): '')
            		
            		));
        } else {
        	echo jsonBadResult();
        }
    break;
    case 'inviteform':
    	$type = altSubValue($_POST, 'type', '');
    	$subtype = altSubValue($_POST, 'subtype', '');
    	$id = altSubValue($_POST, 'id', '');
    	$target = altSubValue($_POST, 'target', '');
    	if (! isValidOrganisationType($type) ) {//for convenience we have made a project an organisationtype as well //TODO: make this better
    		echo t('There is no such type you can invite people to :'.$type);
    	} else {
    		$obj = Groups::getGroup($type, $id);
    		// See http://drupal.stackexchange.com/questions/98592/ajax-processed-not-added-on-a-form-inside-a-custom-callback-my-module-deliver
    		// for additions below
    		$form = drupal_get_form("vals_soc_invite_form", $obj, $target, '', $subtype);
    		if(isset($_POST['path'])){
    			$form['#action'] = url($_POST['path']);
    		}
    		unset($_POST);
    		renderForm($form, $target);
    	}
    	break;
    case 'edit':
        $type = altSubValue($_POST, 'type', '');
        $id = altSubValue($_POST, 'id', '');
        $target = altSubValue($_POST, 'target', '');
        if (! isValidOrganisationType($type) ) {//for convenience we have made a project an organisationtype as well //TODO: make this better
        	echo t('There is no such type to edit :'.$type);
        } else {
        	$obj = Groups::getGroup($type, $id);
        	// See http://drupal.stackexchange.com/questions/98592/ajax-processed-not-added-on-a-form-inside-a-custom-callback-my-module-deliver
        	// for additions below
        	$originalPath = false;
        	if(isset($_POST['path'])){
        		$originalPath = $_POST['path'];
        	}
        	unset($_POST);
        	$form = drupal_get_form("vals_soc_${type}_form", $obj, $target);
        	if($originalPath){
        		$form['#action'] = url($originalPath);
        	}
        	// Process the submit button which uses ajax
        	//$form['submit'] = ajax_pre_render_element($form['submit']);
        	// Build renderable array
//         	$build = array(
//         			'form' => $form,
//         			'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
//         	);
        	renderForm($form, $target);
        }
    break;
    case 'save':
        $type = altSubValue($_POST, 'type', '');
        $id = altSubValue($_POST, 'id', '');
        $show_action = altSubValue($_POST, 'show_action', '');
        //TODO do some checks here
        if(! isValidOrganisationType($type) ){//&& ($type !== 'project')
        	$result = NULL;
        	drupal_set_message(tt('This is not a valid type: %s', $type), 'error');
        	echo jsonBadResult();
        	return;
        }

        $properties = Groups::filterPost($type, $_POST);
        if (!$id){
        	$new = true;
        	$result = ($type == _STUDENT_GROUP) ? Groups::addStudentGroup($properties) :
        		($type == 'project' ? Project::getInstance()->addProject($properties) : Groups::addGroup($properties, $type));
        } else {
        	$new = false;
        	$result = Groups::changeGroup($type, $properties, $id);
        }

		if ($result){
            echo json_encode(array(
            		'result'=>TRUE,
            		'id' => $id,
            		'type'=> $type,
					'new_tab' => !$id ? $result : 0,
            		'show_action' => $show_action,
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