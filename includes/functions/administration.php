<?php
/*Expects data for every tab:
 * [translate, label, action, type, id, extra GET arguments]
 */
function renderTabs($count, $tab_label, $target_label, $type, $data, $id='',
	$render_targets=false, $active_content='', $active_tab=1){?>
	<ol id="toc"><?php
	$label_start = t($tab_label);
	$title = "";
	$un_named = 1;
	for ($t=0; $t < $count;$t++){
		$target = $target_label.($t + 1); ?>
		<li><a href="#tab_<?php echo $target;?>" <?php
		//title
		if ($data[$t][0] == 1){
			$link_text = t($data[$t][1]);
			$title = "";
		} elseif ($data[$t][0] == 0) {
			$link_text = "$label_start $un_named";
			$un_named++;
			$title = " title = '".$data[$t][1]."' ";
		} else {
			$link_text = $data[$t][1];
			$title = "";
		}
		echo $title;
		
		//onclick action
		if (isset($data[$t][2])){
				$action = $data[$t][2];
				$type = isset($data[$t][3]) ? $data[$t][3] : $type;
				$id =  isset($data[$t][4]) ? $data[$t][4] : $id;
			if (isset($data[$t][5])){
				$action	.= "&".$data[$t][5];
			}
			echo "onclick=\"ajaxCall('administration', '$action', {type:'$type', id:$id, target:'$target'}, '$target');\"";
		}
		
		?>><span><?php echo $link_text;?></span></a>
    	</li>
	<?php
	}?>
	</ol><?php
	if ($render_targets){
		for ($i=1; $i<= $count;$i++){
			echo "<div id='$target_label$i' class='content'>".
				"<div id='msg_$target_label$i'></div>".
				(($i == $active_tab) ? $active_content : '')."</div>";
		}
	}
	
}
function showRoleDependentAdminPage($role){
	switch ($role){
		case 'administrator':
			echo '<h2>'.t('Your groups').'</h2>';
			echo renderGroups();
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

function showSupervisorPage(){
	//TODO check for the role of current user
	echo '<h2>'.t('Your student groups').'</h2>';
// 	echo renderGroups();
// 	echo "<hr/>";
	//Get my groups
	$groups = Participants::getOrganisations('group', $GLOBALS['user']->uid);
	if (! $groups->rowCount()){
		echo t('You have no group yet registered');
		$add_tab = '<h2>'.t('Add your group').'</h2>';
		$f3 = drupal_get_form('vals_soc_group_form', '', 'group_page-1');
		$add_tab .= drupal_render($f3);
		$data = array();
		$data[] = array(1, 'Add', 'addgroup', 'group', null, "target=admin_container");
		echo renderTabs(1, null, 'group_page-', 'group', $data, null, TRUE, $add_tab);
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
		$data2[] = array(1, 'All Students', 'showmembers', 'group', null);
		$activating_tabs2 = array("'group2_page-$nr2'");
		foreach ($groups as $group){
			if ($nr == 1){
				$id = $group->group_id;
				$my_group = $group;
			}
			$activating_tabs[] = "'group_page-$nr'";
			$data[] = array(0, $group->name, 'view', 'group', $group->group_id);
			$nr++;
			
			
			$nr2++;
			$activating_tabs2[] = "'group2_page-$nr2'";
			$data2[] = array(0, 'Group', 'showmembers', 'group', $group->group_id);
		}
		
		$data[] = array(1, 'Add', 'addgroup', 'group', null, "target=group_page-$nr");
		$activating_tabs[] = "'group_page-$nr'";
		
		echo sprintf('<h3>%1$s</h3>', t('Your groups'));
		echo renderTabs($nr, 'Group', 'group_page-', 'group', $data, $id, TRUE, 
			renderOrganisation('group', $my_group, null, "group_page-1"));
	
	echo "<hr>";
	echo '<h2>'.t('All the registered students of your groups').'</h2>';
	echo renderTabs($nr2, 'Group', 'group2_page-', 'group', $data2, $id, TRUE, 
			renderParticipants('student', '', $my_group->group_id, 'group'));
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
	$institutes = Participants::getOrganisations('institute', $GLOBALS['user']->uid);
	if (! $institutes->rowCount()){
		echo t('You have no institute yet registered');
		echo '<h2>'.t('Add your institute').'</h2>';
		
		$f3 = drupal_get_form('vals_soc_institute_form', '', 'group_page-1');
		$add_tab .= drupal_render($f3);
		$data = array();
		$data[] = array(1, 'Add', 'add', 'institute', null, "target=admin_container");
		echo renderTabs(1, null, 'inst_page-', 'institute', $data, null, TRUE, $add_tab);
		?>
		<script type="text/javascript">
        	   activatetabs('tab_', ['inst_page-1']);
        </script><?php
		
	} else {
		$my_institute = $institutes->fetchObject();
		echo sprintf('<h3>%1$s</h3>', t('Your institute'));
        
        $nr = 2;
		$data = array();
		$tabs = array();
		$data[] = array(2, $my_institute->name, 'view', 'institute', $my_institute->inst_id);
		$data[] = array(1, 'Edit', 'edit', 'institute', $my_institute->inst_id);
		$tabs = array("'inst_page-1'", "'inst_page-2'");
		
		
		$nr2 = 2;
		$data2 = array();
// 		[translate, label, action, type, id, extra GET arguments]
		$data2[] = array(1, 'All your Supervisors', 'showmembers', 'institute', $my_institute->inst_id, "subtype=supervisor");
		$data2[] = array(1, 'All your Students', 'showmembers', 'institute', $my_institute->inst_id, "subtype=student");
		
		$tabs2 = array("'member_page-1'", "'member_page-2'");
		
		//[number of tabs, label start, tab id start, type, data, id, render targets, active target content, active tab]
		echo renderTabs($nr, '', 'inst_page-', 'institute', $data, $my_institute->inst_id, TRUE,
				renderOrganisation('institute', $my_institute, null, "inst_page-1"));
	    echo "<hr>";
	    	
	    echo '<h2>'.t('The registered supervisors and students of your institute').'</h2>';
	    echo renderTabs($nr2, '', 'member_page-', 'institute', $data2, $my_institute->inst_id, TRUE,
	    		renderParticipants('supervisor', '', $my_institute->inst_id, 'institute'));
	    ?>
	    <script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(',', $tabs);?>]);
			activatetabs('tab_', [<?php echo implode(',', $tabs2);?>], null, true);
		</script>
	    <?php
	}
}

function showOrganisationPage(){
	//Get my organisations
	$organisations = Participants::getOrganisations('organisation', $GLOBALS['user']->uid);
	if (! $organisations->rowCount()){
		echo t('You have no organisation yet registered');
		echo '<h2>'.t('Add your organisation').'</h2>';
		$f3 = drupal_get_form('vals_soc_organisation_form', '', 'organisation_page-1');
		$add_tab = drupal_render($f3);
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
		$data2[] = array(1, 'All your Mentors', 'showmembers', 'organisation', null, 'subtype=mentor');
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
		
		echo sprintf('<h3>%1$s</h3>', t('Your organisations'));
		echo renderTabs($nr, 'Org', 'organisation_page-', 'organisation', $data, $id, TRUE,
				renderOrganisation('organisation', $my_organisation, null, "organisation_page-1"));
	    echo "<hr>";
	    	
	    echo '<h2>'.t('The registered mentors of your organisation').'</h2>';
	    echo renderTabs($nr2, 'Org', 'mentor_page-', 'organisation', $data2, null, TRUE,
	    		renderParticipants('mentor', '', '', 'organisation'));
	    ?>
	    <script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(',', $tabs);?>]);
			activatetabs('tab_', [<?php echo implode(',', $tabs2);?>], null, true);
		</script>
	    <?php
	}
}