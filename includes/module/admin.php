<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/tabs.css');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/activatetabs.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/ajax.js');
module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
module_load_include('php', 'vals_soc', 'includes/classes/Participants');
module_load_include('inc', 'vals_soc', 'includes/module/ui/participant');
//To test we switch here the user
//31 student stuutje, 30 tutor zelfstandig, 27 salamanca (org inst), 25 orgadmin, 1 admin

//$GLOBALS['user'] = user_load(30,  TRUE);
$role = getRole();
//$role='institute_admin';
//$role='organisation_admin';

/*
global $user;
foreach ($user->roles as $role) {
	echo '<b>role='.$role.'</b><br>';
	
}
if (user_access('administer site configuration')) {
	echo '<b>IS SITE ADMIN</b><br>';
}
*/
function renderTabs($count, $tab_label, $target_label, $type, $data, $actions, $id=''){?>
	<ol id="toc"><?php
	$label_start = t($tab_label);
	$title = "";
	for ($t=0; $t < $count;$t++){
		$target = $target_label.($t + 1); ?>
		<li><a href="#tab_<?php echo $target;?>" <?php
		//title
		if ($data[$t][0]){
			$link_text = t($data[$t][1]);
			$title = "";
		} else {
			$link_text = $label_start.' '.($t + 1);
			$title = " title = '".$data[$t][1]."' ";
		}
		echo $title;
		
		//onclick action
		if (isset($data[$t][2])){
			//if (is_array($data[$t])){
				$action = $data[$t][2];
				$type = isset($data[$t][3]) ? $data[$t][3] : $type;
				$id =  isset($data[$t][4]) ? $data[$t][4] : $id;
// 			} else {
// 				$action = $data[$t];
// 			}
			if (isset($data[$t][5])){
				$action	.= "&".$data[$t][5];
			}
			echo "onclick=\"ajaxCall('vals_soc', '$action', {type:'$type', id:$id}, '$target');\"";
		}
		
		?>><span><?php echo $link_text;?></span></a>
    	</li>
	<?php
	}?>
	</ol><?php
}

