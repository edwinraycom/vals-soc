<?php
//The following are copied from the current database state. Can be neater to derive and put in the cache
//or something like that
define('_ANONYMOUS_ROLE', 1);
define('_USER_ROLE', 2);

/**
 * Function to create the custom roles needed in drupal for the summer of code programme.
 */
function create_roles(){
	$roles = get_default_roles();
	// Call the array with your default roles.
	foreach ($roles as $key => $value) {
		// Check that a role is not already returned.
		if (!user_role_load_by_name($key)) {
			$role = new stdClass(); // create your Class
			// Match values to the role Class.
			$role->name = $key; // Used to be $value['name'], but that is reserved for a readable string in the UI
			$role->weight = $value['weight'];
			// Save the Role using Drupal's API.
			user_role_save($role);
		}
	}
}

function create_permissions(){
// 	$perms['anonymous user'] = array(
// 			//'vals admin register' => TRUE,
// 			'vals browse projects' => TRUE,
// 			//'vals admin projects' => TRUE,
// 	);
// 	$perms[' authenticated user'] = array(
// 			'vals admin register' => TRUE,
// 			'vals browse projects' => TRUE,
// 			'vals admin projects' => TRUE,
// 	);
	$perms['soc'] = array(
			//'vals admin register' => TRUE,
			'vals browse projects' => TRUE,
			//'vals admin projects' => TRUE,
	);
	$perms['organisation_admin'] = array(
			'vals admin register' => TRUE,
			'vals browse projects' => TRUE,
			'vals admin projects' => TRUE,
	);
	$perms['supervisor'] = array(
			'vals admin register' => TRUE,
			'vals browse projects' => TRUE,
			//'vals admin projects' => TRUE,
	);
	$perms['mentor'] = array(
			//'vals admin register' => TRUE,
			'vals browse projects' => TRUE,
			'vals admin projects' => TRUE,
	);
	$perms['student'] = array(
			//'vals admin register' => TRUE,
			'vals browse projects' => TRUE,
			//'vals admin projects' => TRUE,
			'vals apply projects' => TRUE,
	);
	$perms['institute_admin'] = array(
			'vals admin register' => TRUE,
			'vals browse projects' => TRUE,
			'vals edit projects' => TRUE,
	);
	foreach ($perms as $role => $permissions){
		echo "Bekijkt nu $role";
		if (!$role_obj = user_role_load_by_name($role)) {
			$role_temp = new stdClass(); // create your Class
			// Match values to the role Class.
			$role_temp->name = $role; // Used to be $value['name'], but that is reserved for a readable string in the UI
			//If we leave the wight out, user_role_save will take the max weight sofar
			//$role_temp->weight = 2;//TODO: find out whether this value is important and what it should be
			// Save the Role using Drupal's API.
			if (user_role_save($role_temp)) {
				$role_obj = user_role_load_by_name($role);
				$rid = $role_obj->rid;
			}
		} else {
			$rid = $role_obj->rid;
		}
		// Grant permissions to this role
		user_role_change_permissions($rid, $permissions);
	}
}

/**
 * Create codes
 */
function create_codes(){
	$roles = get_default_roles();
	// Call the array with your default roles.
    if ($roles){
        $query = db_insert('soc_codes')->fields(array('type' ,'code'));
        foreach ($roles as $key => $value) {
            $query->values(array(
                'type' => $key,
                'code' => $value['code'],
                'org' => 0,
                ));
        }
        //Now we have added all the code records
        $query->execute();
    }
}

/**
 * Function which defines a set of roles and returns them in an array.
 * @return multitype:multitype:string
 */
/**
 * Function which defines a set of roles and returns them in an array.
 * Note that we use the key as the string in the database and throughout the code. The name is used in the interface
 * in case the admin likes to revise the roles and permissions.
 * @return multitype:multitype:string
 */
function get_default_roles() {
	$roles = array();
    $roles['administrator'] = array(
			'name' => 'Admin',
			'weight' => '2',
			'code' => 'BHTGSKKRWP'
	);
    // We need the role soc for the members of the team setting up a Semester of Code
    $roles['soc'] = array(
			'name' => 'Semester of Code',
			'weight' => '2',
			'code' => 'EICUN14SOC'
	);
	$roles['organisation_admin'] = array(
			'name' => 'Organisation Administrator',
			'weight' => '3',
			'code' => 'AHGLL765OW'
	);
	$roles['supervisor'] = array(
			'name' => 'Academic Supervisor',
			'weight' => '4',
			'code' => 'MNUFD783SF'
	);
	$roles['mentor'] = array(
			'name' => 'Mentor',
			'weight' => '5',
			'code' => 'QTU8TR44KJ'
	);
	$roles['student'] = array(
			'name' => 'Student',
			'weight' => '6',
			'code' => 'PGI99OPAQW'
	);
    $roles['institute_admin'] = array(
			'name' => 'Institute Responsible',
			'weight' => '2',
			'code' => 'A99ODF435W'
	);
    
	return $roles;
}

function getRoles($uid=''){
    global $user;
	
    if ($uid) {
    	$roles = doAssocQuery("select r.rid,r.name from users_roles as ur left join role as r on r.rid=ur.rid ".
    			"where ur.uid = $uid");//->fetchAll();
    	return $roles;
    } else {
    	 if (isset($user)){
	        return $user->roles;
	    } else {
	        return array();
	    }
    }
}

function getRole(){
    $roles = getRoles();
    if ($roles) {
        foreach ($roles as $rid => $name){
            if ($rid == _ANONYMOUS_ROLE ){
                return 'anonymous user';
            } elseif ($rid !== _USER_ROLE) {
                return $name;
            }
        }
        return 'authenticated user';
    }
}

function getUserRoleId($roles=''){
    $roles = $roles ?: getRoles();
    //Every user has exactly two roles:  authenticated user (role 2) and some other role > 2
    //Sort associative, reversed: [10 => 0, 9 => 9, 8 => 0,....]
    arsort($roles);
    foreach ($roles as $rid => $role_val){
        if ($role_val != 0) {
            return $rid;
        }
    }
    return _USER_ROLE;
}
