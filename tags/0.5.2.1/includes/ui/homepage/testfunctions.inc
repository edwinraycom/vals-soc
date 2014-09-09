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

function getUserPretendTester(){
	$users = Users::getAllUsers()->fetchAll();
	$options = array(0 => 'No pretending');
	foreach($users as $user){
		if ($user->uid){
			$options[$user->uid] = 'Name:'.$user->fullname. ", type:".$user->type;
		}
	}
	
	echo "
	<div>
	<h1 >Choose a user</h1>
	<form action='' method='post'>
	<select id='pretend' name='pretend'>";
	foreach ($options as $uid => $name){
		echo "<option value='$uid'>$name</option>";
	}
	
	echo "
	</select>
	<input type='submit' value='Pretend'>
	</form>
	</div>";
}

if (_DEBUG){
	getTimelineTester();
}

if (_DEBUG && isset($GLOBALS['vals_soc_pretend_possible']) && 
			$GLOBALS['vals_soc_pretend_possible']){
	getUserPretendTester();
}