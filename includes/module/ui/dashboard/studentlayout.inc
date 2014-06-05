<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/module/ui/dashboard/css/dashboard.css');

function initDashboardLayout(){
	echo '
		<div class="dashboard" id="main-dashboard">
  <div class="dashboard-head">
    <span>
      
      Main dashboard
    </span>
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
        
          <div class="column-entry proposals">
            <h4>
              <a class="dashboard-link component-link"
                 href="#proposals"
                 title="Proposals">Proposals</a>
              
            </h4>
            <p>List of my submitted proposals</p>
            
          </div>
        
          <div class="column-entry participants">
            <h4>
              <a class="dashboard-link component-link"
                 href="/vals/dashboard/organisations/browse"
                 title="Proposals">Browse Organisations</a>
              
            </h4>
            <p>Browse the current list of Organisations</p>
            
          </div>
			
          <div class="column-entry participants">
            <h4>
              <a class="dashboard-link component-link"
                 href="/vals/dashboard/projects/browse"
                 title="Proposals">Browse Projects</a>
              
            </h4>
            <p>Browse the current list Projects</p>
            
          </div>
			
    </div>
    
    <div class="column">
        
          <div class="column-entry shipment_tracking">
            <h4>
              <a class="dashboard-link component-link"
                 href="#shipment_tracking"
                 title="Shipment tracking">Shipment tracking</a>
              
            </h4>
            <p>List of shipments</p>
            
          </div>
        
    </div>
    

    
  </div>
</div>
			';
}

initDashboardLayout();

