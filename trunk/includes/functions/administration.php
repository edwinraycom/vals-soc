<?php
include(_VALS_SOC_ROOT.'/includes/functions/tab_functions.php');
function showRoleDependentAdminPage($role){
	switch ($role){
		case 'administrator':
			showAdminPage();
			break;
		case 'supervisor':
			showSupervisorPage();
			break;
		case 'institute_admin':
			showInstitutePage();
			break;
		case 'organisation_admin':
			showOrganisationPage();
			break;
	}
}

function showProjectPage(){
	//TODO check for the role of current user
	
	$role = getRole();
	//Get my groups
	$my_organisations = Groups::getGroups('organisation');
	//print_r($my_organisations); die('dit later weer weg');
	if (!$my_organisations->rowCount()){
		//There are no organisations yet for this user
		if ($role == 'organisation_admin') {
			echo t('You have no organisation yet.').'<br/>';
			echo "<a href='"._VALS_SOC_URL. "/dashboard/members'>".t('Please go to the organisation register page')."</a>";
			
		} else {
			echo t('You are not connected to any organisation yet.').'<br/>';
			echo "<a href='"._VALS_SOC_URL. "/user'>".t('Please go to the register page')."</a>";
			
		}
	} else {
		echo '<h2>'.t('Your projects').'</h2>';
		//TODO weg print_r($my_organisations->fetchCol());die();
		$projects = Project::getProjectsByUser($role, $GLOBALS['user']->uid, $my_organisations->fetchCol());
		if (! $projects){
			echo t('You have no project yet registered');
			echo '<h2>'.t('Add your project').'</h2>';
			/*
				$f3 = drupal_get_form('vals_soc_project_form', '', 'group_page-1');
			$add_tab .= drupal_render($f3);
			*/
		
			$form = drupal_get_form('vals_soc_project_form', '', 'project_page-1');
			$form['#action'] = url('dashboard/projects/administer');
			// Process the submit button which uses ajax
			$form['submit'] = ajax_pre_render_element($form['submit']);
			// Build renderable array
			$build = array(
					'form' => $form,
					'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
			);
			// Print $form
			$add_tab = drupal_render($build);
			// Print JS
			$add_tab .= drupal_get_js();
		
			$data = array();
			$data[] = array(1, 'Add', 'addproject', 'project', null, "target=admin_container");
			echo renderTabs(1, null, 'project_page-', 'project', $data, null, TRUE, $add_tab);
			?>
				<script type="text/javascript">
		        	   activatetabs('tab_', ['project_page-1']);
		        </script><?php
		} else {
			$nr = 1;
			$data = array();
			$activating_tabs = array();
			
			foreach ($projects as $project){
				if ($nr == 1){
					$id = $project->pid;
					$my_project = $project;
				}
				$activating_tabs[] = "'project_page-$nr'";
				$data[] = array(0, $project->title, 'view', 'project', $project->pid);
				$nr++;
			}
	
			$data[] = array(1, 'Add', 'add', 'project', 0, "target=project_page-$nr");
			$activating_tabs[] = "'project_page-$nr'";
	
			$nr2 = 1;
			$data2 = array();
			// 		[translate, label, action, type, id, extra GET arguments]
			$data2[] = array(1, 'All Projects', 'list', 'project', null);
			$activating_tabs2 = array("'project2_page-$nr2'");
			if ($my_organisations->rowCount() > 1){
				foreach ($my_organisations as $organisation){
					$nr2++;
					$activating_tabs2[] = "'project2_page-$nr2'";
					$data2[] = array(2, $organisation->title, 'list', 'project', $organisation->org_id);
				}
			}
			echo renderTabs($nr, 'Project', 'project_page-', 'project', $data, $id, TRUE,
				renderProject($my_project, "project_page-1"));
	
			echo "<hr>";
			
			echo '<h2>'.t('All your projects').'</h2>';
			echo renderTabs($nr2, 'Organisation', 'project2_page-', 'organisation', $data2, null, TRUE,
				renderProjects('', $projects));
			?>
			<script type="text/javascript">
				activatetabs('tab_', [<?php echo implode(', ', $activating_tabs);?>]);
				activatetabs('tab_', [<?php echo implode(', ', $activating_tabs2);?>], null, true);
			</script>
		<?php
		}
	}
}


