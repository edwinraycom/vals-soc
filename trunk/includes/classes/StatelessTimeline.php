<?php
//define('PROGRAM_ACTIVE', 1);
//
define('PROGRAM_NOT_YET_STARTED',0);
define('PRE_ORG_SIGNUP_PERIOD',10);
define('ORG_SIGNUP_PERIOD',20);
define('PRE_ORGS_ANNOUNCED_PERIOD',30);
define('POST_ORGS_ANNOUNCED_PERIOD',40);
define('STUDENT_SIGNUP_PERIOD',50);
define('PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE',60);
define('PRE_PROPOSAL_MATCHED_DEADLINE',70);
define('PRE_STUDENTS_ANNOUNCED_DEADLINE',80);
define('PRE_BONDING_PERIOD',90);
define('PRE_CODING_PERIOD',100);
define('PRE_SUGGESTED_CODING_END_DATE',110);
define('PRE_CODING_DEADLINE',120);
define('OUT_OF_SEASON',130);
define('PROGRAM_INACTIVE',140);
//



class StatelessTimeline {

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
	private $dummy_test_date = NULL;

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
	public function getProgramActive(){
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

	public function setDummyTestDate($dummy){
		$this->dummy_test_date = $dummy;
	}

	/***********************************
	 * 		Helper methods
	* *********************************
	*/
	/**
	 * Put this in one place, which makes it easier to test the timeline i.e. we just change now
	 * @return DateTime
	 */
	public function getNow(){
		if (!isset($this->dummy_test_date)){
			$now = new DateTime();
		}
		else{
			$now = new DateTime($this->dummy_test_date);
		}
		return $now;
	}

	public function getDate($date_format){
		$date = new DateTime($date_format);
		return $date;
	}

	public function isOrganisationSignupPeriod(){
		if($this->cached_org_signup_start_date < $this->getNow() && $this->cached_org_signup_end_date > $this->getNow()){
			return true;
		}
		return false;
	}
	
	public function isPreOrganisationSignupPeriod(){
		if($this->cached_org_signup_start_date > $this->getNow()){
			return true;
		}
		return false;
	}

	public function isAfterOrganisationSignupPeriod(){
		if($this->cached_org_signup_end_date < $this->getNow()){
			return true;
		}
		return false;
	}


	public function isStudentsSignupPeriod(){
		if($this->cached_student_signup_start_date <= $this->getNow() && $this->cached_student_signup_end_date >= $this->getNow() && $this->isProgramActive()){
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
		if($this->cached_student_signup_end_date < $this->getNow() && $this->cached_accepted_students_announced_deadline_date > $this->getNow()){
			return true;
		}
		return false;
	}

	public function isCodingPeriod(){
		if($this->cached_coding_start_date < $this->getNow() && $this->cached_coding_end_date > $this->getNow()){
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
		if($this->cached_accepted_students_announced_deadline_date < $this->getNow() && $this->cached_coding_start_date > $this->getNow()){
			return true;
		}
		return false;
	}
	
	public function getCommunityBondingPeriodStart(){
		return $this->cached_accepted_students_announced_deadline_date;
	}

	public function isAfterOrgsAnnouncedDate(){
		if($this->cached_accepted_org_announced_date < $this->getNow() && $this->isProgramActive()){
			return true;
		}
		return false;
	}

	public function isProgramActive(){
		if($this->cached_program_active && $this->hasProgramStarted()){
			return true;
		}
		return false;
	}


	public function hasProgramStarted(){
		if($this->cached_program_start_date < $this->getNow()){
			return true;
		}
		return false;
	}

	public function hasProgramFinished(){
		if($this->cached_program_end_date > $this->getNow()){
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

	public function getCurrentPeriod(){
		$now = $this->getNow();
		if(Timeline::getInstance()->getProgramActive()){
			// has it started?
			if(!Timeline::getInstance()->hasProgramStarted()){
				return PROGRAM_NOT_YET_STARTED;
			}
			// its started so where are we?
			else if(Timeline::getInstance()->getOrgsSignupStartDate() > $now){
				// programme is running but orgs cant register yet
				return PRE_ORG_SIGNUP_PERIOD;
			}
			else if(Timeline::getInstance()->isOrganisationSignupPeriod()){
				// programme is running orgs can now register
				return ORG_SIGNUP_PERIOD;
			}
			else if(Timeline::getInstance()->getStudentsSignupStartDate() > $now){
				// before student applications start
				// check to see if the org announced date is pending..
				$orgs_announced_date = Timeline::getInstance()->getOrgsAnnouncedDate();
				// accepted orgs not yet announced
				if($orgs_announced_date > $now){
					return PRE_ORGS_ANNOUNCED_PERIOD;
				}
				// accepted orgs already announced
				else{
					return POST_ORGS_ANNOUNCED_PERIOD;
				}
			}
			else if(Timeline::getInstance()->isStudentsSignupPeriod()){
				// student registration period
				return STUDENT_SIGNUP_PERIOD;
			}
			else if(Timeline::getInstance()->isPreCommunityBondingPeriod()){
				//before community bonding period starts and after student signup period
				$orgs_review_student_apps_date = Timeline::getInstance()->getOrgsReviewApplicationsDate();
				$students_matched_deadline = Timeline::getInstance()->getStudentsMatchedToMentorsDate();
				$accepted_students_announced_date = Timeline::getInstance()->getAcceptedStudentsAnnouncedDate();
					
				if($orgs_review_student_apps_date > $now){
					return PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE;
				}
				else if($students_matched_deadline > $now){
					return PRE_PROPOSAL_MATCHED_DEADLINE;
				}
				else if($accepted_students_announced_date > $now){
					return PRE_STUDENTS_ANNOUNCED_DEADLINE;
				}
				else{
					return PRE_BONDING_PERIOD;
				}
			}
			else if(Timeline::getInstance()->isCommunityBondingPeriod()){
				return PRE_CODING_PERIOD;
			}
			else if(Timeline::getInstance()->isCodingPeriod()){
				$suggested_coding_end_date = Timeline::getInstance()->getSuggestedCodingDeadline();
				$coding_end_date = Timeline::getInstance()->getCodingEndDate();
				if($suggested_coding_end_date > $now){
					return PRE_SUGGESTED_CODING_END_DATE;
				}
				else{
					return PRE_CODING_DEADLINE;
				}
			}
			else{
				return OUT_OF_SEASON;
			}
		}
		else{
			return PROGRAM_INACTIVE;
		}
	}
	
	function __destruct(){}
}