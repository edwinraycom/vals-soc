<?php
include_once(_VALS_SOC_ROOT.'/includes/functions/tab_functions.php');//it is sometimes included after administration.php which does the same

function showMyProposalPage(){
	//TODO check for the role of current user

	$role = getRole();
	if (!Users::isStudent()){
		echo t('You can only see this page as a student');
		return;
	}
	//Get my groups
	$my_proposals = Proposal::getInstance()->getMyProposals();//::getGroups(_ORGANISATION_GROUP);
	if (!$my_proposals){
		echo t('You have no proposal edited yet.').'<br/>';
		echo "<a href='"._VALS_SOC_URL. "/projects/browse'>".t('Please find yourself a project')."</a>.";
	} else {
		showMyProposals($my_proposals);
	}
}

function renderDefaultField($field, $obj, $alternative_field=''){
	static $unknown = null;
	if (! $unknown) {
		$unknown = t('The %1$s is not known yet');
	}
	if (isset($obj->$field) && $obj->$field){
		return $obj->$field;
	} elseif ($alternative_field && isset($obj->$alternative_field) && $obj->$alternative_field){
		return $obj->$alternative_field;
	} else {
		return sprintf($unknown, t(str_replace('_', ' ', $field)));
	}
}

function renderProposal($proposal, $target='none'){
	//A proposal consists of: fields = array('proposal_id', 'owner_id', 'org_id', 'inst_id',
	//'supervisor_id', 'pid', 'solution_short', 'solution_long', 'state',);
	$propid = $proposal->proposal_id;
	$buttons = '';
	if (Users::isStudent() && Groups::isOwner('proposal', $propid)){
		$delete_action = "onclick='if(confirm(\"".t('Are you sure you want to delete this proposal?')."\")){ajaxCall(\"proposal\", \"delete\", ".
			"{type: \"proposal\", proposal_id: $propid, target: \"$target\"}, \"refreshTabs\", \"json\", [\"proposal\", \"$target\", \"proposal\"]);}'";
		$edit_action = "onclick='ajaxCall(\"proposal\", \"edit\", {type: \"proposal\", proposal_id: $propid, target: ".
			"\"$target\", format:\"html\"}, \"formResult\", \"html\", [\"$target\", \"proposal\"]);'";
		$buttons .= "<input type='button' value='".t('edit')."' $edit_action/>";
		$buttons .= "<input type='button' value='".t('delete')."' $delete_action/>";
	}
	$content = 
	"$buttons".
	"<h1>".($proposal->title ? $proposal->title : Proposal::getDefaultName('', $proposal))."</h1>

	<div id='personalia'>
		<ul>
			<li>".t('Supervisor').": ".renderDefaultField('supervisor_name', $proposal, 'supervisor_user_name')."</i>".
			"<li>".t('Mentor').": ".renderDefaultField('mentor_name', $proposal, 'mentor_user_name')."</i>".
			"<li>".t('Student').": ".renderDefaultField('student_name', $proposal, 'name')."</i>".
			"<li>".t('Institute').": ".renderDefaultField('i_name', $proposal)."</i>".
			"<li>".t('Organisation').": ".renderDefaultField('o_name', $proposal)."</i>".
		"</ul>
	</div>".
	"<div id='project'>
		".t('Project').": ".$proposal->pr_title."
	</div>".
	"<div id='proposal_text'>
		<h2>".t('Solution Summary')."</h2>
		".renderDefaultField('solution_short', $proposal)."<br/>".
		//<a href='javascript:void(0)' onclick='makeVisible(\"solution_$propid\");'>view complete proposal</a>
		"<input type='button' value='View more' onclick='makeVisible(\"solution_$propid\");'/>

			<div id='solution_$propid' class='invisible'>
			".renderDefaultField('solution_long', $proposal)."
			</div>
	</div>";
	
	module_load_include('inc', 'vals_soc', 'includes/ui/comments/threaded_comments');
	$content .= initComments($propid, 'proposal');
	return $content;
	
}

