<?php
include_once(_VALS_SOC_ROOT.'/includes/functions/tab_functions.php');//it is sometimes included after propjects.php which does the same

function showRoleDependentAdminPage($role, $show_action='administer', $show_last= FALSE){
	echo "<a href='"._VALS_SOC_URL."/dashboard'>".t('Back To Dashboard')."</a><br/>";
	switch ($role){
		case _ADMINISTRATOR_TYPE:
			showAdminPage($show_action, $show_last);
			break;
		case _SUPERVISOR_TYPE:
			showSupervisorPage($show_action, $show_last);
			break;
		case _INSTADMIN_TYPE:
			showInstitutePage($show_action, $show_last);
			break;
		case _MENTOR_TYPE:
		case _ORGADMIN_TYPE:
			showOrganisationPage($show_action, $show_last);
			break;
	}
}

function showAdminPage($show_action, $show_last=FALSE){
	//TODO check for the role of current user
	echo '<h2>'.t('All the groups and persons').'</h2>';
	$data = array();
	$data[] = array(1, 'Institutes', 'list', _INSTITUTE_GROUP);
	$data[] = array(1, 'Organisations', 'list', _ORGANISATION_GROUP);
	$data[] = array(1, 'Tutors', 'list', _SUPERVISOR_TYPE);
	$data[] = array(1, 'Mentors', 'list', _MENTOR_TYPE);
	$data[] = array(1, 'Students', 'list', _STUDENT_TYPE);
	$data[] = array(1, 'Organisation Admins', 'list', _ORGADMIN_TYPE);
	$data[] = array(1, 'Institute Admins', 'list', _INSTADMIN_TYPE);
	$nr_tabs = count($data);
	echo renderTabs($nr_tabs, null, 'admin_page-', '', $data, 0, TRUE,
			renderOrganisations(_INSTITUTE_GROUP, '', 'all', 'admin_page-1'));
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

function showSupervisorPage($show_action, $show_last=FALSE){
	//Get my institutions
	$institutes = Groups::getGroups(_INSTITUTE_GROUP, $GLOBALS['user']->uid);
	if (! $institutes->rowCount()){
		echo t('You have not registered yourself to an institute yet. ');
	} else {
		$my_institute = $institutes->fetchObject();
		
		if ($show_action == 'administer'){
			echo t('You should be an admin member to edit this institute');//showInstituteAdminPage($my_institute);
		} elseif ($show_action == 'view'){
			showInstituteAdminPage($my_institute, $show_action);
		} elseif ($show_action == 'members') {
			showInstituteMembersPage($my_institute, $show_last);
		} elseif ($show_action == 'groups'){
			showInstituteGroupsAdminPage($my_institute, $show_last);
		} else {
			echo tt('there is no such action possible %1$s', $show_action);	
		}
	}
}
/*function showSupervisorPage($show_action, $show_last=FALSE){
	//TODO check for the role of current user
	echo '<h2>'.t('Your students').'</h2>';
	//Get my groups
	$groups = Groups::getGroups(_STUDENT_GROUP, $GLOBALS['user']->uid);
	$tab_prefix = 'group_page-';
	$tab_id_prefix2 = 'group_page2-';
	$target = "${tab_prefix}1";
	if (! $groups->rowCount()){
		echo t('You have no group yet registered');
		echo '<h2>'.t('Add your group').'</h2>';

		$form = drupal_get_form('vals_soc_studentgroup_form', '', $target, $show_action);
		$form['#action'] = url('administer/members');
		// Process the submit button which uses ajax
		//$form['submit'] = ajax_pre_render_element($form['submit']);
		// Build renderable array
// 		$build = array(
// 				'form' => $form,
// 				'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
// 		);
		// Print $form
		$add_tab = renderForm($form, $target, true);
		// Print JS
		//$add_tab .= drupal_get_js();

		$data = array();
		$data[] = array(1, 'Add', 'add', _STUDENT_GROUP, null, "target=admin_container&show_action=$show_action");
		echo renderTabs(1, null, $tab_prefix, _STUDENT_GROUP, $data, null, TRUE, $add_tab);
		?>
		<script type="text/javascript">
				transform_into_rte();
				activatetabs('tab_', ['<?php echo $target;?>']);
        </script><?php
	} else {
		$nr = 1;
		$data = array();
		$activating_tabs = array();

		$nr2 = 1;
		$data2 = array();
		// 		[translate, label, action, type, id, extra GET arguments]
		$data2[] = array(1, 'All Students', 'showmembers', _STUDENT_GROUP, null);
		$activating_tabs2 = array("'$tab_id_prefix2$nr2'");
		foreach ($groups as $group){
			if ($nr == 1){
				$id = $group->studentgroup_id;
				$my_group = $group;
			}
			$activating_tabs[] = "'$tab_id_prefix$nr'";
			$data[] = array(0, $group->name, 'view', _STUDENT_GROUP, $group->studentgroup_id);
			$nr++;


			$nr2++;
			$activating_tabs2[] = "'$tab_id_prefix2$nr2'";
			$data2[] = array(0, 'Group', 'showmembers', _STUDENT_GROUP, $group->studentgroup_id);
		}

		//$data[] = array(1, 'Add', 'add', _STUDENT_GROUP, null, "target=group_page-$nr&show_action=$show_action");
		$activating_tabs[] = "'$tab_id_prefix$nr'";

		echo sprintf('<h3>%1$s</h3>', t('Your students'));
		echo renderTabs($nr, 'Group', $tab_id_prefix, _STUDENT_GROUP, $data, $id, TRUE,
			renderOrganisation(_STUDENT_GROUP, $my_group, null, $target));

		echo "<hr>";
		echo '<h2>'.t('All the registered students of your groups').'</h2>';
		echo renderTabs($nr2, 'Group', $tab_id_prefix2, _STUDENT_GROUP, $data2, $id, TRUE,
			renderUsers(_STUDENT_TYPE, '', $my_group->studentgroup_id, _STUDENT_GROUP));
		?>


		<script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(', ', $activating_tabs);?>]);
			activatetabs('tab_', [<?php echo implode(', ', $activating_tabs2);?>], null, true);
		</script>
	<?php
	}
}
*/

function showInstitutePage($show_action, $show_last=FALSE){
	//Get my institutions
	$institutes = Groups::getGroups(_INSTITUTE_GROUP, $GLOBALS['user']->uid);
	if (! $institutes->rowCount()){
		echo t('You have no institute yet registered.');
		$add_tab = '<h2>'.t('Add your institute').'</h2>';
		$tab_prefix = 'inst_page-';
		$target = "${tab_prefix}1";
		$form = drupal_get_form('vals_soc_institute_form', '', $target, $show_action);
		unset($form['cancel']);
		$add_tab .= renderForm($form, $target, true);
		$data = array();
		$data[] = array(1, 'Add', 'add', _INSTITUTE_GROUP, null, "target=admin_container&show_action=$show_action", false);
		echo renderTabs(1, null, $tab_prefix, _INSTITUTE_GROUP, $data, null, TRUE, $add_tab);
		?>
		<script type="text/javascript">
			transform_into_rte();
			activatetabs('tab_', ['<?php echo $target;?>']);
        </script><?php

	} else {
		$my_institute = $institutes->fetchObject();
		if ($show_action == 'administer'){
			//show_last is not relevant as we always expect max 1 institute
			showInstituteAdminPage($my_institute, $show_action);
		} elseif ($show_action == 'view'){
			//show_last is not relevant as we always expect max 1 institute
			showInstituteAdminPage($my_institute, $show_action);
		} elseif ($show_action == 'members') {
			showInstituteMembersPage($my_institute, $show_last);
		} elseif ($show_action == 'groups'){
			showInstituteGroupsAdminPage($my_institute, $show_last);
		} else {
			echo tt('there is no such action possible %1$s', $show_action);	
		}
	}
}

function showInstituteGroupsAdminPage($my_institute, $show_last){
	
		$id = 0;
		$nr3 = $nr4 = 0;
		$data3 = $tabs3= $data4 = $tabs4 = array();
		
		$groups = Groups::getGroups(_STUDENT_GROUP, $GLOBALS['user']->uid);
		
		$tab_id_prefix = 'group_page-';
		$tab_id_prefix2 = 'group2_page-';
		$nr_groups = $groups->rowCount();
		$current_tab = $show_last ? $nr_groups : 1;
		foreach ($groups as $group){
			$nr3++;
			$nr4++;
			if ($nr3 == $current_tab){
				$id = $group->studentgroup_id;
				$my_group = $group;
				$students = Users::getStudents($id);
			}
			$tabs3[] = "'$tab_id_prefix$nr3'";
			$data3[] = array(2, $group->name, 'view', _STUDENT_GROUP, $group->studentgroup_id);
			
			$tabs4[] = "'$tab_id_prefix2$nr3'";
			$data4[] = array(2, $group->name, 'showmembers', _STUDENT_GROUP, $group->studentgroup_id);
		}
		$nr3++;
	//	[translate, label, action, type, id, extra GET arguments, render with rich text area, render tab to the right]
		$data3[] = array(1, 'Add', 'add', _STUDENT_GROUP, null, 
			"target=$tab_id_prefix$nr3&show_action=groups", false, 'addition');
		$tabs3[] = "'$tab_id_prefix$nr3'";

	    echo '<h2>'.t('The student groups of your institute').'</h2>';
	   
	    echo renderTabs($nr3, 'Group', $tab_id_prefix, _STUDENT_GROUP, $data3, $id, TRUE,
	    		(($nr3 > 1) ?
	    		renderOrganisation(_STUDENT_GROUP, $my_group, null, "${tab_id_prefix}$current_tab"):
	    		tt('There is no group yet. Click "%1$s" to add one.', t('Add')))
	    		,$current_tab		);
	    
	    if ($nr4 > 0){//There is more than the add tab
		    echo sprintf('<h2>%1$s</h2>', t('Your students as divided in groups'));
		    
		    echo renderTabs($nr4, 'Group', $tab_id_prefix2, _STUDENT_GROUP, $data4, $id, TRUE,
		    		renderStudents('', $students));
	    }
	    ?>
	    <script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(', ', $tabs3);?>], '<?php echo 
					"$tab_id_prefix$current_tab";?>');
			activatetabs('tab_', [<?php echo implode(', ', $tabs4);?>], null, true);
		</script>
	    <?php
}

