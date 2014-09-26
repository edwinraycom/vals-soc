<?php
class Proposal extends AbstractEntity{
	
	private static $instance; 	 	 	 	 	 	 	 	 	
	public static $fields = array('proposal_id', 'owner_id', 'org_id', 'inst_id', 'supervisor_id', 'pid', 'title', 'solution_short', 'solution_long', 'state',);
	
	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}
    
    public function getProposals(){
    	$proposals = db_select('soc_proposals')->fields('soc_proposals')->execute()->fetchAll(PDO::FETCH_ASSOC);
    	return $proposals;
    }
    
    public function getProposalById($id, $details= false){
    	$query = db_select('soc_proposals', 'p')->fields('p', self::$fields)->condition('p.proposal_id', $id);
    	if ($details){
    		$query->leftjoin('users', 'u1', 'p.owner_id = %alias.uid');
    		$query->leftjoin('users', 'supervisor_user', 'p.supervisor_id = %alias.uid');
    		
    		$query->leftjoin('soc_names', 'student', 'p.owner_id = %alias.names_uid');
    		$query->leftjoin('soc_names', 'supervisor', 'p.supervisor_id = %alias.names_uid');
    		$query->leftjoin('soc_institutes', 'i', 'p.inst_id = %alias.inst_id');
    		$query->leftjoin('soc_organisations', 'o', 'p.org_id = %alias.org_id');
    		$query->leftjoin('soc_projects', 'pr', 'p.pid = %alias.pid');
    		$query->leftjoin('users', 'mentor_user', 'pr.mentor_id = %alias.uid');
    		$query->leftjoin('soc_names', 'mentor', 'pr.mentor_id = %alias.names_uid');
    		$query->fields('u1', array('mail', 'name'));
    		$query->fields('supervisor_user', array('mail', 'name'));
    		$query->fields('student', array('name'));
    		$query->fields('supervisor', array('name'));
    		$query->fields('i', Institutes::$fields);
    		$query->fields('o', Organisations::$fields);
    		$query->fields('pr', array('title', 'description', 'url', 'owner_id', 'proposal_id', 'selected'));
    		$query->fields('mentor_user', array('mail', 'name'));
    		$query->fields('mentor', array('name'));
    	}
    	//echo $query;
    	$proposal = $query->execute()->fetch(PDO::FETCH_OBJ);
    	return $proposal;
    }
    
    public static function getProposalsPerProject($project_id, $student_id=0, $details=false){
    	$query = db_select('soc_proposals', 'p')->fields('p', self::$fields);
    	$query->condition('p.pid', $project_id);
    	if ($student_id){
    		$query->condition('p.owner_id', $student_id);
    	}
    	$query->leftjoin('soc_names', 'student', 'p.owner_id = %alias.names_uid');
    	$query->leftjoin('soc_institutes', 'i', 'p.inst_id = %alias.inst_id');
    	$query->leftjoin('soc_organisations', 'o', 'p.org_id = %alias.org_id');
    	$query->leftjoin('soc_projects', 'pr', 'p.pid = %alias.pid');
    	if($details){// details gets the supervisor and student names & email addresses also
    		$query->leftjoin('users', 'mentor_user', 'pr.mentor_id = %alias.uid');
    		$query->leftjoin('soc_names', 'mentor', 'pr.mentor_id = %alias.names_uid');
    		$query->leftjoin('users', 'u1', 'p.owner_id = %alias.uid');
    		$query->leftjoin('users', 'supervisor_user', 'p.supervisor_id = %alias.uid');
    		$query->fields('u1', array('mail', 'name'));
    		$query->fields('supervisor_user', array('mail', 'name'));
    		$query->fields('mentor_user', array('mail', 'name'));
    		$query->fields('mentor', array('name'));
    	}
    	$query->fields('student', array('name'));
    	$query->fields('i', array('name'));
    	$query->fields('o', array('name'));
    	$query->fields('pr', array('title'));
    	
    	$query->orderBy('pid', 'ASC');
    
    	return $query->execute()->fetchAll();
    }
    
    public static function getDefaultName($proposal_id , $proposal=''){
    	if (! $proposal) {
    		$proposal = self::getProposalById($proposal_id);
    	}
    	$pid = $proposal->pid;
    	$title = $proposal->pr_title;
    	return tt('Proposal for: %1$s', $title);
    }
    
    public function getProposalsRowCountBySearchCriteria($student='', $institute='', $organisation='', $project=''){
    	$query = db_select('soc_proposals', 'p')->fields('p');
    	if($student){
    		$query->condition('owner_id', $student);
    	}
    	if($institute){
    		$query->condition('inst_id', $institute);
    	}
    	if($organisation){
    		$query->condition('org_id', $organisation);
    	}
    	if($project){
    		$query->condition('pid', $project);
    	}
    	return $query->execute()->rowCount();
    }
    
    public function getMyProposals(){
    	// this was never returning the first record if startindex was 1.
    	return self::getProposalsBySearchCriteria($GLOBALS['user']->uid, '', '', '', '', 0, 1000);
    }
    
    public function getProposalsBySearchCriteria($student='', $institute='', $organisation='', $project='', $sorting='pid',
    	$startIndex=1, $pageSize=10)
    {
    	
    	$query = db_select('soc_proposals', 'p')->fields('p', array(
    			'proposal_id', 'owner_id', 'org_id', 'inst_id', 'supervisor_id', 'pid', 'title', 'state'));
    	if($student){
    		$query->condition('p.owner_id', $student);
    	}
    	if($institute){
    		$query->condition('p.inst_id', $institute);
    	}
    	if($organisation){
    		$query->condition('p.org_id', $organisation);
    	}
    	if($project){
    		$query->condition('p.pid', $project);
    	}
    	$query->leftjoin('soc_names', 'student', 'p.owner_id = %alias.names_uid');
    	$query->leftjoin('soc_institutes', 'i', 'p.inst_id = %alias.inst_id');
    	$query->leftjoin('soc_organisations', 'o', 'p.org_id = %alias.org_id');
    	$query->leftjoin('soc_projects', 'pr', 'p.pid = %alias.pid');
    	$query->leftjoin('users', 'u', 'p.owner_id = %alias.uid');
    	
    	$query->fields('student', array('name'));
    	$query->fields('i', array('name'));
    	$query->fields('o', array('name'));
    	$query->fields('pr', array('title', 'proposal_id', 'selected'));
    	$query->fields('u', array('name'));
    	//We expect the jtable lib to give a sorting of the form field [ASC, DESC]
    	if ($sorting){
    		$parts = explode(' ', $sorting);
    		$sorting = $parts[0];
    		$direction = (isset($parts[1])? $parts[1]: 'DESC');
    		$query->orderBy($sorting, $direction);
    	}
    	$query->range($startIndex, $pageSize);
    	return $query->execute()->fetchAll(); 
    }
    
    static function insertProposal($props, $project_id){
    	if (! $props){
    		drupal_set_message(t('Insert requested with empty (filtered) data set'), 'error');
    		return false;
    	}
    
    	global $user;
    
    	$txn = db_transaction();
    	try {
    		$uid = $user->uid;
    		if (!Users::isOfType(_STUDENT_TYPE, $uid)){
    			drupal_set_message(t('You must be a student to submit a proposal'), 'error');
    			return false; 
    		}
    		$project = Project::getProjectById($project_id);
    		
    		$student_details = Users::getStudentDetails($uid);
    		$props['owner_id'] = $uid;
    		$props['org_id'] = $project['org_id'];
    		$props['inst_id'] = $student_details->inst_id ;
    		$props['supervisor_id'] = $student_details->supervisor_id ;  		
    		$props['pid'] = $project['pid'];
    		if (!isset($props['state'])){
    			$props['state'] = 'draft' ;
    		}
    		try{
    			// inserts where the field length is exceeded fails silently here
    			// i.e. the date strinf is too long for the mysql field type
    			$id = db_insert(tableName('proposal'))->fields($props)->execute();
    		}catch(Exception $e) {
    			drupal_set_message($e->getMessage());
			}
    		if ($id){
    			//TODO: notify mentor???
    			drupal_set_message('You have only saved your proposal. You can continue editing it later.');
    			return $id;
    		} else {
    			drupal_set_message(tt('We could not add your %1$s.', $type), 'error');
    		}
    
    		return $result;
    
    	} catch (Exception $ex) {
    		$txn->rollback();
    		drupal_set_message(t('We could not add your proposal.'). (_DEBUG? $ex->__toString(): ''), 'error');
    	}
    	return FALSE;
    }
    
    static function updateProposal($props, $proposal_id){
    	if (! $props){
    		drupal_set_message(t('Update requested with empty (filtered) data set'), 'error');
    		return false;
    	}
    
    	global $user;
    
    	$txn = db_transaction();
    	try {
    		$uid = Users::getMyId();
    		if (!Users::isOfType(_STUDENT_TYPE, $uid) && !Users::isAdmin()){
    			drupal_set_message(t('You must be a student to submit a proposal'), 'error');
    			return FALSE;
    		}
    		//$project = Project::getProjectById($project_id);
    
//    		$student_details = Users::getStudentDetails($uid);
//     		$props['owner_id'] = $uid;
//     		$props['org_id'] = $project['org_id'];
//     		$props['inst_id'] = $student_details->inst_id ;
//     		$props['supervisor_id'] = $student_details->supervisor_id ;
    		//$props['pid'] = $project['pid'];
    		//$props['state'] = 'draft' ;
    		$id = db_update(tableName('proposal'))->fields($props)
    			->condition(self::keyField('proposal'), $proposal_id)->execute();
//     		if ($id){
//     			//TODO: notify mentor???
//     			drupal_set_message('You have saved your proposal. Later you can edit it.');
//     			return TRUE;
//     		} else {
//     			drupal_set_message(tt('We could not add your %1$s.', $type), 'error');
//     		}
    
    		return TRUE;
    
    	} catch (Exception $ex) {
    		$txn->rollback();
    		drupal_set_message(t('We could not update your proposal.'). (_DEBUG? $ex->__toString(): ''), 'error');
    	}
    	return FALSE;
    }
    
    static function filterPost(){
    	
    	//TODO: get the db fields from schema 
    	$fields = array('title', 'solution_short', 'solution_long');
    	
    	$input = array();
    	foreach ($fields as $prop){
    		if (isset($_POST[$prop])){
    			$input[$prop] = $_POST[$prop];
    		}
    	}
    	return $input;
    }
}