function showAdminPage(){
	//TODO check for the role of current user
	echo '<h2>'.t('All the groups and persons').'</h2>';
	$data = array();
	$data[] = array(1, 'Institutes', 'list', 'institute');
	$data[] = array(1, 'Organisations', 'list', 'organisation');
	$data[] = array(1, 'Tutors', 'list', 'supervisor');
	$data[] = array(1, 'Mentors', 'list', 'mentor');
	$data[] = array(1, 'Students', 'list', 'student');
	$data[] = array(1, 'Organisation Admins', 'list', 'organisation_admin');
	$data[] = array(1, 'Institute Admins', 'list', 'institute_admin');
	$nr_tabs = count($data);
	echo renderTabs($nr_tabs, null, 'admin_page-', '', $data, 0, TRUE,
			renderOrganisations('institute', '', 'all', 'admin_page-1'));
	$s = '';
	for ($i=1;$i <= $nr_tabs;$i++){
		$s .= ($i > 1)? ', ':'';
		$s .= "'admin_page-$i'";
	}
	?>
	<script type="text/javascript">
        	   activatetabs('tab_', [<?php echo $s;?>]);
        </script><?php
}


function showSupervisorPage(){
	//TODO check for the role of current user
	echo '<h2>'.t('Your student groups').'</h2>';
	//Get my groups
	$groups = Groups::getGroups('studentgroup', $GLOBALS['user']->uid);
	if (! $groups->rowCount()){
		echo t('You have no group yet registered');
		echo '<h2>'.t('Add your group').'</h2>';
		/*
		$f3 = drupal_get_form('vals_soc_studentgroup_form', '', 'group_page-1');
		$add_tab .= drupal_render($f3);
		*/

		$form = drupal_get_form('vals_soc_studentgroup_form', '', 'group_page-1');
		$form['#action'] = url('administer/members');
		// Process the submit button which uses ajax
		$form['submit'] = ajax_pre_render_element($form['submit']);
		// Build renderable array
		$build = array(
				'form' => $form,
				'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
		);
		// Print $form
		$add_tab = drupal_render($build);
		// Print JS
		$add_tab .= drupal_get_js();

		$data = array();
		$data[] = array(1, 'Add', 'addgroup', 'studentgroup', null, "target=admin_container");
		echo renderTabs(1, null, 'group_page-', 'studentgroup', $data, null, TRUE, $add_tab);
		?>
		<script type="text/javascript">
        	   activatetabs('tab_', ['group_page-1']);
        </script><?php
	} else {
		$nr = 1;
		$data = array();
		$activating_tabs = array();

		$nr2 = 1;
		$data2 = array();
		// 		[translate, label, action, type, id, extra GET arguments]
		$data2[] = array(1, 'All Students', 'showmembers', 'studentgroup', null);
		$activating_tabs2 = array("'group2_page-$nr2'");
		foreach ($groups as $group){
			if ($nr == 1){
				$id = $group->studentgroup_id;
				$my_group = $group;
			}
			$activating_tabs[] = "'group_page-$nr'";
			$data[] = array(0, $group->name, 'view', 'studentgroup', $group->studentgroup_id);
			$nr++;


			$nr2++;
			$activating_tabs2[] = "'group2_page-$nr2'";
			$data2[] = array(0, 'Group', 'showmembers', 'studentgroup', $group->studentgroup_id);
		}

		$data[] = array(1, 'Add', 'addgroup', 'studentgroup', null, "target=group_page-$nr");
		$activating_tabs[] = "'group_page-$nr'";

		echo sprintf('<h3>%1$s</h3>', t('Your groups'));
		echo renderTabs($nr, 'Group', 'group_page-', 'studentgroup', $data, $id, TRUE,
			renderOrganisation('studentgroup', $my_group, null, "group_page-1"));

		echo "<hr>";
		echo '<h2>'.t('All the registered students of your groups').'</h2>';
		echo renderTabs($nr2, 'Group', 'group2_page-', 'studentgroup', $data2, $id, TRUE,
			renderUsers('student', '', $my_group->studentgroup_id, 'studentgroup'));
		?>


		<script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(', ', $activating_tabs);?>]);
			activatetabs('tab_', [<?php echo implode(', ', $activating_tabs2);?>], null, true);
		</script>
	<?php
	}
}

