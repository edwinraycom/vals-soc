<?php
//TODO this function may go 
function renderProject($project){
	$content = "<h2>".$project['title']."</h2>";
	$content .= '<p>'.$project['description']. '</p>';
	if ( $project['url']){
		$content .= '<p>'.tt('More information can be found at %1$s', "<a href='${project['url']}'> ${project['url']}</a>"). '</p>';
	}
	$content .="<div class=\"totheright\">";
	$content .="<br/><br/><input type='button' onclick=\"getProposalFormForProject(".$project['pid'].")\" value='Submit proposal for this project'/>";
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