function showMyProposals($proposals){
	$nr = 0;
	$apply_projects = $apply_projects = vals_soc_access_check('dashboard/projects/apply') ? 1 : 0;
	$tab_id_prefix = "proposal_page";
	$data = array();
	$activating_tabs = array();
	$current_tab = 1;
	$current_tab_id = "$tab_id_prefix$current_tab";

	$current_tab_content = '';
	foreach ($proposals as $proposal){
		$nr++;
		if ($nr == $current_tab){
			//$id = $proposal->pid;
			$current_tab_content = renderProposal(Proposal::getInstance()->getProposalById(
					$proposal->proposal_id, TRUE), $current_tab_id);
		}
		$activating_tabs[] = "'$tab_id_prefix$nr'";
		$data[] = array(0, $proposal->title, 'view', 'proposal', $proposal->proposal_id);

	}

	echo renderTabs($nr, 'Proposal', $tab_id_prefix, 'proposal', $data, 0, TRUE, $current_tab_content,
		$current_tab, 'proposal');?>
	<script type="text/javascript">
		window.view_settings = {};
		window.view_settings.apply_projects = <?php echo $apply_projects ? 1: 0;?>;
		activatetabs('tab_', [<?php echo implode(', ', $activating_tabs);?>], '<?php echo $current_tab_id;?>');
	</script>
<?php
}

function initBrowseProposalsLayout(){
	$org_id=0;
	$apply_projects = vals_soc_access_check('dashboard/projects/apply') ? 1 : 0;
	$browse_proposals = vals_soc_access_check('dashboard/proposals/browse') ? 1 : 0;
	$proposal_tabs = array();
	if(isset($_GET['organisation'])){
		$org_id = $_GET['organisation'];
	}
	if ($apply_projects && !$browse_proposals){
		//A student may only browse their own proposals
		$student_id = $GLOBALS['user']->uid;
		$student = Users::getStudentDetails($student_id);
		$inst_id = $student->inst_id;
		$student_section_class = 'invisible';
	} else {
		$student_section_class = '';
		$student_id=0;
		if(isset($_GET[_STUDENT_TYPE])){
			$student_id = $_GET[_STUDENT_TYPE];
		}
		$inst_id=0;
		if(isset($_GET['institute'])){
			$inst_id = $_GET['institute'];
		}
	}
	?>
<div class="filtering" style="width: 800px;">
	<span id="infotext" style="margin-left: 34px"></span>
	<form id="proposal_filter">
        <?php echo t('Select the proposals');?>:
        <?php // echo t('Organisations');?>
        <select id="organisation" name="organisation">
			<option <?php echo  (! $org_id) ? 'selected="selected"': ''; ?>
				value="0"><?php
	            	echo t('All Organisations');?></option><?php
			$result = Organisations::getInstance()->getOrganisationsLite();
			foreach ($result as $record) {
				$selected = ($record->org_id == $org_id ? 'selected="selected" ' : '');
				echo '<option ' .$selected.'value="'.$record->org_id.'">'.$record->name.'</option>';
			}?>
        </select> <span id='student_section'
			class='<?php echo $student_section_class;?>'> <select id="institute"
			name="institute">
				<option <?php echo  (! $inst_id) ? 'selected="selected"': ''; ?>
					value="0"><?php
	            	echo t('All Institutes');?></option><?php
				$result = Groups::getGroups(_INSTITUTE_GROUP, 'all');
				foreach ($result as $record) {
					$selected = ($record->inst_id == $inst_id ? 'selected="selected" ' : '');
					echo '<option ' .$selected.'value="'.$record->inst_id.'">'.$record->name.'</option>';
				}?>
	        </select> <select id="student" name="student">
				<option <?php echo  (! $student_id) ? 'selected="selected"': ''; ?>
					value="0"><?php
	            	echo t('All Students');?></option><?php
				$result = Users::getUsers(_STUDENT_TYPE, ($inst_id ? _INSTITUTE_GROUP: 'all'), $inst_id);
				foreach ($result as $record) {
					$selected = ($record->uid == $student_id ? 'selected="selected" ' : '');
					echo '<option ' .$selected.'value="'.$record->uid.'">'.$record->name.':'.$record->mail.'</option>';
				}?>
	        </select>
		</span>
	</form>
</div>
<div id="TableContainer" style="width: 800px;"></div>
<script type="text/javascript">

		jQuery(document).ready(function($){

			//We make the ajax script path absolute as the language module might add a language code
			//to the path
			window.view_settings = {};
			window.view_settings.apply_projects = <?php echo $apply_projects ? 1: 0;?>;


			function loadFilteredProposals(){
				$("#TableContainer").jtable("load", {
        			student: $("#student").val(),
                                organisation: $("#organisation").val(),
        			institute: $("#institute").val()
        		});
			}

		    //Prepare jTable
			$("#TableContainer").jtable({
				//title: "Table of proposals",
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: "pid ASC",
				actions: {
					listAction: moduleUrl + "actions/proposal_actions.php?action=list_proposals"
				},
				fields: {
					proposal_id: {
						key: true,
						create: false,
						edit: false,
						list: false
					},
					pid: {
						width: "2%",
    					title: "Project",
						sorting: true,
    					display: function (data) {
							return "<a title=\"View project details\" href=\"javascript:void(0);\" onclick=\"getProjectDetail("+data.record.pid+");\">"+
									"<span class=\"ui-icon ui-icon-info\"></span></a>";
    					},
    					create: false,
    					edit: false
					},
					owner_id: {
						title: "Student",
						width: "30%",
						display: function (data){
							if(data.record.name){
								return data.record.name;
							}else{
								return data.record.u_name;
							}
						}
					},
					inst_id: {
						title: "Institute",
						width: "26%",
						create: false,
						edit: false,
						display: function (data){return data.record.i_name;}
					},
					org_id: {
						title: "Organisation",
						width: "20%",
						display: function (data){return data.record.o_name;}
					},

					solution_short : {
						//width: "2%",
    					title: "Proposal details",
						sorting: false,
    					display: function (data) {
							return "<a title=\"See this Proposal\" href=\"javascript:void(0);\" "+
								"onclick=\"getProposalDetail("+data.record.proposal_id+")\">"+
									"<span class=\"ui-icon ui-icon-info\">See details</span></a>";
    					},

    					create: false,
    					edit: false
					},

				},
			});

			//Load proposal list from server on initial page load
			loadFilteredProposals();

			$("#organisation").change(function(e) {
           		e.preventDefault();
           		loadFilteredProposals();
        	});

			$("#institute").change(function(e) {
           		e.preventDefault();
           		loadFilteredProposals();
        	});

			$("#student").change(function(e) {
				e.preventDefault();
				loadFilteredProposals();
			});

			$("#proposal_filter").submit(function(e){
				e.preventDefault();
				loadFilteredProposals()
			});

			// define these at the window level so that they can still be called once loaded in the modal
			//window.getProposalFormForProject = getProposalFormForProject;
			//window.getProjectDetail = getProjectDetail;
			//window.getProposalDetail = getProposalDetail;

		});
	</script><?php
}

