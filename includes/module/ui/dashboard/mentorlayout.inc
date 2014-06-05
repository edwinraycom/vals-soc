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
        
          <div class="column-entry connections_dashboard">
            <h4>
              <a class="dashboard-link"
                 href="#connections_dashboard"
                 title="Connections">Connections</a>
              
            </h4>
            <p>Connect with organizations, check current status and participate in the program.</p>
            
              <div>
                
                  
                    <a href="/gsoc/connection/list/user/google/gsoc2014" title="See your connections">See your connections</a>
                  
                  |
                
                  
                    <a href="/gsoc/connection/pick/google/gsoc2014" title="Connect with organizations">Connect with organizations</a>
                  
                  
                
              </div>
            
          </div>
        
          <div class="column-entry documents">
            <h4>
              <a class="dashboard-link component-link"
                 href="#documents"
                 title="Important documents">Important documents</a>
              
            </h4>
            <p>List of important documents</p>
            
          </div>
        
    </div>
    
    <div class="column">
        
          <div class="column-entry todo">
            <h4>
              <a class="dashboard-link component-link"
                 href="#todo"
                 title="My todos">My todos</a>
              
            </h4>
            <p>List of my todos</p>
            
          </div>
        
          <div class="column-entry adminning_organizations">
            <h4>
              <a class="dashboard-link component-link"
                 href="#adminning_organizations"
                 title="My organizations">My organizations</a>
              
            </h4>
            <p>List of organizations which I participate in</p>
            
          </div>
        
    </div>
    

    
  </div>
</div>
';
}

initDashboardLayout();
