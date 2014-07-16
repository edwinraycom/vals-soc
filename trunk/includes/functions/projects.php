<?php
include_once(_VALS_SOC_ROOT.'/includes/functions/tab_functions.php');//it is sometimes included after administration.php which does the same

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
		//echo '<h2>'.t('Your projects').'</h2>';
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
			$data[] = array(1, 'Add', 'addproject', 'project', null, "target=admin_container", true, 'adding from the right');
			echo renderTabs(1, null, 'project_page-', 'project', $data, null, TRUE, $add_tab,'1','project');
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
	
			$data[] = array(1, 'Add', 'add', 'project', 0, "target=project_page-$nr", true, 'adding from the right');
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
				renderProject($my_project, "project_page-1"),'1','project');
	
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

function renderProjects($organisation_selection='', $projects='', $target=''){
	if (!$projects){
		//if we pass empty value to getGroups the current supervisor is assumed
		$projects = Groups::getGroups($organisation_selection);
	}
	$target_set = ! empty($target);
	if ($projects){
		$s = "<ul class='projectlist'>";
		foreach($projects as $project){
			$project = objectToArray($project);
			$s .= "<li>";
			// $member_url = "/vals/actions/project"
			if (!$target_set) {
				$target = "show_${project['pid']}";
			}
			$s .= "<a href='javascript: void(0);' onclick='".
				//($target_set ? "" : "\$jq(\"#$target\").toggle();").
			"ajaxCall(\"project\", \"view\", {id:${project['pid']},type:\"project\", target:\"$target\"}, \"$target\");'>${project['title']}</a>";
			if (! $target_set) {
				$s .= "<div id='$target' ></div>";
			}
			$s .= "</li>";
		}
		$s .= "</ul>";
		return $s;
	} 
	else {
		return t('You have no projects yet');
	}
}

function renderProject($project='', $target=''){
	if (!$project){
		return t('I cannot show this project. It seems empty');
	}
	if (is_object($project)){
		$project = objectToArray($project);
	} else {
		//return 'het Is GEEN object dus array';
	}
	$key_name = Groups::keyField('project');
	$id = $project[$key_name];
	$content = "<h2>".$project['title']."</h2>";
	$content .= '<p>'.$project['description']. '</p>';
	if ($project['url']){
		$content .= '<p>'.tt('More information can be found at %1$s', "<a href='${project['url']}'> ${project['url']}</a>"). '</p>';
	}
	$role = getRole();
	$content .="<div class=\"totheright\">";
	if ('student' == getRole()){
		$content .="<br/><br/><input type='button' onclick=\"getProposalFormForProject(".$project['pid'].
		")\" value='Submit proposal for this project'/>";
	}
	if (Groups::isAssociate('project', $id)){
		$delete_action = "onclick='if(confirm(\"".t('Are you sure?')."\")){ajaxCall(\"project\", \"delete\", {type: \"project\", id: $id}, \"refreshTabs\", \"json\", [\"project\", \"$target\", \"project\"]);}'";
		$edit_action = "onclick='ajaxCall(\"project\", \"edit\", {type: \"project\", id: $id, target: \"$target\"}, \"formResult\", \"html\", [\"$target\", \"project\"]);'";
		//$edit_action = "onclick='ajaxCall(\"project\", \"edit\", {type: \"project\", id: $id, target: \"$target\"}, \"formResult\", \"html\", \"$target\");'";
	
		$content .= "<input type='button' value='".t('edit')."' $edit_action/>";
		$content .= "<input type='button' value='".t('delete')."' $delete_action/>";
	}
	$content .="</div>";
	return $content;
}

