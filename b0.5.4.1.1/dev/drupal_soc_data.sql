-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Machine: 127.0.0.1
-- Genereertijd: 04 jun 2014 om 15:38
-- Serverversie: 5.5.27
-- PHP-versie: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: 'drupal'
-- Ideally we would for every table update (or all at once) use the following structure (next commit):
-- LOCK TABLES `soc_codes` WRITE, `soc_institutes' WRITE, .....;
-- do all updates
-- UNLOCK TABLES;

-- Summary changes:
-- all values ' group' are replaced by 'studentgroup'

-- use vals_vps;

TRUNCATE TABLE `soc_codes`;
TRUNCATE TABLE `soc_studentgroups`;
TRUNCATE TABLE `soc_institutes`;
TRUNCATE TABLE `soc_organisations`;
TRUNCATE TABLE `soc_projects`;
TRUNCATE TABLE `soc_proposals`;
TRUNCATE TABLE `soc_user_membership`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `users_roles`;
TRUNCATE TABLE `role`;
TRUNCATE TABLE `role_permission`;
TRUNCATE TABLE `soc_names`;
TRUNCATE TABLE `soc_comments`;

INSERT INTO `soc_names` (`names_uid`, `type`, `name`) VALUES
(28, 'supervisor', 'Julian Sas'),
(31, 'student', 'Jantje Smit dus'),
(38, 'student', 'Edwin Student');
--
-- Gegevens worden uitgevoerd voor tabel 'role'
--

INSERT INTO role (rid, `name`, weight) VALUES
(3, 'administrator', 2),
(1, 'anonymous user', 0),
(2, 'authenticated user', 1),
(12, 'institute_admin', 2),
(5, 'mentor', 4),
(8, 'organisation_admin', 3),
(14, 'soc', 2),
(4, 'student', 6),
(9, 'supervisor', 5);

--
-- Gegevens worden uitgevoerd voor tabel 'role_permission'
--

INSERT INTO role_permission (rid, permission, module) VALUES
(1, 'use text format filtered_html', 'filter'),
(2, 'access content', 'node'),
(2, 'use text format filtered_html', 'filter'),
(3, 'access administration menu', 'admin_menu'),
(3, 'access administration pages', 'system'),
(3, 'access content', 'node'),
(3, 'access content overview', 'node'),
(3, 'access contextual links', 'contextual'),
(3, 'access dashboard', 'dashboard'),
(3, 'access overlay', 'overlay'),
(3, 'access site in maintenance mode', 'system'),
(3, 'access site reports', 'system'),
(3, 'access toolbar', 'toolbar'),
(3, 'access user profiles', 'user'),
(3, 'administer actions', 'system'),
(3, 'administer blocks', 'block'),
(3, 'administer content types', 'node'),
(3, 'administer ctools access ruleset', 'ctools_access_ruleset'),
(3, 'administer custom content', 'ctools_custom_content'),
(3, 'administer filters', 'filter'),
(3, 'administer front page', 'front_page'),
(3, 'administer image styles', 'image'),
(3, 'administer languages', 'locale'),
(3, 'administer menu', 'menu'),
(3, 'administer modules', 'system'),
(3, 'administer nodes', 'node'),
(3, 'administer page manager', 'page_manager'),
(3, 'administer permissions', 'user'),
(3, 'administer search', 'search'),
(3, 'administer shortcuts', 'shortcut'),
(3, 'administer site configuration', 'system'),
(3, 'administer software updates', 'system'),
(3, 'administer stylizer', 'stylizer'),
(3, 'administer taxonomy', 'taxonomy'),
(3, 'administer themes', 'system'),
(3, 'administer url aliases', 'path'),
(3, 'administer users', 'user'),
(3, 'block IP addresses', 'system'),
(3, 'bypass node access', 'node'),
(3, 'cancel account', 'user'),
(3, 'change own username', 'user'),
(3, 'create article content', 'node'),
(3, 'create page content', 'node'),
(3, 'create url aliases', 'path'),
(3, 'customize shortcut links', 'shortcut'),
(3, 'delete any article content', 'node'),
(3, 'delete any page content', 'node'),
(3, 'delete own article content', 'node'),
(3, 'delete own page content', 'node'),
(3, 'delete revisions', 'node'),
(3, 'delete terms in 1', 'taxonomy'),
(3, 'edit any article content', 'node'),
(3, 'edit any page content', 'node'),
(3, 'edit own article content', 'node'),
(3, 'edit own page content', 'node'),
(3, 'edit terms in 1', 'taxonomy'),
(3, 'revert revisions', 'node'),
(3, 'search content', 'search'),
(3, 'select account cancellation method', 'user'),
(3, 'switch shortcut sets', 'shortcut'),
(3, 'translate interface', 'locale'),
(3, 'use advanced search', 'search'),
(3, 'use bulk exporter', 'bulk_export'),
(3, 'use page manager', 'page_manager'),
(3, 'use text format filtered_html', 'filter'),
(3, 'use text format full_html', 'filter'),
(3, 'view own unpublished content', 'node'),
(3, 'view revisions', 'node'),
(3, 'view the administration theme', 'system'),
(8, 'access administration menu', 'admin_menu'),
(8, 'administer ckeditor', 'ckeditor'),
(8, 'administer date tools', 'date_tools'),
(8, 'administer front page', 'front_page'),
(8, 'administer smtp module', 'smtp'),
(8, 'customize ckeditor', 'ckeditor'),
(8, 'display drupal links', 'admin_menu'),
(8, 'flush caches', 'admin_menu'),
(8, 'view date repeats', 'date_repeat_field'),

(8, 'vals access dashboard', 'vals_soc'), # organisation_admin role
(5, 'vals access dashboard', 'vals_soc'), # mentor role
(12, 'vals access dashboard', 'vals_soc'), # institute_admin role
(9, 'vals access dashboard', 'vals_soc'), # supervisor role
(4, 'vals access dashboard', 'vals_soc'), # student role

(14, 'vals browse organisations', 'vals_soc'), # soc ???
(8, 'vals browse organisations', 'vals_soc'),  # organisation_admin role
(5, 'vals browse organisations', 'vals_soc'),  # mentor role
(12, 'vals browse organisations', 'vals_soc'), # institute_admin role
(9, 'vals browse organisations', 'vals_soc'),  # supervisor role
(4, 'vals browse organisations', 'vals_soc'),  # student role
(1, 'vals browse organisations', 'vals_soc'),  # unauthenticated can see orgs only after orgs announced

(14, 'vals browse projects', 'vals_soc'), # soc ???
(8, 'vals browse projects', 'vals_soc'),  # organisation_admin role
(5, 'vals browse projects', 'vals_soc'),  # mentor role
(12, 'vals browse projects', 'vals_soc'), # institute_admin role
(9, 'vals browse projects', 'vals_soc'),  # supervisor role
(4, 'vals browse projects', 'vals_soc'),  # student role

(8, 'vals admin projects', 'vals_soc'), # organisation_admin role
(5, 'vals admin projects', 'vals_soc'), # mentor role

(8, 'vals admin register', 'vals_soc'),  # organisation_admin role
(12, 'vals admin register', 'vals_soc'), # institute_admin role
(9, 'vals admin register', 'vals_soc'),  # supervisor role

(5, 'vals admin view', 'vals_soc'),  # mentor role

(12, 'vals edit projects', 'vals_soc'),  # institute_admin role

(4, 'vals apply projects', 'vals_soc');  # student role

--
-- Gegevens worden uitgevoerd voor tabel 'soc_codes'
--

INSERT INTO soc_codes (code_id, `type`, code, entity_id, studentgroup_id) VALUES
(1, 'administrator', 'BHTGSKKRWP', 0, 0),
(2, 'institute_admin', 'A99ODF435W', 0, 0),
(3, 'mentor', 'QTU8TR44KJ', 0, 0),
(4, 'organisation_admin', 'AHGLL765OW', 0, 0),
(5, 'soc', 'EICUN14SOC', 0, 0),
(6, 'student', 'PGI99OPAQW', 0, 0),
(7, 'supervisor', 'MNUFD783SF', 0, 0),
(8, 'studentgroup', 'MNU77783SF', 5, 22),
(9, 'studentgroup', 'MPOPUFD783', 5, 21);

--
-- Gegevens worden uitgevoerd voor tabel 'soc_institutes'
--

INSERT INTO soc_institutes (inst_id, owner_id, `name`, contact_name, contact_email) VALUES
(1, 0, 'London Universityeee', 'John', 'johnny_walker@wiskey.com'),
(3, 0, 'Salamanca Universidad', 'JUan', 'juan@raycom.com'),
(5, 0, 'Salamanca Again', 'Me, who If then Else', 'edwin@raycom.com');


--
-- Gegevens worden uitgevoerd voor tabel 'soc_organisations'
--

INSERT INTO soc_organisations (org_id, owner_id, `name`, contact_name, contact_email, url, description) VALUES
(1, 25, 'Apache Software Foundation', 'P Sharples', 'psharples@apache.org', 'http://www.apache.org', 'Established in 1999, the all-volunteer Foundation oversees nearly one hundred fifty leading Open Source projects, \r\nincluding Apache HTTP Server the world''s most popular Web server software. Through the ASF''s meritocratic process known as ''The Apache Way'', more than 350 individual Members and 3,000 Committers successfully collaborate to develop freely available enterprise-grade software, benefiting millions of users worldwide: thousands of software solutions are distributed under the Apache License; and the community actively participates in ASF mailing lists, mentoring initiatives, and ApacheCon, the Foundation''s official user conference, trainings, and expo. The ASF is a US 501(3)(c) not-for-profit charity, funded by individual donations and corporate sponsors including Citrix, Facebook, Google, Yahoo!, Microsoft, AMD, Basis Technology, Cloudera, Go Daddy, Hortonworks, HP, Huawei, InMotion Hosting, IBM, Matt Mullenweg, PSW GROUP, SpringSource/VMWare, and WANDisco.'),
(2, 25, 'Acme Foundation and so on', 'F Smith', 'fsmith@acme.org', 'http://www.acme.org', 'blah blah blah and more bla'),
(3, 26, 'Drupal', 'David Day', 'dday@drupal.org', 'http://www.drupal.org', 'blah blah '),
(4, 34, 'Groovy Community', 'Pat Garr', 'pgarr@groovy.org', 'http://www.groovy.org', 'The Groovy programming language for the JVM gathers a community and ecosystem around it made of various \r\nprojects, like web frameworks, testing libraries, concurrency toolkits, and more. The Groovy Community proposes \r\nto be the umbrella for all the project of the Groovy ecosystem.');

-- --------------------------------------------------------



--
-- Gegevens worden uitgevoerd voor tabel 'soc_projects'
--

INSERT INTO soc_projects (pid, owner_id, title, description, url, state, org_id, mentor_id, proposal_id, selected, tags) VALUES
(1, 0, 'Poor performance / OutOfMemoryError for sequences, choices and nested with large minOccurs/maxOccurs', 'We now handle large minOccurs/maxOccurs on element/wildcard particles more gracefully by creating a compact representation in the DFA and using counters to check the occurence constraints, however we will still fully expand the content model for minOccurs/maxOccurs on sequences and choices which could still lead to an OutOfMemoryError or very poor performance (i.e. could still take several minutes to build the DFA). Sequences, choices and nested minOccurs/maxOccurs are somewhat tricker to handle. We would need a more general solution than the one implemented for elements and wildcards to improve those. With the introduction of XML Schema 1.1 support we would also need to consider how to improve this for the enhanced xs:all model groups.', '', 'pending', 1, 0, 0, 0, 'java, php, python, javascript, c++, CSS, HTML, node.js, open source'),
(2, 0, 'Improvements to Autoscaling in Apache Stratos', 'You can find some details about autoscaling from [1] and [2]. 1. Improve Autoscaling to predict the number of instances required in the next time interval. Currently we predict the load for next time interval. Then we use a threshold to decide on scale up or down. Factors that we are considering are the requests in flight at load balancer and Load average and memory consumption of cartridge instance. Best approach would be to decide the number of instances that will be required to handle the load in next time interval. 2. Predict the load according to a schedule defined by end user. This aspect will be needed to handle seasonal load expectations. E.g. High load on Christmas and new year period. [1] http://www.sc.ehu.es/ccwbayes/isg/administrator/components/com_jresearch/files/publications/autoscaling.pdf [2] http://docs.aws.amazon.com/AutoScaling/latest/DeveloperGuide/WhatIsAutoScaling.html', '', 'pending', 1, 0, 0, 0, 'java, php, python, javascript, c++, CSS, HTML, node.js, open source'),
(3, 0, 'Optical Character Recognition (OCR)', 'Brief explanation: The PDFBox library is widely used to extract text from PDF files. However, many PDF files embed text in a malformed manner which renders text extraction useless. There has recently been interest in extracting governmental data from PDF files, the PDF Liberation commons being a notable example, see https://github.com/pdfliberation for more details. Many end-users of PDFBox have been making use of OCR tools such as Google''s Tesseract https://code.google.com/p/tesseract-ocr/ which are run on the final image generated by PDFBox. We think that by adding a more integrated OCR API to PDFBox it will be possible to do a better job. PDFBox often has access to encoding and positioning information for individual glyphs. Even when their extracted text is meaningless, a character-by-character, or line-by-line OCR could be more accurate. PDFBox also has information such as image orientation which could allow it to better perform OCR on pages such as embedded landscape tables.', '', 'pending', 1, 0, 0, 0, 'python, CSS, HTML, open source'),
(4, 0, 'Improve flexibility and testability of the classification module', 'Lucene classification module''s flexibility and capabilities may be improved with the following: make it possible to use them "online" (or provide an online version of them) so that if the underlying index(reader) is updated the classifier doesn''t need to be trained again to take into account newly added docs eventually pass a different Analyzer together with the text to be classified (or directly a TokenStream) to specify custom tokenization/filtering. - normalize score calculations of existing classifiers - provide publicly available dataset based accuracy and speed tests - more Lucene based classification algorithms. Specific subtasks for each of the above topics should be created to discuss each of them in depth.', '', 'pending', 1, 0, 0, 0, 'HTML, node.js, open source'),
(5, 0, 'Define SPARQL commands in Jena rules', 'The goal of this project is allow the definition of SPARQL commands in Jena rules. Thus, we increase the expressiveness of Jena. Something look alike is spin-rules, where SPIN means SPARQL Inferencing Notation, a SPARQL-based rule [1][2]. However, the purpose is not to implement SPIN in Jena but provide Jena with the mechanisms to take the same expressiveness as the spin frameworks. The main tasks of this project are: 1. Defining how a SPARQL command can be declared in a rule. This task encompass the discussion with the Jena community. 2. Provide Jena with the mechanisms defined in 1. ', '', 'pending', 1, 0, 0, 0, 'php, open source'),
(6, 0, 'Strong Gravitational Lens Time Delays and Detecting Strong lenses in Images', 'To apply new strong gravitational lensing time delay measurements which can enable constraints on dark energy. Quasar variability can be used to measure the time delay between two or more quasar images in a strongly lensed system. To achieve precision cosmological constraints, the error on this measurement needs to be reduced. The proposed approach will reduce this error for finding time delays. This project will also perform model based identification of strong gravitational lenses in Images.', '', 'pending', 2, 0, 0, 0, 'java, c++'),
(7, 0, 'OWASP PHP Security Project', 'OWASP PHPSEC is an effort by a group of developers in securing PHP web applications, using a collection of decoupled flexible secure PHP libraries, as well as a collection of PHP tools. On top of a collection of libraries and tools, PHPSEC contains a sample framework to demonstrate proper usage of the tools and libraries. It can also be easily merged with existing PHP code, because it is both decoupled and flexible. Proper usage of PHPSEC will result in the target system being much more secure.', '', 'pending', 2, 0, 0, 0, 'java, javascript'),
(8, 0, 'Marble Game', 'The project aims to design educational games using Marble.The user can click anywhere on the map and the details of the clicked division will be displayed in a pop up window. The game will also let the user select a particular division from a list of available divisions and learn about that geographical area. To test what have you learnt, the game will offer different types of quizzes.You can switch anytime from game mode to normal mode or vice-versa using a menu entry for the same.', '', 'pending', 2, 0, 0, 0, 'node.js'),
(9, 0, 'Full body and hands gestures tracking', 'Integration of full body motion and hand gesture tracking of astronauts to ERAS(European MaRs Analogue Station for Advanced Technologies Integration) virtual station. Skeleton tracking based feature extraction methods will be used for tracking whole body movements and hand gestures, which will have a visible representation in terms of the astronaut avatar moving in the virtual ERAS Station environment.', '', 'pending', 2, 0, 0, 0, 'java, c++'),
(10, 0, 'GLSpace', 'My project GLSpace is a 3D website that can be dynamically edited. It utilizes WebGL and works only on Firefox for now. My aim is to extend its compatibility on all the browsers and integrate ViewSync feature into it so you can render 3D websites on multiple screens. I think future "websites" should be like "halls", "webpages" should be like "walls of the hall" and "links" should be like "rooms in the hall". It''ll replace the way you surf the internet today with a more convenient manner.', '', 'pending', 2, 0, 0, 0, 'dot net, javascript'),
(11, 0, 'Image Pixel Based Photometric Redshift Estimation', 'Current techniques for photometric redshift estimation rely upon reduced integrated information from images. The information that is wasted on a pixel level can be made use of in order to get a better estimate. The performance of this technique can then be checked on different hardware like GPUs and against the machine learning algorithm used.', '', 'pending', 2, 0, 0, 0, 'java, c++'),
(12, 0, 'Improvements to the Geometry Module', 'The goal of this project is to improve the current geometry module such that it can include some important 3-D and 2-D classes and improve its current classes so that the geometry module works better as a whole.', '', 'pending', 2, 0, 0, 0, 'vb.net, asp.net'),
(13, 0, 'Systers Portal', 'Systers Portal is a unified platform for Systers and its sub-groups to share information and get the latest news. It solve the communication and interaction issue between groups.', '', 'pending', 2, 0, 0, 0, 'operating systems, linux'),
(14, 0, 'WYSIWYG inline entity', 'The project aims at building a framework to support embedding of entities in WYSIWYG editor. The single framework will be able to handle all different types of entities in general. Upon successful completion, this project will deliver a robust set of APIs to support embeds, integration with Ckeditor and a basic UI. The developed framework will be released in form of a module called WYSIWYG Inline entity.', '', 'open', 2, 0, 0, 1, 'cobol, pascal'),
(15, 0, 'Migrate DrupalLadder.org to Drupal 8', 'Project Description: drupalladder.org is a central place for people to find and contribute lessons and materials. drupalladder.org is built on the Drupal Ladder distrobution, which people can download and use on their personal computers to work through the Drupal Ladder lessons. Semi-related, Google Code-In students finished many tasks creating and updating lessons/ladders. If the project seems too basic, add a requirement that lessons are responsively viewed on mobile/tablet devices? Discussing a hosting infrastructure upgrade with migration, researching/recommending providers, and accomplishing the system administration tasks will help take project up a few levels with exposure to plenty of open-source tools along the way. Project Goal: Migrate DrupalLadder.org into newest version Drupal 8. Any new user interested in contributing to Drupal can find value in DrupalLadder.org. Not only will student migrate website into newest version of Drupal, but they will help fix/improve basic functionality that has been sadly ignored by a busy community. Project goal is for DrupalLadder to be even more accessibile to new users ultimatley allowing stronger growth of our community. Project Resources: Visit drupalladder.org, create a user account, and finish the "GSoC Student Ladder". It is clear to see the benefits of ladder system with the potential it contains, but is a bit clunky. Examples, the URLs of lessons/ladders are random characters, site is running multiple version updates behind, and lacks proper permissions blocking new contributor access. Maybe the domain should be forced to https similar to drupal.org? Layout with UI/UX is too basic and not mobile friendly. Plus site is not running on the newest version of Drupal which needs to be required moving forward. A great place to start learning more about the program is @ https://groups.drupal.org/drupal-ladder and of course creating an account at drupalladder.org. It will help students to finish multiple ladders to get started with Drupal faster. Do a bit more research on drupal.org about site migrations between Drupal versions. Which modules are running on druapalladders.org now that are not ready in Drupal 8 yet?', '', 'pending', 3, 0, 0, 0, 'drupal, php'),
(16, 0, 'Offline Sync - Content Staging Solution for Drupal 8', 'The idea is to create a drupal 7(or 8) module which allows persons to create many copies of a website for offline distributed content management. This would work by having one central website that all data is ultimately fed to. The other ''satellite'' websites(which can operate offline) have their own databases but keep track all new edits and content that it created. Once one of these sites goes online, an admin will have the option of synchronizing their data with the central ''main'' site. This solution can leverage the migrate module to achieve this: https://drupal.org/project/migrate. In Drupal 8 the IMP initiative adds a migration API that would help people migrating from D6 and D7 to D8, and to help developers to build migrations from whatever else to Drupal. But nothing stops you to migrate from D8 to D8, so this could provide a nice framework for implementing a content staging tool based on migrate API.', '', 'pending', 3, 0, 0, 0, 'drupal, php'),
(17, 0, 'Dial First Call and Send SMS via Drupal 8', 'VoIP Drupal is a versatile open source communication toolkit that adds the power of voice and Internet-telephony to Drupal websites. It can be used to build hybrid applications that combine regular touchtone phones, the Web, SMS, and other channels in a variety of ways, facilitating community outreach and providing an online presence even to those who are technically challenged, or who do not have regular access to computers.', '', 'pending', 3, 0, 0, 0, 'drupal, php'),
(18, 0, 'Port Apache Solr AJAX to Drupal 7', 'Project Description: Create a Drupal 7 module port of the current Drupal 6 module already accomplishing this functionality. Project Goal: Allow users to utilize Apache Solr searching functionality within an active page using AJAX. Project Resources: First step is to setup a testing server with Drupal and Apache Solr in Drupal 6 to see this type of functionality. It important to see how Solr runs parallel to Drupal and how/why Solr creates indexes. After you have a good understanding of Solr searching works in Drupal, then spend time reviewing the current Drupal 6 version of the module preparing for a port into Drupal 7. api.drupal.org will help show the diff between Drupal versions.', '', 'pending', 3, 0, 0, 0, 'drupal, php, ajax'),
(19, 0, 'Drupal 8 media subsystem', 'Media has been a challenging part of Drupal for a long time. For Drupal 8 we decided to go with new solution, that will be built using knowledge and experience from all solutions that are available for Drupal 7. This project will focus on this new solution. There are many subsystems which can be part of this project. This project will likely focus on one or two subsystems only, which will be decided together with a student (preferably before the student application). It is also possible to split this project into more smaller project that focus on different parts of the media ecosystem.', '', 'pending', 3, 0, 0, 0, 'drupal, php, media, css'),
(20, 0, 'Groovy on Android', 'Currently, Groovy is not able to run properly on Google''s Android mobile platform out of the box. A couple years ago, a first GSoC project (nicknamed DiscoBot), started porting Groovy to Android, using Groovy 1.7, but performance wasn''t there (20s to startup a simple Hello World). The goal of this GSoC project is to work with the Groovy core team and the past contributors of the DiscoBot project, towards the goal of making any Groovy program to run on the Android platform well, so that apps for such mobile phone can be written fully in Groovy.It will be interesting to investigate what modifications can be brought to Groovy to make it support Android in a more straightforward manner, how we can leverage static compilation capabilities, and also see how Groovy builders and other features can help further streamline the development of Android applications using Groovy.', '', 'pending', 4, 0, 0, 0, 'java, c++'),
(21, 0, 'An Antlr v4 grammar for Groovy', 'As of today, Groovy 2 still uses Antlr v2 for its grammar. The original grammar was based off of the Java grammar itself. But we would like to create a dedicated grammar for Groovy with the latest version of Antlr, ie. with version 4. Antlr v4 has evolved nicely and makes it easier to evolve grammars, without the painful work of rule disambiguation. So the idea is to develop a clean room implementation of the Groovy grammar for the upcoming versions of Groovy, that would be able to also cover new syntax elements, like the support of Java 8 lambda syntax, or the type annotation JSR, and we''d also take the opportunity to tackle things that we haven''t covered so far, like JavaDoc comments in the resulting AST.', '', 'pending', 4, 0, 0, 0, 'java, c++'),
(22, 0, 'Groovy and Java joint compiler without stubs', 'Groovy already has a joint compiler, but it works by producing stubs for groovy files. Not only does the resulting disc IO increase the time the compiler needs to compile something a lot, it also has certain limitations. Examples are unapplied xforms and certain situations where direct calls to the super class are required, but the joint compiler cannot produce them, because it has no idea how the class looks like. This proposal is to create a bridge between javac and groovyc internal data structures similar to what was been done for the groovy-eclipse compiler.', '', 'pending', 4, 0, 0, 0, 'java, c++');

-- --------------------------------------------------------


--
-- Gegevens worden uitgevoerd voor tabel 'soc_proposals'
--

INSERT INTO soc_proposals (proposal_id, owner_id, org_id, inst_id, supervisor_id, pid, title, solution_short, solution_long, modules, state) VALUES
(1, 31, 4, 3, 30, 20,  'Uni \r\nnog meer uni', 'test', 'meer details', 'jquery|ww.jquery.org|jaja', 'draft'),
(2, 31, 2, 3, 30, 10, 'a fantastic proposal', '', 'ja even wat hier', '', 'draft'),
(3, 31, 3, 3, 30, 17, 'some proposal', '', '', '', 'draft');

-- --------------------------------------------------------


--
-- Gegevens worden uitgevoerd voor tabel 'soc_studentgroups'
--

INSERT INTO soc_studentgroups (studentgroup_id, owner_id, inst_id, `name`, description) VALUES
(1,30, 3, 'Een of andere groep met Ã©Ã©n woord erin met acute', 'NIets te zeggen'),
(2, 30, 3, 'nog een groep maar nu zonder ', 'dusss'),
(3, 30, 3, 'Mijn klasje', 'Wiskunde B voor autisten'),
(4, 30, 3, 'Tabor', 'wis b bijles'),
(5, 29, 5, 'test', 'meer van dat'),
(21, 29, 5, 'meer van dat en zo', 'beschr'),
(22, 29, 5, 'nog een groep', 'klajsdlkfj');

-- --------------------------------------------------------


--
-- Gegevens worden uitgevoerd voor tabel 'soc_user_membership'
--

INSERT INTO soc_user_membership (mem_id, uid, `type`, group_id) VALUES
(1, 1, 'institute', 1),
(6, 27, 'institute', 5),
(7, 29, 'institute', 5),
(8, 30, 'institute', 3),
(36, 31, 'institute', 3),
(10, 31, 'institute', 3),
(9, 30, 'studentgroup', 2),
(11, 31, 'studentgroup', 2),
(12, 30, 'studentgroup', 3),
(13, 30, 'studentgroup', 4),
(14, 30, 'studentgroup', 1),
(15, 29, 'studentgroup', 5),
(31, 29, 'studentgroup', 21),
(32, 29, 'studentgroup', 22),

(40, 25, 'organisation', 1),  # orgadmin1 is a member of org1 (apache) owner is orgadmin1
(41, 25, 'organisation', 2),  # orgadmin1 is a member of org2 (acme) owner is orgadmin1
(42, 25, 'organisation', 3),  # orgadmin1 is a member of org3 (drupal) owner is orgadmin2
(43, 26, 'organisation', 3),  # orgadmin2 is a member of org3 (drupal) owner is orgadmin2
(44, 34, 'organisation', 4),  # orgadmin3 is a member of org4 (groovy) owner is orgadmin3
(45, 32, 'organisation', 2), # mentor1 is a member of org 3 (acme)
(46, 32, 'organisation', 3), # mentor1 is a member of org 3 (drupal)
(47, 36, 'organisation', 3), # mentor2 is a member of org 3 (drupal)
(48, 37, 'organisation', 4); # mentor3 is a member of org 4 (groovy)

-- --------------------------------------------------------
-- Can we keep these user/passwords the same. Records 25-32 passwords are the 
-- same as the username i.e. -u mentor1 -p mentor1.  i dont know what 33+34 are.
INSERT INTO users (uid, `name`, pass, mail, theme, signature, signature_format, created, access, login, `status`, timezone, `language`, picture, init, `data`) VALUES
(0, '', '', '', '', '', NULL, 0, 0, 0, 0, NULL, '', 0, '', NULL),
(1, 'admin', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin@raycom.com', '', '', 'filtered_html', 1394721311, 1401884149, 1401450007, 1, 'Europe/Paris', 'nl', 0, 'edwin@raycom.com', 'a:6:{s:16:"ckeditor_default";s:1:"t";s:20:"ckeditor_show_toggle";s:1:"t";s:14:"ckeditor_width";s:4:"100%";s:13:"ckeditor_lang";s:2:"en";s:18:"ckeditor_auto_lang";s:1:"t";s:7:"overlay";i:0;}'),
(27, 'instadmin', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+3@raycom.com', '', '', 'filtered_html', 1397210143, 1400577479, 1400257398, 1, 'Europe/Paris', 'es', 0, 'edwin+3@raycom.com', 'b:0;'),
(35, 'instadmin2', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+99@raycom.com', '', '', 'filtered_html', 1397210143, 1400577479, 1400257398, 1, 'Europe/Paris', 'es', 0, 'edwin+99@raycom.com', 'b:0;'),
(28, 'tutor1', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+4@raycom.com', '', '', 'filtered_html', 1397211191, 0, 0, 1, 'Europe/Paris', 'it', 0, 'edwin+4@raycom.com', NULL),
(29, 'tutor2', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+5@raycom.com', '', '', 'filtered_html', 1397211502, 1400752361, 1400676920, 1, 'Europe/Paris', 'en', 0, 'edwin+5@raycom.com', 'a:5:{s:16:"ckeditor_default";s:1:"t";s:20:"ckeditor_show_toggle";s:1:"t";s:14:"ckeditor_width";s:4:"100%";s:13:"ckeditor_lang";s:2:"en";s:18:"ckeditor_auto_lang";s:1:"t";}'),
(30, 'tutor3', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+6@raycom.com', '', '', 'filtered_html', 1397211598, 1399972053, 1397224028, 1, 'Europe/Paris', 'el', 0, 'edwin+6@raycom.com', 'b:0;'),
(31, 'student1', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+9@raycom.com', '', '', 'filtered_html', 1397222564, 1401449439, 1401447650, 1, 'Europe/Paris', 'en', 0, 'edwin+9@raycom.com', 'b:0;'),
(25, 'orgadmin1', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+1@raycom.com', '', '', 'filtered_html', 1397205806, 1400256334, 1400160734, 1, 'Europe/Paris', 'es', 0, 'edwin+1@raycom.com', 'a:5:{s:16:"ckeditor_default";s:1:"t";s:20:"ckeditor_show_toggle";s:1:"t";s:14:"ckeditor_width";s:4:"100%";s:13:"ckeditor_lang";s:2:"en";s:18:"ckeditor_auto_lang";s:1:"t";}'),
(26, 'orgadmin2', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+2@raycom.com', '', '', 'filtered_html', 1397209973, 0, 0, 1, 'Europe/Paris', 'es', 0, 'edwin+2@raycom.com', NULL),
(34, 'orgadmin3', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+12@raycom.com', '', '', 'filtered_html', 1400754935, 1400765047, 1400754987, 1, 'Europe/Paris', 'nl', 0, 'edwin+12@raycom.com', 'a:5:{s:16:"ckeditor_default";s:1:"t";s:20:"ckeditor_show_toggle";s:1:"t";s:14:"ckeditor_width";s:4:"100%";s:13:"ckeditor_lang";s:2:"en";s:18:"ckeditor_auto_lang";s:1:"t";}'),
(32, 'mentor1', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+21@raycom.com', '', '', 'filtered_html', 1400586065, 1400586093, 1400586093, 1, 'Europe/Paris', 'en', 0, 'edwin+21@raycom.com', 'b:0;'),
(36, 'mentor2', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+67@raycom.com', '', '', 'filtered_html', 1400586065, 1400586093, 1400586093, 1, 'Europe/Paris', 'en', 0, 'edwin+21@raycom.com', 'b:0;'),
(37, 'mentor3', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+68@raycom.com', '', '', 'filtered_html', 1400586065, 1400586093, 1400586093, 1, 'Europe/Paris', 'en', 0, 'edwin+21@raycom.com', 'b:0;'),
(33, 'Een mentor', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin+11@raycom.com', '', '', 'filtered_html', 1400753564, 0, 0, 1, 'Europe/Paris', 'en', 0, 'edwin+11@raycom.com', NULL);
-- --------------------------------------------------------

INSERT INTO users_roles (uid, rid) VALUES
(1, 3),
(31, 4),
(32, 5),
(36, 5),
(37, 5),
(25, 8),
(26, 8),
(34, 8),
(28, 9),
(29, 9),
(30, 9),
(33, 5),
(27, 12),
(35, 12);

-- -------------------------------------------------------------
DELETE from variable where name like 'vals_timeline%';
INSERT INTO `variable` VALUES 
('vals_timeline_program_active','i:1;'),
('vals_timeline_program_start_date','s:16:\"2014-07-01 00:00\";'),
('vals_timeline_program_end_date','s:16:\"2015-05-01 00:00\";'),
('vals_timeline_org_app_start_date','s:16:\"2014-07-21 00:00\";'),
('vals_timeline_org_app_end_date','s:16:\"2014-09-12 00:00\";'),
('vals_timeline_accepted_org_announced_date','s:16:\"2014-09-15 00:00\";'),
('vals_timeline_student_signup_start_date','s:16:\"2014-09-15 07:00\";'),
('vals_timeline_student_signup_end_date','s:16:\"2014-11-14 00:00\";'),
('vals_timeline_org_review_student_applications_date','s:16:\"2014-11-21 00:00\";'),
('vals_timeline_students_matched_to_mentors_deadline_date','s:16:\"2014-11-28 00:00\";'),
('vals_timeline_accepted_students_announced_deadline_date','s:16:\"2014-12-01 00:00\";'),
('vals_timeline_coding_start_date','s:16:\"2014-12-01 07:00\";'),
('vals_timeline_suggested_coding_deadline','s:16:\"2015-04-13 00:00\";'),
('vals_timeline_coding_end_date','s:16:\"2015-04-30 00:00\";');
-- The end