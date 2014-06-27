<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
// include(_VALS_SOC_ROOT.'/includes/classes/AbstractEntity.php');
include(_VALS_SOC_ROOT.'/includes/classes/Users.php');
include(_VALS_SOC_ROOT.'/includes/classes/Project.php');
include(_VALS_SOC_ROOT.'/includes/module/ui/participant.inc');
include(_VALS_SOC_ROOT.'/includes/functions/projects.php');
include(_VALS_SOC_ROOT.'/includes/functions/administration.php');

//return result depending on action parameter
switch ($_GET['action']){
	case 'list':
		$type = altSubValue($_POST, 'type');
		switch ($type){
			case 'institute':
			case 'organisation':
			case 'project':
			case 'studentgroup': echo renderOrganisations($type, '', 'all', $_POST['target']);break;
			case 'supervisor':
			case 'student':
			case 'mentor':
			case 'organisation_admin':
			case 'institute_admin':
			case 'administer': echo renderUsers($type, '', 'all');break;
			default:
				echo tt('No such type: %1$s', $type);
// 				showError(tt('No such type: %1$s', $type));
		}
		//echo jsonResult($result);
	break;
	case 'addgroup':
		$target = altSubValue( $_GET, 'target');
		echo '<h2>'.t('Add a group to your list of groups').'</h2>';
		//$f2 = drupal_get_form('vals_soc_studentgroup_form', null, $target);
		//print drupal_render($f2);

		$form = drupal_get_form('vals_soc_studentgroup_form', null, $target);
		$form['#action'] = url('administer/members');
		// Process the submit button which uses ajax
		$form['submit'] = ajax_pre_render_element($form['submit']);
		// Build renderable array
		$build = array(
				'form' => $form,
				'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
		);
		// Print $form
		$form_to_print = drupal_render($build);
		// Print JS
		$form_to_print .= drupal_get_js();
		echo $form_to_print;

	break;
	case 'add':
		$target = altSubValue($_POST, 'target');
		$type = altSubValue($_POST, 'type');
		echo
		'<h2>'.
			(($type == 'studentgroup') ? t('Add a group to your list of groups') :
			tt('Add your %1$s', t($type))).
		'</h2>'; // when is this ever called for a student group? - there is a separate addgroup task above.
		echo "<div id='msg_$target'></div>";

		$form = drupal_get_form("vals_soc_${type}_form", null, $target);
		$form['#action'] = url('administer/members');
		// Process the submit button which uses ajax
		$form['submit'] = ajax_pre_render_element($form['submit']);
		// Build renderable array
		$build = array(
			'form' => $form,
			'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
		);
		// Print $form
		print drupal_render($build);
		// Print JS
		print drupal_get_js();
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
    break;
    case 'view':
    	$type = altSubValue($_POST, 'type');
    	$id = altSubValue($_POST, 'id');
    	$target = altSubValue($_POST, 'target', '');
    	if (! ($id && $type && $target)){
    		die(t('There are missing arguments. Please inform the administrator of this mistake.'));
    	}
    	$is_project = ($type == 'project');
    	$organisation = $is_project ? Project::getInstance()->getProjectById($id, TRUE) : 
    		Groups::getGroup($type, $id);
    	if (! $organisation){
    		echo tt('The %1$s cannot be found', t($type));
    	} else {
    		//$is_owner = Groups::isOwner($type, $id);
//     		 echo $is_owner ? sprintf('<h3>%1$s</h3>', tt('Your %1$s', t($type))):
//     		 	sprintf('<h3>%1$s</h3>', ($is_project ? $organisation['title'] : $organisation->name));
    		 echo "<div id='msg_$target'></div>";
    		 echo $is_project ? renderProject($organisation, $target) : renderOrganisation($type, $organisation, null, $target);
    	}
    break;
    case 'delete':
    	$type = altSubValue($_POST, 'type', '');
    	$id = altSubValue($_POST, 'id', '');
    	if (! isValidOrganisationType($type)) {
    		echo t('There is no such type we can delete');
    	} else {
    		$result = Groups::removeGroup($type, $id);
    		echo $result ? jsonGoodResult() : jsonBadResult();
    	}
    break;
    case 'edit':
    	//echo "<form action='"
    	//just a tryout
        $type = altSubValue($_POST, 'type', '');
        $id = altSubValue($_POST, 'id', '');
        $target = altSubValue($_POST, 'target', '');
        if (! isValidOrganisationType($type) ) {//&& ($type !== 'project') for convenience we have made a project an organisationtype as well //TODO: make this better
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
        	$form['submit'] = ajax_pre_render_element($form['submit']);
        	// Build renderable array
        	$build = array(
        			'form' => $form,
        			'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
        	);
        	print "<div id='msg_$target'></div>";
        	// Print $form
        	print drupal_render($build);
        	
        	/* if ($form['#attached']['js']){
        		foreach ($form['#attached']['js'] as $incl){
        			if ($incl['type'] == 'file'){
        				echo single_getJs(_VALS_SOC_URL.'/'.$incl['data']);
        			}
        		}
        	} */
        	// Print JS
        	print drupal_get_js();?>
        <?php
        	
        }
    break;
    case 'save':
        $type = altSubValue($_POST, 'type', '');
        $id = altSubValue($_POST, 'id', '');
        //TODO do some checks here
        if(! isValidOrganisationType($type) ){//&& ($type !== 'project')
        	$result = NULL;
        	drupal_set_message(tt('This is not a valid type: %s', $type), 'error');
        	echo jsonBadResult();
        	return;
        }

        $properties = Groups::filterPost($type, $_POST);
        if (!$id){
        	$result = ($type == 'studentgroup') ? Groups::addStudentGroup($properties) :
        		($type == 'project' ? Groups::addProject($properties) : Groups::addGroup($properties, $type));
        } else {
        	$result = Groups::changeGroup($type, $properties, $id);
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