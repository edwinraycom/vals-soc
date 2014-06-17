<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/module/ui/dashboard/css/dashboard.css');

function initDashboardLayout(){
	//echo '<h1>'.Timeline::getInstance()->getNow()->format('F j, Y, g:i a').'</h1>';
	$timeline_args = array();
	$timeline_args['viewOrganisations'] = FALSE;
	$timeline_args['viewProjectIdeas'] = FALSE;
	$timeline_args['connectionsVisible'] = FALSE;
	$timeline_args['myOrganisationsVisible'] = FALSE; // a list of organisations i participate in
	$timeline_args['myProposalsVisible'] = FALSE; // proposals I have submitted
	$timeline_args['myAcceptedProjectsVisible'] = FALSE;
	$timeline_args['dashboardLegend'] = "";
	
	$period = Timeline::getInstance()->getCurrentPeriod();
	switch ($period) {
		case PROGRAM_NOT_YET_STARTED:
			$timeline_args['dashboardLegend'] =
			t("Program has not started yet.  Menu options will be available after the student signup date. ") .
					Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a');
			break;
		case PRE_ORG_SIGNUP_PERIOD:
		case ORG_SIGNUP_PERIOD:
		case PRE_ORGS_ANNOUNCED_PERIOD:
			$timeline_args['dashboardLegend'] =
			t("Program has now started.  Menu options will be available once the student signup period begins. ") .
					Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a');
			break;
		case POST_ORGS_ANNOUNCED_PERIOD: // orgs announced so lets student look at the project ideas - cant apply yet
			$timeline_args['dashboardLegend'] = t("You can now browse the organisations and project ideas. ".
					"You can start to apply for projects once the student sign period begins. ").
					Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a');
			$timeline_args['viewOrganisations'] = TRUE; // look at the organisations
			$timeline_args['viewProjectIdeas'] = TRUE; // look at the project ideas
			$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
			break;
		case STUDENT_SIGNUP_PERIOD: // Students can now apply for projects
			$timeline_args['dashboardLegend'] = t("Student signup period. You can now submit project proposals." .  
					"Please complete any project proposals before the following date. ").
					Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a');;
			$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
			$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
			$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
			$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
			break;
		case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:
			$timeline_args['dashboardLegend'] = t("Project proposals due to be evaluated by. ") .
					Timeline::getInstance()->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
			$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
			$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
			$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
			$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
			break;
		case PRE_PROPOSAL_MATCHED_DEADLINE:
			$timeline_args['dashboardLegend'] = t("Students projects matched to mentors on the following date. ") .
					Timeline::getInstance()->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');
			$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
			$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
			$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
			$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
			break;
		case PRE_STUDENTS_ANNOUNCED_DEADLINE:
			$timeline_args['dashboardLegend'] = t("The list of students and projects will become visable to everyone after the following date. ") .
					Timeline::getInstance()->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
			$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
			$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
			$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
			$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
			break;
		case PRE_BONDING_PERIOD: // here is where student sees his accepted projects
			$timeline_args['dashboardLegend'] = t("The list of students and projects is now visible to other users of the system. " .
					"The Bonding period starts on the following date. ") .
					Timeline::getInstance()->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
			$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
			$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
			$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
			$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
			$timeline_args['myAcceptedProjectsVisible'] =  TRUE; // projects I am accepted onto
			$timeline_args['myOrganisationsVisible'] = TRUE; // Organisations I am a member of
			break;
		case PRE_CODING_PERIOD:
			$timeline_args['dashboardLegend'] = t("Community bonding period.  Coding starts on the following date. ") .
				Timeline::getInstance()->getCodingStartDate()->format('F j, Y, g:i a');
			$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
			$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
			$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
			$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
			$timeline_args['myAcceptedProjectsVisible'] =  TRUE; // projects I am accepted onto
			$timeline_args['myOrganisationsVisible'] = TRUE; // Organisations I am a member of
			break;
		case PRE_SUGGESTED_CODING_END_DATE:
			$timeline_args['dashboardLegend'] = t("Coding period. The following is the suggested end date for coding. ") .
				Timeline::getInstance()->getSuggestedCodingDeadline()->format('F j, Y, g:i a');
			$timeline_args['viewOrganisations'] = TRUE; // look at ALL of the organisations
			$timeline_args['viewProjectIdeas'] = TRUE; // look at ALL of the project ideas
			$timeline_args['connectionsVisible'] = TRUE; // allow students to communicate with other users - perhaps ask questions of mentors etc
			$timeline_args['myProposalsVisible'] = TRUE;// see just my proposals - not yet accepted
			$timeline_args['myAcceptedProjectsVisible'] =  TRUE; // projects I am accepted onto
			$timeline_args['myOrganisationsVisible'] = TRUE; // Organisations I am a member of
			break;
		case PRE_CODING_DEADLINE:
			$timeline_args['dashboardLegend'] = t("Coding period. The following is the deadline date for coding. ") .
				Timeline::getInstance()->getCodingEndDate()->format('F j, Y, g:i a');
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
	return getStudentLayout($timeline_args);
}

function getStudentLayout($timeline_args){
	global $base_url;
	$output ='
		<div class="dashboard" id="main-dashboard">
			<div class="dashboard-head">
				<span>'.t("Main dashboard").'</span>
			</div>
	
			<div class="block block-dashboard">
				<span id="dashboardLegend">'.$timeline_args['dashboardLegend'].'</span>
	
				<!-- column one -->
				<div class="column first">
	';
	if($timeline_args['connectionsVisible']){
		$output .='
					<div class="column-entry connections_dashboard">
						<h4>
							<a class="dashboard-link"
							href="#"
							title="'.t("Connections").'">'.t("Connections").'</a>
						</h4>
						<p>'.t("Connect with organisations, check current status and participate in the program.").'</p>
						<div>
							<a href="#" title="'.t("See your connections").'">'.t("See your connections").'</a> |
							<a href="#" title="'.t("Connect with organisations").'">'.t("Connect with organisations").'</a>
						</div>
					</div>
		';
	}

	if($timeline_args['viewOrganisations']){
		$output .='
					<div class="column-entry participants">
						<h4>
							<a class="dashboard-link component-link"
							href="'.$base_url.'/dashboard/organisations/browse"
							title="'.t("Browse Orgs").'">'.t("Browse organisations").'</a>
						</h4>
						<p>'.t("Browse the current list of Organisations").'</p>
					</div>
		';
	}

	if($timeline_args['viewProjectIdeas']){
		$output .='
					<div class="column-entry participants">
						<h4>
							<a class="dashboard-link component-link"
							href="'.$base_url.'/dashboard/projects/browse"
							title="'.t("Browse projects").'">'.t("Browse projects").'</a>
						</h4>
						<p>'.t("Browse the current list of project ideas").'</p>
					</div>
		';
	}

	
	$output .='
				</div>
				<!-- column two -->
				<div class="column">
	';
	
	if($timeline_args['myOrganisationsVisible']){
		$output .='
					<div class="column-entry adminning_organizations">
						<h4>
							<a class="dashboard-link component-link"
							href="#"
							title="'.t("My organisations").'">'.t("My organisations").'</a>
						</h4>
						<p>'.t("List of organisations which I participate in").'</p>
					</div>
		';
	}
	
	if($timeline_args['myProposalsVisible']){
		$output .='
					<div class="column-entry proposals_submitted">
						<h4>
							<a class="dashboard-link component-link"
							href="'.$base_url.'/dashboard/proposals/mine"
							title="'.t("Proposals").'">'.t("Proposals").'</a>
						</h4>
						<p>'.t("List of my proposals submitted").'</p>
					</div>
		';
	}
	
	if($timeline_args['myAcceptedProjectsVisible']){
		$output .='
					<div class="column-entry proposals">
						<h4>
							<a class="dashboard-link component-link"
							href="'.$base_url.'/dashboard/projects/mine"
							title="'.t("My project").'">'.t("My project").'</a>
						</h4>
						<p>'.t("Accepted project I am working on").'</p>
					</div>
		';
	}
	
	$output .='
				</div>
	
			</div>
	
		</div>
	';
	echo $output;	
}

initDashboardLayout();
