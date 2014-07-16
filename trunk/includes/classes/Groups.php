<?php
class Groups extends AbstractEntity{
	public static function getGroups($org_type, $group_head_id='', $id='')
	{
		global $user;
	
		$group_head = $user->uid;
		//todo: find out whether current user is institute_admin
	
		if ($group_head_id == 'all'){
			$members = db_query("SELECT o.* from soc_${org_type}s as o");
		} else {
			$key_column = self::keyField($org_type);
			$code_key_column = ($org_type == 'studentgroup') ? 'studentgroup_id' : 'entity_id';
			$member_type = ($org_type == 'studentgroup') ? 'studentgroup' :(($org_type == 'organisation') ? 'mentor': 'supervisor');
			if ($id){
					
				$members = db_query(
						"SELECT o.*, c.code from ".tableName($org_type)." as o ".
						"left join soc_codes as c ".
						" on o.$key_column = c.$code_key_column ".
						"WHERE o.$key_column = $id ");
			} else {
				$group_head_id = $group_head_id ?: $group_head;
		
				$members = db_query(
						"SELECT o.*, c.code from ".tableName($org_type)." as o ".
						"left join soc_user_membership as um on o.$key_column = um.group_id ".
						"left join soc_codes as c on o.$key_column = c.$code_key_column AND c.type = '$member_type'".
						"WHERE um.type = '$org_type' AND um.uid = $group_head_id ");
			}
		}	
		return $members;
	}
	
	public static function getGroup($org_type, $id='')
	{
		return self::getGroups($org_type, '', $id)->fetchObject();
	}
	
	static function changeGroup($type, $organisation, $id)
	{
		if (! $organisation){
			drupal_set_message(t('Update requested with empty data set'));
			return false;
		}
		$key = self::keyField($type);
	
		$res = db_update(tableName($type))
		->condition($key, $id)
		->fields($organisation)
		->execute();
		// the returned value from db_update is how many rows where updated rather than a boolean 
		// - however if the user submits the form without changing anything no rows are actually updated and
		// zero is returned, which is not an error per se. so as a hack set this back to '1'
		// until we find a better way of handling this
		if($res==0){
			$res=1;
		}
		return $res;
	}

	static function isOwner($type, $id){
		if (! in_array($type, array('studentgroup', 'institute', 'organisation', 'project', 'proposal'))){
			drupal_set_message(tt('You cannot be the owner of an entity called %1$s', $type));
			return FALSE;
		}
		$key_field = self::keyField($type);
		$entity = db_query("SELECT * FROM ".tableName($type)." WHERE $key_field = $id")->fetchAssoc();
		//fetchAssoc returns next record (array) or false if there is none
		return $entity && ($entity['owner_id'] == $GLOBALS['user']->uid);
	}
	
	static function isAssociate($type, $id){	
		$scope_table = array('institute'=>'institute','organisation'=>'organisation',
				'studentgroup'=>'institute', 'project' =>'organisation', 'proposal' => 'institute');
		if (! in_array($type, array_keys($scope_table))){
			drupal_set_message(tt('You cannot be the associate of an entity called %1$s', $type));
			return FALSE;
		}
		$key_field = self::keyField($type);
		$entity = db_query("SELECT * FROM ".tableName($type)." WHERE $key_field = $id")->fetchAssoc();
		//fetchAssoc returns next record (array) or false if there is none
		if (!$entity) {
			return false;
		}
	
		//Is the current user the owner of this object, fine
		if ($entity['owner_id'] == $GLOBALS['user']->uid) {
			return true;
		}
		
		//Check if the current user is member of the organisation in scope to edit for example
		//If not, return here that the user is not associated (like a supervisor with students in his institute
		if (!self::isMember($scope_table[$type], $entity[self::keyField($scope_table[$type])])){
			return false;
		}
		//We impose some extra role restrictions: students can only have extensive rights for their own proposals
		//institutes and organisations can only be edited by admins
		if ($type == 'institute'){
			return Users::isInstituteAdmin(); 
		} elseif ($type == 'organisation'){
			return Users::isOrganisationAdmin();
		} elseif($type == 'proposal'){
			return ! Users::isStudent();
		}
		return true;
	}
	
	static function isMember($type, $id){
		global $user;
		if (!$user) {
			echo "hij vindt geenuser";
			return false;
		}
		//Assuming there is always an owner inside the group
		return db_query("SELECT * FROM soc_user_membership WHERE type = '$type' AND group_id = $id AND uid = ".$user->uid)->rowCount() > 0;
	}	
	
	static function hasMembers($type, $id){
		//Assuming there is always an owner inside the group
		return db_query("SELECT * FROM soc_user_membership WHERE type = '$type' AND group_id = $id")->rowCount() > 1;
	}
	
