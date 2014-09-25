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
	case 'recommend':
		if (!(Users::isSuperVisor()|| Users::isInstituteAdmin() ||_DEBUG)){
			echo t('You can only rate a project as staff member of an institute.');
			return;
		}
		$mails = array();
		$id = getRequestVar('id');
		$mail = array('from' => $GLOBALS['user']->mail);
		$mail['body'] = tt('Hello,')."\n\n".
				tt('I would like to recommend the following project to you: %1$s',
						_VALS_SOC_FULL_URL."/projects/browse?pid=9$id."). "\n\n".
						t('Kind regards,')."\n\n".
						Users::getMyName();
		$mail['subject'] = t('Recommendation from your supervisor');
		$mail['plain'] = true;
		
		$email = str_replace(' ', '', getRequestVar('email'));
		
		//TODO: remove this later. FYI both smtp module and mail can handle comma separated mail recipient lists. If one of 
		//those is invalid, the others are sent, but as I thought I had to use vals_soc_send_emails_now for multiple messages
		//one of them is sent with invalid address and so the following buggy message (punctuation and content) is shown: 
		//'Invalid address: testingnonsensYou must provide at least one recipient email address. '
		//this is a bad message for a user sending a list of users one email. Better is to send one mail with multiple recipiendt
		//and let smtp or php mail sort out that one of those is incorrect. Last try now: 1. send one mail with smtp where one 
		//address is wrong, 2. sending with php mail and one incorrect address.
		//REsult: for 1, since there are other valid addresses: the invalid one is ignored completely: test: have one nonsens address only
		//That gives: the result true and no messages. 
		//Moreover: all the mail addressses are visible to all the recipients!
		//So we choose to send all emails apart and then to hide the messages from drupal and produce our own.
		//It is a bit inefficient, but ok. A valid email address in smtp is when: 
		
		$emails = explode(',', $email);
		if (count($emails) > 1){
			$no = 0;
			foreach ($emails as $email){
				if ($email) {
					$mails[] = $mail;
					$mails[$no]['to'] = $email;
					$no++;
				}
			}
		} else {
			$mails[] = $mail;
			$mails[0]['to'] = $email;
		}
		
// 		$mail['to'] = $email;//NEW
// 		$mails[] = $mail;//NEW
		module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');
		if (vals_soc_send_emails_now($mails)) {
			echo successDiv(t('You sent your recommendation(s)'));
		} else {
			echo errorDiv(t('Something wrong with sending your recommendation(s): ').getDrupalMessages('error'));
		}
		break;
	case 'rate':
		//do something
		if (!(Users::isSuperVisor()|| _DEBUG)){
			echo t('You can only rate a project as supervisor.');
			return;
		}
		$rate = getRequestVar('rate');
		$id = getRequestVar('id');
		$table = tableName('supervisor_rate');
		$result = FALSE;
		$num_deleted = db_delete($table)
			->condition('pid', $id)
			->condition('uid', $GLOBALS['user']->uid)
			->execute();
		if ($num_deleted !== FALSE){
			$result = db_insert($table)
				->fields(array('pid'=> $id,
					'uid'=>$GLOBALS['user']->uid,
					'rate'=>$rate))
				->execute();
		}
		echo $result ? t('You have marked this project succesfully'): t('Something went wrong with the project rating.');

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
			//Save it for navigation
			$_SESSION['lists']['projects'] = array();
			$_SESSION['lists']['projects']['nr'] = count($jTableResult['Records']);
			$_SESSION['lists']['projects']['list'] = $jTableResult['Records'];
			$_SESSION['lists']['projects']['current'] = -1;
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
	case 'list_search_proposal_count':
		$organisation=null;
		if(isset($_POST['organisation']) && $_POST['organisation']){
			$organisation = $_POST['organisation'];
		}
		//Return result to jTable
		$recs = Project::getInstance()->getProjectsAndProposalCountByCriteria(
				$organisation, $_GET["jtSorting"], $_GET["jtStartIndex"], $_GET["jtPageSize"]);
		$cnt = Project::getInstance()->getProjectsAndProposalCountByCriteriaRowCount($organisation);	
		jsonGoodResultJT($recs, $cnt);
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
		$project_id = getRequestVar('project_id', null);
		$project = null;
		if($project_id){
			try {
				if (isset($_SESSION['lists']['projects']) && $_SESSION['lists']['projects']){
					$current = getRequestVar('current',-1);
					if ($current >=0){
						$project = $_SESSION['lists']['projects']['list'][$current];
					} else {
						$current = 0;
						foreach($_SESSION['lists']['projects']['list'] as $project_from_list){
							if ($project_from_list->pid == $project_id){
								$project = objectToArray($project_from_list);
								$_SESSION['lists']['projects']['list']['current'] = $current;
								$next_nr = $current < ($_SESSION['lists']['projects']['nr'] -1) ? $current + 1 : FALSE;
								$next_pid = $next_nr ? $_SESSION['lists']['projects']['list'][$next_nr]->pid : FALSE;
								$prev_nr = ($current > 0) ? ($current - 1) : FALSE;
								$prev_pid = ($prev_nr !== FALSE) ? $_SESSION['lists']['projects']['list'][$prev_nr]->pid : FALSE;
								$project['nav'] = array(
									'next_pid' =>  $next_pid,
									'next_nr' => $next_nr,
									'prev_pid' =>  $prev_pid,
									'prev_nr' => $prev_nr,
								);
								break;
							}
							$current++;
						}
					}
				}
				if (!$project){
					 $project = Project::getProjectById($project_id);
				}
				jsonGoodResult($project);
			} catch (Exception $e){
				jsonBadResult($e->getMessage());
			}
		}
		else{
			jsonBadResult( t("No valid project identifier submitted!"));
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