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
    	$project = db_select('soc_projects')->fields('soc_projects')->condition('pid', $id)->execute()->fetch(PDO::FETCH_ASSOC);
    	//project is one asociative array
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
    
    public static function getProjectsByUser($user_type, $user_id='', $organisations='')
    {
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
    			db_query("SELECT p.* from $table as p WHERE p.mentor = $user_id")->fetchAll();
    	}
    	
    	return $my_projects;
	}
}
