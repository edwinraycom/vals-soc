<?php

class Timeline {

	private static $instance;
	private $cached_program_active;
	private $cached_program_start_date;
	private $cached_program_end_date;
	private $cached_org_signup_start_date;
	private $cached_org_signup_end_date;
	private $cached_accepted_org_announced_date;
	private $cached_student_signup_start_date;
	private $cached_student_signup_end_date;
	private $cached_org_review_student_applications_date;
	private $cached_students_matched_to_mentors_deadline_date;
	private $cached_accepted_students_announced_deadline_date;
	//private $cached_students_start_submit_forms_date;
	//private $cached_community_bonding_start_date;
	//private $cached_community_bonding_end_date;
	private $cached_coding_start_date;
	private $cached_coding_end_date;
	private $cached_suggested_coding_deadline;
	
	private function __construct(){
		$this->fetchDates();
	}

	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}

	private function fetchDates(){
		$this->cached_program_active = variable_get('vals_timeline_program_active', 0);
		$this->sanityCheck($this->cached_program_start_date, variable_get('vals_timeline_program_start_date'));
		$this->sanityCheck($this->cached_program_end_date, variable_get('vals_timeline_program_end_date'));
		$this->sanityCheck($this->cached_org_signup_start_date, variable_get('vals_timeline_org_app_start_date')); 
		$this->sanityCheck($this->cached_org_signup_end_date, variable_get('vals_timeline_org_app_end_date'));
		$this->sanityCheck($this->cached_accepted_org_announced_date, variable_get('vals_timeline_accepted_org_announced_date'));
		$this->sanityCheck($this->cached_student_signup_start_date, variable_get('vals_timeline_student_signup_start_date'));
		$this->sanityCheck($this->cached_student_signup_end_date, variable_get('vals_timeline_student_signup_end_date'));
		$this->sanityCheck($this->cached_org_review_student_applications_date, variable_get('vals_timeline_org_review_student_applications_date'));
		$this->sanityCheck($this->cached_students_matched_to_mentors_deadline_date, variable_get('vals_timeline_students_matched_to_mentors_deadline_date'));
		$this->sanityCheck($this->cached_accepted_students_announced_deadline_date, variable_get('vals_timeline_accepted_students_announced_deadline_date'));
		//$this->sanityCheck($this->cached_students_start_submit_forms_date, variable_get('vals_timeline_students_start_submit_forms_date'));
		//$this->sanityCheck($this->cached_community_bonding_start_date, variable_get('vals_timeline_community_bonding_start_date'));
		//$this->sanityCheck($this->cached_community_bonding_end_date, variable_get('vals_timeline_community_bonding_end_date'));
		$this->sanityCheck($this->cached_coding_start_date, variable_get('vals_timeline_coding_start_date'));
		$this->sanityCheck($this->cached_coding_end_date, variable_get('vals_timeline_coding_end_date'));
		$this->sanityCheck($this->cached_suggested_coding_deadline, variable_get('vals_timeline_suggested_coding_deadline'));
	}	

	/***********************************
	 * 		Getter methods
	* *********************************
	*/
	public function isProgramActive(){
		return $this->cached_program_active;
	}
	
	public function getProgramStartDate(){
		return $this->cached_program_start_date;
	}
	
	public function getProgramEndDate(){
		return $this->cached_program_end_date;
	}
	
	public function getOrgsSignupStartDate(){
		return $this->cached_org_signup_start_date;
	}
	
	public function getOrgsSignupEndDate(){
		return $this->cached_org_signup_end_date;
	}

	public function getOrgsAnnouncedDate(){
		return $this->cached_accepted_org_announced_date;
	}

	public function getStudentsSignupStartDate(){
		return $this->cached_student_signup_start_date;
	}
	
	public function getStudentsSignupEndDate(){
		return $this->cached_student_signup_end_date;
	}
		
	public function getOrgsReviewApplicationsDate(){
		return $this->cached_org_review_student_applications_date;
	}
	
	public function getStudentsMatchedToMentorsDate(){
		return $this->cached_students_matched_to_mentors_deadline_date;
	}
	
	public function getAcceptedStudentsAnnouncedDate(){
		return $this->cached_accepted_students_announced_deadline_date;
	}
	
	/*
	public function getStudentsSubmitFormsDate(){
		return $this->cached_students_start_submit_forms_date;
	}
	
	public function getCommunityBondingStartDate(){
		return $this->cached_community_bonding_start_date;
	}

	public function getCommunityBondingEndDate(){
		return $this->cached_community_bonding_end_date;
	}
*/
	public function getCodingStartDate(){
		return $this->cached_coding_start_date;
	}

	public function getCodingEndDate(){
		return $this->cached_coding_end_date;
	}
	
	public function getSuggestedCodingDeadline(){
		return $this->cached_suggested_coding_deadline;
	}
	
	
	/***********************************
	 * 		Helper methods
	 * *********************************
	 */
	public function isOrganisationSignupPeriod(){
		$now = new DateTime();
		if($this->cached_org_signup_start_date < $now && $this->cached_org_signup_end_date > $now){
			return true;
		}
		return false;
	}

	public function isStudentsSignupPeriod(){
		$now = new DateTime();
		if($this->cached_student_signup_start_date < $now && $this->cached_student_signup_end_date > $now){
			return true;
		}
		return false;
	}
	
	/**
	 * The pre-community bonding period is worked out by comparing the end of student signup
	 * period and until the students announced date starts
	 * @return boolean
	 */
	public function isPreCommunityBondingPeriod(){
		$now = new DateTime();
		if($this->cached_student_signup_end_date < $now && $this->cached_accepted_students_announced_deadline_date > $now){
			return true;
		}
		return false;
	}
	
	public function isCodingPeriod(){
		$now = new DateTime();
		if($this->cached_coding_start_date < $now && $this->cached_coding_end_date > $now){
			return true;
		}
		return false;
	}
	
	/**
	 * The community bonding period is worked out by comparing the when the student list was announced
	 * and until the the coding start date is due to start
	 * @return boolean
	 */
	public function isCommunityBondingPeriod(){
		$now = new DateTime();
		if($this->cached_accepted_students_announced_deadline_date < $now && $this->cached_coding_start_date > $now){
			return true;
		}
		return false;
	}
	
	public function hasProgramStarted(){
		if($this->cached_program_start_date < new DateTime()){
			return true;
		}
		return false;
	}
	
	public function hasProgramFinished(){
		if($this->cached_program_end_date > new DateTime()){
			return true;
		}
		return false;
	}
	
	private function sanityCheck(&$localCache, $value){
		if(isset($value) && $this->validateDate($value)){
			$localCache = new DateTime($value);
		}
		else{
			// TODO - what do we do when these are not set.
			//for now we'll just drop NOW in there.
			$localCache = new DateTime();
		}
	}

	private function validateDate($date){
		$d = DateTime::createFromFormat('Y-m-d H:i', $date);
		return $d && $d->format('Y-m-d H:i') == $date;
	}
	
	public function resetCache(){
		$this->fetchDates();
	}

	function __destruct(){}
}