/***********************************************************
 * IN PROGRESS - NEW PROPOSAL VIEWS
 * ********************************************************/

function initBrowseProposalsByTypeLayout($owner_only=''){

	$owner_id = isset($owner_only) ? $GLOBALS['user']->uid : '';
	
	if( hasRole(array(_ORGADMIN_TYPE)) || hasRole(array(_MENTOR_TYPE)) ){

//	TODO - decide how to make the two views here - one for ORGS/MENTORS and another for INST/SUPERVISORS
//  TODO - decide how to show ALL record & show mine only as we do in the projects view
	
	$org_id=0;

	if(isset($_GET['organisation'])){
		$org_id = $_GET['organisation'];
	}
/*
	echo "<a href='"._VALS_SOC_URL. "/dashboard/projects/administer'>".t('Show all')."</a>";
	echo " | ";
	echo "<a href='"._VALS_SOC_URL. "/dashboard/projects/administer/mine'>".t('Show only mine')."</a>";
*/
	?>
	<div class="filtering" style="width: 800px;">
		<span id="infotext" style="margin-left: 34px"></span>
		<form id="proposal_filter">
	        <?php echo t('Filter by Organisation');?>:
	        <?php // echo t('Organisations');?>
	        <select id="organisation" name="organisation">
				<option <?php echo  (! $org_id) ? 'selected="selected"': ''; ?> value="0"><?php echo t('All My Organisations');?></option><?php
				$result = Organisations::getInstance()->getMyOrganisations(TRUE);
				foreach ($result as $record) {
					$selected = ($record->org_id == $org_id ? 'selected="selected" ' : '');
					echo '<option ' .$selected.'value="'.$record->org_id.'">'.$record->name.'</option>';
				}?>
			</select>
		</form>
	</div>
	<div id="TableContainer" style="width: 800px;"></div>
	<script type="text/javascript">

			jQuery(document).ready(function($){
				window.view_settings = {};

				function loadFilteredProposals(){
					$("#TableContainer").jtable("load", {
						organisation: $("#organisation").val(),
					});
				}

			    //Prepare jTable
				$("#TableContainer").jtable({
					paging: true,
					pageSize: 10,
					sorting: true,
					defaultSorting: "pid ASC",
					actions: {
						listAction: moduleUrl + "actions/project_actions.php?action=list_search_proposal_count"
					},
					fields: {
						pid: {
							key: true,
	    					create: false,
	    					edit: false,
	    					list: false
						},
						title: {
							title: "Project",
							width: "49%",
							display: function (data) {
								return "<a title=\"View project details\" href=\"javascript:void(0);\" onclick=\"getProjectDetail("+data.record.pid+")\">"
										+ data.record.title+"</a>";
								},
						},
						org_name: {
							title: "Organisation",
							width: "35%",
							display: function (data){return data.record.org_name;}
						},
						proposal_count : {
							title: "Proposals",
							width: "10%",
							display: function (data){return data.record.proposal_count;}
						},

						proposal_view : {
							width: "6%",
	    					title: "View",
							sorting: false,
	    					display: function (data) {
		    					if(data.record.proposal_count > 0){
								return "<a title=\"View Proposals\" href=\"javascript:void(0);\" "+
									"onclick=\"getProposalsForProject("+data.record.pid+")\">"+
										"<span class=\"ui-icon ui-icon-info\">See detail</span></a>";
		    					}
	    					},

	    					create: false,
	    					edit: false
						},

					},
				});

				//Load proposal list from server on initial page load
				loadFilteredProposals();

				$("#organisation").change(function(e) {
	           		e.preventDefault();
	           		loadFilteredProposals();
	        	});

				$("#proposal_filter").submit(function(e){
					e.preventDefault();
					loadFilteredProposals()
				});

			});
		</script><?php
		
		}
		else if(hasRole(array(_INSTADMIN_TYPE)) || hasRole(array(_SUPERVISOR_TYPE))){
		
		$studentgroup_id=0;

		if(isset($_GET['group'])){
			$studentgroup_id = $_GET['group'];
		}

		 ?>
		 	<div class="filtering" style="width: 800px;">
				<span id="infotext" style="margin-left: 34px"></span>
				<form id="proposal_filter">
			        <?php echo t('Filter by Group');?>:
			        <?php // echo t('Organisations');?>
			        <select id="group" name="group">
						<option <?php echo  (! $studentgroup_id) ? 'selected="selected"': ''; ?> value="0"><?php echo t('All My Groups');?></option><?php
						$result = Groups::getGroups(_STUDENT_GROUP, $GLOBALS['user']->uid);
						foreach ($result as $record) {
							$selected = ($record->studentgroup_id == $studentgroup_id ? 'selected="selected" ' : '');
							echo '<option ' .$selected.'value="'.$record->studentgroup_id.'">'.$record->name.'</option>';
						}?>
					</select>
				</form>
			</div>
		 	<div id="TableContainer" style="width: 800px;"></div>
		 	<script type="text/javascript">
		 
		 			jQuery(document).ready(function($){
		 				window.view_settings = {};
		 
		 				function loadFilteredProposals(){
		 					$("#TableContainer").jtable("load", {
		 						group: $("#group").val(),
		 					});
		 				}
		 
		 			    //Prepare jTable
		 				$("#TableContainer").jtable({
		 					paging: true,
		 					pageSize: 10,
		 					sorting: true,
		 					defaultSorting: "pid ASC",
		 					actions: {
		 						listAction: moduleUrl + "actions/institute_actions.php?action=list_search_proposal_count_student"
		 					},
		 					fields: {
		 						uid: {
		 							key: true,
		 	    					create: false,
		 	    					edit: false,
		 	    					list: false
		 						},
		 						username: {
		 							title: "Student",
		 							width: "42%",
		 							display: function (data) {
		 								return  data.record.username;
		 								},
		 						},
		 						groupname: {
		 							title: "Group name",
		 							width: "42%",
		 							display: function (data){return data.record.groupname;}
		 						},
		 						proposal_count : {
		 							title: "Proposals",
		 							width: "10%",
		 							display: function (data){return data.record.proposal_count;}
		 						},
		 
		 						proposal_view : {
		 							width: "6%",
		 	    					title: "View",
		 							sorting: false,
		 	    					display: function (data) {
		 		    					if(data.record.proposal_count > 0){
		 								return "<a title=\"View Proposals\" href=\"javascript:void(0);\" "+
		 									"onclick=\"getProposalsForStudent("+data.record.uid+")\">"+
		 										"<span class=\"ui-icon ui-icon-info\">See detail</span></a>";
		 		    					}
		 	    					},
		 
		 	    					create: false,
		 	    					edit: false
		 						},
		 
		 					},
		 				});
		 
		 				//Load proposal list from server on initial page load
		 				loadFilteredProposals();
		 
		 				$("#group").change(function(e) {
		 	           		e.preventDefault();
		 	           		loadFilteredProposals();
		 	        	});
		 
		 				$("#proposal_filter").submit(function(e){
		 					e.preventDefault();
		 					loadFilteredProposals()
		 				});
		 
		 			});
		 		</script><?php
		}
		else{}
		
}

