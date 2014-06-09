<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/module/ui/dashboard/css/dashboard.css');
function initDashboardLayout(){
	echo'
<div class="dashboard" id="main-dashboard">
	<div class="dashboard-head">
		<span>Main dashboard</span>
	</div>

	<div class="block block-dashboard">

		<div class="column first">

			<div class="column-entry documents">
				<h4>
					<a class="dashboard-link component-link"
						href="#documents"
						title="Important documents">Important documents</a>
				</h4>
				<p>List of important documents</p>
			</div>

			<div class="column-entry org_app">
				<h4>
					<a class="dashboard-link component-link"
						href="/vals/dashboard/members"
						title="Managed organizations">Managed organizations</a>
				</h4>
				<p>Organizations for which you are an administrator.</p>
			</div>

			<div class="column-entry todo">
				<h4>
					<a class="dashboard-link component-link"
						href="#todo"
						title="My todos">My todos</a>
				</h4>
				<p>List of my todos</p>
			</div>

		</div>

		<div class="column">

			<div class="column-entry adminning_organizations">
				<h4>
					<a class="dashboard-link component-link"
						href="#adminning_organizations"
						title="My organizations">My organizations</a>
				</h4>
				<p>List of organizations which I participate in</p>
			</div>

			<div class="column-entry participants">
				<h4>
					<a class="dashboard-link component-link"
						href="#participants"
						title="Members of my organizations">Members of my organizations</a>
				</h4>
				<p>List of your organizations members</p>
			</div>

			<div class="column-entry proposals_submitted">
				<h4>
					<a class="dashboard-link component-link"
						href="#proposals_submitted"
						title="Proposals submitted to my organizations">Proposals submitted to my organizations</a>
				</h4>
				<p>List of proposals submitted to my organizations</p>
			</div>

		</div>

	</div>

</div>
	';
	
}

initDashboardLayout();
