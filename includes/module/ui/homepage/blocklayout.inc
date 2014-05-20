<?php
function generateValsFrontPageLayout(){
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