<?php
class Project extends AbstractEntity{
	
	private static $instance;
	public static $fields = array('pid', 'owner_id', 'title', 'description', 'url', 'state', 
		'org_id', 'mentor', 'proposal_id', 'selected', 'tags');
	
	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	public function getKeylessFields(){
		// we dont want to return the key fields here
		return array_slice(Project::$fields, 2);
	}
	
	public function getAllFields(){
		return Project::$fields;
	}
	//Todo: never used. Keep it?
    public function getAllProjects($fetch_style=PDO::FETCH_ASSOC){
    	$projects = db_select('soc_projects')->fields('soc_projects')->execute()->fetchAll($fetch_style);
    	return $projects;
    }
    
    public static function getProjectById($id, $details= false, $fetch_style=PDO::FETCH_ASSOC){
    	$query = db_select('soc_projects', 'p')->fields('p', self::$fields)->condition('pid', $id);
    	if ($details){
    		$query->leftjoin('soc_names', 'owner', 'p.owner_id = %alias.names_uid');
    		$query->leftjoin('soc_names', 'mentor', 'p.mentor = %alias.names_uid');
    		$query->leftjoin('soc_organisations', 'o', 'p.org_id = %alias.org_id');

    		$query->fields('owner', array('name'));
    		$query->fields('mentor', array('name'));
    		$query->fields('o', array('name'));
    	}
    	//project is one asociative array
    	$project = $query->execute()->fetch($fetch_style);
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

    public static function getProjects($project_id='', $owner_id='', $organisations=''){
    	if ($project_id){
    		$p = self::getProjectById($project_id, FALSE, NULL);
    		$projects = $p ? array($p) : array();
    	} elseif ($organisations) {
    		$table = tableName('project');
    		$projects = db_query("SELECT p.* from $table as p WHERE p.org_id IN (:orgs) ",
    			array(':orgs' => $organisations))->fetchAll();
    	} elseif ($owner_id){
    		//$projects = self::getProjectsByUser_orig($role, $owner_id);
    		$projects = self::getProjectsByUser($owner_id);
    	} else {
    		$projects = self::getAllProjects(NULL);
    	}
    	return $projects;
    }
       
    //TODO Rewrite this function a bit: multiple returns, unclear why the user_type should be passed
    public static function getProjectsByUser_orig($user_type, $user_id='', $organisations=''){
    	global $user;
   
    	$org_admin_or_mentor = $user->uid;
    	$user_id = $user_id ?: $org_admin_or_mentor;
    	$my_role = getRole();
    	//todo: find out whether current user is institute_admin
     
    	$table = tableName('project');
    	if ($user_type == 'organisation_admin') {
    		if ($my_role != 'organisation_admin'){
    			drupal_set_message(t('You are not allowed to perform this action'), 'error');
    			return array();
    		} else {
    			$my_orgs = $organisations ?: db_query("SELECT o.org_id from $table as o ".
    					"LEFT JOIN soc_User_membership as um on o.org_id = um.group_id ".
    					" WHERE um.uid = $user_id AND um.type = 'organisation'")->fetchCol();
    			if ($my_orgs){
	    			$my_projects = 
	    				db_query("SELECT p.* from $table as p WHERE p.org_id IN (:orgs) ",
	    					array(':orgs' => $my_orgs))->fetchAll();	    			
    			} else {
    				drupal_set_message(t('You have no organisation yet'), 'error');
    				return array();
    			}
    		}
    	} else {
    		if (($my_role != 'organisation_admin') && ($user_id != $org_admin_or_mentor)){
    			drupal_set_message(t('You are not allowed to perform this action'), 'error');
    			return array();
    		}
    		$my_projects =
    			db_query("SELECT p.* from $table as p WHERE p.owner_id = $user_id")->fetchAll();
    	}
    	
    	return $my_projects;
	}
	
	public static function getProjectsByUser($user_id='', $organisations=''){
		global $user;
		 
		$org_admin_or_mentor = $user->uid;
		$user_id = $user_id ?: $org_admin_or_mentor;
		$my_role = getRole();
		//todo: find out whether current user is institute_admin
		 
		$table = tableName('project');
		if (in_array($my_role, array('organisation_admin', 'mentor'))){
			$my_orgs = $organisations ?: db_query("SELECT o.org_id from $table as o ".
					"LEFT JOIN soc_User_membership as um on o.org_id = um.group_id ".
					" WHERE um.uid = $user_id AND um.type = 'organisation'")->fetchCol();
			if (! $my_orgs){
				drupal_set_message(t('You have no organisation yet'), 'error');
				return array();
			}
			if ($my_role == 'organisation_admin') {
				$my_projects =
					db_query("SELECT p.* from $table as p WHERE p.org_id IN (:orgs) ",array(':orgs' => $my_orgs))
					->fetchAll();
			} else {
				$my_projects =
					db_query("SELECT p.* from $table as p WHERE p.org_id IN (:orgs) AND p.owner_id = $user_id",array(':orgs' => $my_orgs))
					->fetchAll();
			}
		} else {
			drupal_set_message(t('You are not allowed to perform this action'), 'error');
			return array();
		}
		 
		return $my_projects;
	}
	
	static function addProject($props){
		if (! $props){
			drupal_set_message(t('Insert requested with empty (filtered) data set'), 'error');
			return false;
		}
		// sort and process the datetime array structure
		// pre sql statement.
		Project::normaliseFormArrays($props);
		
		global $user;
		$txn = db_transaction();
		try {
			$uid = $user->uid;
			$props['owner_id'] = $uid;
			$result = FALSE;
			$query = db_insert(tableName('project'))->fields($props);
			$id = $query->execute();
			if ($id){
				$result = $id;
			}
			else {
				drupal_set_message(t('We could not add your project'), 'error');
			}
		}
		catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not add your project. '). (_DEBUG? $ex->__toString(): ''), 'error');
		}
		return $result;
	}
	
	static function changeProject($props, $id){
		if (!$props){
			drupal_set_message(t('Update requested with empty data set'));
			return false;
		}
		$key = self::keyField('project');
		//Project::normaliseFormArrays($props);
		$query = db_update(tableName('project'))
			->condition($key, $id)
			->fields($props);
		$res = $query->execute();
		// the returned value from db_update is how many rows were updated rather than a boolean
		// - however if the user submits the form without changing anything no rows are actually updated and
		// zero is returned, which is not an error per se. so as a hack set this back to '1'
		// until we find a better way of handling this
		if($res==0){
			$res=1;
		}
		return $res;
	}
	
	static function normaliseFormArrays(&$props){
		$processedProps = array();
		foreach ($props as $key => $value) {
			if (is_array($value)) {
				$value = implode(" ",$value);
			}
			// dont use empty values
			if($value !=  " "){
				$processedProps[$key]=$value;
			}
		}
		$props = $processedProps;
	}
	
}
