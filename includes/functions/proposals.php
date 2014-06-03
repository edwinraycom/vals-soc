<?php

function initBrowseProposalsLayout(){
	$orgId=0;
	if(isset($_GET['oid'])){
		$orgId = $_GET['oid'];
	}
	$student_id=0;
	if(isset($_GET['stid'])){
		$student_id = $_GET['stid'];
	}
	$inst_id=0;
	if(isset($_GET['inid'])){
		$inst_id = $_GET['inid'];
	}?>
	<div class="filtering" style="width: 800px;">
	<span id="infotext" style="margin-left: 34px"></span>
    <form id="proposal_filter">
        <?php echo t('Select the proposals');?>:
        <?php // echo t('Organisations');?>
        <select id="organisation" name="organisation">
            <option selected="selected" value="0"><?php echo t('All Organisations');?></option><?php
			$result = Organisations::getInstance()->getGroupsLite();
			foreach ($result as $record) {
				$selected = ($record->org_id == $orgId ? 'selected ' : '');
				echo '<option ' .$selected.'value="'.$record->org_id.'">'.$record->name.'</option>';
			}?>
        </select>
        <?php //echo t('Institutes');?>
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
    </form>
	</div>
	<div id="TableContainer" style="width: 800px;"></div>
					
	<script type="text/javascript">
					
		jQuery(document).ready(function($){

			//We make the ajax script path absolute as the language module might add a language code
			//to the path
			var baseUrl = "/vals/sites/all/modules/vals_soc/";

			function getProposalForm(proposalId){
				Drupal.CTools.Modal.dismiss();
				alert("get the proposal form for proposal#"+proposalId);
			}

			function getProposalDetail(proposal_id){
				var url = baseUrl + "actions/proposal_actions.php?action=proposal_detail&proposal_id=" + proposal_id;
				$.get(url,function(data,status){
    				 generateAndPopulateModal(data);
  				});
			}
			
			function getProjectDetail(project_id){
				var url = baseUrl + "actions/project_actions.php?action=project_detail&project_id=" + project_id;
				$.get(url,function(data,status){
    				 generateAndPopulateModal(data);
  				});
			}		

			function getProposalFormForProject(projectId){
				Drupal.CTools.Modal.dismiss();
				ajaxCall("student", "proposal", {id: projectId, target:'content'}, "content");
			}
			
			function generateAndPopulateModal(data){
				//TODO - work more on the formating
				// and add other fields from DB
				var result = jQuery.parseJSON(data);
				/*
				var content = "<h2>"+result[0].title+"</h2>";
					content += result[0].description;
					content +="<div class=\"centered\">";
					content +="<br/><br/><a href=\"#\" onclick=\"Drupal.CTools.Modal.dismiss()\">Close</a>";
					content +="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					content +="<a href=\"#\" onclick=\"getProposalFormForProposal("+result[0].pid+")\">Submit proposal for this proposal</a>";
					content +="</div>";
				*/
				var content = ' hallo dit uitwerken' ;
				Drupal.CTools.Modal.show();
				$("#modal-title").html("&nbsp;"); // doesnt render unless theres something there!
				$("#modal-content").html(content);
				Drupal.attachBehaviors();
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
					propid: {
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
							return "<a title=\"View project details\" href=\"#\" onclick=\"getProjectDetail("+data.record.pid+")\">"+
									"<span class=\"ui-icon ui-icon-info\">view project</span></a>";
    					},
    					create: false,
    					edit: false
					},
					owner_id: {
						title: "Student",
						width: "40%",
						display: function (data){return data.record.name;}
					},
					instid: {
						title: "Institute",
						width: "26%",
						create: false,
						edit: false,
						display: function (data){return data.record.i_name;}
					},
					oid: {
						title: "Organisation",
						width: "20%",
						display: function (data){return data.record.o_name;}
					},
					
					solution_short : {
						//width: "2%",
    					title: "Proposal details",
						sorting: false,
    					display: function (data) {
							return "<a title=\"Propose a proposal for this idea\" href=\"#\" "+
								"onclick=\"getProposalDetail("+data.record.propid+")\">"+
									"<span class=\"ui-icon ui-icon-info\">See details</span></a>";
    					},
    					create: false,
    					edit: false
					},
					/*
						solution_long : {
						//width: "2%",
    					title: "Detailed Description",
						sorting: false,
    					display: function (data) {
							return "<a title=\"Propose a proposal for this idea\" href=\"#\" onclick=\"getProposalFormForProject("+data.record.pid+")\">"+
									"<span class=\"ui-icon ui-icon-script\"></span></a>";
    					},
    					create: false,
    					edit: false
					}
					*/
					
				},
					
				recordsLoaded: function(event, data) {
					$(".jtable-data-row td:first-child").click(function() {
						var row_id = $(this).parent().attr("data-record-key");
						getProposalDetail(row_id);
        			});
    			}
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
			
			// define these at the window level so that they can still be called once loaded
			window.getProposalFormForProject = getProposalFormForProject;
			window.getProjectDetail = getProjectDetail;
			window.getProposalDetail = getProposalDetail;

		});
	</script><?php
}