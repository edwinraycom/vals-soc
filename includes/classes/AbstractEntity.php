<?php
/**
 * Common abstract base class for all entities requiring the same functionality
 * 
 * @author paul
 *
 */
abstract class AbstractEntity {

	static function keyField($type){
		switch ($type){
			case 'studentgroup': return 'studentgroup_id';break;
			case 'institute': return 'inst_id';break;
			case 'organisation': return 'org_id';break;
			case 'project': return 'pid';break;
			case 'proposal': return 'proposal_id';break;
			default: return '';
		}
	}
	static function participationGroup($type){
		switch ($type){
			case 'organisation_admin':
			case 'mentor': $group = 'organisation';break;
			case 'institute_admin':
			case 'supervisor':
			case 'student': $group = 'institute';break;
// 			case 'organisation_admin':
// 			case 'mentor': $group = 'organisation';break;
			default: $group = '';
		}
		return $group;
	}
	
	static function filterPostLite($fieldz){
		$input = array();
		foreach ($fieldz as $prop){
			if (isset($_POST[$prop])){
				$input[$prop] = $_POST[$prop];
			}
		}
		return $input;
	}

}