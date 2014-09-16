/*
* While we are developing can we use this file to update/build the DB 
* rather than rewriting and adding updates to the vals_soc.install.
* When we are nearer our final DB structure, we can write/update
* the relevant bits in the vals_soc_schema.inc file.
* 
* Also we can setup the same test data (same records)
* 
* Summary of changes:
* soc_codes: group_id -> studentgroup_id
* 			org ->entity_id (maybe also 'group_id'???)
* soc_user_membership : oid -> group_id
* soc_projects: oid ->org_id
* 		-student, -supervisor, + proposal_id (rationale: project table coupd be joined with proposals and if no result, there was no supervisor and/or student either)
* soc_studentgroups ->soc_studentgroups
* soc_proposal: oid ->org_id
* 
* 20-6-14:
* 
ALTER TABLE `soc_proposals` CHANGE `cv` `cv` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `solution_short` `solution_short` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `solution_long` `solution_long` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `modules` `modules` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL 

-- 25-6-14
ALTER TABLE `soc_projects` CHANGE `url` `url` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '''''',
CHANGE `mentor` `mentor_id` MEDIUMINT( 9 ) NOT NULL DEFAULT '0',
CHANGE `proposal_id` `proposal_id` MEDIUMINT( 9 ) NOT NULL DEFAULT '0',
CHANGE `tags` `tags` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''''''
*/
-- use vals_vps;


CREATE TABLE IF NOT EXISTS `soc_names` (
  `names_uid` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tabelstructuur voor tabel 'soc_codes'
--

DROP TABLE IF EXISTS soc_codes;
CREATE TABLE IF NOT EXISTS soc_codes (
  code_id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Code id.',
  `type` varchar(128) NOT NULL DEFAULT '' COMMENT 'The type of user.',
  `code` varchar(10) NOT NULL DEFAULT '' COMMENT 'The code to enter at registration.',
  entity_id mediumint(9) NOT NULL DEFAULT '0' COMMENT 'The organisation/institute etc.',
  studentgroup_id int(11) DEFAULT NULL COMMENT 'To make it easier to retrieve the group of the code a student uses to register',
  PRIMARY KEY (code_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Create some random codes so that not just anybody can...' AUTO_INCREMENT=10 ;

--
-- Tabelstructuur voor tabel 'soc_institutes'
--

DROP TABLE IF EXISTS soc_institutes;
CREATE TABLE IF NOT EXISTS soc_institutes (
  inst_id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Institute id.',
  owner_id int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'The name of the institute.',
  contact_name varchar(128) NOT NULL DEFAULT '' COMMENT 'The name of the contact person.',
  contact_email varchar(128) NOT NULL DEFAULT '' COMMENT 'The email of the contact person.',
  PRIMARY KEY (inst_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='The institutes gettting involved in the Semester of Code' AUTO_INCREMENT=6 ;

--
-- Tabelstructuur voor tabel 'soc_organisations'
--

DROP TABLE IF EXISTS soc_organisations;
CREATE TABLE IF NOT EXISTS soc_organisations (
  org_id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Institute id.',
  owner_id int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'The name of the organisation.',
  contact_name varchar(128) NOT NULL DEFAULT '' COMMENT 'The name of the contact person.',
  contact_email varchar(128) NOT NULL DEFAULT '' COMMENT 'The email of the contact person.',
  url varchar(256) DEFAULT '' COMMENT 'The website of the organisation',
  description TEXT DEFAULT '' COMMENT 'Description of the organisation',
  PRIMARY KEY (org_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='The organisations gettting involved in the Semester of Code' AUTO_INCREMENT=5 ;

--
-- Tabelstructuur voor tabel 'soc_projects'
--

DROP TABLE IF EXISTS soc_projects;
CREATE TABLE IF NOT EXISTS soc_projects (
  pid int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Project id.',
  owner_id int(11) NOT NULL,
  title varchar(255) NOT NULL DEFAULT '' COMMENT 'The title of the project.',
  description TEXT COMMENT 'The description of the project.',
  url varchar(1024),
  state varchar(128) DEFAULT NULL COMMENT 'The state of the project',
  org_id int(11) unsigned NOT NULL,
  mentor_id mediumint(9),
  proposal_id mediumint(9),
  selected tinyint(4) DEFAULT '0' COMMENT 'Whether the project is chosen by a student',
  tags varchar(255),
  PRIMARY KEY (pid)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='The table of the projects' AUTO_INCREMENT=23 ;

--
-- Tabelstructuur voor tabel 'soc_proposals'
--

DROP TABLE IF EXISTS soc_proposals;
CREATE TABLE IF NOT EXISTS soc_proposals (
  proposal_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  owner_id mediumint(8) unsigned NOT NULL,
  org_id mediumint(8) unsigned NOT NULL,
  inst_id mediumint(8) unsigned NOT NULL,
  supervisor_id mediumint(9) NOT NULL,
  pid mediumint(8) unsigned NOT NULL,
  title varchar(512) NOT NULL DEFAULT '' COMMENT 'The title of the proposal.',
  solution_short varchar(512) NOT NULL,
  solution_long varchar(1024) NOT NULL,
  -- modules varchar(1024) DEFAULT NULL,
  state enum('draft','published','accepted','rejected','finished','archived') NOT NULL,
  PRIMARY KEY (proposal_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;
-- RC: used to be soc_studentgroups, key changed to: studentgroup_id 
-- Tabelstructuur voor tabel 'soc_studentgroups' 
--

DROP TABLE IF EXISTS soc_studentgroups;
CREATE TABLE IF NOT EXISTS soc_studentgroups (
  studentgroup_id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Group id.',
  owner_id int(11) NOT NULL COMMENT 'The id of the teacher',
  inst_id int(11) NOT NULL COMMENT 'Institute id.',
  `name` varchar(255) NOT NULL COMMENT 'The name of the group to remind.',
  description TEXT DEFAULT '' COMMENT 'Some description or comment',
  PRIMARY KEY (studentgroup_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='The students will be divided in groups, each teacher...' AUTO_INCREMENT=23 ;

-- Tabelstructuur voor tabel 'soc_user_membership'
-- RC: org changed to group_id 


DROP TABLE IF EXISTS soc_user_membership;
CREATE TABLE IF NOT EXISTS soc_user_membership (
  mem_id int(11) NOT NULL AUTO_INCREMENT COMMENT 'org relation id.',
  uid int(11) NOT NULL COMMENT 'The id of the user.',
  `type` varchar(128) NOT NULL COMMENT 'The type of the organisation.',
  group_id int(11) NOT NULL COMMENT 'The id of the organisation/institute/group etc.',
  PRIMARY KEY (mem_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='All users are member of some organisation, either a...' AUTO_INCREMENT=37 ;

--
-- Tabelstructuur voor tabel 'soc_comments'
--

DROP TABLE IF EXISTS `soc_comments`;
CREATE TABLE `soc_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `entity_id` int(11) unsigned NOT NULL,
  `entity_type` varchar(128) NOT NULL,
  `author` int(11) unsigned NOT NULL,
  `date_posted` datetime NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;