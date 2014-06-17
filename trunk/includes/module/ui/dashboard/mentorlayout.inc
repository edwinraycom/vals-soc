<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/module/ui/dashboard/css/dashboard.css');

function initDashboardLayout(){
	$timeline_args = array();
	$timeline_args['myOrganisationsVisible'] = FALSE; // a list of organisations i participate in
	$timeline_args['manageProjectIdeasVisible'] = FALSE; // manage the project ideas?
	$timeline_args['connectionsVisible'] = FALSE; // communication tools with other users of the system
	$timeline_args['organisationMembersVisible'] = FALSE;// a list of users per org I am attached to
	$timeline_args['proposalsVisible'] = FALSE; // submitted proposals
	$timeline_args['projectsIamMentorForVisible'] = FALSE; // matched actual projects
	$timeline_args['dashboardLegend'] = "";
	$period = Timeline::getInstance()->getCurrentPeriod();
	switch ($period) {
		case PROGRAM_NOT_YET_STARTED:
			//mentor starts to do things after orgs are announced
			$timeline_args['dashboardLegend'] =
				t("Program has not started yet.  Menu options will be available from the following date. ") . 
					Timeline::getInstance()->getOrgsAnnouncedDate()->format('F j, Y, g:i a');
			break;
		case PRE_ORG_SIGNUP_PERIOD:
		case ORG_SIGNUP_PERIOD:
		case PRE_ORGS_ANNOUNCED_PERIOD:
			$timeline_args['dashboardLegend'] =
				t("Program has started.  Menu options will be available from the following date. ") .
					Timeline::getInstance()->getOrgsAnnouncedDate()->format('F j, Y, g:i a');;
			break;
		case POST_ORGS_ANNOUNCED_PERIOD:
			$timeline_args['dashboardLegend'] = t("Here you can view your organisations and see the project ideas.");
			$timeline_args['myOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE; // should we also allow mentors to modify project ideas?
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			break;
		case STUDENT_SIGNUP_PERIOD:
			$timeline_args['dashboardLegend'] = t("Student signup period. Students can now submit project proposals");
			$timeline_args['myOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			break;
		case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:
			$timeline_args['dashboardLegend'] = t("Please review your project applications before the following date. ") .
					Timeline::getInstance()->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
			$timeline_args['myOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			break;
		case PRE_PROPOSAL_MATCHED_DEADLINE:
			$timeline_args['dashboardLegend'] = t("Please ensure you have matched all students projects to mentors before the following date. ") .
					Timeline::getInstance()->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');
			$timeline_args['myOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			break;
		case PRE_STUDENTS_ANNOUNCED_DEADLINE:
			$timeline_args['dashboardLegend'] = t("The list of students and projects will become visable to everyone after the following date. ") .
					Timeline::getInstance()->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
			$timeline_args['myOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			break;
		case PRE_BONDING_PERIOD:
			$timeline_args['dashboardLegend'] = t("The list of students and projects is now visible to other users of the system. " .
					"The Bonding period starts on the following date. ") .
					Timeline::getInstance()->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
			$timeline_args['myOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			$timeline_args['projectsIamMentorForVisible'] = TRUE;
			break;
		case PRE_CODING_PERIOD:
			$timeline_args['dashboardLegend'] = t("Community bonding period.  Coding starts on the following date. ") .
					Timeline::getInstance()->getCodingStartDate()->format('F j, Y, g:i a');
			$timeline_args['myOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			$timeline_args['projectsIamMentorForVisible'] = TRUE;
			break;
		case PRE_SUGGESTED_CODING_END_DATE:
			$timeline_args['dashboardLegend'] = t("Coding period. The following is the suggested end date for coding. ") .
					Timeline::getInstance()->getSuggestedCodingDeadline()->format('F j, Y, g:i a');
			$timeline_args['myOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
			$timeline_args['projectsIamMentorForVisible'] = TRUE;
			break;
		case PRE_CODING_DEADLINE:
			$timeline_args['dashboardLegend'] = t("Coding period. The following is the deadline date for coding. ") .
					Timeline::getInstance()->getCodingEndDate()->format('F j, Y, g:i a');
			$timeline_args['myOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
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
	return getMentorLayout($timeline_args);
}

function getMentorLayout($timeline_args){
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
	if($timeline_args['manageProjectIdeasVisible']){
		$output .='
					<div class="column-entry org_app manage_project_ideas">
						<h4>
							<a class="dashboard-link component-link"
								href="#"
								title="'.t("Manage project ideas").'">'.t("Manage project ideas").'</a>
						</h4>
						<p>'.t("Allows you to add and amend project ideas for students.").'</p>
					</div>
		';
	}
	
	$output .='
				</div>
				<!-- column two -->
				<div class="column">
	';
	
	if($timeline_args['organisationMembersVisible']){
		$output .='
					<div class="column-entry participants">
						<h4>
							<a class="dashboard-link component-link"
							href="#"
							title="'.t("Members of my organisations").'">'.t("Members of my organisations").'</a>
						</h4>
						<p>'.t("List of your organisations members").'</p>
					</div>
		';
	}
	
	if($timeline_args['proposalsVisible']){
		$output .='
					<div class="column-entry proposals_submitted">
						<h4>
							<a class="dashboard-link component-link"
								href="#"
								title="'.t("Proposals submitted to my organisations").'">'.t("Proposals submitted to my organisations").'</a>
						</h4>
						<p>'.t("List of proposals submitted to my organisations").'</p>
					</div>
		';
	}
	
	if($timeline_args['projectsIamMentorForVisible']){
		$output .='
					<div class="column-entry proposals_submitted">
						<h4>
							<a class="dashboard-link component-link"
								href="#"
								title="'.t("Projects I am mentor for").'">'.t("Projects I am mentor for").'</a>
						</h4>
						<p>'.t("Projects I am mentor for").'</p>
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