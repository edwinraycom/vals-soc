<?php
class Organisations extends Groups{

	private static $instance;
	public static $type = 'organisation';	
	public static $fields = array('org_id', 'owner_id', 'name', 'contact_name', 'contact_email', 'url', 'description');
	
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
	public function getOrganisationsLite(){
		return db_query("SELECT o.org_id, o.name FROM soc_organisations o;");
	}
	
    public function getOrganisations(){
    	return db_select('soc_organisations')->fields('soc_organisations')->execute()->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getOrganisationById($id){
    	return db_select('soc_organisations')->fields('soc_organisations')->condition('org_id', $id)->execute()->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getOrganisationsRowCountBySearchCriteria($name){
    	$count_query = db_select('soc_organisations');
    	if(isset($name)){
    		$count_query->condition('name', '%'.$name.'%', 'LIKE');
    	}
    	$count_query->fields('soc_organisations');
    	return $count_query->execute()->rowCount();
    }
    
    public function getOrganisationsBySearchCriteria($name, $sorting, $startIndex, $pageSize){
    	$queryString = "SELECT o.org_id, o.name, o.url"
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