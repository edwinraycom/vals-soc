<?php
class Organisations {

	private static $instance;

	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}

	/**
	 * function used to just get the organisation Id and name
	 * Used in some drop down menus of the UI.
	 */
	public function getGroupsLite(){
		return db_query("SELECT o.org_id, o.name FROM soc_organisations o;");
	}
	
    public function getGroupss(){
    	$projects = db_select('soc_organisations')->fields('soc_organisations')->execute()->fetchAll(PDO::FETCH_ASSOC);
    	return $projects;
    }
    
    public function getGroupById($id){
    	$project = db_select('soc_organisations')->fields('soc_organisations')->condition('org_id', $id)->execute()->fetchAll(PDO::FETCH_ASSOC);
    	return $project;
    }
    
    public function getGroupsRowCountBySearchCriteria($name){
    	$projectCount = db_select('soc_organisations');
    	if(isset($name)){
    		$projectCount->condition('name', '%'.$name.'%', 'LIKE');
    	}
    	$projectCount->fields('soc_organisations');
    	return $projectCount->execute()->rowCount();
    }
    
    public function getGroupsBySearchCriteria($name, $sorting, $startIndex, $pageSize){
    	$queryString = "SELECT o.org_id  as org_id, o.name as oname"
    			." FROM soc_organisations o";
    	 
    	if(isset($name)){
    		$queryString .=	 " WHERE name LIKE '%".$name."%'";
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