function showInstitutePage(){
	//Get my institutions
	$institutes = Groups::getGroups('institute', $GLOBALS['user']->uid);
	if (! $institutes->rowCount()){
		echo t('You have no institute yet registered');
		$add_tab = '<h2>'.t('Add your institute').'</h2>';

		$f3 = drupal_get_form('vals_soc_institute_form', '', 'group_page-1');
		$add_tab .= drupal_render($f3);
		$data = array();
		$data[] = array(1, 'Add', 'add', 'institute', null, "target=admin_container", true);
		echo renderTabs(1, null, 'inst_page-', 'institute', $data, null, TRUE, $add_tab);
		?>
		<script type="text/javascript">
			   	
			activatetabs('tab_', ['inst_page-1']);
        </script><?php

	} else {
		$my_institute = $institutes->fetchObject();
		echo sprintf('<h3>%1$s</h3>', t('Your institute'));

        $nr = 3;
		$data = array();
		$tabs = array();
		$data[] = array(2, $my_institute->name, 'view', 'institute', $my_institute->inst_id, "buttons=0");
		$data[] = array(1, 'Edit', 'edit', 'institute', $my_institute->inst_id);
		$data[] = array(1, 'Delete', 'delete', 'institute', $my_institute->inst_id, '', false, 'delete');
		$tabs = array("'inst_page-1'", "'inst_page-2'", "'inst_page-3'");


		$nr2 = 2;
		$data2 = array();
// 		 [translate, label, action, type, id, extra GET arguments, render with rich text area, render tab to the right]
		$data2[] = array(1, 'All your Supervisors', 'showmembers', 'institute', $my_institute->inst_id, "subtype=supervisor");
		$data2[] = array(1, 'All your Students', 'showmembers', 'institute', $my_institute->inst_id, "subtype=student");

		$tabs2 = array("'member_page-1'", "'member_page-2'");
		
		$id = 0;
		$nr3 = $nr4 = 0;
		$data3 = $tabs3= $data4 = $tabs4 = array();
		$groups = Groups::getGroups('studentgroup', $GLOBALS['user']->uid);
		foreach ($groups as $group){
			$nr3++;
			$nr4++;
			if ($nr3 == 1){
				$id = $group->studentgroup_id;
				$my_group = $group;
				$students= Users::getStudents($id);
			}
			$tabs3[] = "'group_page-$nr3'";
			$data3[] = array(0, $group->name, 'view', 'studentgroup', $group->studentgroup_id);
			
			$tabs4[] = "'group2_page-$nr3'";
			$data4[] = array(0, 'Group', 'showmembers', 'studentgroup', $group->studentgroup_id);
		}
		$nr3++;
	//	[translate, label, action, type, id, extra GET arguments, render with rich text area, render tab to the right]
		$data3[] = array(1, 'Add', 'addgroup', 'studentgroup', null, "target=group_page-$nr3", true);
		$tabs3[] = "'group_page-$nr3'";

		
		//[number of tabs, label start, tab id start, type, data, id, render targets, active target content, active tab]
		echo renderTabs($nr, '', 'inst_page-', 'institute', $data, $my_institute->inst_id, TRUE,
				renderOrganisation('institute', $my_institute, null, "inst_page-1", false));
	    echo "<hr>";

	    echo '<h2>'.t('The registered supervisors and students of your institute').'</h2>';
	    echo renderTabs($nr2, '', 'member_page-', 'institute', $data2, $my_institute->inst_id, TRUE,
	    		renderUsers('supervisor', '', $my_institute->inst_id, 'institute'));
	    
	    echo '<h2>'.t('The groups of students of your institute').'</h2>';
	   
	    echo renderTabs($nr3, 'Group', 'group_page-', 'studentgroup', $data3, $id, TRUE,
	    		(($nr3 > 1) ?
	    		renderOrganisation('studentgroup', $my_group, null, "group_page-1"):
	    		tt('There is no group yet. Click "%1$s" to add one.', t('Add')))
	    				);
	    
	    if ($nr4 > 0){//There is more than the add tab
		    echo sprintf('<h2>%1$s</h2>', t('Your students in groups'));
		    
		    echo renderTabs($nr4, 'Group', 'group2_page-', 'studentgroup', $data4, $id, TRUE,
		    		renderStudents('', $students));
	    }
	    ?>
	    <script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(',', $tabs);?>]);
			activatetabs('tab_', [<?php echo implode(', ', $tabs2);?>], null, true);
			activatetabs('tab_', [<?php echo implode(', ', $tabs3);?>], null, true);
			activatetabs('tab_', [<?php echo implode(', ', $tabs4);?>], null, true);
		</script>
	    <?php
	}
}

