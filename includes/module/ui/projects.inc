<?php
module_load_include('php', 'vals_soc', 'includes/classes/Project');
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/vals_soc.css');

function initProjectLayout(){
	echo '<div id="" class="">';
	$allProjects = Project::getInstance()->getProjects();
	
	echo "<div id='project_container'>";
	foreach($allProjects as $project){
		echo "<div id='project_item'>";
		echo "<div id='project_header'>";
		echo "${project['title']}";
		echo "</div>";
		echo "<div id='project_desc'>${project['description']}</div>";
		echo "</div>";
	}
	
	echo "</div>";
	echo "</div>";

}
initProjectLayout();