function showProposalsForProject($project_id){
	global $base_url;

	echo '<div id="baktoprops"><a href=" '.$base_url.'/dashboard/proposals/browsebytype'.'">'.t('Back to proposals overview').'</a></div>';
	$project = Project::getInstance()->getProjectById($project_id);
	echo '<h2>'.t('Proposals for project idea \''. $project['title']).'\'</h2>';
	?>
		<div id="TableContainer" style="width: 800px;"></div>
		<script type="text/javascript">
	
				jQuery(document).ready(function($){
					window.view_settings = {};
	
					function loadFilteredProposals(){
						$("#TableContainer").jtable("load", {
							project: <?php echo $project_id?>
						});
					}
	
				    //Prepare jTable
					$("#TableContainer").jtable({
						paging: true,
						pageSize: 10,
						sorting: true,
						defaultSorting: "pid ASC",
						actions: {
							listAction: moduleUrl + "actions/proposal_actions.php?action=list_proposals"
						},
						fields: {
							proposal_id: {
								key: true,
								create: false,
								edit: false,
								list: false
							},
							owner_id: {
								title: "Student",
								width: "30%",
								display: function (data){
									if(data.record.name){
										return data.record.name;
									}else{
										return data.record.u_name;
									}
								}
							},
							inst_id: {
								title: "Institute",
								width: "26%",
								create: false,
								edit: false,
								display: function (data){return data.record.i_name;}
							},
							solution_short : {
								//width: "2%",
		    					title: "Proposal details",
								sorting: false,
		    					display: function (data) {
		    						if(data.record.state != 'published'){
										return "Draft only";
		    						
									}else{
										return "<a title=\"See this Proposal\" href=\"javascript:void(0);\" "+
											"onclick=\"getProposalDetail("+data.record.proposal_id+")\">"+
												"<span class=\"ui-icon ui-icon-info\">See details</span></a>";
									}
		    					},

		    					create: false,
		    					edit: false
							},
						},
					});
					//Load proposal list from server on initial page load
					loadFilteredProposals();
				});
			</script><?php
}

