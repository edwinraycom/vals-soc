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
	foreach ($roles as $value) {
		// Check that a role is not already returned.
		if (!user_role_load_by_name($value)) {
			$role = new stdClass(); // create your Class
			// Match values to the role Class.
			$role->name = $value['name'];
			$role->weight = $value['weight'];
			// Save the Role using Drupal's API.
			user_role_save($role);
		}
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
 * @return multitype:multitype:string
 */
function get_default_roles() {
	$roles = array();
    $roles['administrator'] = array(
			'name' => 'Admin',
			'weight' => '2',
			'code' => 'BHTGSKKRWP'
	);
    // why do we need the role soc?
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

function getRoles(){
    global $user;

    if (isset($user)){
        return $user->roles;
    } else {
        return array();
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
    //Every user has exactly two roles: verified user (role 2) and some other role > 2
    //Sort associative, reversed: [10 => 0, 9 => 9, 8 => 0,....]
    arsort($roles);
    foreach ($roles as $rid => $role_val){
        if ($role_val != 0) {
            return $rid;
        }
    }
    return _USER_ROLE;
}
/*
 * TODO - REFINE ROLES and ACCESS per role instance
*
* THIS IS HOW TO SET PERMISSIONS ON A ROLE
*
// Role to grant the permissions to
$editor_role = user_role_load_by_name('editor');
$editor_rid = $editor_role->rid;
// Define our 'editor' role permissions
$editor_permissions = array(
		'administer blocks' => TRUE, // Grant permission
		'access dashboard' => FALSE, // Revoke permission
		..., // more permissions' name
);
// Grant permissions to our 'editor' role
user_role_change_permissions($editor_rid, $editor_permissions);
*/
