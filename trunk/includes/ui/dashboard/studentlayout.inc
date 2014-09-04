<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/css/dashboard/css/dashboard.css', array(
      'type' => 'file',
      'group' => CSS_THEME
    ));

function initDashboardLayout(){
	return getStudentLayout(Timeline::getStudentTimelineVars());
}

function getStudentLayout($timeline_args){
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
	
	
	if($timeline_args['myProposalsVisible']){
		$output .='
					<div class="column-entry proposals_submitted">
						<h4>
							<a class="dashboard-link component-link"'.
							//href="'.$base_url.'/dashboard/proposals/mine"
							'href="'.$base_url.'/dashboard/proposals/mine"
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