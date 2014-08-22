<?php
module_load_include('php', 'vals_soc', 'includes/classes/StatelessTimeline');
class Timeline {

	public static function getInstance($timelineTestDate=null){
		Timeline::setupSession($timelineTestDate);
		return StatelessTimeline::getInstance();
	}
	
	private static function setupSession($timelineTestDate){
		if(!isset($_SESSION)){
			session_id('TimelineMultipageSession');
			session_start();
		}
		if(isset($timelineTestDate)){
			$_SESSION['timelineDate'] = $timelineTestDate;
			StatelessTimeline::getInstance()->setDummyTestDate($_SESSION['timelineDate']);
			watchdog('setting NOW from new value', $timelineTestDate);
			return;
		}
		if (isset($_SESSION['timelineDate'])){
			watchdog('setting NOW from value found in session',$_SESSION['timelineDate']);
			StatelessTimeline::getInstance()->setDummyTestDate($_SESSION['timelineDate']);
		}
		
	}
	
	public static function getStudentTimelineVars(){
		$timeline = Timeline::getInstance();
		$timeline_args = array();
		$timeline_args['viewOrganisations'] = FALSE;
		$timeline_args['viewProjectIdeas'] = FALSE;
		$timeline_args['connectionsVisible'] = FALSE;
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
				$timeline_args['viewOrganisations'] = TRUE; // look at the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				break;
			case STUDENT_SIGNUP_PERIOD: // Students can now apply for projects
				$timeline_args['dashboardLegend'] = t("Student signup period. You can now submit project proposals." .
				"Please complete any project proposals before the following date. ").
				$timeline->getStudentsSignupEndDate()->format('F j, Y, g:i a');;
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				break;
			case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Project proposals due to be evaluated by. ") .
				$timeline->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				break;
			case PRE_PROPOSAL_MATCHED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("Students projects matched to mentors on the following date. ") .
				$timeline->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				break;
			case PRE_STUDENTS_ANNOUNCED_DEADLINE:
				$timeline_args['dashboardLegend'] = t("The list of students and projects will become visable to everyone after the following date. ") .
				$timeline->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
				$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
				$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
				$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
				$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
				break;
			case PRE_BONDING_PERIOD: // here is where student sees his accepted projects
				$timeline_args['dashboardLegend'] = t("The list of students and projects is now visible to other users of the system. " .
					"The Bonding period starts on the following date. ") .
				$timeline->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
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
		$timeline_args['organisationMembersVisible'] = FALSE;
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
}