function showInstituteMembersPage($my_institute, $show_last=FALSE){

		
		$nr2 = 2;
		$data2 = array();
// 		 [translate, label, action, type, id, extra GET arguments, render with rich text area, render tab to the right]
		$data2[] = array(1, 'Staff', 'showmembers', _INSTITUTE_GROUP, $my_institute->inst_id, "subtype=staff");
		$data2[] = array(1, 'Students', 'showmembers', _INSTITUTE_GROUP, $my_institute->inst_id, "subtype=student");

		$tabs2 = array("'member_page-1'", "'member_page-2'");
		
		$id = 0;
// 		$nr3 = 0;
		$nr4 = 0;
// 		$data3 = $tabs3= array();
		$data4 = $tabs4 = array();
		$groups = Groups::getGroups(_STUDENT_GROUP, $GLOBALS['user']->uid);
		
// 		$tab_id_prefix = 'group_page-';
		$tab_id_prefix2 = 'group2_page-';
		//$nr_groups = $groups->rowCount();
		//$current_tab = $show_last ? $nr_groups : 1;
		$current_tab = 1;
		foreach ($groups as $group){
			//$nr3++;
			$nr4++;
			if ($nr4 == $current_tab){
				$id = $group->studentgroup_id;
				$my_group = $group;
				$students = Users::getStudents($id);
			}
// 			$tabs3[] = "'$tab_id_prefix$nr3'";
// 			$data3[] = array(0, $group->name, 'view', _STUDENT_GROUP, $group->studentgroup_id);
			
			$tabs4[] = "'$tab_id_prefix2$nr4'";
			$data4[] = array(0, $group->name, 'showmembers', _STUDENT_GROUP, $group->studentgroup_id);//'Group'
		}
// 		$nr4++;
// 		$tabs4[] = "'$tab_id_prefix2$nr4'";
// 		$data4[] = array(1, 'Add', 'add', _STUDENT_GROUP, 0, 'show_action=groups', false, 'totheright');//'Group
// 		$nr3++;
	//	[translate, label, action, type, id, extra GET arguments, render with rich text area, render tab to the right]
// 		$data3[] = array(1, 'Add', 'add', _STUDENT_GROUP, null, "target=$tab_id_prefix$nr3", true);
// 		$tabs3[] = "'$tab_id_prefix$nr3'";
		echo '<h2>'.t('The registered staff and students of your institute').'</h2>';
	    echo renderTabs($nr2, '', 'member_page-', _INSTITUTE_GROUP, $data2, $my_institute->inst_id, TRUE,
	    		renderUsers(_INSTADMIN_TYPE, '', $my_institute->inst_id, _INSTITUTE_GROUP, TRUE).
	    		renderUsers(_SUPERVISOR_TYPE, '', $my_institute->inst_id, _INSTITUTE_GROUP, TRUE));
	    		
	    if ($nr4 > 0){//There is more than the add tab
		    echo sprintf('<h2>%1$s</h2>', t('Your students as divided in groups'));
		    
		    echo renderTabs($nr4, 'Group', $tab_id_prefix2, _STUDENT_GROUP, $data4, $id, TRUE,
		    		($students && $students->rowCount()) ? renderStudents('', $students) :
		    			t('There are no students yet in this group registered.'));
	    } else {
	    	echo "<br/>".t('There are no groups yet in this institute.'). "<a href='".
	    	_VALS_SOC_URL."/dashboard/institute/group/administer'>".t('Create new groups'). "</a>";
	    }
	    ?>
	    <script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(', ', $tabs2);?>]);
			<?php if ($nr4 > 0){?>activatetabs('tab_', [<?php echo implode(', ', $tabs4);?>], null, true);<?php }?>
		</script>
	    <?php
	
}

