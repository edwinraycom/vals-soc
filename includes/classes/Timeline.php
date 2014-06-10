<?php
module_load_include('php', 'vals_soc', 'includes/classes/StatelessTimeline');
class Timeline {

	public static function getInstance(){
		// uncomment line below to use test dates 
		// - setting when NOW is between page requests
		//$timelineTestDate = "2014-03-04 16:14:15";
		if(isset($timelineTestDate)){
			session_id('TimelineMultipageSession');
			if(!isset($_SESSION)){
				session_start();
			}
			if (!isset($_SESSION['timelineDate'])){
				$_SESSION['timelineDate'] = $timelineTestDate;
			}
			StatelessTimeline::getInstance()->setDummyTestDate($_SESSION['timelineDate']);
		}
		return StatelessTimeline::getInstance();
	}
	
}