<?php 

module_load_include('php', 'vals_soc', 'includes/classes/Timeline');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/melange.timeline.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/raphael.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/countdown/jquery.plugin.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/countdown/jquery.countdown.js');
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/vals_soc.css');

/**
 * Function which will setup the timeline graphic showing the timeline as
 * a chart over the year. Additionally if a particulr date is less than
 * 24 hours away, it will also show a clock counting down to that time. 
 * @param unknown $timeline_args
 * @return string
 */
function getScript($timeline_args){
	$date_insertions = '';
	foreach ($timeline_args['program_dates'] as $timeline_event){
		$date_insertions .= '{"to": "'.$timeline_event[0].'", "from": "'.$timeline_event[1].'", "title": "'.$timeline_event[2].'"},';
	}
	// depending on how the timezone is set in drupal, we need to get the current
	// timezone and replace the values for the melange pie chart with a 'T' character
	$date_insertions = str_replace(date('T'), 'T', $date_insertions);
	
	// TODO - timezone handling can be improved - for now stick to UTC and disable users able to change it in their account settings
	// if server set to BST replace GMT which can occur later in the year - remove this once timezones better handled
	$date_insertions = str_replace('GMT', 'T', $date_insertions);
	//$tz = drupal_get_user_timezone();
	
	$script = '
		<script>
		jQuery(document).ready(function($){
			var timeline_data = {
				"title_selector": "#timeline-head-title",
				"now": "'.$timeline_args['now']->format('Y-m-dTH:i:s').'",
				"timerange_selector": "#timeline-head-timerange",
				"slices": [' . rtrim($date_insertions, ",") . ']
				};
			jQuery("#timeline-body").timeline(timeline_data);
		';
		// if the time left is less than a day, start a countdown timer
		if(isDateLessThanADayAway($timeline_args['countdown'])){
			$script .= '
				var d_day = new Date();
				// note month in js is oddly 0-11 based rather than 1-12
				d_day = new Date('.$timeline_args['countdown']->format('Y,m-1,d,H,i,s').');
			    // TODO - the following does not work on older versions of webkit - results in NAN error
				$("#vals-timeline-countdown").countdown({until: d_day});
			';
		}
		// otherwise just list as text
		else{
			$dDate = formatDateDiff($timeline_args['now'], $timeline_args['countdown']);
			$script .= '
				$("#vals-timeline-countdown").html("'.$dDate.'");
			';
		}
		$script .= '	
		});
		</script>
	';
	
	return $script;
}

/**
 * Gets the html & js code for the timeline block
 * @param $timeline_args - a multidimensional array of configuration and date info
 * @param string $show_countdown - boolean whether to show a count down or not
 * @return string - the html to render
 */
function getLayout($timeline_args, $show_countdown = true){
	$outputBuf = '';
	$countRemaining = '';
	if($show_countdown){
		$outputBuf = getScript($timeline_args);
		$countRemaining = t('Remaining');
	}
	return $outputBuf . '
		<div id="program-map" class="block block-project-timeline block-padding">
			<h4 id="title-section-program-timeline">'.t('Program Timeline').'</h4>
			<div id="block-program-timeline-content" class="block-content">
				<div class="block-program-timeline-new">
					<div class="timeline-head">
						<div id="timeline-head-title" class="title"></div>
						<div id="timeline-head-timerange" class="timerange"></div>
					</div>
					<div id="timeline-body" class="timeline-body"></div>
				</div>
				<div id="block-program-timeline-content-countdown" class="block-project-timeline-countdown">
					<strong class="deadline">'.$timeline_args['deadline_one'].'</strong>
					<strong class="deadline">'.$timeline_args['deadline_two'].'</strong>
					<strong id="vals-timeline-countdown" class="countdown"></strong>
					<strong class="countdown">'.$countRemaining.'</strong>
				</div>
			</div>
		</div>
';
}

/**
 * Works out how long it is from a given date from now and returns
 * a pretty version of the time remaining
 * @param $start - should be now()
 * @param string $end - the date to compare against
 * @return string - pretty date
 */
