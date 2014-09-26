<?php
class Project extends AbstractEntity{
	
	private static $instance;
	public static $fields = array('pid', 'owner_id', 'title', 'description', 'url', 'state', 
		'org_id', 'mentor_id', 'proposal_id', 'selected', 'tags');
	
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
    		$query->leftjoin('soc_names', 'mentor', 'p.mentor_id = %alias.names_uid');
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
    	$this->addProjectCondition($projectCount, '');
    	$projectCount->fields('soc_projects');
    	return $projectCount->execute()->rowCount();
    }
    
    public static function addProjectCondition(&$query, $project_alias='p.'){
    	//we want to deliver all the non-draft projects except for the owner (or colleagues) of these projects
    	$myorgs = Organisations::getMyOrganisations();
    	if (gettype($query) == 'string') {
    		$query .= " AND (${project_alias}state <> 'draft'".
    			($myorgs ? " OR ${project_alias}org_id IN (".implode($myorgs, ',')."))" : ")");
    	} else {
    		if ($myorgs){
	    		$query->condition(
					db_or()
						->condition("${project_alias}state", 'draft', '<>')
						->condition("${project_alias}org_id", $myorgs, 'IN'));
    		} else {
    			$query->condition('state', 'draft', '<>');
    		}
    	}
    }
    
    public function getProjectsBySearchCriteria($tags, $organisation, $sorting, $startIndex, $pageSize){
    	$queryString = "SELECT p.pid, p.title, p.description, p.tags, o.name"
    			." FROM soc_projects p
    			   LEFT JOIN soc_organisations o on p.org_id = o.org_id"
    			." WHERE 1=1 ";
    	if(isset($tags)){
    		$queryString .=	 " AND tags LIKE '%".$tags."%'";
    	}
    	if(isset($organisation) && $organisation !="0"){
    		$queryString .=	 " AND p.org_id = ".$organisation;
    	}
    	$this->addProjectCondition($queryString);
    	$queryString .= 	 " ORDER BY " . $sorting
    	." LIMIT " . $startIndex . "," . $pageSize . ";";	//die($queryString);
    	$result = db_query($queryString);
    
    	$rows = array();
    	foreach ($result as $record) {
    		$rows[] = $record;
    	}
    	return $rows;
    }

    public function  getStudentsAndProposalCountByCriteriaRowCount($group=''){
       	if(!$group){
    		$group = array();
    		$result = Groups::getGroups(_STUDENT_GROUP, $GLOBALS['user']->uid);
    		foreach ($result as $record) {
    			array_push($group, $record->studentgroup_id);
    		}
    	}
    	$role = 4;
    	$query ="
    		SELECT u.uid,u.name as username, sg.name as groupname,COUNT(v.proposal_id) AS proposal_count
			FROM users AS u
			LEFT JOIN soc_proposals AS v ON ( u.uid = v.owner_id )
			LEFT JOIN users_roles as r ON (u.uid = r.uid)
			LEFT JOIN soc_user_membership as m ON (u.uid = m.uid)
			LEFT JOIN soc_studentgroups AS sg ON (sg.studentgroup_id = m.group_id)".
			($group ? "WHERE sg.studentgroup_id IN (:grps) AND " : "WHERE ").
			"sg.owner_id = ".$GLOBALS['user']->uid." AND r.rid = $role AND m.type = 'studentgroup'
			GROUP BY username";
    	$projects =  db_query($query,array(':grps' => $group))->rowCount();
    	return $projects;
    }
    
    public static function getStudentsAndProposalCountByCriteria($group, $sorting='p.pid',
    	$startIndex=1, $pageSize=10){
    	if(!$group){
    		$group = array();
    		$result = Groups::getGroups(_STUDENT_GROUP, $GLOBALS['user']->uid);
    		foreach ($result as $record) {
    			array_push($group, $record->studentgroup_id);
    		}
    	}
    	$role = 4; 
    	$query ="
    		SELECT u.uid,u.name as username, sg.name as groupname,COUNT(v.proposal_id) AS proposal_count
			FROM users AS u
			LEFT JOIN soc_proposals AS v ON ( u.uid = v.owner_id )
			LEFT JOIN users_roles as r ON (u.uid = r.uid)
			LEFT JOIN soc_user_membership as m ON (u.uid = m.uid)
			LEFT JOIN soc_studentgroups AS sg ON (sg.studentgroup_id = m.group_id)".
			($group ? "WHERE sg.studentgroup_id IN (:grps) AND " : "WHERE ").
			"sg.owner_id = ".$GLOBALS['user']->uid." AND r.rid = $role AND m.type = 'studentgroup'
			GROUP BY username";

    	if (!$sorting){
    		$sorting = 'groupname ASC, username ASC';
    	}
    	$query .= 	 " ORDER BY " . $sorting
    	." LIMIT " . $startIndex . "," . $pageSize . ";";
    	//echo $query;
    	$students = db_query($query, array(':grps' => $group))->fetchAll();
    	return $students;
    }
    
    public function  getProjectsAndProposalCountByCriteriaRowCount($organisation='', $owner_id=''){
    	if(!$organisation){
    		$organisation = array();
    		$result = Organisations::getInstance()->getMyOrganisations(TRUE);
    		foreach ($result as $record) {
    			array_push($organisation, $record->org_id);
    		}
    	}
    	$query = "SELECT p.* from soc_projects as p WHERE p.org_id IN (:orgs) ";
    	if($owner_id){
    		$query .= "AND p.owner_id = " . $owner_id;
    	}
    	$projects =  db_query($query, array(':orgs' => $organisation))->rowCount();
    	return $projects;
    }
    
    public static function getProjectsAndProposalCountByCriteria($organisation, $owner_id='', $sorting='p.pid',
    	$startIndex=1, $pageSize=10){

    	if(!$organisation){
    		$organisation = array();
    		$result = Organisations::getInstance()->getMyOrganisations(TRUE);
    		foreach ($result as $record) {
    			array_push($organisation, $record->org_id);
    		}
    	}
    	$query =" 
    		SELECT p.pid, p.title, o.name AS org_name, COUNT(v.proposal_id) AS proposal_count
			FROM soc_projects AS p
			LEFT JOIN soc_proposals AS v ON ( v.pid = p.pid )
			LEFT JOIN soc_organisations AS o ON ( p.org_id = o.org_id ) 
			WHERE p.org_id IN (:orgs) ";
    	
    	if($owner_id){
    		$query .= "AND p.owner_id = " . $owner_id . " ";
    	}
    	
		$query .= 	 "GROUP BY p.pid ";
    	
    	if (!$sorting){
    		$sorting = 'pid ASC';
    	}
    	$query .= 	 " ORDER BY " . $sorting
    	." LIMIT " . $startIndex . "," . $pageSize . ";";
    	$projects = db_query($query, array(':orgs' => $organisation))->fetchAll();
    	return $projects;
    }
    
    public static function getProjects($project_id='', $owner_id='', $organisations=''){
    	if ($project_id){
    		$p = self::getProjectById($project_id, FALSE, NULL);
    		$projects = $p ? array($p) : array();
    	} elseif ($organisations) {
    		$table = tableName('project');
    		//
    		if (!$owner_id){
    			$projects = db_query("SELECT p.* from $table as p WHERE p.org_id IN (:orgs) ",
    				array(':orgs' => $organisations))->fetchAll();
    		} else {
    			$projects = db_query("SELECT p.* from $table as p WHERE p.org_id IN (:orgs) AND p.owner_id IN (:uid)",
    					array(':orgs' => $organisations, ':uid' => $owner_id))->fetchAll();
    		}
    		//
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
    	if ($user_type == _ORGADMIN_TYPE) {
    		if ($my_role != _ORGADMIN_TYPE){
    			drupal_set_message(t('You are not allowed to perform this action'), 'error');
    			return array();
    		} else {
    			$my_orgs = $organisations ?: 
      				db_query(
      					"SELECT o.org_id from $table as o ".
    					"LEFT JOIN soc_User_membership as um on o.org_id = um.group_id ".
    					"WHERE um.uid = $user_id AND um.type = :organisation",
      						array(':organisation' =>_ORGANISATION_GROUP))->fetchCol();
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
    		if (($my_role != _ORGADMIN_TYPE) && ($user_id != $org_admin_or_mentor)){
    			drupal_set_message(t('You are not allowed to perform this action'), 'error');
    			return array();
    		}
    		$my_projects =
    			db_query("SELECT p.* from $table as p WHERE p.owner_id = $user_id")->fetchAll();
    	}
    	
    	return $my_projects;
	}
	
	public static function getProjectsByUser($user_id='', $organisations='', $show_all=_VALS_SOC_MENTOR_ACCESS_ALL){
		global $user;
		 
		$org_admin_or_mentor = $user->uid;
		$user_id = $user_id ?: $org_admin_or_mentor;
		$my_role = getRole();
		//todo: find out whether current user is institute_admin
		 
		$table = tableName('project');
		if (in_array($my_role, array(_ORGADMIN_TYPE, _MENTOR_TYPE))){
			$my_orgs = $organisations ?: db_query(
					"SELECT o.org_id from $table as o ".
					"LEFT JOIN soc_user_membership as um on o.org_id = um.group_id ".
					"WHERE um.uid = $user_id AND um.type = :organisation",
      					array(':organisation' =>_ORGANISATION_GROUP))->fetchCol();
			if (! $my_orgs){
				drupal_set_message(t('You have no organisation yet'), 'error');
				return array();
			}
			if ($show_all || _VALS_SOC_MENTOR_ACCESS_ALL || ($my_role == _ORGADMIN_TYPE)) {
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
	static function getInterestedSupervisors($project_id){
		return db_query("SELECT R.uid, U.name, N.name as full_name FROM ".
				tableName('supervisor_rate'). " R ".
				" LEFT JOIN soc_names N on R.uid = N.names_uid ".
				" LEFT JOIN users U on R.uid = U.uid ".
				" WHERE R.pid = $project_id"
				//." AND R.type = 'supervisor'"
			. " AND R.rate >= 0"
		)->fetchAll();
	}
	
	static function getRating($project_id, $user_id){
		$rating = db_query("SELECT R.rate FROM ".
				tableName('supervisor_rate'). " R ".
				" WHERE R.pid = $project_id AND R.uid = $user_id "
				//." AND ( R.type = 'supervisor' OR R.type = 'institute_admin') "
				//. " AND R.rate >= 0"
		)->fetchObject();
		return $rating ? $rating->rate : -2;
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
			//TODO: for now we assume the mentor is the same as creating the project. As long as we have not built
			//funcitonality to connect mentors to projects, this is a valid assumption
			$props['mentor_id'] = $uid;
			if (!isset($props['state']) ){
				$props['state'] = 'pending';
			}
			//We normalise urls: if they start with http or https we assume the user inserted a full url
			//otherwise we assume a non-https full url
			if (isset($props['url']) && $props['url'] && (stripos($props['url'], 'http') === FALSE)){
				$props['url'] = 'http://'.$props['url'];
			}
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
		if (isset($props['url']) && $props['url'] && (stripos($props['url'], 'http') === FALSE)){
			$props['url'] = 'http://'.$props['url'];
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
