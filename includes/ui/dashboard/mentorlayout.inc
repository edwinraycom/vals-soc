<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/css/dashboard/css/dashboard.css', array(
      'type' => 'file',
      'group' => CSS_THEME
    ));

//Will be called at the end of this file
function initDashboardLayout(){
	return getMentorLayout(Timeline::getInstance()->getMentorTimelineVars());
}

function getMentorLayout($timeline_args){
	global $base_url;
	$output ='
		<div class="dashboard" id="main-dashboard">
			<div class="dashboard-head">
				<span>'.t("Main dashboard").'</span>
			</div>
	
			<div class="block block-dashboard">
				<p id="dashboardLegend">'.$timeline_args['dashboardLegend'].'</p>
	
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
							href="'.$base_url.'/dashboard/organisation/view"
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
								href="'.$base_url.'/dashboard/projects/administer"
								title="'.t("Manage your project ideas").'">'.t("Manage your project ideas").'</a>
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
							href="'.$base_url.'/dashboard/organisation/members"
							title="'.t("Members of my organisations").'">'.t("Members of my organisations").'</a>
						</h4>
						<p>'.t("List of your organisations members").'</p>
					</div>
		';
	}
	if($timeline_args['browseProjectIdeasVisible']){
		$output .='
					<div class="column-entry projects">
						<h4>
							<a class="dashboard-link component-link"
								href="'.$base_url.'/dashboard/projects/browse"
								title="'.t("All project ideas (read only)").'">'.t("All project ideas (read only)").'</a>
						</h4>
						<p>'.t("All project ideas from all organisations").'</p>
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