//Just being lazy: we can also view the institute with this function
function showInstituteAdminPage($my_institute, $action='administer'){	
		$nr = 1;
		$tab_id_prefix = 'inst_page-';
		$data = array();
		$tabs = array("'${tab_id_prefix}1'");
		
		//we pass on the buttons=0 since we have the buttons as tabs
		$data[] = array(2, $my_institute->name, 'view', _INSTITUTE_GROUP, $my_institute->inst_id, "buttons=0");
		if ($action=='administer'){
			$data[] = array(1, 'Delete', 'delete', _INSTITUTE_GROUP, $my_institute->inst_id, '', false, 'delete');$nr++;
			$data[] = array(1, 'Edit', 'edit', _INSTITUTE_GROUP, $my_institute->inst_id, '', false, 'editing');$nr++;
			$tabs[] = "'${tab_id_prefix}2'";
			$tabs[] = "'${tab_id_prefix}3'";
		} 

		//[number of tabs, label start, tab id start, type, data, id, render targets, active target content, active tab]
		echo renderTabs($nr, '', $tab_id_prefix, _INSTITUTE_GROUP, $data, $my_institute->inst_id, TRUE,
				renderOrganisation(_INSTITUTE_GROUP, $my_institute, null, "${tab_id_prefix}1", false));
	    ?>
	    <script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(',', $tabs);?>]);
		</script>
	    <?php
}