echo '<BR>In admin.php: I am a '.$role;
echo "<div id='admin_container' class='tabs_container'>";
switch ($role){
    case 'administrator':
        echo '<h2>'.t('Your groups').'</h2>';
        renderGroups();
    break;
    case 'supervisor':
        echo '<h2>'.t('Your student groups').'</h2>';
        renderGroups();
        //Get my groups
        $groups = Participants::getOrganisations('group', $GLOBALS['user']->uid);
        if (! $groups->rowCount()){
        	echo t('You have no group yet registered');
        	echo '<h2>'.t('Add your group').'</h2>';
        	$f3 = drupal_get_form('vals_soc_group_form');
        	print drupal_render($f3);
        } else {
        	//$my_group = $groups->fetchObject();
        	//print_r($groups);
        	$nr = 1;
        	//$labels = array();
        	$data = array();
        	foreach ($groups as $group){
        		if ($nr == 1){
        			$id = $group->group_id;
        			$my_group = $group;
        		}
        		$nr++;
        		$data[] = array(0, $group->name, 'view', 'group', $group->group_id);
        		//$actions[] = array(); 
        	}
        	$data[] = array(1, 'Add', 'addgroup', 'group', null, "target=group_page-$nr");
        	//$labels[] = array();
        	//print_r($actions);print_r($labels); echo "en we hebben $id";
        	echo sprintf('<h3>%1$s</h3>', t('Your groups'));
        	//$count, $tab_label, $target_label, $type, $data, $id=''
        	echo renderTabs($nr, 'Group', 'group_page-', 'group', $data, $id);
        	?>
            	    
                     <div class="content" id="group_page-1">
                         <?php renderOrganisation('group', $my_group);?>
                     </div>
                     <?php
                     //Add the remaining target divs and gather the labels to activate
                     $activating_tabs = array("'group_page-1'");
                     for($g=2; $g <= $nr;$g++){
                     	echo "<div class='content' id='group_page-$g'></div>";
                     	$activating_tabs[] = "'group_page-$g'";
                     }
         
                     echo "<hr>";
                     echo '<h2>'.t('All the registered students of your groups').'</h2>';?>
            	     <ol id="toc">
                         <li><a href="#tab_page-1"><span><?php echo t('All Students');?></span></a></li>
                     </ol>
                     <div class="content" id="page-1">
                         <?php renderParticipants('student', '', $my_group->group_id, 'group');?>
                     </div>
                     
                     <script type="text/javascript">
         				activatetabs('tab_', [<?php echo implode(', ', $activating_tabs);?>]);
         				activatetabs('tab_', ['page-1']);
         			</script>
                     <?php
                 }
        
    break;
    case 'institute_admin':
        //Get my institutions
        $institutes = Participants::getOrganisations('institute', $GLOBALS['user']->uid);
        if (! $institutes->rowCount()){
            echo t('You have no institute yet registered');
            echo '<h2>'.t('Add your institute').'</h2>';
            $f3 = drupal_get_form('vals_soc_institute_form');
            print drupal_render($f3);
        } else {
            $my_institute = $institutes->fetchObject();
            echo sprintf('<h3>%1$s</h3>', t('Your institute'));?>
            <ol id="toc">
                <li><a href="#tab_inst_page-1"><span><?php echo $my_institute->name;?></span></a></li>
                <li><a href="javascript:void(0);" data-target='#tab_inst_page-2' onclick="ajaxCall('vals_soc', 'edit', {type:'institute', id:<?php echo $my_institute->inst_id;?>}, 'inst_page-2');"><span><?php echo t('Edit');?></span></a></li>
            </ol>
            <div class="content" id="inst_page-1">
                <?php renderOrganisation('institute', $my_institute);?>
            </div>
            <div class="content" id="inst_page-2">
            </div><?php

            echo "<hr>";

            echo '<h2>'.t('The registered students and supervisors').'</h2>';?>
            <ol id="toc">
                <li><a href="#tab_page-1"><span><?php echo t('Students');?></span></a></li>
                <li><a href="#tab_page-2"><span><?php echo t('Supervisors');?></span></a></li>
            </ol>
            <div class="content" id="page-1">
                <?php renderParticipants('student', '', $my_institute->inst_id, 'institute');?>
            </div>
            <div class="content" id="page-2">
                <?php renderParticipants('supervisor', '', $my_institute->inst_id, 'institute');?>
            </div>
            <script type="text/javascript">
				activatetabs('tab_', ['page-1', 'page-2']);
				activatetabs('tab_', ['inst_page-1', 'inst_page-2']);
			</script>
            <?php
        }
    break;
    case 'organisation_admin':
    	//Get my organisations
    	$organisations = Participants::getOrganisations('organisation', $GLOBALS['user']->uid);
    	if (! $organisations->rowCount()){
    		echo t('You have no organisation yet registered');
    		echo '<h2>'.t('Add your organisation').'</h2>';
    		$f3 = drupal_get_form('vals_soc_organisation_form');
    		print drupal_render($f3);
    	} else {
    		$my_organisation = $organisations->fetchObject();
    	    echo sprintf('<h3>%1$s</h3>', t('Your organisation'));
    	    //$count, $tab_label, $target_label, $type, $data, $actions, $id='')
    	    $label_data = array(
    	    	array(1, 'Organisation', 'view', null, $my_organisation->org_id),
    	    	(array(1, 'Edit', null, $my_organisation->org_id)));
    	    echo renderTabs(2, 'Organisation', 'org_page-', 'organisation',
    	    	$label_data,  $my_organisation->org_id);
    	    ?>
    	    
             <div class="content" id="org_page-1">
                 <?php renderOrganisation('organisation', $my_organisation);?>
             </div>
             <div class="content" id="org_page-2"></div><?php
 
             echo "<hr>";
    	
             echo '<h2>'.t('The registered mentors of your organisation').'</h2>';?>
    	     <ol id="toc">
                 <li><a href="#tab_page-1"><span><?php echo t('Mentors');?></span></a></li>
             </ol>
             <div class="content" id="page-1">
                 <?php renderParticipants('mentor', '', $my_organisation->org_id, 'organisation');?>
             </div>
             
             <script type="text/javascript">
 				activatetabs('tab_', ['org_page-1', 'org_page-2']);
 				activatetabs('tab_', ['page-1']);
 			</script>
             <?php
         }
     break;
}
echo "</div>";//end of admin_container

switch ($role){
    case 'administrator':
        echo '<h2>'.t('Administer the groups, institutes and organisations').'</h2>';
        $f1 = drupal_get_form('vals_soc_organisation_form');
        print drupal_render($f1);
    break;
    case 'supervisor':
      
    break;
    case 'institute_admin':
//        echo '<h2>'.t('Add your institute').'</h2>';
//        $f3 = drupal_get_form('vals_soc_institute_form');
//        print drupal_render($f3);
    break;
    case 'organisation_admin':
//         echo '<h2>'.t('Add your organisation').'</h2>';
//         $f4 = drupal_get_form('vals_soc_organisation_form');
//         print drupal_render($f4);
    break;
}