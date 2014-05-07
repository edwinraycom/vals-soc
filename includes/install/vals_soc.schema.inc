<?php 

function vals_soc_schema_handler() {
	// NOTE: {variable} needs to be created before all other tables, as
	// some database drivers, e.g. Oracle and DB2, will require variable_get()
	// and variable_set() for overcoming some database specific limitations.

	$schema['soc_projects'] = array(
			'description' => 'The table of the projects',
			'fields' => array(
					'pid' => array(
							'description' => 'Project id.',
							'type' => 'varchar',
							'length' => 255,
							'not null' => TRUE,
							'default' => '',
					),
					'title' => array(
							'description' => 'The title of the project.',
							'type' => 'varchar',
							'length' => 255,
							'not null' => TRUE,
							'default' => '',
					),
					'description' => array(
							'description' => 'The description of the project.',
							'type' => 'varchar',
							'length' => 255, // TODO perhaps change this to another data type
							'not null' => FALSE,
							'default' => '',
					),
					'selected' => array(
							'description' => 'Whether the project is chosen by a student',
							'type' => 'int',
							'size' => 'tiny',
							'not null' => TRUE,
							'default' => 0,
					),
                'state' => array(
							'description' => 'The state of the project',
							'type' => 'varchar',
							'length' => 128,
							'not null' => FALSE,
					),
			),
			'primary key' => array('pid'),
        /*
         * ALTER TABLE `soc_projects` ADD `organisation` MEDIUMINT NOT NULL AFTER `state` ,
ADD `mentor` MEDIUMINT NOT NULL AFTER `organisation` ,
ADD `student` MEDIUMINT NOT NULL AFTER `mentor` ,
ADD `supervisor` MEDIUMINT NOT NULL AFTER `student`
         * ALTER TABLE `soc_projects` ADD `url` VARCHAR( 1024 ) NOT NULL AFTER `description` 
         * 
         */
	);
	$schema['soc_codes'] = array(
			'description' => 'Create some random codes so that not just anybody can register.',
			'fields' => array(
                    'code_id' => array(
							'description' => 'Code id.',
							'type' => 'serial',
							'length' => 'medium',
							'not null' => TRUE,
							//'default' => '',
					),
					'type' => array(
							'description' => 'The type of user.',
							'type' => 'varchar',
							'length' => 128,
							'not null' => TRUE,
							'default' => '',
					),
					'code' => array(
							'description' => 'The code to enter at registration.',
							'type' => 'varchar',
							'length' => 10,
							'not null' => TRUE,
							'default' => '',
					),
                    'org' => array(
							'description' => 'The organisation/institute etc.',
							'type' => 'int',
							'length' => 'medium',
							'not null' => TRUE,
							'default' => 0,
					),
                    'group_id' => array(
							'description' => 'To make it easier to retrieve the group of the code a student uses to register',
							'type' => 'int',
							'length' => 'medium',
							'not null' => FALSE,
					),
			),
			'primary key' => array('code_id'),
	);
    
    $schema['soc_institutes'] = array(
			'description' => 'The institutes gettting involved in the Semester of Code',
			'fields' => array(
					'inst_id' => array(
							'description' => 'Institute id.',
							'type' => 'serial',
							'length' => 'small',
							'not null' => TRUE,
							//'default' => '',
					),
                    'name' => array(
							'description' => 'The name of the institute.',
							'type' => 'varchar',
							'length' => 255,
							'not null' => TRUE,
							'default' => '',
					),
					'contact_name' => array(
							'description' => 'The name of the contact person.',
							'type' => 'varchar',
							'length' => 128,
							'not null' => TRUE,
							'default' => '',
					),
                    'contact_email' => array(
							'description' => 'The email of the contact person.',
							'type' => 'varchar',
							'length' => 128,
							'not null' => TRUE,
							'default' => '',
					),
			),
			'primary key' => array('inst_id'),
	);
    $schema['soc_organisations'] = array(
			'description' => 'The organisations gettting involved in the Semester of Code',
			'fields' => array(
					'org_id' => array(
							'description' => 'Institute id.',
							'type' => 'serial',
							'length' => 'small',
							'not null' => TRUE,
							//'default' => '',
					),
                    'name' => array(
							'description' => 'The name of the organisation.',
							'type' => 'varchar',
							'length' => 255,
							'not null' => TRUE,
							'default' => '',
					),
					'contact_name' => array(
							'description' => 'The name of the contact person.',
							'type' => 'varchar',
							'length' => 128,
							'not null' => TRUE,
							'default' => '',
					),
                    'contact_email' => array(
							'description' => 'The email of the contact person.',
							'type' => 'varchar',
							'length' => 128,
							'not null' => TRUE,
							'default' => '',
					),
                    'url' => array(
							'description' => 'The website of the organisation',
							'type' => 'varchar',
							'length' => 256,
							'not null' => FALSE,
							'default' => '',
					),
					'description' => array(
							'description' => 'Description of the organisation',
							'type' => 'varchar',
							'length' => 1024,
							'not null' => FALSE,
							'default' => '',
					)
			),
			'primary key' => array('org_id'),
	);
    $schema['soc_groups'] = array(
			'description' => 'The students will be divided in groups, each teacher probably having one or more groups',
			'fields' => array(
					'group_id' => array(
							'description' => 'Group id.',
							'type' => 'serial',
							'length' => 'small',
							'not null' => TRUE,
					),
                    'inst_id' => array(
							'description' => 'Institute id.',
							'type' => 'int',
							'length' => 'small',
							'not null' => TRUE,
					),
                    'supervisor_id' => array(
							'description' => 'The id of the teacher',
							'type' => 'int',
							'length' => 'medium',
							'not null' => TRUE,
					),
                    'name' => array(
							'description' => 'The name of the group to remind.',
							'type' => 'varchar',
							'length' => 255,
							'not null' => TRUE,
					),
					'description' => array(
							'description' => 'Some description or comment',
							'type' => 'varchar',
							'length' => 512,
							'not null' => FALSE,
							'default' => '',
					),
			),
			'primary key' => array('group_id'),
	);
    $schema['soc_user_membership'] = array(
			'description' => 'All users are member of some organisation, either a university or some software company etc.',
			'fields' => array(
					'mem_id' => array(
							'description' => 'org relation id.',
							'type' => 'serial',
							'length' => 'small',
							'not null' => TRUE,
					),
                    'uid' => array(
							'description' => 'The id of the user.',
							'type' => 'int',
							'length' => 'medium',
							'not null' => TRUE,
							
					),
					'type' => array(
							'description' => 'The type of the organisation.',
							'type' => 'varchar',
							'length' => 128,
							'not null' => TRUE,
					),
                    'oid' => array(
							'description' => 'The id of the organisation/institute/group etc.',
							'type' => 'int',
							'length' => 'medium',
							'not null' => TRUE,
					),
			),
			'primary key' => array('mem_id'),
	);

	return $schema;
}
