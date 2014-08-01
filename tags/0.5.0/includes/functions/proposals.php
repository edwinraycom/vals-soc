<?php
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
        	<option <?php echo  (! $org_id) ? 'selected="selected"': ''; ?> value="0"><?php 
	            	echo t('All Organisations');?></option><?php
			$result = Organisations::getInstance()->getOrganisationsLite();
			foreach ($result as $record) {
				$selected = ($record->org_id == $org_id ? 'selected="selected" ' : '');
				echo '<option ' .$selected.'value="'.$record->org_id.'">'.$record->name.'</option>';
			}?>
        </select>
        <span id='student_section' class='<?php echo $student_section_class;?>'>
	        <select id="institute" name="institute">
	            <option <?php echo  (! $inst_id) ? 'selected="selected"': ''; ?> value="0"><?php 
	            	echo t('All Institutes');?></option><?php
				$result = Groups::getGroups('institute', 'all');
				foreach ($result as $record) {
					$selected = ($record->inst_id == $inst_id ? 'selected="selected" ' : '');
					echo '<option ' .$selected.'value="'.$record->inst_id.'">'.$record->name.'</option>';
				}?>
	        </select>
	        <select id="student" name="student">
	            <option <?php echo  (! $student_id) ? 'selected="selected"': ''; ?> value="0"><?php 
	            	echo t('All Students');?></option><?php
				$result = Users::getUsers('student', ($inst_id ? 'institute': 'all'), $inst_id);
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
			//window.getProposalFormForProject = getProposalFormForProject;
			//window.getProjectDetail = getProjectDetail;
			//window.getProposalDetail = getProposalDetail;

		});
	</script><?php
}