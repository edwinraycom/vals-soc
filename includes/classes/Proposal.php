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
    		$query->condition('propid', $student);
    		$queryString .=	 " AND studid  = $student";
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
    		$query->condition('propid', $student);
    		$queryString .=	 " AND studid  = $student";
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
}
