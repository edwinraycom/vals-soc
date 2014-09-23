<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/css/dashboard/css/dashboard.css');
function initInstDashboardLayout(){
	return getInstAdminLayout(Timeline::getInstadminTimelineVars());
}	

function getInstAdminLayout($timeline_args){
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
	if($timeline_args['managedOrganisationsVisible']){
		$output .='
					<div class="column-entry org_app">
						<h4>
							<a class="dashboard-link component-link"
								href="'.$base_url.'/dashboard/institute/administer"
								title="'.t("My institution").'">'.t("My institution").'</a>
						</h4>
						<p>'.t("Institution for which you are an administrator").'</p>
					</div>
		';
	}
	if($timeline_args['organisationMembersVisible']){
		$output .='
		<div class="column-entry participants">
		<h4>
		<a class="dashboard-link component-link"
		href="'.$base_url.'/dashboard/institute/members"
		title="'.t('Members of my institution').'">'.t('Members of my institution').'</a>
		</h4>
		<p>'.t("Lists of your institution members").'</p>
		</div>
		';
	}
	
	if($timeline_args['connectionsVisible']){
		$output .='
					<div class="column-entry connections_dashboard">
						<h4>
							<a class="dashboard-link"
							href="'.$base_url.'/dashboard/institute/group/administer"
							title="'.t("Groups").'">'.t("Groups").'</a>
						</h4>
						<p>'.t("Create groups of students to manage codes and the initial supervisor-student relation.").'</p>
						<div>
							<a href="#" title="'.t("Create Groups").'">'.t("Create and see your list of groups").'</a> | 
							<!-- <a href="#" title="'.t("Connect with organisations").'">'.t("Connect with organisations").'</a>-->
						</div>
					</div>
		';
	}
	
	$output .='	
				</div>
				<!-- column two -->
				<div class="column">
	';

	if($timeline_args['groupsVisible']){
		$output .='
		<div class="column-entry participants">
		<h4>
		<a class="dashboard-link"
		href="'.$base_url.'/dashboard/institute/group/administer"
		title="'.t("Groups").'">'.t("Groups").'</a>
		</h4>
		<p>'.t("Create groups of students to manage codes and the initial supervisor-student relation.").'</p>
		<div>
		<!--<a href="#" title="'.t("Create Groups").'">'.t("Create and see your list of groups").'</a> |
		<a href="#" title="'.t("Manage student groups").'">'.t("Manage student groups").'</a>-->
		</div>
		</div>
		';
	}
	
	if($timeline_args['manageProjectIdeasVisible']){
		$output .='
		<div class="column-entry org_app manage_project_ideas">
		<h4>
		<a class="dashboard-link component-link"
		href="'.$base_url.'/dashboard/projects/browse"
		title="'.t("Review project ideas").'">'.t("Review project ideas").'</a>
		</h4>
		<p>'.t("Allows you to add comments to project ideas and to filter accidental entries.").'</p>
		</div>
		';
	}
	
	if($timeline_args['proposalsVisible']){
		$output .='
					<div class="column-entry proposals_submitted">
						<h4>
							<a class="dashboard-link component-link"
								href="#"
								title="'.t("Proposals submitted within my institute").'">'.t("Proposals submitted by students from my institute").'</a>
						</h4>
						<p>'.t("List of proposals submitted within my institute").'</p>
					</div>
		';
	}	

	if($timeline_args['matchedProjectsVisible']){
		$output .='
					<div class="column-entry proposals_submitted">
						<h4>
							<a class="dashboard-link component-link"
								href="'.$base_url.'/dashboard/projects/administer?state=active"
								title="'.t("Proposals accepted for our institute").'">'.t("Proposals accepted for our institute").'</a>
						</h4>
						<p>'.t("List of accepted proposals that are active or will be soon").'</p>
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

initInstDashboardLayout();