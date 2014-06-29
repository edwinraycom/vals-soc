<?php
class ProgramTimeline extends AbstractEntity {

	private static $instance;
	public static $keyfields = array('timeline_id' ,'owner_id');
	public static $keylessFields = array(
		'program_active'
		,'program_start_date'
		,'org_app_start_date'
		,'org_app_end_date'
		,'accepted_org_announced_date'
		,'student_signup_start_date'
		,'student_signup_end_date'
		,'org_review_student_applications_date'
		,'students_matched_to_mentors_deadline_date'
		,'accepted_students_announced_deadline_date'
		,'coding_start_date'
		,'suggested_coding_deaadline_date'
		,'coding_end_date'
		,'program_end_date');

	public function getFields(){
		return ProgramTimeline::$keylessFields;
	}
	
	public function getAllFields(){
		return ProgramTimeline::$keyfields + ProgramTimeline::$keylessFields;
	}
	
	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}

	public function getTimelines(){
		$timelines = db_select('soc_timelines')->fields('soc_timelines')->execute()->fetchAll(PDO::FETCH_ASSOC);
		return $timelines;
	}
	
	public static function getTimelineById($id){
		$timeline = db_query("SELECT o.* from soc_timelines as o WHERE o.owner_id = $id ");
		return $timeline;
	}
	
	public static function getTimeline($id){
		return self::getTimelineById($id)->fetchObject();
	}
	
	static function addTimeline($props){
		if (! $props){
			drupal_set_message(t('Insert requested with empty (filtered) data set'), 'error');
			return false;
		}
		// sort and process the datetime array structure
		// pre sql statement.
		ProgramTimeline::normaliseFormArrays($props);
		
		global $user;
		$txn = db_transaction();
		try {
			$uid = $user->uid;
			$props['owner_id'] = $uid;
			$result = FALSE;
			$query = db_insert(tableName('timeline'))->fields($props);
			$id = $query->execute();			
			if ($id){
				$result = $id;
			} 
			else {
				drupal_set_message(t('We could not add your timeline'), 'error');
			}
	
		} 
		catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not add your timeline. '). (_DEBUG? $ex->__toString(): ''), 'error');
		}
		return $result;
	}
	
	static function changeTimeline($props, $id){
		if (!$props){
			drupal_set_message(t('Update requested with empty data set'));
			return false;
		}
		$key = self::keyField('timeline');
		ProgramTimeline::normaliseFormArrays($props);
		$query = db_update(tableName('timeline'))
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
	
	static function removeTimeline($type, $id){
		if (! self::isOwner($type, $id)){
			drupal_set_message(t('You are not authorised to perform this action'), 'error');
			return FALSE;
		}
		$num_deleted = db_delete(tableName($type))
		->condition(self::keyField($type), $id)
		->execute();
		return $num_deleted;
	}
	
	static function isOwner($type, $id){
		$key_field = self::keyField($type);
		$entity = db_query("SELECT * FROM ".tableName($type)." WHERE $key_field = $id")->fetchAssoc();
		//fetchAssoc returns next record (array) or false if there is none
		return $entity && ($entity['owner_id'] == $GLOBALS['user']->uid);
	}
	
	static function normaliseFormArrays(&$props){
		$processedProps = array();
		foreach ($props as $key => $value) {
			if (is_array($value)) {
				$value = implode(" ",$value);
			}
			// dont use empty dates for now.
			if($value !=  " "){
				$processedProps[$key]=$value;
			}
		}
		$props = $processedProps;
	}
}