function showProposalsForStudent($student_id){
	global $base_url;

	echo '<div id="baktoprops"><a href=" '.$base_url.'/dashboard/proposals/browsebytype'.'">'.t('Back to proposals overview').'</a></div>';
	$student = Users::getStudentDetails($student_id);
	$s_name = $student->student_name;
	echo '<h2>'.t('Project proposals made by \''. $s_name).'\'</h2>';
?>
		<div id="TableContainer" style="width: 800px;"></div>
		<script type="text/javascript">
	
				jQuery(document).ready(function($){
					window.view_settings = {};
	
					function loadFilteredProposals(){
						$("#TableContainer").jtable("load", {
							student: <?php echo $student_id?>
						});
					}
	
				    //Prepare jTable
					$("#TableContainer").jtable({
						paging: true,
						pageSize: 10,
						sorting: true,
						defaultSorting: "pid ASC",
						actions: {
							listAction: moduleUrl + "actions/proposal_actions.php?action=list_proposals"
						},
						fields: {
							proposal_id: {
								key: true,
								create: false,
								edit: false,
								list: false
							},
							pid: {
								title: "Project",
								width: "26%",
								create: false,
								edit: false,
								display: function (data){return data.record.pr_title;}
							},
							o_name: {
								title: "Organisation",
								width: "30%",
								display: function (data){return data.record.o_name;}
							},
							state: {
								title: "Is published?",
								width: "10%",
								display: function (data){
									if(data.record.state == 'published'){
										return 'yes';
									}else{
										return 'no';
									}
								}
							},
							//if(data.record.state != 'published'){
							solution_short : {
								//width: "2%",
		    					title: "Proposal details",
								sorting: false,
		    					display: function (data) {
										return "<a title=\"See this Proposal\" href=\"javascript:void(0);\" "+
											"onclick=\"getProposalDetail("+data.record.proposal_id+")\">"+
												"<span class=\"ui-icon ui-icon-info\">See details</span></a>";
									
		    					},

		    					create: false,
		    					edit: false
							},
						},
					});
					//Load proposal list from server on initial page load
					loadFilteredProposals();
				});
			</script><?php
}
