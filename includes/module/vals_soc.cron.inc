<?php
function vals_soc_cron_queue_info_handler() {
	drupal_set_message('vals_soc_cron_queue_info_handler called');
	module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');
	$queues['vals_soc_cron_email'] = array( 'worker callback' => 'vals_soc_cron_email_handler', // This is the callback function for each queue item.
		'time' => 180); // This is the max run time per cron run in seconds. 
		return $queues;
}