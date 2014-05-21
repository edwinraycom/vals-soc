<?php
module_load_include('php', 'vals_soc', 'includes/classes/Project');
module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
drupal_add_library('system','ui.widget');
drupal_add_library('system','ui.dialog');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jquery-migrate-1.2.1.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jtable/jquery.jtable.js');
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/js/jtable/jtable.css');
ctools_include('modal');
ctools_modal_add_js();


function initBrowseProjectLayout(){
	$orgId=0;
	if(isset($_GET['oid'])){
		$orgId = $_GET['oid'];
	}
	echo '
	<div class="filtering">
	<span id="infotext" style="margin-left: 34px"></span>
    <form id="project_filter">
        Tags: <input type="text" name="tags" id="tags" />
        Organisations:
        <select id="organisation" name="organisation">
            <option selected="selected" value="0">All Organisations</option>';
			$result = Organisations::getInstance()->getOrganisationsLite();
			foreach ($result as $record) {
				$selected = ($record->org_id == $orgId ? 'selected ' : '');
				echo '<option ' .$selected.'value="'.$record->org_id.'">'.$record->name.'</option>';
			}
			echo '
        </select>
    </form>
	</div>
	<div id="ProjectTableContainer" style="width: 600px;"></div>
					
	<script type="text/javascript">
		jQuery(document).ready(function($){
					
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
				var result = jQuery.parseJSON(data);
				var content = result[0].description;
					content +="<br/><br/><a href=\"#\" onclick=\"Drupal.CTools.Modal.dismiss()\">close</a>";
				var title = result[0].title;
				Drupal.CTools.Modal.show();
				$("#modal-title").html(title);
				$("#modal-content").html(content);
				Drupal.attachBehaviors();	
			}
			
			//We make the ajax script path absolute as the language module might add a language code
			//to the path
			var baseUrl = "/vals/sites/all/modules/vals_soc/actions/";
		    //Prepare jTable
			$("#ProjectTableContainer").jtable({
				//title: "Table of projects",
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: "title ASC",
				actions: {
					listAction: baseUrl + "project_actions.php?action=list_projects"
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
						width: "30%",
						create: false,
						edit: false
					}
				},
					
				recordsLoaded: function(event, data) {
        			$(".jtable-data-row").click(function() {
            			var row_id = $(this).attr("data-record-key");
            			var url = baseUrl + "project_actions.php?action=project_detail&project_id=" + row_id;
						$.get(url,function(data,status){
    						 generateAndPopulateModal(data);
  						});
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

		});
	</script>
	';
}

initBrowseProjectLayout();