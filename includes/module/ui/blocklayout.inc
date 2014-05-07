<?php
function generateLayout(){
	echo '<div id="vals_block_content" class="">';
	// get the intro block
	echo '	<div class="vals-intro block-padding">';
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/intro');
	echo ob_get_clean();
	echo '	</div>';
	echo '	<div class="vals-news block-padding">intro section TODO</div>';
	ob_start();
	module_load_include('inc', 'vals_soc', 'includes/module/ui/timeline');
	echo ob_get_clean();
	
	echo '	<div class="vals-program block-padding">'.t('program section TODO').'</div>';
	
	echo '</div>';
	
}
generateLayout();