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
			case 'group': return 'group_id';break;
			case 'institute': return 'inst_id';break;
			case 'organisation': return 'org_id';break;
			case 'project': return 'pid';break;
			case 'proposal': return 'propid';break;
			default: return '';
		}
	}
	
}