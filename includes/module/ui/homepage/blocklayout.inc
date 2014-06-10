<?php
function generateValsFrontPageLayout(){
	module_load_include('php', 'vals_soc', 'includes/classes/Timeline');
	//echo '<h1>'.Timeline::getInstance()->getNow()->format('F j, Y, g:i a').'</h1>';
	echo '<div id="vals_block_content" class="">';
	
	// get the intro div
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/homepage/intro');
	echo ob_get_clean();
	
	// get the news div
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/homepage/news');
	echo ob_get_clean();
	
	// get the timeline div
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/homepage/timeline');
	echo ob_get_clean();
	
	// get the program div
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/homepage/program');
	echo ob_get_clean();

	echo '</div>';
	
}
generateValsFrontPageLayout();