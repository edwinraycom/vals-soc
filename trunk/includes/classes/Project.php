<?php
class Project {
	
	private static $instance;
	public static $fields = array('pid', 'owner_id', 'title', 'description', 'url', 'state', 
		'org_id', 'mentor', 'proposal_id', 'supervisor', 'selected', 'tags');
	
	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}
    
    public function getProjects(){
    	$projects = db_select('soc_projects')->fields('soc_projects')->execute()->fetchAll(PDO::FETCH_ASSOC);
    	return $projects;
    }
    
    public function getProjectById($id){
    	//todo: better to use fetch to get just one project instead of an array with one array in it
    	$project = db_select('soc_projects')->fields('soc_projects')->condition('pid', $id)->execute()->fetch(PDO::FETCH_ASSOC);
    	return $project;
    }
    
    public function getProjectsRowCountBySearchCriteria($tags, $organisation){
    	$projectCount = db_select('soc_projects');
    	if(isset($tags)){
    		$projectCount->condition('tags', '%'.$tags.'%', 'LIKE');
    	}
    	if(isset($organisation) && $organisation !="0"){
    		$projectCount->condition('org_id', $organisation);
    	}
    	$projectCount->fields('soc_projects');
    	return $projectCount->execute()->rowCount();
    }
    
    public function getProjectsBySearchCriteria($tags, $organisation, $sorting, $startIndex, $pageSize){
    	$queryString = "SELECT p.pid, p.title, o.name, p.description, p.tags"
    			." FROM soc_projects p, soc_organisations o"
    			." WHERE p.org_id = o.org_id";
    	if(isset($tags)){
    		$queryString .=	 " AND tags LIKE '%".$tags."%'";
    	}
    	if(isset($organisation) && $organisation !="0"){
    		$queryString .=	 " AND p.org_id = ".$organisation;
    	}
    	$queryString .= 	 " ORDER BY " . $sorting
    	." LIMIT " . $startIndex . "," . $pageSize . ";";
    	$result = db_query($queryString);
    	
    	$rows = array();
    	foreach ($result as $record) {
    		$rows[] = $record;
    	}
    	return $rows;
    }
}
