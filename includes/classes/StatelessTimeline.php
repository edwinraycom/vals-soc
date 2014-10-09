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

	public function organisationSignupPeriodOpened(){
		if($this->cached_org_signup_start_date < $this->getNow()){
			return true;
		}
		return false;
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
		if($this->isProgramActive()){
			$now = $this->getNow();
			return ($this->cached_student_signup_start_date <= $now)  && ($this->cached_student_signup_end_date >= $now);
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
	
	public static function getStudentTimelineVars(){
		$timeline = Timeline::getInstance();
		$timeline_args = array();
		$timeline_args['viewOrganisations'] = FALSE;
		$timeline_args['viewProjectIdeas'] = FALSE;
		$timeline_args['connectionsVisible'] = FALSE;
		$timeline_args['myInstitutionVisible'] = FALSE;
		$timeline_args['myOrganisationsVisible'] = FALSE; // a list of organisations i participate in
		$timeline_args['myProposalsVisible'] = FALSE; // proposals I have submitted
		$timeline_args['myAcceptedProjectsVisible'] = FALSE;
		$timeline_args['dashboardLegend'] = "";
	
		$period = $timeline->getCurrentPeriod();
		switch ($period) {
			case PROGRAM_NOT_YET_STARTED:
				$timeline_args['dashboardLegend'] =
				t("Program has not started yet.  Menu options will be available after the student signup date. ") .
				$timeline->getStudentsSignupStartDate()->format('F j, Y, g:i a');
				break;
			case PRE_ORG_SIGNUP_PERIOD:
			case ORG_SIGNUP_PERIOD:
			case PRE_ORGS_ANNOUNCED_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Program has now started.  Menu options will be available once the student signup period begins. ") .
				$timeline->getStudentsSignupStartDate()->format('F j, Y, g:i a');
				break;
			case POST_ORGS_ANNOUNCED_PERIOD: // orgs announced so lets student look at the project ideas - cant apply yet
				$timeline_args['dashboardLegend'] = t("You can now browse the organisations and project ideas. ".
						"You can start to apply for projects once the student sign period begins. ").
						$timeline->getStudentsSignupStartDate()->format('F j, Y, g:i a');
						$timeline_args['myInstitutionVisible'] = TRUE; // see my institution details
						$timeline_args['viewOrganisations'] = TRUE; // look at the organisations
						$timeline_args['viewProjectIdeas'] = TRUE; // look at the project ideas
						$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
						break;
			case STUDENT_SIGNUP_PERIOD: // Students can now apply for projects
				$timeline_args['dashboardLegend'] = t("Student signup period. You can now submit project proposals." .
						"Please complete any project proposals before the following date. ").
						$timeline->getStudentsSignupEndDate()->format('F j, Y, g:i a');;
						$timeline_args['myInstitutionVisible'] = TRUE; // see my institution details
						$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
						$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
						$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
						$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
						break;
			case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Project proposals due to be evaluated by. ") .
				$timeline->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
				$timeline_args['myInstitutionVisible'] = TRUE; // see my institution details
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				break;
			case PRE_PROPOSAL_MATCHED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Students projects matched to mentors on the following date. ") .
				$timeline->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');
				$timeline_args['myInstitutionVisible'] = TRUE; // see my institution details
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				break;
			case PRE_STUDENTS_ANNOUNCED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("The list of students and projects will become visable to everyone after the following date. ") .
				$timeline->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
				$timeline_args['myInstitutionVisible'] = TRUE; // see my institution details
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				break;
			case PRE_BONDING_PERIOD: // here is where student sees his accepted projects
				$timeline_args['dashboardLegend'] = t("The list of students and projects is now visible to other users of the system. " .
						"The Bonding period starts on the following date. ") .
				$timeline->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
				$timeline_args['myInstitutionVisible'] = TRUE; // see my institution details
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				$timeline_args['myAcceptedProjectsVisible'] =  TRUE; // projects I am accepted onto
				$timeline_args['myOrganisationsVisible'] = TRUE; // Organisations I am a member of
				break;
			case PRE_CODING_PERIOD:
				$timeline_args['dashboardLegend'] = t("Community bonding period.  Coding starts on the following date. ") .
				$timeline->getCodingStartDate()->format('F j, Y, g:i a');
				$timeline_args['myInstitutionVisible'] = TRUE; // see my institution details
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				$timeline_args['myAcceptedProjectsVisible'] =  TRUE; // projects I am accepted onto
				$timeline_args['myOrganisationsVisible'] = TRUE; // Organisations I am a member of
				break;
			case PRE_SUGGESTED_CODING_END_DATE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the suggested end date for coding. ") .
				$timeline->getSuggestedCodingDeadline()->format('F j, Y, g:i a');
				$timeline_args['myInstitutionVisible'] = TRUE; // see my institution details
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				$timeline_args['myAcceptedProjectsVisible'] =  TRUE; // projects I am accepted onto
				$timeline_args['myOrganisationsVisible'] = TRUE; // Organisations I am a member of
				break;
			case PRE_CODING_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the deadline date for coding. ") .
				$timeline->getCodingEndDate()->format('F j, Y, g:i a');
				$timeline_args['myInstitutionVisible'] = TRUE; // see my institution details
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				$timeline_args['myAcceptedProjectsVisible'] =  TRUE; // projects I am accepted onto
				$timeline_args['myOrganisationsVisible'] = TRUE; // Organisations I am a member of
				break;
			case OUT_OF_SEASON:
				$timeline_args['dashboardLegend'] = t("The program is currently out of season.");
				break;
			case PROGRAM_INACTIVE:
			default:
				$timeline_args['dashboardLegend'] = t("No program currently active.");
				break;
		}
		return $timeline_args;
	}
	
	public static function getSupervisorTimelineVars(){
		$timeline = Timeline::getInstance();
		$timeline_args = array();
		$timeline_args['managedOrganisationsVisible'] = FALSE;
		$timeline_args['manageProjectIdeasVisible'] = FALSE;
		$timeline_args['connectionsVisible'] = FALSE;
		//$timeline_args['groupsVisible'] = FALSE;
		$timeline_args['organisationMembersVisible'] = FALSE;
		$timeline_args['proposalsVisible'] = FALSE;
		$timeline_args['matchedProjectsVisible'] = FALSE;
		$timeline_args['dashboardLegend'] = "";
		$period = $timeline->getCurrentPeriod();
	
		$timeline_args['groupsVisible'] = $period >= ORG_SIGNUP_PERIOD;
		switch ($period) {
			case PROGRAM_NOT_YET_STARTED:
				$timeline_args['dashboardLegend'] =
				t("Program has not started yet.  Menu options will be available from the following date. ") .
				$timeline->getProgramStartDate()->format('F j, Y, g:i a');
				break;
			case PRE_ORG_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Program is active, however you must wait until the following date to register your organization/s. ") .
				$timeline->getOrgsSignupStartDate()->format('F j, Y, g:i a');
				break;
			case ORG_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Enter your organisation details and project ideas. You have until the following date when you can no longer add or delete entries. ") .
				$timeline->getOrgsSignupEndDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				break;
			case PRE_ORGS_ANNOUNCED_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Modify your organisation details and project ideas. You have until the following date when your organisations and project ".
						"ideas become visible to students. ") .
						$timeline->getOrgsAnnouncedDate()->format('F j, Y, g:i a');
						$timeline_args['managedOrganisationsVisible'] = TRUE; // only modify entries
						$timeline_args['manageProjectIdeasVisible'] = TRUE;
						$timeline_args['organisationMembersVisible'] = TRUE;
						break;
			case POST_ORGS_ANNOUNCED_PERIOD:
				$timeline_args['dashboardLegend'] = t("Your organisations and project ideas are now visible to other users of the system.");
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				break;
			case STUDENT_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] = t("Student signup period. Students can now submit project proposals");
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Please review your project applications before the following date. ") .
				$timeline->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_PROPOSAL_MATCHED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Please ensure you have matched all students projects to mentors before the following date. ") .
				$timeline->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_STUDENTS_ANNOUNCED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("The list of students and projects will become visable to everyone after the following date. ") .
				$timeline->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_BONDING_PERIOD:
				$timeline_args['dashboardLegend'] = t("The list of students and projects is now visible to other users of the system. " .
						"The Bonding period starts on the following date. ") .
						$timeline->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
						$timeline_args['managedOrganisationsVisible'] = TRUE;
						$timeline_args['manageProjectIdeasVisible'] = TRUE;
						$timeline_args['organisationMembersVisible'] = TRUE;
						$timeline_args['connectionsVisible'] = TRUE;
						$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
						$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
						break;
			case PRE_CODING_PERIOD:
				$timeline_args['dashboardLegend'] = t("Community bonding period.  Coding starts on the following date. ") .
				$timeline->getCodingStartDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				break;
			case PRE_SUGGESTED_CODING_END_DATE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the suggested end date for coding. ") .
				$timeline->getSuggestedCodingDeadline()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				break;
			case PRE_CODING_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the deadline date for coding. ") .
				$timeline->getCodingEndDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				break;
			case OUT_OF_SEASON:
				$timeline_args['dashboardLegend'] = t("The program is currently out of season.");
				break;
			case PROGRAM_INACTIVE:
			default:
				$timeline_args['dashboardLegend'] = t("No program currently active.");
				break;
		}
		return $timeline_args;
	}
	
	public static function getInstadminTimelineVars(){
		$timeline = Timeline::getInstance();
		$timeline_args = array();
		$timeline_args['managedOrganisationsVisible'] = FALSE;
		$timeline_args['manageProjectIdeasVisible'] = FALSE;
		$timeline_args['connectionsVisible'] = FALSE;
	
		$timeline_args['organisationMembersVisible'] = FALSE;
		$timeline_args['proposalsVisible'] = FALSE;
		$timeline_args['matchedProjectsVisible'] = FALSE;
		$timeline_args['dashboardLegend'] = "";
		$period = $timeline->getCurrentPeriod();
	
		//TODO: instead of repeating the same vars for every period, most of them hold from a certain point on. As they
		// 		can be ordered synchronously as wel as numerically we can easily do some numeric equations to find the right vars
		$timeline_args['groupsVisible'] = $period >= ORG_SIGNUP_PERIOD;
	
		switch ($period) {
			case PROGRAM_NOT_YET_STARTED:
				$timeline_args['dashboardLegend'] =
				t("Program has not started yet.  Menu options will be available from the following date. ") .
				$timeline->getProgramStartDate()->format('F j, Y, g:i a');
				break;
			case PRE_ORG_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Program is active, however you must wait until the following date to register your organization/s. ") .
				$timeline->getOrgsSignupStartDate()->format('F j, Y, g:i a');
				break;
			case ORG_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Enter your organisation details and project ideas. You have until the following date when you can no longer add or delete entries. ") .
				$timeline->getOrgsSignupEndDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				break;
			case PRE_ORGS_ANNOUNCED_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Modify your organisation details and project ideas. You have until the following date when your organisations and project ".
						"ideas become visible to students. ") .
						$timeline->getOrgsAnnouncedDate()->format('F j, Y, g:i a');
						$timeline_args['managedOrganisationsVisible'] = TRUE; // only modify entries
						$timeline_args['manageProjectIdeasVisible'] = TRUE;
						$timeline_args['organisationMembersVisible'] = TRUE;
						break;
			case POST_ORGS_ANNOUNCED_PERIOD:
				$timeline_args['dashboardLegend'] = t("Your organisations and project ideas are now visible to other users of the system.");
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				break;
			case STUDENT_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] = t("Student signup period. Students can now submit project proposals");
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Please review your project applications before the following date. ") .
				$timeline->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_PROPOSAL_MATCHED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Please ensure you have matched all students projects to mentors before the following date. ") .
				$timeline->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_STUDENTS_ANNOUNCED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("The list of students and projects will become visable to everyone after the following date. ") .
				$timeline->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_BONDING_PERIOD:
				$timeline_args['dashboardLegend'] = t("The list of students and projects is now visible to other users of the system. " .
						"The Bonding period starts on the following date. ") .
						$timeline->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
						$timeline_args['managedOrganisationsVisible'] = TRUE;
						$timeline_args['manageProjectIdeasVisible'] = TRUE;
						$timeline_args['organisationMembersVisible'] = TRUE;
						$timeline_args['connectionsVisible'] = TRUE;
						$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
						$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
						break;
			case PRE_CODING_PERIOD:
				$timeline_args['dashboardLegend'] = t("Community bonding period.  Coding starts on the following date. ") .
				$timeline->getCodingStartDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				break;
			case PRE_SUGGESTED_CODING_END_DATE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the suggested end date for coding. ") .
				$timeline->getSuggestedCodingDeadline()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				break;
			case PRE_CODING_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the deadline date for coding. ") .
				$timeline->getCodingEndDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				break;
			case OUT_OF_SEASON:
				$timeline_args['dashboardLegend'] = t("The program is currently out of season.");
				break;
			case PROGRAM_INACTIVE:
			default:
				$timeline_args['dashboardLegend'] = t("No program currently active.");
				break;
		}
			
		return $timeline_args;
	}
	
	public static function getMentorTimelineVars(){
		$timeline = Timeline::getInstance();
		$timeline_args = array();
		$timeline_args['myOrganisationsVisible'] = FALSE; // a list of organisations i participate in
		$timeline_args['manageProjectIdeasVisible'] = FALSE; // manage the project ideas?
		$timeline_args['browseProjectIdeasVisible'] = FALSE;
		$timeline_args['organisationMembersVisible'] = FALSE;// a list of users per org I am attached to
		$timeline_args['connectionsVisible'] = FALSE; // communication tools with other users of the system
		$timeline_args['proposalsVisible'] = FALSE; // submitted proposals
		$timeline_args['projectsIamMentorForVisible'] = FALSE; // matched actual projects
		$timeline_args['dashboardLegend'] = "";
		$period = $timeline->getCurrentPeriod();
		switch ($period) {
			case PROGRAM_NOT_YET_STARTED:
				//mentor starts to do things after orgs are announced
				$timeline_args['dashboardLegend'] =
				t("Program has not started yet.  Menu options will be available from the following date. ") .
				$timeline->getOrgsAnnouncedDate()->format('F j, Y, g:i a');
				break;
			case PRE_ORG_SIGNUP_PERIOD:
			case ORG_SIGNUP_PERIOD:
			case PRE_ORGS_ANNOUNCED_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Program has started.  More menu options will be available from the following date. ") .
				$timeline->getOrgsAnnouncedDate()->format('F j, Y, g:i a');
				$timeline_args['myOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE; // should we also allow mentors to modify project ideas?
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				break;
			case POST_ORGS_ANNOUNCED_PERIOD:
				$timeline_args['dashboardLegend'] = t("Here you can view your organisations and see the project ideas.");
				$timeline_args['myOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE; // should we also allow mentors to modify project ideas?
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				break;
			case STUDENT_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] = t("Student signup period. Students can now submit project proposals");
				$timeline_args['myOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Please review your project applications before the following date. ") .
				$timeline->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
				$timeline_args['myOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_PROPOSAL_MATCHED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Please ensure you have matched all students projects to mentors before the following date. ") .
				$timeline->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');
				$timeline_args['myOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_STUDENTS_ANNOUNCED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("The list of students and projects will become visable to everyone after the following date. ") .
				$timeline->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
				$timeline_args['myOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_BONDING_PERIOD:
				$timeline_args['dashboardLegend'] = t("The list of students and projects is now visible to other users of the system. " .
						"The Bonding period starts on the following date. ") .
						$timeline->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
						$timeline_args['myOrganisationsVisible'] = TRUE;
						$timeline_args['manageProjectIdeasVisible'] = TRUE;
						$timeline_args['browseProjectIdeasVisible'] = TRUE;
						$timeline_args['organisationMembersVisible'] = TRUE;
						$timeline_args['connectionsVisible'] = TRUE;
						$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
						$timeline_args['projectsIamMentorForVisible'] = TRUE;
						break;
			case PRE_CODING_PERIOD:
				$timeline_args['dashboardLegend'] = t("Community bonding period.  Coding starts on the following date. ") .
				$timeline->getCodingStartDate()->format('F j, Y, g:i a');
				$timeline_args['myOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['projectsIamMentorForVisible'] = TRUE;
				break;
			case PRE_SUGGESTED_CODING_END_DATE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the suggested end date for coding. ") .
				$timeline->getSuggestedCodingDeadline()->format('F j, Y, g:i a');
				$timeline_args['myOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				$timeline_args['projectsIamMentorForVisible'] = TRUE;
				break;
			case PRE_CODING_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the deadline date for coding. ") .
				$timeline->getCodingEndDate()->format('F j, Y, g:i a');
				$timeline_args['myOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['projectsIamMentorForVisible'] = TRUE;
				break;
			case OUT_OF_SEASON:
				$timeline_args['dashboardLegend'] = t("The program is currently out of season.");
				break;
			case PROGRAM_INACTIVE:
			default:
				$timeline_args['dashboardLegend'] = t("No program currently active.");
				break;
		}
			
		return $timeline_args;
	}
	
	public static function getOrgadminTimelineVars(){
		$timeline = Timeline::getInstance();
		$timeline_args = array();
		$timeline_args['managedOrganisationsVisible'] = FALSE;
		$timeline_args['manageProjectIdeasVisible'] = FALSE;
		$timeline_args['browseProjectIdeasVisible'] = FALSE;
		$timeline_args['organisationMembersVisible'] = FALSE;
		$timeline_args['connectionsVisible'] = FALSE;
		$timeline_args['proposalsVisible'] = FALSE;
		$timeline_args['matchedProjectsVisible'] = FALSE;
		$timeline_args['dashboardLegend'] = "";
		$period = $timeline->getCurrentPeriod();
		switch ($period) {
			case PROGRAM_NOT_YET_STARTED:
				$timeline_args['dashboardLegend'] =
				t("Program has not started yet.  Menu options will be available from the following date. ") .
				$timeline->getProgramStartDate()->format('F j, Y, g:i a');
				break;
			case PRE_ORG_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Program is active, however you must wait until the following date to register your organization/s. ") .
				$timeline->getOrgsSignupStartDate()->format('F j, Y, g:i a');
				break;
			case ORG_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Enter your organisation details and project ideas. You have until the following date when you can no longer add or delete entries. ") .
				$timeline->getOrgsSignupEndDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				break;
			case PRE_ORGS_ANNOUNCED_PERIOD:
				$timeline_args['dashboardLegend'] =
				t("Modify your organisation details and project ideas. You have until the following date when your organisations and project ".
						"ideas become visible to students. ") .
						$timeline->getOrgsAnnouncedDate()->format('F j, Y, g:i a');
						$timeline_args['managedOrganisationsVisible'] = TRUE; // only modify entries
						$timeline_args['manageProjectIdeasVisible'] = TRUE;
						$timeline_args['browseProjectIdeasVisible'] = TRUE;
						$timeline_args['organisationMembersVisible'] = TRUE;
						break;
			case POST_ORGS_ANNOUNCED_PERIOD:
				$timeline_args['dashboardLegend'] = t("Your organisations and project ideas are now visible to other users of the system.");
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				break;
			case STUDENT_SIGNUP_PERIOD:
				$timeline_args['dashboardLegend'] = t("Student signup period. Students can now submit project proposals");
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Please review your project applications before the following date. ") .
				$timeline->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_PROPOSAL_MATCHED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Please ensure you have matched all students projects to mentors before the following date. ") .
				$timeline->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_STUDENTS_ANNOUNCED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("The list of students and projects will become visable to everyone after the following date. ") .
				$timeline->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				break;
			case PRE_BONDING_PERIOD:
				$timeline_args['dashboardLegend'] = t("The list of students and projects is now visible to other users of the system. " .
						"The Bonding period starts on the following date. ") .
						$timeline->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
						$timeline_args['managedOrganisationsVisible'] = TRUE;
						$timeline_args['manageProjectIdeasVisible'] = TRUE;
						$timeline_args['browseProjectIdeasVisible'] = TRUE;
						$timeline_args['organisationMembersVisible'] = TRUE;
						$timeline_args['connectionsVisible'] = TRUE;
						$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
						$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
						break;
			case PRE_CODING_PERIOD:
				$timeline_args['dashboardLegend'] = t("Community bonding period.  Coding starts on the following date. ") .
				$timeline->getCodingStartDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				break;
			case PRE_SUGGESTED_CODING_END_DATE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the suggested end date for coding. ") .
				$timeline->getSuggestedCodingDeadline()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				break;
			case PRE_CODING_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Coding period. The following is the deadline date for coding. ") .
				$timeline->getCodingEndDate()->format('F j, Y, g:i a');
				$timeline_args['managedOrganisationsVisible'] = TRUE;
				$timeline_args['manageProjectIdeasVisible'] = TRUE;
				$timeline_args['browseProjectIdeasVisible'] = TRUE;
				$timeline_args['organisationMembersVisible'] = TRUE;
				$timeline_args['connectionsVisible'] = TRUE;
				$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
				$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
				break;
			case OUT_OF_SEASON:
				$timeline_args['dashboardLegend'] = t("The program is currently out of season.");
				break;
			case PROGRAM_INACTIVE:
			default:
				$timeline_args['dashboardLegend'] = t("No program currently active.");
				break;
		}
			
		return $timeline_args;
	}
	
	function __destruct(){}
}