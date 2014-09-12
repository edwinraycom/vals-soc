<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
// module_load_include('php', 'vals_soc', 'includes/classes/AbstractEntity');
module_load_include('php', 'vals_soc', 'includes/classes/ThreadedComments');
module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
module_load_include('php', 'vals_soc', 'includes/classes/Project');
module_load_include('php', 'vals_soc', 'includes/functions/projects');
module_load_include('php', 'vals_soc', 'includes/functions/ajax_functions');

$mine = ('mine' == (array_pop(explode('/', $_SERVER['HTTP_REFERER']))));//needed for save and delete
switch ($_GET['action']){
	case 'project_page':
		module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
		initBrowseProjectLayout();
	break;
	case 'list_search':
		try{
			$tags=null;
			if(isset($_POST['tags'])){
				$tags = $_POST['tags'];
			}
			$organisation=null;
			if(isset($_POST['organisation'])){
				$organisation = $_POST['organisation'];
			}
			$project_id = getRequestVar('pid', null);
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			if ($project_id){
				$project = Project::getProjectById($project_id);
				if ($project){
					$jTableResult['TotalRecordCount'] = 1;
					$jTableResult['Records'] = array($project);
				} else {
					$jTableResult['TotalRecordCount'] = 0;
					$jTableResult['Records'] = array();
				}
			} else {
				$jTableResult['TotalRecordCount'] = Project::getInstance()->getProjectsRowCountBySearchCriteria(
						$tags, $organisation);
				$jTableResult['Records'] = Project::getInstance()->getProjectsBySearchCriteria($tags,
						$organisation, $_GET["jtSorting"], $_GET["jtStartIndex"], $_GET["jtPageSize"]);
			}
			print json_encode($jTableResult);
		}
		catch(Exception $ex){
			//Return error message
			$jTableResult = array();
			$jTableResult['Result'] = "ERROR";
			$jTableResult['Message'] = $ex->getMessage();
			print json_encode($jTableResult);
		}
	break;
	case 'list':
		try{
			$target = getRequestVar('target', null);
			$inline = getRequestVar('inline', false);
			$org_id = getRequestVar('org', null);
			$mine   = getRequestVar('mine', false);
			$organisations = $org_id ?  array($org_id) : null;
			echo renderProjects($organisations, '', $target, $inline, true, $mine);
		}
		catch(Exception $ex){
			//Return error message
			errorDiv($ex->getMessage());
		}
		break;
	case 'project_detail':
		$project_id=null;
		if(isset($_GET['project_id'])){
			try {
				$project = Project::getProjectById($_GET['project_id']);
				jsonGoodResult($project);
			} catch (Exception $e){
				jsonBadResult($e->getMessage());
			}
		}
		else{
			jsonBadResult( t("No Project identifier submitted!"));
		}
	break;
	case 'view':
		$type = 'project';
		$id = altSubValue($_POST, 'id');
		$target = altSubValue($_POST, 'target', '');
		$inline = getRequestVar('inline', FALSE);
		if (! ($id && $type && $target)){
			die(t('There are missing arguments. Please inform the administrator of this mistake.'));
		}
		$project = Project::getProjectById($id, TRUE);
		if (! $project){
			echo t('The project cannot be found');
		} else {
			echo "<div id='msg_$target'></div>";
			echo renderProject($project, $target, $inline);
		}
		break;
	case 'add':
		$target = altSubValue($_POST, 'target');
		$type = altSubValue($_POST, 'type');
		$org  = altSubValue($_GET, 'org');
		echo '<h2>'.t('Add new project').'</h2>';
		echo "<div id='msg_$target'></div>"; 
		$form = drupal_get_form("vals_soc_project_form", '', $target, $org);
		// Process the submit button which uses ajax
		//$form['submit'] = ajax_pre_render_element($form['submit']);
		// Build renderable array
		/*
		$build = array(
			'form' => $form,
			'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
		);
		// Print $form
		print drupal_render($build);
		*/
		
		// Print $form
		renderForm($form, $target);
		
	break;
	case 'save':
		$type = altSubValue($_POST, 'type', '');
		$id = altSubValue($_POST, 'id', '');
		$draft = altSubValue($_POST, 'draft', false);
		$properties = Project::getInstance()->filterPostLite(Project::getInstance()->getKeylessFields(), $_POST);
		$properties['state'] = ($draft ? 'draft' :'pending');
		if (!$id){
			$new = $properties['org_id'];
			$result = Project::getInstance()->addProject($properties);
		} else {
			$new = false;
			$result = Project::getInstance()->changeProject($properties, $id);
		}
		if ($result){
			
			echo json_encode(array(
					'result'=>TRUE,
					'id' => $id,
					'type'=> $type,
					'new_tab' => !$id ? $properties['org_id'] : 0,//so we can distinguish which tab to open
					'extra' => ($mine? array( 'mine' =>1) :''),
					'msg'=>
					($id ? tt('You succesfully changed the data of your %1$s', t($type)):
							tt('You succesfully added your %1$s', t($type))).
					(_DEBUG ? showDrupalMessages(): '')
			));
		}
		else {
			echo jsonBadResult();
		}
	break;
	case 'show':
		$show_last = altSubValue($_POST, 'new_tab', false);
		$owner_only = altSubValue($_POST, 'mine', false);
		showProjectPage($show_last, $owner_only);
	break;
	case 'edit':
		$type = altSubValue($_POST, 'type', '');
		$id = altSubValue($_POST, 'id', '');
		$target = altSubValue($_POST, 'target', '');

		$obj = Project::getProjectById($id, FALSE, NULL);
		if (!$obj){
			echo t('The project could not be found');
			return;
		}
		
		// See http://drupal.stackexchange.com/questions/98592/ajax-processed-not-added-on-a-form-inside-a-custom-callback-my-module-deliver
		// for additions below
		$originalPath = false;
		if(isset($_POST['path'])){
			$originalPath = $_POST['path'];
		}
		unset($_POST);
		$form = drupal_get_form("vals_soc_project_form", $obj, $target);
		if($originalPath){
			$form['#action'] = url($originalPath);
		}
		// Process the submit button which uses ajax
		//$form['submit'] = ajax_pre_render_element($form['submit']);
		// Build renderable array
// 		$build = array(
// 				'form' => $form,
// 				'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
// 		);
		renderForm($form, $target);

	    break;
    case 'delete':
    	$type = altSubValue($_POST, 'type', '');
    	$id = altSubValue($_POST, 'id', '');
    	if (! isValidOrganisationType($type)) {
    		echo jsonBadResult(t('There is no such type we can delete'));
    	} else {
    		$result = Groups::removeGroup($type, $id);
    		ThreadedComments::getInstance()->removethreadsForEntity($id, $type);
    		echo $result ? jsonGoodResult(true, '', array('extra'=> ($mine? array( 'mine' =>1) :''))) : jsonBadResult();
    	}
    break;
	//
	default: echo "No such action: ".$_GET['action'];
}