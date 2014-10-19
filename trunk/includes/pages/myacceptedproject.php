<?php
function initMyProjectLayout(){
	global $base_url;
	
	$my_agreement = Agreement::getInstance()->getSingleStudentsAgreement();
	$output = '
	 	<script type="text/javascript">
		window.view_settings = {};
		window.view_settings.apply_projects = 0;
		window.view_settings.rate_projects  = 0;
	</script>
	';
	 
	$output .= '<div id="baktoprops"><a href=" '.$base_url.'/dashboard">'.t('Back to dashboard').'</a></div>';
	
	$output .= '
		<div class="dashboard" id="main-dashboard">
			<div class="dashboard-head">
				<span>'.t("My project").'</span>
			</div>
	
			<div class="block block-dashboard">
				<p id="dashboardLegend">'.t('Here are the resources for your accepted project').'</p>
	
				<!-- column one -->
				<div class="column first">
	';

		$output .='
					<div class="column-entry org_app">
						<h4>
							<a class="dashboard-link component-link"
								href="'.$base_url.'/dashboard/projects/mine/agreement"
								title="'.t("My project agreement").'">'.t("My project agreement").'</a>
						</h4>
						<p>'.t("Optionally create an agreement between you, your supervisor and your mentor").'</p>
					</div>
		';


	$output .='
				</div>
				<!-- column two -->
				<div class="column">
	';
	

	$output .='
			<div class="column-entry proposals_submitted">
						<h4>
							<a class="dashboard-link component-link" '.
							'href="javascript:void(0);" ' .
							'onclick="getProposalDetail('.$my_agreement->proposal_id.')"'.
									
							' title="'.t("My project details").'">'.t("My project details").'</a>
						</h4>
						<p>'.t("Shows you the original project idea along with the proposal you originally submitted").'</p>
			</div>
		';

	
	$output .='
				</div>
	
			</div>
	
		</div>
	';
	echo $output;	
}
