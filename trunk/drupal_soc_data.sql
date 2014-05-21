use drupal;

--
-- Dumping data for table `soc_codes`
--

LOCK TABLES `soc_codes` WRITE;
/*!40000 ALTER TABLE `soc_codes` DISABLE KEYS */;
INSERT INTO `soc_codes` VALUES 
(1,'administrator','BHTGSKKRWP',0,NULL),
(2,'soc','EICUN14SOC',0,NULL),
(3,'organisation_admin','AHGLL765OW',0,NULL),
(4,'supervisor','MNUFD783SF',0,NULL),
(5,'mentor','QTU8TR44KJ',0,NULL),
(6,'student','PGI99OPAQW',0,NULL),
(7,'institute_admin','A99ODF435W',0,NULL),
(8,'supervisor','AA',1,NULL);
/*!40000 ALTER TABLE `soc_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `soc_groups`
--

LOCK TABLES `soc_groups` WRITE;
INSERT INTO `soc_groups` (`group_id`, `owner_id`, `inst_id`, `name`, `description`, `supervisor_id`) VALUES
(1, 0, 3, 'Een of andere groep met één woord erin met acute', 'NIets te zeggen', 30),
(2, 0, 3, 'nog een groep maar nu zonder ', 'dusss', 30),
(3, 0, 3, 'Mijn klasje', 'Wiskunde B voor autisten', 30),
(4, 0, 3, 'Tabor', 'wis b bijles', 30),
(5, 0, 5, 'test', 'meer van dat', 29),
(6, 0, 0, 'test2', '', 0),
(7, 0, 0, 'aap', '', 0),
(8, 0, 0, 'aap', '', 0),
(9, 0, 0, 'apin', 'ge', 0),
(10, 0, 0, 'mogli', '', 0),
(11, 0, 0, 'qwertyy', '', 0),
(12, 0, 0, 'qwertyy', '', 0),
(13, 0, 0, 'qwerty keyboard', '', 0),
(14, 29, 5, 'tralalala', 'jajha', 29),
(15, 29, 5, 'tralalala2', 'jajha', 29),
(21, 29, 5, 'meer van dat en zo', 'beschr', 29),
(22, 29, 5, 'nog een groep', 'klajsdlkfj', 29),
(23, 29, 5, 'nog een groep', 'klajsdlkfj', 29);
UNLOCK TABLES;

--
-- Dumping data for table `soc_institutes`
--

LOCK TABLES `soc_institutes` WRITE;
INSERT INTO `soc_institutes` (`inst_id`, `owner_id`, `name`, `contact_name`, `contact_email`) VALUES
(1, 0, 'London University', 'John', 'johnny_walker@wiskey.com'),
(3, 0, 'Salamanca Universidad', 'JUan', 'juan@raycom.com'),
(5, 0, 'Salamanca Again', 'Me, who Else', 'edwin@raycom.com');
/*!40000 ALTER TABLE `soc_institutes` DISABLE KEYS */;
/*!40000 ALTER TABLE `soc_institutes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `soc_organisations`
--

LOCK TABLES `soc_organisations` WRITE;
/*!40000 ALTER TABLE `soc_organisations` DISABLE KEYS */;
INSERT INTO `soc_organisations` VALUES 
(1, 26, 'Apache Software Foundation','P Sharples','psharples@apache.org','http://www.apache.org',
"Established in 1999, the all-volunteer Foundation oversees nearly one hundred fifty leading Open Source projects, 
including Apache HTTP Server the world's most popular Web server software. Through the ASF's meritocratic process known as 'The Apache Way', more than 350 individual Members and 3,000 Committers successfully collaborate to develop freely available enterprise-grade software, benefiting millions of users worldwide: thousands of software solutions are distributed under the Apache License; and the community actively participates in ASF mailing lists, mentoring initiatives, and ApacheCon, the Foundation\'s official user conference, trainings, and expo. The ASF is a US 501(3)(c) not-for-profit charity, funded by individual donations and corporate sponsors including Citrix, Facebook, Google, Yahoo!, Microsoft, AMD, Basis Technology, Cloudera, Go Daddy, Hortonworks, HP, Huawei, InMotion Hosting, IBM, Matt Mullenweg, PSW GROUP, SpringSource/VMWare, and WANDisco."),
(2,26, 'Acme Foundation','F Smith','fsmith@acme.org','http://www.acme.org','blah blah blah'),
(3,26, 'Drupal','David Day','dday@drupal.org','http://www.drupal.org','blah blah blah'),
(4,26, 'Groovy Community','Pat Garr','pgarr@groovy.org','http://www.groovy.org',
"The Groovy programming language for the JVM gathers a community and ecosystem around it made of various 
projects, like web frameworks, testing libraries, concurrency toolkits, and more. The Groovy Community proposes 
to be the umbrella for all the project of the Groovy ecosystem.");


/*!40000 ALTER TABLE `soc_organisations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `soc_projects`
--

LOCK TABLES `soc_projects` WRITE;
/*!40000 ALTER TABLE `soc_projects` DISABLE KEYS */;
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (1,"Poor performance / OutOfMemoryError for sequences, choices and nested with large minOccurs/maxOccurs","We now handle large minOccurs/maxOccurs on element/wildcard particles more gracefully by creating a compact representation in the DFA and using counters to check the occurence constraints, however we will still fully expand the content model for minOccurs/maxOccurs on sequences and choices which could still lead to an OutOfMemoryError or very poor performance (i.e. could still take several minutes to build the DFA). Sequences, choices and nested minOccurs/maxOccurs are somewhat tricker to handle. We would need a more general solution than the one implemented for elements and wildcards to improve those. With the introduction of XML Schema 1.1 support we would also need to consider how to improve this for the enhanced xs:all model groups.",0,"pending",1,"java, php, python, javascript, c++, CSS, HTML, node.js, open source");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (2,"Improvements to Autoscaling in Apache Stratos","You can find some details about autoscaling from [1] and [2]. 1. Improve Autoscaling to predict the number of instances required in the next time interval. Currently we predict the load for next time interval. Then we use a threshold to decide on scale up or down. Factors that we are considering are the requests in flight at load balancer and Load average and memory consumption of cartridge instance. Best approach would be to decide the number of instances that will be required to handle the load in next time interval. 2. Predict the load according to a schedule defined by end user. This aspect will be needed to handle seasonal load expectations. E.g. High load on Christmas and new year period. [1] http://www.sc.ehu.es/ccwbayes/isg/administrator/components/com_jresearch/files/publications/autoscaling.pdf [2] http://docs.aws.amazon.com/AutoScaling/latest/DeveloperGuide/WhatIsAutoScaling.html",0,"pending",1,"java, php, python, javascript, c++, CSS, HTML, node.js, open source");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (3,"Optical Character Recognition (OCR)","Brief explanation: The PDFBox library is widely used to extract text from PDF files. However, many PDF files embed text in a malformed manner which renders text extraction useless. There has recently been interest in extracting governmental data from PDF files, the PDF Liberation commons being a notable example, see https://github.com/pdfliberation for more details. Many end-users of PDFBox have been making use of OCR tools such as Google's Tesseract https://code.google.com/p/tesseract-ocr/ which are run on the final image generated by PDFBox. We think that by adding a more integrated OCR API to PDFBox it will be possible to do a better job. PDFBox often has access to encoding and positioning information for individual glyphs. Even when their extracted text is meaningless, a character-by-character, or line-by-line OCR could be more accurate. PDFBox also has information such as image orientation which could allow it to better perform OCR on pages such as embedded landscape tables.",0,"pending",1,"python, CSS, HTML, open source");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (4,"Improve flexibility and testability of the classification module","Lucene classification module's flexibility and capabilities may be improved with the following: make it possible to use them \"online\" (or provide an online version of them) so that if the underlying index(reader) is updated the classifier doesn't need to be trained again to take into account newly added docs eventually pass a different Analyzer together with the text to be classified (or directly a TokenStream) to specify custom tokenization/filtering. - normalize score calculations of existing classifiers - provide publicly available dataset based accuracy and speed tests - more Lucene based classification algorithms. Specific subtasks for each of the above topics should be created to discuss each of them in depth.",0,"pending",1,"HTML, node.js, open source");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (5,"Define SPARQL commands in Jena rules","The goal of this project is allow the definition of SPARQL commands in Jena rules. Thus, we increase the expressiveness of Jena. Something look alike is spin-rules, where SPIN means SPARQL Inferencing Notation, a SPARQL-based rule [1][2]. However, the purpose is not to implement SPIN in Jena but provide Jena with the mechanisms to take the same expressiveness as the spin frameworks. The main tasks of this project are: 1. Defining how a SPARQL command can be declared in a rule. This task encompass the discussion with the Jena community. 2. Provide Jena with the mechanisms defined in 1. ",0,"pending",1,"php, open source");

INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (6,"Strong Gravitational Lens Time Delays and Detecting Strong lenses in Images","To apply new strong gravitational lensing time delay measurements which can enable constraints on dark energy. Quasar variability can be used to measure the time delay between two or more quasar images in a strongly lensed system. To achieve precision cosmological constraints, the error on this measurement needs to be reduced. The proposed approach will reduce this error for finding time delays. This project will also perform model based identification of strong gravitational lenses in Images.",0,"pending",2,"java, c++");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (7,"OWASP PHP Security Project","OWASP PHPSEC is an effort by a group of developers in securing PHP web applications, using a collection of decoupled flexible secure PHP libraries, as well as a collection of PHP tools. On top of a collection of libraries and tools, PHPSEC contains a sample framework to demonstrate proper usage of the tools and libraries. It can also be easily merged with existing PHP code, because it is both decoupled and flexible. Proper usage of PHPSEC will result in the target system being much more secure.",0,"pending",2,"java, javascript");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (8,"Marble Game","The project aims to design educational games using Marble.The user can click anywhere on the map and the details of the clicked division will be displayed in a pop up window. The game will also let the user select a particular division from a list of available divisions and learn about that geographical area. To test what have you learnt, the game will offer different types of quizzes.You can switch anytime from game mode to normal mode or vice-versa using a menu entry for the same.",0,"pending",2,"node.js");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (9,"Full body and hands gestures tracking","Integration of full body motion and hand gesture tracking of astronauts to ERAS(European MaRs Analogue Station for Advanced Technologies Integration) virtual station. Skeleton tracking based feature extraction methods will be used for tracking whole body movements and hand gestures, which will have a visible representation in terms of the astronaut avatar moving in the virtual ERAS Station environment.",0,"pending",2,"java, c++");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (10,"GLSpace","My project GLSpace is a 3D website that can be dynamically edited. It utilizes WebGL and works only on Firefox for now. My aim is to extend its compatibility on all the browsers and integrate ViewSync feature into it so you can render 3D websites on multiple screens. I think future \"websites\" should be like \"halls\", \"webpages\" should be like \"walls of the hall\" and \"links\" should be like \"rooms in the hall\". It'll replace the way you surf the internet today with a more convenient manner.",0,"pending",2,"dot net, javascript");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (11,"Image Pixel Based Photometric Redshift Estimation","Current techniques for photometric redshift estimation rely upon reduced integrated information from images. The information that is wasted on a pixel level can be made use of in order to get a better estimate. The performance of this technique can then be checked on different hardware like GPUs and against the machine learning algorithm used.",0,"pending",2,"java, c++");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (12,"Improvements to the Geometry Module","The goal of this project is to improve the current geometry module such that it can include some important 3-D and 2-D classes and improve its current classes so that the geometry module works better as a whole.",0,"pending",2,"vb.net, asp.net");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (13,"Systers Portal","Systers Portal is a unified platform for Systers and its sub-groups to share information and get the latest news. It solve the communication and interaction issue between groups.",0,"pending",2,"operating systems, linux");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (14,"WYSIWYG inline entity","The project aims at building a framework to support embedding of entities in WYSIWYG editor. The single framework will be able to handle all different types of entities in general. Upon successful completion, this project will deliver a robust set of APIs to support embeds, integration with Ckeditor and a basic UI. The developed framework will be released in form of a module called WYSIWYG Inline entity.",1,"open",2,"cobol, pascal");

INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (15,"Migrate DrupalLadder.org to Drupal 8","Project Description: drupalladder.org is a central place for people to find and contribute lessons and materials. drupalladder.org is built on the Drupal Ladder distrobution, which people can download and use on their personal computers to work through the Drupal Ladder lessons. Semi-related, Google Code-In students finished many tasks creating and updating lessons/ladders. If the project seems too basic, add a requirement that lessons are responsively viewed on mobile/tablet devices? Discussing a hosting infrastructure upgrade with migration, researching/recommending providers, and accomplishing the system administration tasks will help take project up a few levels with exposure to plenty of open-source tools along the way. Project Goal: Migrate DrupalLadder.org into newest version Drupal 8. Any new user interested in contributing to Drupal can find value in DrupalLadder.org. Not only will student migrate website into newest version of Drupal, but they will help fix/improve basic functionality that has been sadly ignored by a busy community. Project goal is for DrupalLadder to be even more accessibile to new users ultimatley allowing stronger growth of our community. Project Resources: Visit drupalladder.org, create a user account, and finish the \"GSoC Student Ladder\". It is clear to see the benefits of ladder system with the potential it contains, but is a bit clunky. Examples, the URLs of lessons/ladders are random characters, site is running multiple version updates behind, and lacks proper permissions blocking new contributor access. Maybe the domain should be forced to https similar to drupal.org? Layout with UI/UX is too basic and not mobile friendly. Plus site is not running on the newest version of Drupal which needs to be required moving forward. A great place to start learning more about the program is @ https://groups.drupal.org/drupal-ladder and of course creating an account at drupalladder.org. It will help students to finish multiple ladders to get started with Drupal faster. Do a bit more research on drupal.org about site migrations between Drupal versions. Which modules are running on druapalladders.org now that are not ready in Drupal 8 yet?",0,"pending",3,"drupal, php");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (16,"Offline Sync - Content Staging Solution for Drupal 8","The idea is to create a drupal 7(or 8) module which allows persons to create many copies of a website for offline distributed content management. This would work by having one central website that all data is ultimately fed to. The other 'satellite' websites(which can operate offline) have their own databases but keep track all new edits and content that it created. Once one of these sites goes online, an admin will have the option of synchronizing their data with the central 'main' site. This solution can leverage the migrate module to achieve this: https://drupal.org/project/migrate. In Drupal 8 the IMP initiative adds a migration API that would help people migrating from D6 and D7 to D8, and to help developers to build migrations from whatever else to Drupal. But nothing stops you to migrate from D8 to D8, so this could provide a nice framework for implementing a content staging tool based on migrate API.",0,"pending",3,"drupal, php");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (17,"Dial First Call and Send SMS via Drupal 8","VoIP Drupal is a versatile open source communication toolkit that adds the power of voice and Internet-telephony to Drupal websites. It can be used to build hybrid applications that combine regular touchtone phones, the Web, SMS, and other channels in a variety of ways, facilitating community outreach and providing an online presence even to those who are technically challenged, or who do not have regular access to computers.",0,"pending",3,"drupal, php");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (18,"Port Apache Solr AJAX to Drupal 7","Project Description: Create a Drupal 7 module port of the current Drupal 6 module already accomplishing this functionality. Project Goal: Allow users to utilize Apache Solr searching functionality within an active page using AJAX. Project Resources: First step is to setup a testing server with Drupal and Apache Solr in Drupal 6 to see this type of functionality. It important to see how Solr runs parallel to Drupal and how/why Solr creates indexes. After you have a good understanding of Solr searching works in Drupal, then spend time reviewing the current Drupal 6 version of the module preparing for a port into Drupal 7. api.drupal.org will help show the diff between Drupal versions.",0,"pending",3,"drupal, php, ajax");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (19,"Drupal 8 media subsystem","Media has been a challenging part of Drupal for a long time. For Drupal 8 we decided to go with new solution, that will be built using knowledge and experience from all solutions that are available for Drupal 7. This project will focus on this new solution. There are many subsystems which can be part of this project. This project will likely focus on one or two subsystems only, which will be decided together with a student (preferably before the student application). It is also possible to split this project into more smaller project that focus on different parts of the media ecosystem.",0,"pending",3,"drupal, php, media, css");

INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (20,"Groovy on Android","Currently, Groovy is not able to run properly on Google's Android mobile platform out of the box. A couple years ago, a first GSoC project (nicknamed DiscoBot), started porting Groovy to Android, using Groovy 1.7, but performance wasn't there (20s to startup a simple Hello World). The goal of this GSoC project is to work with the Groovy core team and the past contributors of the DiscoBot project, towards the goal of making any Groovy program to run on the Android platform well, so that apps for such mobile phone can be written fully in Groovy.It will be interesting to investigate what modifications can be brought to Groovy to make it support Android in a more straightforward manner, how we can leverage static compilation capabilities, and also see how Groovy builders and other features can help further streamline the development of Android applications using Groovy.",0,"pending",4,"java, c++");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (21,"An Antlr v4 grammar for Groovy","As of today, Groovy 2 still uses Antlr v2 for its grammar. The original grammar was based off of the Java grammar itself. But we would like to create a dedicated grammar for Groovy with the latest version of Antlr, ie. with version 4. Antlr v4 has evolved nicely and makes it easier to evolve grammars, without the painful work of rule disambiguation. So the idea is to develop a clean room implementation of the Groovy grammar for the upcoming versions of Groovy, that would be able to also cover new syntax elements, like the support of Java 8 lambda syntax, or the type annotation JSR, and we'd also take the opportunity to tackle things that we haven't covered so far, like JavaDoc comments in the resulting AST.",0,"pending",4,"java, c++");
INSERT INTO soc_projects (pid,title,description,selected,state,oid,tags) VALUES (22,"Groovy and Java joint compiler without stubs","Groovy already has a joint compiler, but it works by producing stubs for groovy files. Not only does the resulting disc IO increase the time the compiler needs to compile something a lot, it also has certain limitations. Examples are unapplied xforms and certain situations where direct calls to the super class are required, but the joint compiler cannot produce them, because it has no idea how the class looks like. This proposal is to create a bridge between javac and groovyc internal data structures similar to what was been done for the groovy-eclipse compiler.",0,"pending",4,"java, c++");
/*!40000 ALTER TABLE `soc_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `soc_user_membership`
--
LOCK TABLES `soc_user_membership` WRITE;
INSERT INTO `soc_user_membership` (`mem_id`, `uid`, `type`, `oid`) VALUES
(1, 1,'institute',1),
(3, 25, 'organisation', 3),
(4, 26, 'organisation', 3),
(6, 27, 'institute', 5),
(7, 29, 'institute', 5),
(8, 30, 'institute', 3),
(9, 30, 'group', 2),
(10, 31, 'institute', 3),
(11, 31, 'group', 2),
(12, 30, 'group', 3),
(13, 30, 'group', 4),
(14, 30, 'group', 1),
(15, 29, 'group', 5),
(16, 0, 'group', 6),
(17, 0, 'group', 7),
(18, 0, 'group', 8),
(19, 0, 'group', 9),
(20, 0, 'group', 10),
(21, 0, 'group', 11),
(22, 0, 'group', 12),
(23, 0, 'group', 13),
(24, 29, 'group', 14),
(25, 29, 'group', 15),
(31, 29, 'group', 21),
(32, 29, 'group', 22),
(33, 29, 'group', 23),
(34, 32, 'organisation', 3),
(35, 32, 'organisation', 3);
UNLOCK TABLES;

TRUNCATE TABLE `users`;

--
-- Gegevens worden uitgevoerd voor tabel `users`
--

INSERT INTO `users` (`uid`, `name`, `pass`, `mail`, `theme`, `signature`, `signature_format`, `created`, `access`, `login`, `status`, `timezone`, `language`, `picture`, `init`, `data`) VALUES
(0, '', '', '', '', '', NULL, 0, 0, 0, 0, NULL, '', 0, '', NULL),
(1, 'admin', '$S$DpBo9xxVTOGQhuXOY5YfmrGKLIp0JgJxotQ73/PdK1cFrITWLlpw', 'edwin@raycom.com', '', '', 'filtered_html', 1394721311, 1400587637, 1400587637, 1, 'Europe/Paris', 'nl', 0, 'edwin@raycom.com', 'a:6:{s:16:"ckeditor_default";s:1:"t";s:20:"ckeditor_show_toggle";s:1:"t";s:14:"ckeditor_width";s:4:"100%";s:13:"ckeditor_lang";s:2:"en";s:18:"ckeditor_auto_lang";s:1:"t";s:7:"overlay";i:1;}'),
(25, 'orgadmin', '$S$DYWYtzyJ5vQ9ycprFQIidBsQopzehN2SeIH0FuLOeFLJowXklBOA', 'edwin+1@raycom.com', '', '', 'filtered_html', 1397205806, 1400256334, 1400160734, 1, 'Europe/Paris', 'es', 0, 'edwin+1@raycom.com', 'a:5:{s:16:"ckeditor_default";s:1:"t";s:20:"ckeditor_show_toggle";s:1:"t";s:14:"ckeditor_width";s:4:"100%";s:13:"ckeditor_lang";s:2:"en";s:18:"ckeditor_auto_lang";s:1:"t";}'),
(26, 'orgadmin2', '$S$Dzy.Xc.PKl3sDQQ3eOCGwHlgvKUc7mIWoSmIxCRpt23ZXkgXaetJ', 'edwin+2@raycom.com', '', '', 'filtered_html', 1397209973, 0, 0, 1, 'Europe/Paris', 'es', 0, 'edwin+2@raycom.com', NULL),
(27, 'instadmin', '$S$DDxmcp7.O2PU.IwYoJnCh9RjtIoHbfME0I8qRvIvMGTJ7VEskgbW', 'edwin+3@raycom.com', '', '', 'filtered_html', 1397210143, 1400577479, 1400257398, 1, 'Europe/Paris', 'es', 0, 'edwin+3@raycom.com', 'b:0;'),
(28, 'tutor1', '$S$DVTNls.DSTIcWxJJtwBQ0loQcqoBFLpvyBBo8BkP68X01CaJOjuD', 'edwin+4@raycom.com', '', '', 'filtered_html', 1397211191, 0, 0, 1, 'Europe/Paris', 'it', 0, 'edwin+4@raycom.com', NULL),
(29, 'tutor2', '$S$DVZPzlQUv2lSre4/ZCPKKB1Ru6MoeNp7THcCytAtXGzEe7NwdwLm', 'edwin+5@raycom.com', '', '', 'filtered_html', 1397211502, 1400599171, 1400587679, 1, 'Europe/Paris', 'en', 0, 'edwin+5@raycom.com', 'a:5:{s:16:"ckeditor_default";s:1:"t";s:20:"ckeditor_show_toggle";s:1:"t";s:14:"ckeditor_width";s:4:"100%";s:13:"ckeditor_lang";s:2:"en";s:18:"ckeditor_auto_lang";s:1:"t";}'),
(30, 'tutor3', '$S$DnIPP8LTBFbSQpJf3qI0/Qbw1aZoANRh1y91zLEHURCStVCncznK', 'edwin+6@raycom.com', '', '', 'filtered_html', 1397211598, 1399972053, 1397224028, 1, 'Europe/Paris', 'el', 0, 'edwin+6@raycom.com', 'b:0;'),
(31, 'student1', '$S$D2aE50D5yhDu70.5bdo3nB02t9INLlyRKiKhiaDxt6Xx2AReK6MJ', 'edwin+9@raycom.com', '', '', 'filtered_html', 1397222564, 0, 0, 1, 'Europe/Paris', 'en', 0, 'edwin+9@raycom.com', NULL),
(32, 'mentor1', '$S$Dq2t3M2XTxsR./.brs7wPBu6k.q4K75GoTYOS5uNvaFFtl88SJ4t', 'edwin+10@raycom.com', '', '', 'filtered_html', 1400586065, 1400586093, 1400586093, 1, 'Europe/Paris', 'en', 0, 'edwin+21@raycom.com', 'b:0;');


LOCK TABLES `soc_codes` WRITE;
TRUNCATE TABLE `soc_codes`;
INSERT INTO `soc_codes` (`code_id`, `type`, `code`, `org`, `group_id`) VALUES
(1, 'administrator', 'BHTGSKKRWP', 0, 0),
(2, 'institute_admin', 'A99ODF435W', 0, 0),
(3, 'mentor', 'QTU8TR44KJ', 3, 0),
(4, 'organisation_admin', 'AHGLL765OW', 0, 0),
(5, 'soc', 'EICUN14SOC', 0, 0),
(6, 'student', 'PGI99OPAQW', 0, 0),
(7, 'supervisor', 'MNUFD783SF', 0, 0),
(8, 'group', 'MNU77783SF', 5, 22),
(9, 'group', 'MPOPUFD783', 5, 23);

UNLOCK TABLES;

TRUNCATE TABLE `role`;
INSERT INTO `role` (`rid`, `name`, `weight`) VALUES
(3, 'administrator', 2),
(1, 'anonymous user', 0),
(2, 'authenticated user', 1),
(12, 'institute_admin', 2),
(5, 'mentor', 4),
(8, 'organisation_admin', 3),
(4, 'student', 6),
(9, 'supervisor', 5);

TRUNCATE TABLE `users_roles`;
INSERT INTO `users_roles` (`uid`, `rid`) VALUES
(1, 3),
(31, 4),
(32, 5),
(25, 8),
(26, 8),
(28, 9),
(29, 9),
(30, 9),
(27, 12);
