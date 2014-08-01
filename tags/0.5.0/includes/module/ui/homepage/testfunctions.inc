<?php
drupal_add_library('system','ui.datepicker');
drupal_add_js(_VALS_SOC_PATH .'/includes/js/ajax.js');


function getTimelineTester(){
	echo '
		<div>
		<h1 id="dateNow">'.Timeline::getInstance()->getNow()->format('F j, Y, g:i a').'</h1>
			<script>
			jQuery(document).ready(function(){
				$jq( "#datepicker" ).datepicker({
					dateFormat: "yy-mm-dd",
					defaultDate: "'.Timeline::getInstance()->getNow()->format('Y-m-d').'",
					onClose: function(dateText, inst){
						$jq.post( url("timeline","setdate"), { date: dateText+" 00:00:00" },
							function(result) {
								if(result){
									console.log(result);
									$jq("#dateNow").html(result);
								}
						});
					}
				}).datepicker("setDate", "'.Timeline::getInstance()->getNow()->format('Y-m-d').'");
			});
			</script>
			<p>System Date today: <input type="text" id="datepicker"></p>
		</div>
';
}
if (_DEBUG){
	getTimelineTester();
}