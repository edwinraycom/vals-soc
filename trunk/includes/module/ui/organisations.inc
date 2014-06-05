<?php
module_load_include('php', 'vals_soc', 'includes/classes/AbstractEntity');
module_load_include('php', 'vals_soc', 'includes/classes/Groups');
module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
drupal_add_library('system','ui.widget');
drupal_add_library('system','ui.dialog');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/settings.js');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/ajax.js');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/target_functions.js');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/jquery-migrate-1.2.1.js');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/jtable/jquery.jtable.js');
drupal_add_css(_VALS_SOC_PATH . '/includes/js/jtable/jtable.css');

function initBrowseOrgsLayout(){
	echo '
	<div class="filtering">
	<span id="infotext" style="margin-left: 34px"></span>
    <form id="organisation_filter">
        Name: <input type="text" name="oname" id="oname" />
    </form>
	</div>
	<div id="OrganisationTableContainer" style="width: 600px;"></div>
					
	<script type="text/javascript">
		jQuery(document).ready(function($){
					
			function testTagInput() {
				var filter = /^[a-z0-9+_.\s]+$/i;
				if (filter.test($("#oname").val()) || $("#oname").val()=="") {
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
		
			var baseUrl = "/vals/";
			var modulePath = "sites/all/modules/vals_soc/actions/";
		    //Prepare jTable
			$("#OrganisationTableContainer").jtable({
				//title: "Table of organisations",
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: "oname ASC",
				actions: {
					listAction: baseUrl + modulePath + "organisation_actions.php?action=list_organisations"
				},
				fields: {
					org_id: {
						key: true,
						create: false,
						edit: false,
						list: false
					},
					oname: {
						title: "Name",
						width: "80%"
					}
				},
					
				recordsLoaded: function(event, data) {
        			$(".jtable-data-row").click(function() {
            			var row_id = $(this).attr("data-record-key");
            			var url = baseUrl + "dashboard/projects/browse?org_id=" + row_id;
						document.location.href=url;
			
        			});
    			}
			});

			//Load org list from server on initial page load
			$("#OrganisationTableContainer").jtable("load");

			$("#organisation_filter").submit(function(e){
				e.preventDefault();
				if(testTagInput()){
            		$("#OrganisationTableContainer").jtable("load", {
	                	oname: $("#oname").val()
            		});
				}
			});

		});
	</script>
	';
}

initBrowseOrgsLayout();