	static function removeGroup($type, $id){
		if (! self::isOwner($type, $id)){
			drupal_set_message(t('You are not authorised to perform this action'), 'error');
			return FALSE;
		}
	
		if (self::hasMembers($type, $id)){
			drupal_set_message(tt('There are already members in this %1$s. You can still edit the %1$s though.',
					t($type)), 'error');
			return FALSE;
		}
		if (!isValidOrganisationType($type)){
				
		}
		$num_deleted = db_delete(tableName($type))
		->condition(self::keyField($type), $id)
		->execute();
		if ($num_deleted){
			$num_deleted2 = db_delete("soc_user_membership")
			->condition('group_id', $id)
			->condition('type', $type)
			->execute();
			if (!$num_deleted2){
				if($type != 'project'){
					drupal_set_message(tt('The group has been deleted, but it had no members.', $type), 'error');
				}
				return $num_deleted;
			}
				
			$subtype = ($type == 'organisation') ? 'mentor' : (($type == 'institute') ? 'supervisor' : 'studentgroup');
				
			$num_deleted3 = db_delete("soc_codes")
			->condition('entity_id', $id)
			->condition('type', $subtype)
			->execute();
				
			if (!$num_deleted3){
				drupal_set_message(tt('The %1$s has been deleted, but it had no code attached.', $type), 'error');
				return $num_deleted;
			}
		} else {
			drupal_set_message(tt('The group seems to have been deleted already, refresh your screen to see if this is true.', $type), 'error');
			return 0;
		}
	
		return $num_deleted2;
	}
	
/* 	static function addProject($props){
		if (! $props){
			drupal_set_message(t('Insert requested with empty (filtered) data set'), 'error');
			return false;
		}
	
		global $user;
		//mentor GRTDWCOCI
		$txn = db_transaction();
		try {
			$uid = $user->uid;
			$props['owner_id'] = $uid;
			$result = FALSE;
			$id = db_insert(tableName($type))->fields($props)->execute();
			if ($id){
				$result = $id;
			} else {
				drupal_set_message(tt('We could not add your %1$s.', $type), 'error');
			}
	
		} catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not add your project. '). (_DEBUG? $ex->__toString(): ''), 'error');
		}
		return $result;
	} */
	
	
	static function addGroup($props, $type){
		if (! $props){
			drupal_set_message(t('Insert requested with empty (filtered) data set'), 'error');
			return false;
		}
	
		global $user;
	//mentor GRTDWCOCI
		$txn = db_transaction();
		try {
			$uid = $user->uid;
			$props['owner_id'] = $uid;
			if ($type == 'organisation'){
				if (!isset($props['url'])) $props[ 'url'] = '';
				if (!isset($props['description'])) $props[ 'description'] = '';
				$subtype = 'mentor';
			} else if ($type == 'institute'){
				$subtype = 'supervisor';
			} else {
				drupal_set_message(tt('This type of group cannot be added: %1$s', $type), 'error');
				return false;
			}

			$id = db_insert(tableName($type))->fields($props)->execute();
			if ($id){
				//Make current user creating this organisation, member
				$result = db_insert('soc_user_membership')->fields( array(
						'uid'=>$uid,
						'type' => $type,
						'group_id'=>$id,
				))->execute();
				if ($result){
					$result = $result && db_insert('soc_codes')->fields( array(
							'type'=>$subtype,
							'code' => createRandomCode($subtype, $id),
							'entity_id'=> $id,
							'studentgroup_id' =>0))->execute();
					if (!$result){
						drupal_set_message(t('We could not add a code.'), 'error');
					}
				} else {
					drupal_set_message(tt('We could not add you to this %1$s.', $type), 'error');
				}
			} else {
				drupal_set_message(tt('We could not add your %1$s.', $type), 'error');
			}
	
			return $result;
	
		} catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not add your group. '). (_DEBUG? $ex->__toString(): ''), 'error');
		}
		return FALSE;
	}
	
	static function addStudentGroup($group){
		if (! $group){
			drupal_set_message(t('Insert requested with empty (filtered) data set'));
			return false;
		}
			
		global $user;
			
		$txn = db_transaction();
		try {
			$uid = $user->uid;
			$institute_ids = db_select('soc_user_membership')->fields('soc_user_membership', array('group_id'))
			->condition('uid', $uid)
			->condition('type', 'institute')
			->execute()->fetchCol();
			if ($institute_ids){
				$inst_id = $institute_ids[0];
			} else {
				$inst_id = 0;
			}
	
			$gid = db_insert('soc_studentgroups')->fields(array(
					'name'=>$group['name'],
					'owner_id' =>  $uid,
					'inst_id' => $inst_id,
					'description' => ($group['description'] ?: ''),
			))->execute();
			if ($gid){
				$result = db_insert('soc_user_membership')->fields( array(
						'uid'=>$uid,
						'type' => 'studentgroup',
						'group_id'=>$gid,
				))->execute();
				if ($result){
					$result = $result && db_insert('soc_codes')->fields( array(
							'type'=>'studentgroup',
							'code' => createRandomCode('studentgroup', $gid),
							'entity_id'=> $inst_id,
							'studentgroup_id' =>$gid))->execute();
					if (!$result){
						drupal_set_message(t('We could not add a code for this group.'), 'error');
					}
				} else {
					drupal_set_message(t('We could not add you to this group.'), 'error');
				}
			} else {
				drupal_set_message(t('We could not add your group.'), 'error');
			}
	
			return $result;
	
		} catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not add your group.'). (_DEBUG? $ex->__toString(): ''), 'error');
		}
		return FALSE;
	}
	
	static function filterPost($type){
	
		//TODO: get the db fields from schema and move foreach out of switch
		$fields = array(
				'institute' => array('name', 'contact_name', 'contact_email'),
				'organisation'=> array('name', 'contact_name', 'contact_email', 'url', 'description'),
				'studentgroup'=> array('name', 'description'),
				'project' => array('org_id', 'title', 'description', 'url', 'tags')
		);
		if (!$type || !isset($fields[$type])){
			return null;
		} else {
			$input = array();
		}
	
		foreach ($fields[$type] as $prop){
			if (isset($_POST[$prop])){
				$input[$prop] = $_POST[$prop];
			}
		}
		return $input;
	}
}