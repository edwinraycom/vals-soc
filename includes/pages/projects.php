<?php
include_once(_VALS_SOC_ROOT.'/includes/functions/tab_functions.php');//it is sometimes included after administration.php which does the same

function showProjectPage($show_last=FALSE, $owner_only=false){
	global $base_url;
	
	//TODO check for the role of current user
	$role = getRole();
	if (!Users::isMentor()){//true for both mentors and organisation admins. Also, they will see their own stuff only
		echo t('You are not allowed to see the projects in this view.');
		return;
	}
	//Get my groups
	$my_organisations = Groups::getGroups(_ORGANISATION_GROUP);	
	if (!$my_organisations->rowCount()){
		//There are no organisations yet for this user
		if ($role == _ORGADMIN_TYPE) {
			echo t('You have no organisation yet.').'<br/>';
			echo "<a href='"._WEB_URL. "/dashboard/organisation/administer'>".t('Please go to the organisation register page')."</a>";

		} else {
			echo t('You are not connected to any organisation yet.').'<br/>';
			echo "<a href='"._WEB_URL. "/user/".Users::getMyId()."/edit'>".t('Please edit your account to connect')."</a>";

		}
	} else {
		$show_all = ! (bool) $owner_only;
		$owner_id = $GLOBALS['user']->uid;
		$orgs = array();
		$orgids = array();
		foreach ($my_organisations as $org){
			$orgs[] = $org;
			$orgids[] = $org->org_id;
		}
		$projects = Project::getProjectsByUser($owner_id, $orgids, $show_all);//$my_organisations->fetchCol());
		
		if (! $projects){
			echo $owner_only ? t('You have no project yet registered') : t('There are no projects yet registered.');
			echo $owner_only ? "<BR>".'<a href="'.$base_url.'/dashboard/projects/administer" '.
				'title="Manage all my organisation\'s projects">Manage all my organisation\'s projects</a>': '';
			echo '<h2>'.t('Add a project').'</h2>';
			
			$tab_prefix = 'project_page-';
			$target = "${tab_prefix}1";
			$form = drupal_get_form("vals_soc_project_form", '', 'project_page-1');
			$form['submit'] = ajax_pre_render_element($form['submit']);

			$add_tab = renderForm($form, $target, true);

			$data = array();
			$data[] = array(1, 'Add', 'add', _PROJECT_OBJ, '0', "target=admin_container", true, 'adding from the right');
			echo renderTabs(1, null, 'project_page-', _PROJECT_OBJ, $data, null, TRUE, $add_tab, 1,_PROJECT_OBJ);
			?>
				<script type="text/javascript">
					   transform_into_rte();
		        	   activatetabs('tab_', ['project_page-1']);
		        </script><?php
		} else {

			echo "<a href='"._WEB_URL. "/dashboard/projects/administer'>".t('Show all')."</a>";
			echo " | ";
			echo "<a href='"._WEB_URL. "/dashboard/projects/administer/mine'>".t('Show only mine')."</a>";

			$org =1;
			$show_org_title = ($my_organisations->rowCount() > 1);
			$org_key = AbstractEntity::keyField(_ORGANISATION_GROUP);
			foreach ($orgs as $organisation){
				$projects = Project::getProjectsByUser($owner_id, array($organisation->$org_key), $show_all);
				showOrganisationProjects($org, $projects, $organisation, $show_org_title, $show_last, TRUE, $owner_only);
				$org++;
			}
		}
	}
}

function showOrganisationProjects($org_nr, $projects, $organisation, $show_org_title=TRUE, $show_last=FALSE, $inline=FALSE, $owner_only=FALSE){
	$org_id = $organisation->org_id;
	$nr = 1;
	$tab_id_prefix = "project_page$org_nr-";
	$data = array();
	$activating_tabs = array();
	$nr_projects = count($projects);
	$current_tab = ($show_last && ($show_last == $org_id)) ? ($nr_projects + 1) : 1;
	$current_tab_id = "$tab_id_prefix$current_tab";
	
	//data is like: [translate, label, action, type, id, extra GET arguments, render with rich text area, render tab to the right]
	$data[] = array(1, 'All', 'list', _PROJECT_OBJ, null, "org=$org_id&inline=".($inline? 1:0)."&mine=".($owner_only? 1:0));
	$activating_tabs[] = "'$tab_id_prefix$nr'";
	$nr++;
	if ($show_org_title){
		echo '<h3>'.tt('Projects in your organisation ').sprintf('<i>%1$s</i>', $organisation->name).'</h3>';
	}
	foreach ($projects as $project){
		if ($nr == $current_tab){
			//$id = $project->pid;
			$my_project = $project;
		}
		$activating_tabs[] = "'$tab_id_prefix$nr'";
		$data[] = array(0, $project->title, 'view', _PROJECT_OBJ, $project->pid);
		$nr++;
	}
	
	$data[] = array(1, 'Add', 'add', _PROJECT_OBJ, 0, "target=$tab_id_prefix$nr&org=$org_id", TRUE, 'right');
	$activating_tabs[] = "'$tab_id_prefix$nr'";
	//If no target is sent along, the project views are shown inline
	$current_tab_content = (1 == $current_tab) ? renderProjects('', $projects, $current_tab_id, $inline, FALSE, FALSE): 
		renderProject($my_project, $current_tab_id, false);
	
	echo renderTabs($nr, 'Project', $tab_id_prefix, _PROJECT_OBJ, $data, 0, TRUE, $current_tab_content,
		$current_tab, _PROJECT_OBJ);?>
	<script type="text/javascript">
		activatetabs('tab_', [<?php echo implode(', ', $activating_tabs);?>], '<?php echo $current_tab_id;?>');
	</script>
	<?php
}

