/*
* While we are developing can we use this file to update/build the DB 
* rather than rewriting and adding updates to the vals_soc.install.
* When we are nearer our final DB structure, we can write/update
* the relevant bits in the vals_soc_schema.inc file.
* 
* Also we can setup the same test data (same records)
*/
use drupal;

DROP TABLE IF EXISTS `soc_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

CREATE TABLE IF NOT EXISTS `soc_codes` (
  `code_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Code id.',
  `type` varchar(128) NOT NULL DEFAULT '' COMMENT 'The type of user.',
  `code` varchar(10) NOT NULL DEFAULT '' COMMENT 'The code to enter at registration.',
  `org` mediumint(9) NOT NULL DEFAULT '0' COMMENT 'The organisation/institute etc.',
  `group_id` int(11) DEFAULT NULL COMMENT 'To make it easier to retrieve the group of the code a student uses to register',
  PRIMARY KEY (`code_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Create some random codes so that not just anybody can...' AUTO_INCREMENT=0 ;

--
-- Table structure for table `soc_groups`
--

DROP TABLE IF EXISTS `soc_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soc_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Group id.',
  `owner_id` int(11) NOT NULL,
  `inst_id` int(11) NOT NULL COMMENT 'Institute id.',
  `name` varchar(255) NOT NULL COMMENT 'The name of the group to remind.',
  `description` varchar(512) DEFAULT '' COMMENT 'Some description or comment',
  `supervisor_id` int(11) NOT NULL COMMENT 'The id of the teacher',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The students will be divided in groups, each teacher...';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soc_institutes`
--

DROP TABLE IF EXISTS `soc_institutes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soc_institutes` (
  `inst_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Institute id.',
  `owner_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'The name of the institute.',
  `contact_name` varchar(128) NOT NULL DEFAULT '' COMMENT 'The name of the contact person.',
  `contact_email` varchar(128) NOT NULL DEFAULT '' COMMENT 'The email of the contact person.',
  PRIMARY KEY (`inst_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The institutes gettting involved in the Semester of Code';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soc_organisations`
--

DROP TABLE IF EXISTS `soc_organisations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soc_organisations` (
  `org_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Institute id.',
   `owner_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'The name of the organisation.',
  `contact_name` varchar(128) NOT NULL DEFAULT '' COMMENT 'The name of the contact person.',
  `contact_email` varchar(128) NOT NULL DEFAULT '' COMMENT 'The email of the contact person.',
  `url` varchar(256) DEFAULT '' COMMENT 'The website of the organisation',
  `description` varchar(1024) DEFAULT '' COMMENT 'Description of the organisation',
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='The organisations gettting involved in the Semester of Code';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soc_projects`
--

DROP TABLE IF EXISTS `soc_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soc_projects` (
  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Project id.',
  `owner_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'The title of the project.',
  `description` text COMMENT 'The description of the project.',
  `url` varchar(1024) NOT NULL,
  `state` varchar(128) DEFAULT NULL COMMENT 'The state of the project',
  `oid` int(11) unsigned NOT NULL,
  `mentor` mediumint(9) NOT NULL,
  `student` mediumint(9) NOT NULL,
  `supervisor` mediumint(9) NOT NULL,
  `selected` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Whether the project is chosen by a student',
  `tags` varchar(255) NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COMMENT='The table of the projects';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soc_user_membership`
--

DROP TABLE IF EXISTS `soc_user_membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soc_user_membership` (
  `mem_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'org relation id.',
  `uid` int(11) NOT NULL COMMENT 'The id of the user.',
  `type` varchar(128) NOT NULL COMMENT 'The type of the organisation.',
  `oid` int(11) NOT NULL COMMENT 'The id of the organisation/institute/group etc.',
  PRIMARY KEY (`mem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='All users are member of some organisation, either a...';
/*!40101 SET character_set_client = @saved_cs_client */;