function formatDateDiff($start, $end=null){
	if(!($start instanceof DateTime)) {
		$start = new DateTime($start);
	}
	 
	if($end === null){
		$end = new DateTime();
	}
	 
	if(!($end instanceof DateTime)){
		$end = new DateTime($start);
	}
	 
	$interval = $end->diff($start);
	$doPlural = function($nb,$str){return $nb>1?$str.'s':$str;};
	 
	$format = array();
	if($interval->y !== 0){
		$format[] = "%y ".$doPlural($interval->y, t('year'));
	}
	if($interval->m !== 0){
		$format[] = "%m ".$doPlural($interval->m, t('month'));
	}
	if($interval->d !== 0){
		$format[] = "%d ".$doPlural($interval->d, t('day'));
	}
	if($interval->h !== 0){
		$format[] = "%h ".$doPlural($interval->h, t('hour'));
	}
	if($interval->i !== 0){
		$format[] = "%i ".$doPlural($interval->i, t('minute'));
	}
	if($interval->s !== 0){
		if(!count($format)){
			return t('less than a minute ago');
		} 
		else {
			$format[] = "%s ".$doPlural($interval->s, "second");
		}
	} 
	// We use the two biggest parts
	if(count($format) > 1) {
		$format = array_shift($format)." and ".array_shift($format);
	} 
	else {
		$format = array_pop($format);
	}

	return $interval->format($format);
}

/**
 * Checks to see if given date is less than a day away
 * @param unknown $dateToCheck
 * @return boolean 
 */
function isDateLessThanADayAway($dateToCheck){
	$dateToCheck_t = clone $dateToCheck;
	$dateToCheck_t -> modify('-1 day');
	if(strtotime('now') > strtotime($dateToCheck_t->format('Y-m-d H:i'))){
		return true;
	}
	return false;
}


/**
 * Logic to figure out where we are in the current timeline and populate an array of values
 * which can be passed over to other functions which generate UI components
 * 
 * Returns the generated html to render
 */
