<?php
/**
 * Here we specify any custom tasks we want to do when cron normally runs
 */
function vals_soc_cron_handler(){
	module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');
	drupal_set_message(t('vals_soc_cron_handler called'));
	//module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');
	//$period = Timeline::getInstance()->getCurrentPeriod();
	//variable_set('vals_soc_current_period', $period);
	$to = "ps3com40@gmail.com";
	$items = array();
	$items[0] = array('to' => $to, 'message_type' => 'vals_soc_email_accepted_organisation');
	/*
	$queue = DrupalQueue::get('vals_soc_cron_email');
	foreach($items as $item) {
		$queue->createItem($item);
	}
	*/
	vals_soc_cron_email_handler($items[0]);
}


/**
 * This function uses a queue and is fired by other events when they want to batch run
 * a number of emails to be sent using cron , rather than just send them in
 * bulk in one go, which can cause the server to run slowly..
 * 
 * @return multitype:string number
 */
function vals_soc_cron_queue_info_handler() {
	drupal_set_message(t('vals_soc_cron_queue_info_handler called'));
	module_load_include('inc', 'vals_soc', 'includes/module/vals_soc.mail');
	$queues['vals_soc_cron_email'] = array( 'worker callback' => 'vals_soc_cron_email_handler', // This is the callback function for each queue item.
		'time' => 180); // This is the max run time per cron run in seconds. 
		return $queues;
}