function showOrganisationPage(){
	//Get my organisations
	$organisations = Groups::getGroups('organisation', $GLOBALS['user']->uid);
	if (! $organisations->rowCount()){
		echo t('You have no organisation yet registered');
		echo '<h2>'.t('Add your organisation').'</h2>';
		/*
		$f3 = drupal_get_form('vals_soc_organisation_form', '', 'organisation_page-1');
		$add_tab = drupal_render($f3);
		*/

		$form = drupal_get_form('vals_soc_organisation_form', '', 'organisation_page-1');
		$form['#action'] = url('administer/members');
		// Process the submit button which uses ajax
		$form['submit'] = ajax_pre_render_element($form['submit']);
		// Build renderable array
		$build = array(
			'form' => $form,
			'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
		);
		// Print $form
		$add_tab = drupal_render($build);
		// Print JS
		$target_fun_src = _VALS_SOC_URL.drupal_get_path('module', 'vals_soc') .'/includes/js/drupal_ajax_functions.js';
		$add_tab .= "<script type='text/javascript' src='$target_fun_src' ></script>";
		//$add_tab .= drupal_get_js();

		$data = array();
		$data[] = array(1, 'Add', 'add', 'organisation', null, "target=admin_container");
		echo renderTabs(1, null, 'organisation_page-', 'organisation', $data, null, TRUE, $add_tab);
		?>
		<script type="text/javascript">
        	   activatetabs('tab_', ['organisation_page-1']);
        </script><?php
	} else {
		$nr = 0;
		$data = array();
		$tabs = array();

		$nr2 = 1;
		$data2 = array();
// 		[translate, label, action, type, id, extra GET arguments]
		$data2[] = array(1, 'All your Mentors', 'showmembers', 'organisation', 0, 'subtype=mentor');
		$tabs2 = array("'mentor_page-$nr2'");
		foreach ($organisations as $org){
			$nr++;
			$nr2++;
			if ($nr == 1){
				$id = $org->org_id;
				$my_organisation = $org;
			}
			$tabs[] = "'organisation_page-$nr'";
			$tabs2[] = "'mentor_page-$nr2'";
			$data[] = array(2, $org->name, 'view', 'organisation', $org->org_id);
			$data2[] = array(2, $org->name, 'showmembers', 'organisation', $org->org_id);
		}
		//To remove the add tab: comment the three lines below
		$nr++;
		$data[] = array(1, 'Add', 'add', 'organisation', null, "target=organisation_page-$nr");
		$tabs[] = "'organisation_page-$nr'";

		echo sprintf('<h3>%1$s</h3>', t('Organisations you are involved in'));
		echo renderTabs($nr, 'Org', 'organisation_page-', 'organisation', $data, $id, TRUE,
				renderOrganisation('organisation', $my_organisation, null, "organisation_page-1"));
	    echo "<hr>";

	    echo '<h2>'.t('The registered mentors of your organisations').'</h2>';
	    echo renderTabs($nr2, 'Org', 'mentor_page-', 'organisation', $data2, null, TRUE,
	    		renderUsers('mentor', '', '', 'organisation'));
	    ?>
	    <script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(',', $tabs);?>]);
			activatetabs('tab_', [<?php echo implode(',', $tabs2);?>], null, true);
		</script>
	    <?php
	}
}