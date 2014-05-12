<?php
module_load_include('php', 'vals_soc', 'includes/classes/Project');
module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
drupal_add_library('system','ui.widget');
drupal_add_library('system','ui.dialog');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jquery-migrate-1.2.1.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jtable/jquery.jtable.js');
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/js/jtable/jtable.css');

function initProjectLayout(){
	echo '

	<div class="filtering">
	<span id="infotext" style="margin-left: 34px"></span>
    <form id="project_filter">
        Tags: <input type="text" name="tags" id="tags" />
        Organisations:
        <select id="organisation" name="organisation">
            <option selected="selected" value="0">All Organisations</option>';
			$result = Organisations::getInstance()->getOrganisations();
			foreach ($result as $record) {
				echo '<option value="'.$record->org_id.'">'.$record->name.'</option>';
			}
			echo '
        </select>
    </form>
	</div>
	<div id="ProjectTableContainer" style="width: 600px;"></div>
					
	<script type="text/javascript">
		jQuery(document).ready(function($){
					
			function testTagInput() {
				var filter = /^[a-z0-9+_.]+$/i;
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
					
			var baseUrl = "../sites/all/modules/vals_soc/actions/";
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
            			alert("clicked row with id "+row_id);
					/*
						var url = baseUrl + "project_actions.php?action=project_detail&project_id=" + row_id;
						var overlay = jQuery("<div id=\"d_overlay\"></div>");
						overlay.appendTo(document.body);
						$( "#d_overlay" ).load( url, function() {
							alert( "Load was performed." );
						});
					*/
        			});
    			}
			});

			//Load person list from server on initial page load
			$("#ProjectTableContainer").jtable("load");
			
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

initProjectLayout();