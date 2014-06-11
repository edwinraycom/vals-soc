<?php
/*Expects data for every tab:
 * [translate, label, action, type, id, extra GET arguments]
*/
function renderProposalTabs($count, $tab_label, $target_label, $type, $data, $id=0,
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
			echo "onclick=\"ajaxCall('proposal', '$action', {type:'$type', id:$id, target:'$target'}, '$target');\"";
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

function initBrowseProposalsLayout(){
	
	$orgId=0;
	$apply_proposals = vals_soc_access_check('dashboard/projects/apply') ? 1 : 0;
	$browse_proposals = vals_soc_access_check('dashboard/proposals/browse') ? 1 : 0;
	$proposal_tabs = array();
	if(isset($_GET['organisation'])){
		$orgId = $_GET['organisation'];
	}
	echo "Wat zijn de rechten van mij? apply $apply_proposals browse $browse_proposals";
	if ($apply_proposals && !$browse_proposals){
		//A student may only browse their own proposals
		$student_id = $GLOBALS['user']->uid;
		$student_id = 31;
		$student = Users::getStudentDetails($student_id);
		$inst_id = $student->inst_id;
		$student_section_class = 'invisible';
	} else {
		$student_section_class = '';
		$student_id=0;
		if(isset($_GET['student'])){
			$student_id = $_GET['student'];
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
            <option selected="selected" value="0"><?php echo t('All Organisations');?></option><?php
			$result = Organisations::getInstance()->getOrganisationsLite();
			foreach ($result as $record) {
				$selected = ($record->org_id == $orgId ? 'selected ' : '');
				echo '<option ' .$selected.'value="'.$record->org_id.'">'.$record->name.'</option>';
			}?>
        </select>
        <span id='student_section' class='<?php echo $student_section_class;?>'>
	        <select id="institute" name="institute">
	            <option selected="selected" value="0"><?php echo t('All Institutes');?></option><?php
				$result = Groups::getGroups('institute', 'all');
				foreach ($result as $record) {
					$selected = ($record->inst_id == $inst_id ? 'selected ' : '');
					echo '<option ' .$selected.'value="'.$record->inst_id.'">'.$record->name.'</option>';
				}?>
	        </select>
	        <?php //echo t('Students');<br/>?>
	        <select id="student" name="student">
	            <option selected="selected" value="0"><?php echo t('All Students');?></option><?php
				$result = Users::getUsers('student', ($inst_id ? 'institute': 'all'), $inst_id);
				foreach ($result as $record) {
					$selected = ($record->uid == $student_id ? 'selected ' : '');
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
			var baseUrl = "/vals/sites/all/modules/vals_soc/";

			/*
			function getProposalForm(proposalId){
				Drupal.CTools.Modal.dismiss();
				alert("get the proposal form for proposal#"+proposalId);
			}
			*/

			function getProposalDetail(proposal_id){
				var tabs = [{tab: 'project', label: 'Project'},
							{tab: 'student', label: 'Student'},
							{tab: 'cv', label: 'Cv'},
							{tab: 'summary', label: 'Solution Summary'},
							{tab: 'solution', label: 'Solution'},
							{tab: 'state', label: 'State'}];
				var url = baseUrl + "actions/proposal_actions.php?action=proposal_detail&proposal_id=" + proposal_id;
				$.get(url,function(data,status){
    				 generateAndPopulateModal(data, renderProposalTabs, tabs);
    				 activatetabs('tab_', ['tab_project', 'tab_student', 'tab_cv', 'tab_summary', 'tab_solution', 
    				      		'tab_state']);
  				});
				
			}
			
			function getProjectDetail(project_id){
				var url = baseUrl + "actions/project_actions.php?action=project_detail&project_id=" + project_id;
				$.get(url,function(data,status){
    				 generateAndPopulateModal(data, renderProject, <?php echo $apply_proposals;?>);
  				});
			}		

			function getProposalFormForProject(projectId){
				Drupal.CTools.Modal.dismiss();
				ajaxCall("student", "proposal", {id: projectId, target:'content'}, "content");
			}

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
					listAction: baseUrl + "actions/proposal_actions.php?action=list_proposals"
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
						display: function (data){return data.record.name;}
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
			window.getProposalFormForProject = getProposalFormForProject;
			window.getProjectDetail = getProjectDetail;
			window.getProposalDetail = getProposalDetail;

		});
	</script><?php
}