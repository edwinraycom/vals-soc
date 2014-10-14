<?php
class Agreement extends AbstractEntity {
	
	public static $fields = array('agreement_id', 'student_id', 'supervisor_id', 
			'mentor_id', 'proposal_id', 'project_id', 'description', 
			'student_signed', 'supervisor_signed', 'mentor_signed');
	
	private static $instance;
	public static $type = _AGREEMENT_OBJ;
	
	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	/**
	 * The student inserts this initially once he/she accepts a project offer
	 * @param unknown $props
	 * @param unknown $proposal_id
	 * @return boolean|unknown
	 */
	static function insertAgreement($props){
		if (! $props){
			drupal_set_message(t('Insert requested with empty (filtered) data set'), 'error');
			return false;
		}
		if (!isset($props['proposal_id'])){
			drupal_set_message(t('Insert requested with no proposal set'), 'error');
			return false;
		}
	
		global $user;
	
		$txn = db_transaction();
		try {
			$proposal = objectToArray(Proposal::getInstance()->getProposalById($props['proposal_id']));
			$project = objectToArray(Project::getProjectById($proposal['pid']));
			if (!isset($props['student_id'])){
				$props['student_id'] = $user->uid;
			}
			if (!isset($props['supervisor_id'])){
				$props['supervisor_id'] = $proposal['supervisor_id'];
			}
			if (!isset($props['mentor_id'])){
				$props['mentor_id'] = $project['mentor_id'];
			}
			
			
			$props['project_id'] = $proposal['pid'];
			
			if (!isset($props['description'])){
				$props['description'] = '';
			}
			if (!isset($props['student_signed'])){
				$props['student_signed'] = 0;
			}
			if (!isset($props['supervisor_signed'])){
				$props['supervisor_signed'] = 0;
			}
			if (!isset($props['mentor_signed'])){
				$props['mentor_signed'] = 0;
			}
			/*
			if (! testInput($props, array('owner_id', 'org_id', 'inst_id', 'supervisor_id','pid', 'title'))){
				return FALSE;
			}
			*/
			try {
				$id = db_insert(tableName(_AGREEMENT_OBJ))->fields($props)->execute();
			} catch (Exception $e) {
				drupal_set_message($e->getMessage(), 'error');
			}
			if ($id){
				drupal_set_message(t('You have created your agreement: you can continue editing it later.'));
				return $id;
			} else {
				drupal_set_message(t('We could not add your agreement. ').
				(_DEBUG ? ('<br/>'.getDrupalMessages()): ""), 'error');
			}
	
			return $result;
	
		} catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not add your agreement.'). (_DEBUG? $ex->__toString(): ''), 'error');
		}
		return FALSE;
	}
	
	static function updateAgreement($props){
		if (! $props){
			drupal_set_message(t('Update requested with empty (filtered) data set'), 'error');
			return false;
		}
	
		$txn = db_transaction();
		try {
			$id = db_update(tableName(_AGREEMENT_OBJ))->fields($props)
			->condition(self::keyField(_AGREEMENT_OBJ), $props['agreement_id'])->execute();
			return TRUE;
	
		} catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not update your agreement.'). (_DEBUG? $ex->__toString(): ''), 'error');
		}
		return FALSE;
	}
}
	