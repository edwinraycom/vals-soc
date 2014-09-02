<?php
module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.access');
//drupal_add_library('system','ui.datepicker');

function generateValsFrontPageLayout(){
	//module_load_include('php', 'vals_soc', 'includes/classes/Timeline');
	// use this for testing the timeline

	if (_DEBUG && isset($GLOBALS['vals_soc_pretend_possible']) && 
			$GLOBALS['vals_soc_pretend_possible']){
		module_load_include('inc', 'vals_soc', 'includes/ui/homepage/testfunctions');
	}

	echo '<div id="vals_block_content" class="">';
	
	// get the intro div
	
	module_load_include('inc', 'vals_soc', 'includes/ui/homepage/intro');
	
	echo "<div id='vals_block_content_bottom' class='block'>";
	// get the timeline div
	module_load_include('inc', 'vals_soc', 'includes/ui/homepage/timeline');
	
	// get the news div
	module_load_include('inc', 'vals_soc', 'includes/ui/homepage/news');
	
	
	
	// get the program div
	module_load_include('inc', 'vals_soc', 'includes/ui/homepage/program');
	
	echo '</div>';
	echo '</div>';
	
}

generateValsFrontPageLayout();