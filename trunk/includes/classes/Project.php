<?php
/* 
*
 */
class Project {
	
	private static $instance;
	
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
    	$project = db_select('soc_projects')->fields('soc_projects')->condition('pid', $id)->execute()->fetchAll(PDO::FETCH_ASSOC);
    	return $project;
    }
    
    public function getProjectsRowCountBySearchCriteria($tags, $organisation){
    	$projectCount = db_select('soc_projects');
    	if(isset($tags)){
    		$projectCount->condition('tags', '%'.$tags.'%', 'LIKE');
    	}
    	if(isset($organisation) && $organisation !="0"){
    		$projectCount->condition('oid', $organisation);
    	}
    	$projectCount->fields('soc_projects');
    	return $projectCount->execute()->rowCount();
    }
    
    public function getProjectsBySearchCriteria($tags, $organisation, $sorting, $startIndex, $pageSize){
    	$queryString = "SELECT p.pid, p.title, o.name, p.description, p.tags"
    			." FROM soc_projects p, soc_organisations o"
    			." WHERE p.oid = o.org_id";
    	if(isset($tags)){
    		$queryString .=	 " AND tags LIKE '%".$tags."%'";
    	}
    	if(isset($organisation) && $organisation !="0"){
    		$queryString .=	 " AND p.oid = ".$organisation;
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