function initBrowseProjectLayout($pid=''){
	$org_id=0;
	if(isset($_GET['organisation'])){
		$org_id = $_GET['organisation'];
	}
	$state = null;
	if(isset($_GET['state'])){
		$state = $_GET['state'];
	}
	$apply_projects = vals_soc_access_check('dashboard/projects/apply') ? 1 : 0;
	$rate_projects = Users::isSuperVisor();
	$is_student = Users::isStudent();
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
			<?php if ($is_student){?>
			<input type='button' value='<?php echo t('Filter on Favourites');?>' id='favourite_filter'/>
			<?php }?>
			
			<?php echo "<BR/>"; echo t('Status');?>:
			<select id="state" name="state">
				<option <?php echo  (! $state) ? 'selected="selected"': ''; ?> value="0"><?php echo t('NA');?></option><?php
				$states = array('draft' => 'draft', 'pending' => 'pending', 'open' => 'open', 'preselected' => 'preselected', 'active' => 'active', 'ended' => 'ended', 'archived' => 'archived');
				if (! Users::isAdmin()){
					if (Users::isMentor()){
						unset($states['archived']);
					} else {
						unset($states['draft']);
						if ($is_student){
							unset($states['pending'], $states['archived']);
						} elseif (Users::isUser()) {
							unset($states['archived']);
						} else {
							$states = array();
						}
					} 
				}
				foreach ($states as $key => $stat) {
					$selected = ($key == $state ? 'selected="selected" ' : '');
					echo "<option $selected value='$key'>$stat</option>";
				}?>
			</select>
			
		</form>
	</div>
	<div id="ProjectTableContainer" style="width: 700px;"></div>

<script type="text/javascript">
	jQuery(document).ready(function($){

		window.view_settings = {};
		window.view_settings.apply_projects = <?php echo $apply_projects ? 1: 0;?>;
		window.view_settings.rate_projects  = <?php echo $rate_projects  ? 1: 0;?>;
	
		//Prepare jTable
		$("#ProjectTableContainer").jtable({
			//title: "Table of projects",
			paging: true,
			pageSize: 10,
			sorting: true,
			defaultSorting: "title ASC",
			actions: {
				listAction: moduleUrl + "actions/project_actions.php?action=list_search"
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
					width: "40%",
					display: function (data) {
						return "<a title=\"View project details\" href=\"javascript:void(0);\" onclick=\"getProjectDetail("+
							data.record.pid+")\">" + data.record.title + "</a>";
						},
						create: false,
						edit: false
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
				proposal_count: {
					title: "Proposals",
					width: "12%",
					create: false,
					edit: false
				},
				state: {
					title: "Status",
					//width: "12%",
					create: false,
					edit: false
				}
				/*
				,
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
				}
				*/
				<?php 
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
			/*
//this makes of each row a filter for that project
			,recordsLoaded: function(event, data) {
				var browse_url = baseUrl + "dashboard/projects/browse?pid=";
				
				$(".jtable-data-row").each(function(){
					var $parent = $(this);
					
					var row_id = $parent.attr("data-record-key");
					$parent.children('td:first-child').click(function() {
						document.location.href=browse_url + row_id;
					});
				});
			}
			*/
		});
	
	//Load project list from server on initial page load
	$("#ProjectTableContainer").jtable("load", {
		tags: $("#tags").val(),
		state: $("#state").val(),
		organisation: $("#organisation").val()<?php 
		if ($pid){echo ", pid: $pid";}?>
	});
		
	$("#tags").keyup(function(e) {
		e.preventDefault();
		// only auto clear when there is no tag info
		if(testTagInput() && $("#tags").val()==""){
			$("#ProjectTableContainer").jtable("load", {
			tags: $("#tags").val(),
			state: $("#state").val(),
			organisation: $("#organisation").val()
			});
		}
	});
		
	$("#organisation").change(function(e) {
		e.preventDefault();
		if(testTagInput()){
			$("#ProjectTableContainer").jtable("load", {
				tags: $("#tags").val(),
				state: $("#state").val(),
				organisation: $("#organisation").val()
			});
		}
	});
	$("#state").change(function(e) {
		e.preventDefault();
		if(testTagInput()){
			$("#ProjectTableContainer").jtable("load", {
				tags: $("#tags").val(),
				state: $("#state").val(),
				organisation: $("#organisation").val()
			});
		}
	});
	<?php if ($is_student){ ?>
	$("#favourite_filter").click(function(e) {
		e.preventDefault();
		//if(testTagInput()){
			$("#ProjectTableContainer").jtable("load", {favourites :true});
		//}
	});
	<?php }?>
	$("#project_filter").submit(function(e){
		e.preventDefault();
		if(testTagInput()){
			$("#ProjectTableContainer").jtable("load", {
				tags: $("#tags").val(),
				state: $("#state").val(),
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