function showOrganisationPage($show_action, $show_last=FALSE){
	//Get my organisations
	$organisations = Groups::getGroups(_ORGANISATION_GROUP, $GLOBALS['user']->uid);
	if (! $organisations->rowCount() && user_access('vals admin register')){
		echo t('You have no organisation yet registered');
		echo '<h2>'.t('Add your organisation').'</h2>';
		$tab_prefix = 'organisation_page-';
		$target = "${tab_prefix}1";
		$form = drupal_get_form('vals_soc_organisation_form', '', $target);
		/*
		$add_tab .= drupal_render($f3);
		*/
// 		$form['submit'] = ajax_pre_render_element($form['submit']);
// 		$extra_js = valssoc_form_get_js($form);
// 		$form['#attached']['js'] = array();
		// Print $form
// 		$add_tab = drupal_render($form);
// 		$add_tab .=  $extra_js;
		
		$add_tab = renderForm($form, $target, true);
		
		$data = array();
		$data[] = array(1, 'Add', 'add', _ORGANISATION_GROUP, null, "target=admin_container&show_action=$show_action", true, 'adding_to_the right');
		echo renderTabs(1, null, $tab_prefix, _ORGANISATION_GROUP, $data, null, TRUE, $add_tab);
		?>
		<script type="text/javascript">
		   transform_into_rte();
		   activatetabs('tab_', ['<?php echo $target;?>']);
        </script><?php
	} else {
		if ($show_action == 'administer'){
			showOrganisationAdminPage($organisations, $show_action, $show_last);
		} elseif ($show_action == 'view'){
			showOrganisationAdminPage($organisations, $show_action, $show_last);
		} elseif ($show_action == 'members') {
			showOrganisationMembersPage($organisations);
		} else {
			echo tt('There is no such action possible %1$s', $show_action);
		}
	}
}

