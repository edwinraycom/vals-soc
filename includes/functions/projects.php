<?php
function initBrowseProjectLayout($target='content'){
	$orgId=0;
	if(isset($_GET['organisation'])){
		$orgId = $_GET['organisation'];
	}
	?>
	<div class="filtering" id="browse_projects">
	<span id="infotext" style="margin-left: 34px"></span>
	<form id="project_filter">
	<?php echo t('Tags');?>: <input type="text" name="tags" id="tags" />
	<?php echo t('Organisations');?>:
	<select id="organisation" name="organisation">
	<option selected="selected" value="0"><?php echo t('All Organisations');?></option><?php
	$result = Organisations::getInstance()->getOrganisationsLite();
	foreach ($result as $record) {
		$selected = ($record->org_id == $orgId ? 'selected ' : '');
		echo '<option ' .$selected.'value="'.$record->org_id.'">'.$record->name.'</option>';
	}?>
	</select>
	</form>
	</div>
	<div id="ProjectTableContainer" style="width: 600px;"></div>

<script type="text/javascript">	
	jQuery(document).ready(function($){

	//We make the ajax script path absolute as the language module might add a language code
	//to the path
	var baseUrl = "/vals/sites/all/modules/vals_soc/";

	function getProposalFormForProject(projectId){
		Drupal.CTools.Modal.dismiss();
		ajaxCall("student", "proposal", {id: projectId, target:"<?php echo $target;?>"}, "content");
	}

	function getProjectDetail(projectId){
		var url = baseUrl + "actions/project_actions.php?action=project_detail&project_id=" + projectId;
		$.get(url,function(data,status){
			generateAndPopulateModal(data);
		});
	}
		
	function testTagInput() {
		var filter = /^[a-z0-9+_.\s]+$/i;
		if (filter.test($("#tags").val()) || $("#tags").val()=="") {
			$("#tags").removeClass("error");
			$("#infotext").removeClass("error");
			$("#infotext").text("");
			return true;
		}
		else {
			$("#tags").addClass("error");
			$("#infotext").addClass("error");
			$("#infotext").text("Invalid character/s entered");
			return false;
		}
	}
	
	function generateAndPopulateModal(data){
		//TODO - work more on the formating
		// and add other fields from DB
		var result = jQuery.parseJSON(data);
		var content = "<h2>"+result.title+"</h2>";
		content += result.description;
		content +="<div class=\"centered\">";
		content +="<br/><br/><a href=\"#\" onclick=\"Drupal.CTools.Modal.dismiss()\">Close</a>";
		content +="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		content +="<a href=\"#\" onclick=\"getProposalFormForProject("+result.pid+")\">Submit proposal for this project</a>";
		content +="</div>";
		Drupal.CTools.Modal.show();
		$("#modal-title").html("&nbsp;"); // doesnt render unless theres something there!
		$("#modal-content").html(content);
		Drupal.attachBehaviors();
	}
		
		
	//Prepare jTable
	$("#ProjectTableContainer").jtable({
		//title: "Table of projects",
		paging: true,
		pageSize: 10,
		sorting: true,
		defaultSorting: "title ASC",
		actions: {
			listAction: baseUrl + "actions/project_actions.php?action=list_projects"
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
			},
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
			}
		
		},
		
		recordsLoaded: function(event, data) {
			$(".jtable-data-row td:first-child").click(function() {
			var row_id = $(this).parent().attr("data-record-key");
			getProjectDetail(row_id);
			});
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