<?php 

function vals_soc_block_info_handler() {
	$blocks['soc'] = array(
			// The name that will appear in the block list.
			'info' => t('Semester of Code'),
			// Default setting.
			'cache' => DRUPAL_CACHE_PER_ROLE,
	);
	$blocks['organise'] = array(
			// The name that will appear in the block list.
			'info' => t('Add or edit groups, organisations, institutes etc.'),
			// Default setting.
			'cache' => DRUPAL_CACHE_PER_ROLE,
	);
	return $blocks;
}

function vals_soc_block_view_handler($delta = '') {
	$block = array();
	switch ($delta) {
		case 'soc':
			$block['subject'] = '';
			if (user_access('access content')) {
				ob_start();
				module_load_include('inc', 'vals_soc', 'includes/module/ui/homepage/blocklayout');
				$block['content'] = ob_get_clean();
			}
			return $block;
			break;
		case 'organise':
			if (user_access('access content')) {
				ob_start();
				module_load_include('inc', 'vals_soc', 'includes/module/ui/administer/blocklayout');
				//module_load_include('php', 'vals_soc', 'includes/module/admin');
				$block['content'] = ob_get_clean();
			}
			return $block;
	}
}