function showOrganisationAdminPage($organisations, $action='administer', $show_last=FALSE){
	$nr = 0;
	$data = array();
	$tabs = array();

	$tab_id_prefix = 'organisation_page-';
	$nr_orgs = $organisations->rowCount();
	$current_tab = $show_last ? $nr_orgs : 1;
	foreach ($organisations as $org){
		$nr++;
		if ($nr == $current_tab){
			$id = $org->org_id;
			$my_organisation = $org;
		}
		$tabs[] = "'$tab_id_prefix$nr'";
		$data[] = array(2, $org->name, 'view', _ORGANISATION_GROUP, $org->org_id);
	}
	// check for org admin editing rights
	if(($action == 'administer') && user_access('vals admin register')){
		//To remove the add tab: comment the three lines below
		$nr++;
		$data[] = array(1, 'Add', 'add', _ORGANISATION_GROUP, null, "target=$tab_id_prefix$nr&show_action=administer", true, 'adding_to_the right');
		$tabs[] = "'$tab_id_prefix$nr'";
	}
	echo sprintf('<h3>%1$s</h3>', t('Organisations you are involved in'));
	echo renderTabs($nr, 'Org', $tab_id_prefix, _ORGANISATION_GROUP, $data, $id, TRUE,
			//$type, $organisation='', $organisation_owner='', $target='', $show_buttons=true)
		renderOrganisation(_ORGANISATION_GROUP, $my_organisation, null, "$tab_id_prefix$current_tab", ($action == 'administer')), $current_tab);
	?>
	<script type="text/javascript">
		activatetabs('tab_', [<?php echo implode(',', $tabs);?>], '<?php echo "$tab_id_prefix$current_tab";?>');
		</script>
	<?php
}

function showOrganisationMembersPage($organisations){
	$tab_offset = 1;
	$data = array();
	$tabs = array();
	// 		[translate, label, action, type, id, extra GET arguments]
	if(user_access('vals admin register')){
		$data[] = array(1, t('All Members'), 'showmembers', _ORGANISATION_GROUP, 'all');
		$tabs = array("'mentor_page-$tab_offset'");
		$tab_offset++;
	}
	
	foreach ($organisations as $org){
		$tabs[] = "'mentor_page-$tab_offset'";
		$data[] = array(2, $org->name, 'showmembers', _ORGANISATION_GROUP, $org->org_id);
		$tab_offset++;
	}
	if ($data){
		$first_tab_group_id = $data[0][4];
		
		echo renderTabs(--$tab_offset, 'Org', 'mentor_page-', _ORGANISATION_GROUP, $data, null, TRUE,
				renderUsers(_ORGADMIN_TYPE, '', $first_tab_group_id, _ORGANISATION_GROUP, TRUE).
				renderUsers(_MENTOR_TYPE, '', $first_tab_group_id, _ORGANISATION_GROUP, TRUE));
	
		?>
		<script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(',', $tabs);?>]);
		</script>
	<?php
	} else {
		echo t('You cannot see organisation members at the moment');	
	}
}
