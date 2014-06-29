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
			case 'timeline': return 'timeline_id';break;
			default: return '';
		}
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