<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/module/ui/dashboard/css/dashboard.css');
function initDashboardLayout(){
	//echo '<h1>'.Timeline::getInstance()->getNow()->format('F j, Y, g:i a').'</h1>';
	$timeline_args = array();
	$timeline_args['managedOrganisationsVisible'] = FALSE;
	$timeline_args['manageProjectIdeasVisible'] = FALSE;
	$timeline_args['connectionsVisible'] = FALSE;
	$timeline_args['organisationMembersVisible'] = FALSE;
	$timeline_args['proposalsVisible'] = FALSE;
	$timeline_args['matchedProjectsVisible'] = FALSE;
	$timeline_args['dashboardLegend'] = "";
	$period = Timeline::getInstance()->getCurrentPeriod();
	switch ($period) {
		case PROGRAM_NOT_YET_STARTED:
			$timeline_args['dashboardLegend'] = 
				t("Program has not started yet.  Menu options will be available from the following date. ") .
					 Timeline::getInstance()->getProgramStartDate()->format('F j, Y, g:i a');
			break;
		case PRE_ORG_SIGNUP_PERIOD:
			$timeline_args['dashboardLegend'] = 
				t("Program is active, however you must wait until the following date to register your organization/s. ") . 
					Timeline::getInstance()->getOrgsSignupStartDate()->format('F j, Y, g:i a');
			break;
		case ORG_SIGNUP_PERIOD:
			$timeline_args['dashboardLegend'] = 
				t("Enter your organisation details and project ideas. You have until the following date when you can no longer add or delete entries. ") . 
					Timeline::getInstance()->getOrgsSignupEndDate()->format('F j, Y, g:i a');
			$timeline_args['managedOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			break;
		case PRE_ORGS_ANNOUNCED_PERIOD:
			$timeline_args['dashboardLegend'] = 
				t("Modify your organisation details and project ideas. You have until the following date when your organisations and project ".
					"ideas become visible to students. ") . 
					Timeline::getInstance()->getOrgsAnnouncedDate()->format('F j, Y, g:i a');
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
				Timeline::getInstance()->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
			$timeline_args['managedOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			break;
		case PRE_PROPOSAL_MATCHED_DEADLINE:
			$timeline_args['dashboardLegend'] = t("Please ensure you have matched all students projects to mentors before the following date. ") . 
				Timeline::getInstance()->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');
			$timeline_args['managedOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			break;
		case PRE_STUDENTS_ANNOUNCED_DEADLINE:
			$timeline_args['dashboardLegend'] = t("The list of students and projects will become visable to everyone after the following date. ") . 
				Timeline::getInstance()->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
			$timeline_args['managedOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			break;
		case PRE_BONDING_PERIOD:
			$timeline_args['dashboardLegend'] = t("The list of students and projects is now visible to other users of the system. " .
				"The Bonding period starts on the following date. ") . 
				Timeline::getInstance()->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
			$timeline_args['managedOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
			break;
		case PRE_CODING_PERIOD:
			$timeline_args['dashboardLegend'] = t("Community bonding period.  Coding starts on the following date. ") . 
				Timeline::getInstance()->getCodingStartDate()->format('F j, Y, g:i a');
			$timeline_args['managedOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
			break;
		case PRE_SUGGESTED_CODING_END_DATE:
			$timeline_args['dashboardLegend'] = t("Coding period. The following is the suggested end date for coding. ") .  
				Timeline::getInstance()->getSuggestedCodingDeadline()->format('F j, Y, g:i a');
			$timeline_args['managedOrganisationsVisible'] = TRUE;
			$timeline_args['manageProjectIdeasVisible'] = TRUE;
			$timeline_args['organisationMembersVisible'] = TRUE;
			$timeline_args['connectionsVisible'] = TRUE;
			$timeline_args['proposalsVisible'] = TRUE;//menu options - Proposals submitted to my Organisations
			$timeline_args['matchedProjectsVisible'] = TRUE;//- MENU OPTION - PROJECTS FOR myOrgs - these are the matched proper projects
			break;
		case PRE_CODING_DEADLINE:
			$timeline_args['dashboardLegend'] = t("Coding period. The following is the deadline date for coding. ") . 
				Timeline::getInstance()->getCodingEndDate()->format('F j, Y, g:i a');
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
	return getOrgAdminLayout($timeline_args);
}	

function getOrgAdminLayout($timeline_args){
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
	
	if($timeline_args['managedOrganisationsVisible']){
		$output .='
					<div class="column-entry org_app">
						<h4>
							<a class="dashboard-link component-link"
								href="'.$base_url.'/dashboard/members"
								title="'.t("Managed organisations").'">'.t("Managed organisations").'</a>
						</h4>
						<p>'.t("Organizations for which you are an administrator").'</p>
					</div>
		';
	}
	if($timeline_args['manageProjectIdeasVisible']){
		$output .='
					<div class="column-entry org_app manage_project_ideas">
						<h4>
							<a class="dashboard-link component-link"
								href="'.$base_url.'/dashboard/projects/administer"
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

	if($timeline_args['matchedProjectsVisible']){
		$output .='
					<div class="column-entry proposals_submitted">
						<h4>
							<a class="dashboard-link component-link"
								href="'.$base_url.'/dashboard/projects/administer?state=active"
								title="'.t("Projects for my organisations").'">'.t("Projects for my organisations").'</a>
						</h4>
						<p>'.t("List of projects being developed for my organisations").'</p>
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