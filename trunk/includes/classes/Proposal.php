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
    
    public function getProposalsBySearchCriteria($student='', $institute='', $organisation='', $sorting='title', $startIndex=1, $pageSize=10){
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
    	$query->orderBy($sorting, 'DESC')
    		->range($startIndex, $pageSize);
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
    		if (!Participants::isOfType('student', $uid)){
    			drupal_set_message(t('You must be a student to submit a proposal'), 'error');
    			return false; 
    		}
    		$project = Project::getInstance()->getProjectById($project_id);
    		
    		$student_details = Participants::getStudentDetails($uid);
    		$props['owner_id'] = $uid;
    		$props['oid'] = $project['oid'];
    		$props['instid'] = $student_details->inst_id ;
    		$props['supervisor_id'] = $student_details->supervisor_id ;  		
    		$props['pid'] =$project['pid'];
    		$props['state'] = 'draft' ;
    		$id = db_insert(self::tableName($type))->fields($props)->execute();
    		if ($id){
    			//Make current user creating this organisation, member
    			//TODO: notify mentor???
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
