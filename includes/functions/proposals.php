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
	$apply_proposals = vals_soc_access_check('proposal apply') ? 1 : 0;
	$proposal_tabs = array();
	if(isset($_GET['organisation'])){
		$orgId = $_GET['organisation'];
	}
	$student_id=0;
	if(isset($_GET['student'])){
		$student_id = $_GET['student'];
	}
	$inst_id=0;
	if(isset($_GET['institute'])){
		$inst_id = $_GET['institute'];
	}?>
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
				var tabs = [{tab: 'project', label: 'Project'}, {tab: 'student', label: 'Student'}];
				var url = baseUrl + "actions/proposal_actions.php?action=proposal_detail&proposal_id=" + proposal_id;
				$.get(url,function(data,status){
    				 generateAndPopulateModal(data, renderProposalTabs, tabs);
  				});
				activatetabs('tab_', ['project', 'student']);
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
			
			function generateAndPopulateModal3(data){
				//TODO - work more on the formating
				// and add other fields from DB
				var result = jQuery.parseJSON(data);
				/*
				Gets:
					Array
					(
					    [0] => Array
					        (
					            [proposal_id] => 2
					            [owner_id] => 31
					            [org_id] => 2
					            [inst_id] => 3
					            [supervisor_id] => 30
					            [pid] => 10
					            [name] => Edwin 2222
					            [cv] => 
					            [solution_short] => 
					            [solution_long] => ja even wat hier
					            [modules] => 
					            [state] => draft
					            [i_inst_id] => 3
					            [i_owner_id] => 0
					            [i_name] => Salamanca Universidad
					            [contact_name] => JUan
					            [contact_email] => juan@raycom.com
					            [o_org_id] => 2
					            [o_owner_id] => 26
					            [o_name] => Acme Foundation and so on
					            [o_contact_name] => F Smith
					            [o_contact_email] => fsmith@acme.org
					            [url] => http://www.acme.org
					            [description] => blah blah blah and more bla
					            [title] => GLSpace
					        )

					)
					*/	
				var content = "<h2>"+result.title+"</h2>";
					
					content +="<div class=\"centered\">";
					//content +="<br/><br/><a href=\"#\" onclick=\"Drupal.CTools.Modal.dismiss()\">Close</a>";
					content +="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					content += result.pr_description;
					content += '<hr>';
					content += '<h2>Summary Solution</h2>';
					content += result.solution_short;
					content += '<h2>Solution</h2>';
					content += result.solution_long;
					content += '<h2>Curriculum Vitae</h2>';
					content += result.cv;
					//content +="<a href=\"#\" onclick=\"getProposalFormForProposal("+result.pid+")\">Submit proposal for this proposal</a>";
					content +="</div>";
			
				//var content = ' hallo dit uitwerken'+result.contact_name ;
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
							return "<a title=\"Propose a proposal for this idea\" href=\"#\" "+
								"onclick=\"getProposalDetail("+data.record.proposal_id+")\">"+
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