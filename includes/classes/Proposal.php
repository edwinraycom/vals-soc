<?php
class Proposal {
	
	private static $instance;
	
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
    
    public function getProposalById($id){
    	$proposal = db_select('soc_proposals')->fields('soc_proposals')->condition('propid', $id)->execute()->fetchAll(PDO::FETCH_ASSOC);
    	return $proposal;
    }
    
    public function getProposalsRowCountBySearchCriteria($student='', $institute='', $organisation=''){
    	$query = db_select('soc_proposals', 'p')->fields('p');
    	if($student){
    		$query->condition('owner_id', $student);
    	}
    	if($institute){
    		$query->condition('instid', $institute);
    	}
    	if($organisation){
    		$query->condition('oid', $organisation);
    	}
    	return $query->execute()->rowCount();
    }
    
    public function getProposalsBySearchCriteria($student='', $institute='', $organisation='', $sorting='pid',
    	$startIndex=1, $pageSize=10)
    {
    	
    	$query = db_select('soc_proposals', 'p')->fields('p', array(
    			'propid','owner_id','oid','instid','supervisor_id','pid', 'name'));
    	if($student){
    		$query->condition('owner_id', $student);
    	}
    	if($institute){
    		$query->condition('instid', $institute);
    	}
    	if($organisation){
    		$query->condition('oid', $organisation);
    	}
    	$query->join('soc_institutes', 'i', 'p.instid = %alias.inst_id');
    	$query->join('soc_organisations', 'o', 'p.oid = %alias.org_id');
    	$query->join('soc_projects', 'pr', 'p.pid = %alias.pid');
    	$query->fields('i', array('name'));
    	$query->fields('o', array('name'));
    	$query->fields('pr', array('title'));
    	//We expect the jtable lib to give a sorting of the form field [ASC, DESC]
    	if ($sorting){
    		$parts = explode(' ', $sorting);
    		$sorting = $parts[0];
    		$direction = (isset($parts[1])? $parts[1]: 'DESC');
    		$query->orderBy($sorting, $direction);
    	}
    	$query->range($startIndex, $pageSize);
    	/*
SELECT p.propid AS propid, p.owner_id AS owner_id, p.oid AS oid, p.instid AS instid, p.supervisor_id AS supervisor_id, p.pid AS pid, p.name AS name, i.name AS i_name, o.name AS o_name, pr.title AS title
FROM 
{soc_proposals} p
INNER JOIN {soc_institutes} i ON p.instid = i.inst_id
INNER JOIN {soc_organisations} o ON p.oid = o.org_id
INNER JOIN {soc_projects} pr ON p.pid = pr.pid
ORDER BY pid ASC
LIMIT 10 OFFSET 0
    	 */
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
    		if (!Users::isOfType('student', $uid)){
    			drupal_set_message(t('You must be a student to submit a proposal'), 'error');
    			return false; 
    		}
    		$project = Project::getInstance()->getProjectById($project_id);
    		
    		$student_details = Users::getStudentDetails($uid);
    		$props['owner_id'] = $uid;
    		$props['oid'] = $project['oid'];
    		$props['instid'] = $student_details->inst_id ;
    		$props['supervisor_id'] = $student_details->supervisor_id ;  		
    		$props['pid'] =$project['pid'];
    		$props['state'] = 'draft' ;
    		$id = db_insert(tableName('proposal'))->fields($props)->execute();
    		if ($id){
    			//TODO: notify mentor???
    			drupal_set_message('You have saved your proposal. Later you can edit it.');
    			return TRUE;
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
    
    static function filterPost(){
    	
    	//TODO: get the db fields from schema 
    	$fields = array('name', 'cv', 'solution_short', 'solution_long', 'modules');
    	
    	$input = array();
    	foreach ($fields as $prop){
    		if (isset($_POST[$prop])){
    			$input[$prop] = $_POST[$prop];
    		}
    	}
    	return $input;
    }
}
