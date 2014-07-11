<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
// module_load_include('php', 'vals_soc', 'includes/classes/AbstractEntity');
// module_load_include('php', 'vals_soc', 'includes/classes/Groups');
module_load_include('php', 'vals_soc', 'includes/classes/Project');
module_load_include('php', 'vals_soc', 'includes/functions/projects');
module_load_include('php', 'vals_soc', 'includes/functions/ajax_functions');

switch ($_GET['action']){
	case 'project_page':
		module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
		module_load_include('php', 'vals_soc', 'includes/functions/projects');
		initBrowseProjectLayout();
	break;
	case 'list_projects':
		try{
			$tags=null;
			if(isset($_POST['tags'])){
				$tags = $_POST['tags'];
			}
			$organisation=null;
			if(isset($_POST['organisation'])){
				$organisation = $_POST['organisation'];
			}
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = Project::getInstance()->getProjectsRowCountBySearchCriteria(
					$tags, $organisation);
				
			$jTableResult['Records'] = Project::getInstance()->getProjectsBySearchCriteria($tags,
					$organisation, $_GET["jtSorting"], $_GET["jtStartIndex"], $_GET["jtPageSize"]);
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
	case 'project_detail':
		$project_id=null;
		if(isset($_GET['project_id'])){
			try {
				$project = Project::getInstance()->getProjectById($_GET['project_id']);
				jsonGoodResult($project);
			} catch (Exception $e){
				jsonBadResult($e->getMessage());
			}
		}
		else{
			jsonBadResult( t("No Project identifier submitted!"));
		}
	break;
	//
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
	case 'add':
		$target = altSubValue($_POST, 'target');
		$type = altSubValue($_POST, 'type');
		echo '<h2>'.t('Add new').'</h2>';
		echo "<div id='msg_$target'></div>"; 
		$form = drupal_get_form("vals_soc_project_form", '', $target);
	
		//$form['#action'] = url('administer/members');
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
	//	print drupal_get_js();
		
	break;
	case 'save':
		$type = altSubValue($_POST, 'type', '');
		$id = altSubValue($_POST, 'id', '');
		$properties = Project::getInstance()->filterPostLite(Project::getInstance()->getKeylessFields(), $_POST);
		if (!$id){
			$result = Project::getInstance()->addProject($properties);
		}
		else {
			$result = Project::getInstance()->changeProject($properties, $id);
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
		}
		else {
			echo jsonBadResult();
		}
	break;
	case 'show':
		showProjectPage();
	break;
	case 'edit':
		$type = altSubValue($_POST, 'type', '');
		$id = altSubValue($_POST, 'id', '');
		$target = altSubValue($_POST, 'target', '');

		$obj = Groups::getGroup($type, $id);
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
		$build = array(
				'form' => $form,
				'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
		);
		print "<div id='msg_$target'></div>";
		// Print $form
		print drupal_render($form);//$build

	    break;
	    case 'delete':
	    	//return var_dump($_POST);
	    	$type = altSubValue($_POST, 'type', '');
	    	$id = altSubValue($_POST, 'id', '');
	    	if (! isValidOrganisationType($type)) {
	    		echo t('There is no such type we can delete');
	    	} else {
	    		$result = Groups::removeGroup($type, $id);
	    		echo $result ? jsonGoodResult() : jsonBadResult();
	    	}
	    break;
	//
	default: echo "No such action: ".$_GET['action'];
}