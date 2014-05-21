<?php

class Participants {
	public static function getGroups($supervisor='')
	{
		global $user;

		$supervisor = $supervisor ?: $user->uid;
		//todo: find out whether current user is supervisor

		if ($supervisor == 'all'){
			$groups = db_select('soc_groups')->fields('soc_groups')->execute()->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$groups = db_select('soc_groups')->fields('soc_groups')->condition('supervisor_id', $supervisor)->
			execute()->fetchAll(PDO::FETCH_ASSOC);
		}

		return $groups;
	}

	public static function getStudents($group=''){
		global $user;

		$supervisor = $user->uid;
		//todo: find out whether current user is supervisor
		 
		if ($group == 'all'){
			$students = db_query('select * from users as u left join users_roles as ur on u.uid=ur.uid left join role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'student'));
		} elseif ($group) {
			$students = db_query("SELECT * from users as u left join soc_user_membership as um".
					" on u.uid = um.uid WHERE um.type = 'group' AND um.oid = $group AND u.uid != $supervisor ");
		} else {
			$groups = db_query("SELECT oid from soc_user_membership um".
					" WHERE um.type = 'group' AND um.uid = $supervisor ")->fetchCol();
			if ($groups){
				$students = db_query("SELECT * from users as u left join soc_user_membership um ".
						"on u.uid = um.uid WHERE um.type = 'group' AND um.oid IN (:groups) AND u.uid != $supervisor ", array(':groups' => $groups));
			} else {
				return NULL;
			}
		}

		return $students;
	}

	/*
	 * Get Participants of type member_type out of the organisation structure of type group_type, based on:
	 * a) group_id == all => get all students, tutors etc.
	 * b) id is set => retrieve participants with that id (should be just one)
	 * c) group id is set or can be derived from current user => get participants from that group
	 */
	public static function getParticipants($member_type, $group_type='', $group_id='', $id='')
	{
		global $user;

		$group_head = $user->uid;
		//todo: find out whether current user is indeed head of the group

		if ($group_id == 'all'){
			$members = db_query(
					'select * from users as u '.
					'left join  users_roles as ur on u.uid=ur.uid '.
					'left join role as r on r.rid=ur.rid '.
					'WHERE r.name=:role ', array(':role' => $member_type));
		} else {
			if ($id){
				$members = db_query(
						"SELECT u.* from users as u".
						"WHERE u.uid = '$id'");

			} else {
				if (!$group_id && $group_type) {
					//get the organisation from the current user, assuming he/she is head of the organisation/group/etc
					$group_ids = db_query(
							"SELECT oid from soc_user_membership um".
							" WHERE um.type = '$group_type' AND um.uid = $group_head ")
							->fetchCol();
				}
				$group_id = 0;
				if ($group_id){
					$members = db_query(
							"SELECT u.* from users as u left join users_roles as ur ".
							" on u.uid = ur.uid left join role as r ".
							" on ur.rid = r.rid left join soc_user_membership as um ".
							" on u.uid = um.uid ".
							"WHERE r.name = '$member_type' AND um.type = '$group_type' AND um.oid IN (".
							implode(',', $group_ids).")");
				} else {
					return NULL;
				}
			}

		}

		return $members;
	}

	public static function getParticipant($id=''){
		return $this->getParticipants('', '', '', $id)->fetchObject();
	}

	public static function getOrganisations($org_type, $group_head_id='', $id='')
	{
		global $user;

		$group_head = $user->uid;
		//todo: find out whether current user is institute_admin

		if ($group_head_id == 'all'){
			$members = db_query("SELECT o.* from soc_$org_type as o");
		} else {
// 			$key_columns = array(
// 					'group' => 'group_id',
// 					'institute' => 'inst_id',
// 					'organisation' => 'org_id');
			$key_column = self::keyField($org_type);
			$code_key_column = ($org_type == 'group') ? 'group_id' : 'org';
			$member_type = ($org_type == 'group') ? 'group' :(($org_type == 'organisation') ? 'mentor': 'supervisor');
			if ($id){
				 
				$members = db_query(
						"SELECT o.*, c.code from soc_${org_type}s as o ".
						"left join soc_codes as c ".
						" on o.$key_column = c.$code_key_column ".
						"WHERE o.$key_column = $id ");
			} else {
				$group_head_id = $group_head_id ?: $group_head;
	
				$members = db_query(
						"SELECT o.*, c.code from soc_${org_type}s as o ".
						"left join soc_user_membership as um ".
						" on o.$key_column = um.oid ".
						"left join soc_codes as c ".
						" on o.$key_column = c.$code_key_column AND c.type = '$member_type'".
						"WHERE um.type = '$org_type' AND um.uid = $group_head_id ");
			}
		}
		
		return $members;
	}

	public static function getOrganisation($org_type, $id='')
	{
		return Participants::getOrganisations($org_type, '', $id)->fetchObject();
	}
	
	public static function getAllStudents($institute='')
	{
		global $user;
	
		$institute_admin = $user->uid;
		//todo: find out whether current user is institute_admin
	
		if ($institute == 'all'){
			$supervisors = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
					'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'student'));
		} else {
			if (!$institute) {
				//get the institute from the institute admin
				$institute = db_query("SELECT oid from soc_user_membership um".
						" WHERE um.type = 'institute' AND um.uid = $institute_admin ")->fetchColumn();
			}
			if ($institute){
				$students = db_query("SELECT u.* from users as u left join users_roles as ur ".
						" on u.uid = ur.uid left join role as r ".
						" on ur.rid = r.rid left join soc_user_membership as um ".
						" on u.uid = um.uid WHERE r.name = 'student' AND um.type = 'institute' AND um.oid = $institute ");
			} else {
				return NULL;
			}
	
		}
	
		return $students;
	}
	
	public static function getSupervisors($institute='')
	{
		global $user;
	
		$institute_admin = $user->uid;
		//todo: find out whether current user is supervisor
	
		if ($institute == 'all'){
			$supervisors = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
					'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'supervisor'));
		} elseif ($institute) {
			$supervisors = db_query("SELECT u.* from users as u left join users_roles as ur ".
					" on u.uid = ur.uid left join role as r ".
					" on ur.rid = r.rid left join soc_user_membership as um ".
					" on u.uid = um.uid WHERE r.name = 'supervisor' AND um.type = 'institute' AND um.oid = $institute AND u.uid != $institute_admin ");
		} else {
			//get the institute from the institute_admin
			$institute = db_query("SELECT oid from soc_user_membership um".
					" WHERE um.type = 'institute' AND um.uid = $institute_admin ")->fetchColumn();
			if ($institute){
				$supervisors = db_query("SELECT u.* from users as u left join soc_user_membership um ".
						"on u.uid = um.uid WHERE um.type = 'institute' AND um.oid = $institute AND u.uid != $institute_admin ");
			} else {
				return NULL;
			}
		}
		return $supervisors;
	}
	
	public static function getMentors($organisation='')
	{
		global $user;
	
		$organisation_admin = $user->uid;
		//todo: find out whether current user is org admin
	
		//get organisations
		if ($organisation == 'all'){
			$mentors = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
					'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'mentor'));
		} elseif ($organisation) {
			 
			$mentors = db_query("SELECT u.* from users as u left join soc_user_membership as um".
					" on u.uid = um.uid WHERE um.type = 'institute' AND um.oid = $organisation AND u.uid != $organisation_admin ");
		} else {
			//get the organisation
			$organisation = db_query("SELECT oid from soc_user_membership um".
					" WHERE um.type = 'organisation' AND um.uid = $organisation_admin ")->fetchColumn();
			if ($organisation){
				$mentors = db_query("SELECT u.* from users as u left join soc_user_membership um ".
						"on u.uid = um.uid WHERE um.type = 'organisation' AND um.oid = $organisation AND u.uid != $organisation_admin ");
			} else {
				return NULL;
			}
		}
	
		return $mentors;
	}
	
	public static function getInstituteAdmins($institute='')
	{
		global $user;
	
		$institute_admin = $user->uid;
		//todo: find out whether current user is supervisor
	
		if ($institute == 'all'){
			$admins = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
					'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'institute_admin'));
		} else {
			if (!$institute) {
				$institute = db_query("SELECT oid from soc_user_membership um".
						" WHERE um.type = 'institute' AND um.uid = $institute_admin ")->fetchColumn();
			}
			if ($institute){
				//Get all the admins from this institute (1?: all users with role institute_admin who are member of this institute
				$admins = db_query("SELECT u.* from users as u left join users_roles as ur ".
						" on u.uid = ur.uid left join role as r ".
						" on ur.rid = r.rid left join soc_user_membership as um ".
						" on u.uid = um.uid WHERE r.name = 'institute_admin'  AND um.type = 'institute' AND um.oid = $institute ");
			} else {
				return NULL;
			}
		}
	
		return $admins;
	}
	
	public static function getOrganisationAdmins($organisation='')
	{
		global $user;
	
		$organisation_admin = $user->uid;
		//todo: find out whether current user is organisation_admin
	
		if ($organisation == 'all'){
			$admins = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
					'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'organisation_admin'));
		} else {
			if (!$organisation) {
				$organisation = db_query("SELECT oid from soc_user_membership um".
						" WHERE um.type = 'organisation' AND um.uid = $organisation_admin ")->fetchColumn();
			}
			if ($organisation){
				//Get all the admins from this organisation (1?: all users with role organisation_admin who are member of this organisation
				$admins = db_query("SELECT u.* from users as u left join users_roles as ur ".
						" on u.uid = ur.uid left join role as r ".
						" on ur.rid = r.name = 'organisation_admin' rid left join soc_user_membership as um ".
						" on u.uid = um.uid WHERE r.um.type = 'organisation' AND um.oid = $organisation ");
			} else {
				return NULL;
			}
		}
	
		return $admins;
	}
	
	static function updateOrganisation($type, $organisation, $id)
	{
		if (! $organisation){
			drupal_set_message(t('Update requested with empty data set'));
			return false;
		}
		$key_columns = array(
				'group' => 'group_id',
				'institute' => 'inst_id',
				'organisation' => 'org_id'
		);
	
		return
		db_update("soc_${type}s")
		->condition($key_columns[$type], $id)
		->fields($organisation)
		->execute();
	}
	 
	static function keyField($type){
		switch ($type){
			case 'group': return 'group_id';break;
			case 'institute': return 'inst_id';break;
			case 'organisation': return 'org_id';break;
			case 'project': return 'pid';break;
			default: return '';
		}
	}
	
	static function isOwner($type, $id){
		if (! in_array($type, array('group', 'institute', 'organisation', 'project'))){
			drupal_set_message(sprintf(t('You cannot be the owner of an entity called %1$s'), $type));
			return FALSE;
		}
		$key_field = self::keyField($type);
		$obj = db_query("SELECT * FROM soc_${type}s WHERE $key_field = $id")->fetchAssoc();
		return $obj['owner_id'] == $GLOBALS['user']->uid;
	}
	
	static function hasMembers($type, $id){
		//Assuming there is always an owner inside the group
		return db_query("SELECT * FROM soc_user_membership WHERE type = '$type' AND oid = $id")->rowCount() > 1;
	}
	
	static function deleteOrganisation($type, $id){
		if (! self::isOwner($type, $id)){
			drupal_set_message(t('You are not authorised to perform this action'), 'error');
			return FALSE;
		}
		
		if (self::hasMembers($type, $id)){
			drupal_set_message(sprintf(t('There are already members in this %1$s. You can still edit the %1$s though.'),
					t($type)), 'error');
			return FALSE;
		}
		if (!isValidOrganisationType($type)){
			
		}
		$num_deleted = db_delete("soc_${type}s")
		->condition(self::keyField($type), $id)
		->execute();
		if ($num_deleted){
			$num_deleted2 = db_delete("soc_user_membership")
			->condition('oid', $id)
			->condition('type', $type)
			->execute();
			if (!$num_deleted2){
				drupal_set_message(tt('The group has been deleted, but it had no members.', $type), 'error');
				return $num_deleted;
			}
			
			$subtype = ($type == 'organisation') ? 'mentor' : (($type == 'institute') ? 'supervisor' : 'group');
			
			$num_deleted3 = db_delete("soc_codes")
			->condition('org', $id)
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
	
	static function tableName($type){
		return "soc_${type}s";
	}

	static function insertOrganisation($props, $type){
		if (! $props){
			drupal_set_message(t('Insert requested with empty (filtered) data set'), 'error');
			return false;
		}
	
		global $user;
	
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
			}
			$id = db_insert(self::tableName($type))->fields($props)->execute();
			if ($id){
				//Make current user creating this organisation, member
				$result = db_insert('soc_user_membership')->fields( array(
						'uid'=>$uid,
						'type' => $type,
						'oid'=>$id,
				))->execute();
				if ($result){
					$result = $result && db_insert('soc_codes')->fields( array(
							'type'=>$subtype,
							'code' => createRandomCode($subtype, $id),
							'org'=> $id,
							'group_id' =>0))->execute();
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
			drupal_set_message(t('We could not add your group.'). (_DEBUG? $ex->__toString(): ''), 'error');
		}
		return FALSE;
	}
	
	static function insertGroup($group){
		if (! $group){
			drupal_set_message(t('Insert requested with empty (filtered) data set'));
			return false;
		}
		 
		global $user;
		 
		$txn = db_transaction();
		try {
			$uid = $user->uid;
			$institute = db_select('soc_user_membership')->fields('soc_user_membership', array('oid'))
			->condition('uid', $uid)
			->condition('type', 'institute')
			->execute()->fetchCol();
			if ($institute){
				$inst_id = $institute[0];
			} else {
				$inst_id = 0;
			}
	
			$gid = db_insert('soc_groups')->fields(array(
					'name'=>$group['name'],
					'owner_id' =>  $uid,
					'supervisor_id' => $uid,
					'inst_id' => $inst_id,
					'description' => ($group['description'] ?: ''),
			))->execute();
			if ($gid){
				$result = db_insert('soc_user_membership')->fields( array(
						'uid'=>$uid,
						'type' => 'group',
						'oid'=>$gid,
				))->execute();
				if ($result){
					$result = $result && db_insert('soc_codes')->fields( array(
							'type'=>'group',
							'code' => createRandomCode('group', $gid),
							'org'=> $inst_id,
							'group_id' =>$gid))->execute();
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
				'group'=> array('name', 'description'),
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