function generateTimelineOutput(){
	$timeline_args = array();
	$now = new DateTime();
	$timeline_args['now'] = $now;
	
	// Is there is an active program
	if(Timeline::getInstance()->isProgramActive()){
		// has it started?
		if(!Timeline::getInstance()->hasProgramStarted()){
			$timeline_args['deadline_one'] = t('Program starts:');
			$timeline_args['deadline_two'] = Timeline::getInstance()->getProgramStartDate()->format('F j, Y, g:i a') . ' '.date('T');
			$timeline_args['countdown'] = Timeline::getInstance()->getProgramStartDate();
		}
		// its started so where are we?
		else if(Timeline::getInstance()->getOrgsSignupStartDate() > $now){
			// programme is running but orgs cant register yet
			$timeline_args['deadline_one'] = t('Org Application opens:');
			$timeline_args['deadline_two'] = Timeline::getInstance()->getOrgsSignupStartDate()->format('F j, Y, g:i a') . ' '.date('T');
			$timeline_args['countdown'] = Timeline::getInstance()->getOrgsSignupStartDate();
		}
		else if(Timeline::getInstance()->isOrganisationSignupPeriod()){
			// programme is running orgs can now register 
			$timeline_args['deadline_one'] = t('Org Application closes:');
			$timeline_args['deadline_two'] = Timeline::getInstance()->getOrgsSignupEndDate()->format('F j, Y, g:i a') . ' '.date('T');
			$timeline_args['countdown'] = Timeline::getInstance()->getOrgsSignupEndDate();
		}
		else if(Timeline::getInstance()->getStudentsSignupStartDate() > $now){
			// before student applications start
			// check to see if the org announced date is pending..
			$orgs_announced_date = Timeline::getInstance()->getOrgsAnnouncedDate();
			// accepted orgs not yet announced
			if($orgs_announced_date > $now){
				$timeline_args['deadline_one'] = t('Accepted Orgs announced on::');
				$timeline_args['deadline_two'] = $orgs_announced_date->format('F j, Y, g:i a') . ' '.date('T');
				$timeline_args['countdown'] = $orgs_announced_date;
			}
			// accepted orgs already announced
			else{
				$timeline_args['deadline_one'] = t('Student Application opens:');
				$timeline_args['deadline_two'] = Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a') . ' '.date('T');
				$timeline_args['countdown'] = Timeline::getInstance()->getStudentsSignupStartDate();
			}
		}
		else if(Timeline::getInstance()->isStudentsSignupPeriod()){
			// student registration period
			$timeline_args['deadline_one'] = t('Student Application closes:');
			$timeline_args['deadline_two'] = Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a') . ' '.date('T');
			$timeline_args['countdown'] = Timeline::getInstance()->getStudentsSignupEndDate();
		}
		else if(Timeline::getInstance()->isPreCommunityBondingPeriod()){
			//before community bonding period starts and after student signup perios
			$orgs_review_student_apps_date = Timeline::getInstance()->getOrgsReviewApplicationsDate();
			$students_matched_deadline = Timeline::getInstance()->getStudentsMatchedToMentorsDate();
			$accepted_students_announced_date = Timeline::getInstance()->getAcceptedStudentsAnnouncedDate();
			
			if($orgs_review_student_apps_date > $now){
				$timeline_args['deadline_one'] = t('Orgs review applications deadline:');
				$timeline_args['deadline_two'] = $orgs_review_student_apps_date->format('F j, Y, g:i a') . ' '.date('T');
				$timeline_args['countdown'] = $orgs_review_student_apps_date;
			}
			else if($students_matched_deadline > $now){
				$timeline_args['deadline_one'] = t('Proposal matched deadline:');
				$timeline_args['deadline_two'] = $students_matched_deadline->format('F j, Y, g:i a') . ' '.date('T');
				$timeline_args['countdown'] = $students_matched_deadline;
			}
			else if($accepted_students_announced_date > $now){
				$timeline_args['deadline_one'] = t('Accepted students announced:');
				$timeline_args['deadline_two'] = $accepted_students_announced_date->format('F j, Y, g:i a') . ' '.date('T');
				$timeline_args['countdown'] = $accepted_students_announced_date;
			}
			else{
				$timeline_args['deadline_one'] = t('Bonding period starts:');
				$timeline_args['deadline_two'] = $accepted_students_announced_date->format('F j, Y, g:i a') . ' '.date('T');
				$timeline_args['countdown'] = $accepted_students_announced_date;
			}
		}
		else if(Timeline::getInstance()->isCommunityBondingPeriod()){
			$coding_period = Timeline::getInstance()->getCodingStartDate();
			$timeline_args['deadline_one'] = t('Coding period starts:');
			$timeline_args['deadline_two'] = $coding_period->format('F j, Y, g:i a') . ' '.date('T');
			$timeline_args['countdown'] = $coding_period;
		}
		else if(Timeline::getInstance()->isCodingPeriod()){
			$suggested_coding_end_date = Timeline::getInstance()->getSuggestedCodingDeadline();
			$coding_end_date = Timeline::getInstance()->getCodingEndDate();
			if($suggested_coding_end_date > $now){
				$timeline_args['deadline_one'] = t('Suggested coding end:');
				$timeline_args['deadline_two'] = $suggested_coding_end_date->format('F j, Y, g:i a') . ' '.date('T');
				$timeline_args['countdown'] = $suggested_coding_end_date;
			}
			else{
				$timeline_args['deadline_one'] = t('Coding deadline:');
				$timeline_args['deadline_two'] = $coding_end_date->format('F j, Y, g:i a') . ' '.date('T');
				$timeline_args['countdown'] = $coding_end_date;
			}
		}
		else{
			$timeline_args['deadline_one'] = t('Out of season');
			$timeline_args['deadline_two'] = $now->format('F j, Y, g:i a') . ' '.date('T');
			$timeline_args['countdown'] = $now;
		}
		// build the timeline data for the js functions in the page
		$program_dates = array(
			array(Timeline::getInstance()->getProgramStartDate()->format('Y-m-dTH:i:s'), 
					Timeline::getInstance()->getCodingEndDate()->format('Y-m-dTH:i:s'), t("Off season")),
			array(Timeline::getInstance()->getOrgsSignupEndDate()->format('Y-m-dTH:i:s'),
					Timeline::getInstance()->getOrgsSignupStartDate()->format('Y-m-dTH:i:s'), t("Organisation applications")),
			array(Timeline::getInstance()->getStudentsSignupEndDate()->format('Y-m-dTH:i:s'),
					Timeline::getInstance()->getStudentsSignupStartDate()->format('Y-m-dTH:i:s'), t("Student applications")),
			array(Timeline::getInstance()->getCodingStartDate()->format('Y-m-dTH:i:s'),
					Timeline::getInstance()->getAcceptedStudentsAnnouncedDate()->format('Y-m-dTH:i:s'), t("Community bonding")),
			array(Timeline::getInstance()->getCodingEndDate()->format('Y-m-dTH:i:s'),
					Timeline::getInstance()->getCodingStartDate()->format('Y-m-dTH:i:s'), t("Students coding"))
		);
		$timeline_args['program_dates'] = $program_dates;
		echo getLayout($timeline_args);
	}
	else{
		$timeline_args['deadline_one'] = t('There are currently no active programmes');
		$timeline_args['deadline_two'] = '';
		echo getLayout($timeline_args, false);
	}
}

//Build!
generateTimelineOutput();