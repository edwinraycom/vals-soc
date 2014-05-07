<?php
module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
function vals_soc_projects(){
	return array();
	$result = db_select('soc_projects');
}
//Rol uitvinden van de user die is ingelogd
$role = getRole();
echo ' I am a '.$role;
echo 'gaat ie het doen';
//module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.schema');
//drupal_install_schema('soc_groups');
//echo "misshien wel";
				$result = vals_soc_projects();
				// Array to contain items for the block to render.
				$items = array();
				// Iterate over the resultset and format as links.
				if ($result){
					module_load_include('php', 'vals_soc', 'includes/classes/Projects');
					$project_obj = new Project();
					foreach ($result as $project) {
						$items[] = array(
							'data' => $project_obj->show_project($project),
						);
					}
				}
				echo '<h2>'.t('Current available projects').'</h2>';
				// No content in the last week.
				if (empty($items)) {
					echo t('No projects available yet.');
				}
				else {
					// Pass data through theme function.
					echo theme('item_list', array('items' => $items));
				}

echo "nu gaan we de current stage bepalen. Het is: ".getCurrentStage();

function getCurrentStage(){
    /*
     * We have the following stages:
     * REGISTER: period to register the users [Program start date, Program end date]
     * PROJECTS to enter the projects 
     * PROJECT_EVALUATION: tutors can do a preselection of the projects and comment on them
     * PROPOSALS: write and submit proposals
     * SELECTIONS: selection process of the best proposals
     * EXECUTION: period to do the actual work
     * SUBMIT:  this can be a short period: the end date is the date for students to have terminated the work and sumit
     *          and the begin of this period, is the time to send out alerts
     * ASSIGNMENT_EVALUATIONS
     */
	
    $today_str = date('y-m-d');
   // $program_start_date = get the var program start date
    
    return 'register, build functionality to derive stage from vars';
}
