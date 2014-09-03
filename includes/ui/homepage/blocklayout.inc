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
	
	
	
	
	
	/*
	 * echo '<div id="vals_block_content" class="">';
	
	module_load_include('inc', 'vals_soc', 'includes/ui/homepage/intro');
	
	
	echo "<table style='width:100%;border:2px solid black;' id='test'><tr><td>";
	// get the program div
	module_load_include('inc', 'vals_soc', 'includes/ui/homepage/program');
	// get the news div
	module_load_include('inc', 'vals_soc', 'includes/ui/homepage/news');
	echo "</td><td rowspan='2' style='background-color: #333333;'>";
	module_load_include('inc', 'vals_soc', 'includes/ui/homepage/timeline');
	echo "</td></tr></table>";
	// get the intro div
	echo '</div>';
	 */
}

generateValsFrontPageLayout();