function initBrowseProjectLayout(){//$target='content'
	$org_id=0;
	if(isset($_GET['organisation'])){
		$org_id = $_GET['organisation'];
	}
	$apply_projects = vals_soc_access_check('dashboard/projects/apply') ? 1 : 0;
	?>
	<div class="filtering" id="browse_projects">
		<span id="infotext" style="margin-left: 34px"></span>
		<form id="project_filter">
		<?php echo t('Tags');?>: <input type="text" name="tags" id="tags" />
		<?php echo t('Organisations');?>:
			<select id="organisation" name="organisation">
			<option <?php echo  (! $org_id) ? 'selected="selected"': ''; ?> value="0"><?php echo t('All Organisations');?></option><?php
			$result = Organisations::getInstance()->getOrganisationsLite();
			foreach ($result as $record) {
				$selected = ($record->org_id == $org_id ? 'selected="selected" ' : '');
				echo '<option ' .$selected.'value="'.$record->org_id.'">'.$record->name.'</option>';
			}?>
			</select>
		</form>
	</div>
	<div id="ProjectTableContainer" style="width: 600px;"></div>

<script type="text/javascript">	
	jQuery(document).ready(function($){

	window.view_settings = {};
	window.view_settings.apply_projects = <?php echo $apply_projects ? 1: 0;?>;
	//window.view_settings.target_id = '<?php //echo $target;?>';
	//var apply_projects = <?php //echo $apply_projects ? 1: 0;?>;
	///var target_id = '<?php //echo $target;?>';//was misused in old getProposalFormForProject only
	

	//Prepare jTable
	$("#ProjectTableContainer").jtable({
		//title: "Table of projects",
		paging: true,
		pageSize: 10,
		sorting: true,
		defaultSorting: "title ASC",
		actions: {
			listAction: moduleUrl + "actions/project_actions.php?action=list_projects"
		},
		fields: {
			pid: {								
				key: true,
				create: false,
				edit: false,
				list: false
			},
			title: {
				title: "Project title",
				width: "40%"
			},
			name: {
				title: "Organisation",
				width: "20%"
			},
			tags: {
				title: "Tags",
				width: "26%",
				create: false,
				edit: false
			},
			Detail: {
				width: "2%",
				title: "",
				sorting: false,
				display: function (data) {
					return "<a title=\"View project details\" href=\"#\" onclick=\"getProjectDetail("+
						data.record.pid+")\"><span class=\"ui-icon ui-icon-info\"></span></a>";
					},
				create: false,
				edit: false
			}<?php 
			if ($apply_projects) {?>
				,
				Propose: {
					width: "2%",
					title: "",
					sorting: false,
					display: function (data) {
						return "<a title=\"Propose a project for this idea\" href=\"#\" onclick=\"getProposalFormForProject("+data.record.pid+")\">"+
						"<span class=\"ui-icon ui-icon-script\"></span></a>";
						},
					create: false,
					edit: false
				}<?php 
			}?>
		
		}
		
	});
	
	//Load project list from server on initial page load
	$("#ProjectTableContainer").jtable("load", {
		tags: $("#tags").val(),
		organisation: $("#organisation").val()
	});
		
	$("#tags").keyup(function(e) {
		e.preventDefault();
		// only auto clear when there is no tag info
		if(testTagInput() && $("#tags").val()==""){
			$("#ProjectTableContainer").jtable("load", {
			tags: $("#tags").val(),
			organisation: $("#organisation").val()
			});
		}
	});
		
	$("#organisation").change(function(e) {
		e.preventDefault();
		if(testTagInput()){
			$("#ProjectTableContainer").jtable("load", {
				tags: $("#tags").val(),
				organisation: $("#organisation").val()
			});
		}
	});
	
	$("#project_filter").submit(function(e){
		e.preventDefault();
		if(testTagInput()){
			$("#ProjectTableContainer").jtable("load", {
				tags: $("#tags").val(),
				organisation: $("#organisation").val()
			});
		}
	});
		
	// define these at the window level so that they can still be called once loaded
	window.getProposalFormForProject = getProposalFormForProject;
	window.getProjectDetail = getProjectDetail;
	
	});
	</script>
<?php
}