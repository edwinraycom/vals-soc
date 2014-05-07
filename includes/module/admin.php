<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/tabs.css');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/module/ui/tabs/activatetabs.js');
drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/ajax.js');
module_load_include('inc', 'vals_soc', 'includes/install/vals_soc.roles');
module_load_include('php', 'vals_soc', 'includes/classes/Participants');
module_load_include('inc', 'vals_soc', 'includes/module/ui/participant');
//To test we switch here the user
//31 student stuutje, 30 tutor zelfstandig, 27 salamanca (org inst), 25 orgadmin, 1 admin

//$GLOBALS['user'] = user_load(27,  TRUE);
$role = getRole();
//$role='institute_admin';
$role='organisation_admin';

/*
global $user;
foreach ($user->roles as $role) {
	echo '<b>role='.$role.'</b><br>';
	
}
if (user_access('administer site configuration')) {
	echo '<b>IS SITE ADMIN</b><br>';
}
*/

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
    break;
    case 'institute_admin':
        //Get my institutions
        $institutes = Participants::getOrganisations('institute', $GLOBALS['user']->uid);
        if (! $institutes->rowCount()){
            echo "You have no institute yet registered";
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
            <?php
        }
    break;
    case 'organisation_admin':
        echo '<h2>'.t('Your student groups').'</h2>';
        renderGroups();
    break;
}
?>

<script type="text/javascript">
activatetabs('tab_', ['page-1', 'page-2']);
activatetabs('tab_', ['inst_page-1', 'inst_page-2']);
</script>
<?php
echo "</div>";//end of admin_container

switch ($role){
    case 'administrator':
        echo '<h2>'.t('Administer the groups, institutes and organisations').'</h2>';
        $f1 = drupal_get_form('vals_soc_organisation_form');
        print drupal_render($f1);
    break;
    case 'supervisor':
        echo '<h2>'.t('Add a group to your list of groups').'</h2>';
        $f2 = drupal_get_form('vals_soc_group_form');
        print drupal_render($f2);
    break;
    case 'institute_admin':
//        echo '<h2>'.t('Add your institute').'</h2>';
//        $f3 = drupal_get_form('vals_soc_institute_form');
//        print drupal_render($f3);
    break;
    case 'organisation_admin':
        echo '<h2>'.t('Add your organisation').'</h2>';
        $f4 = drupal_get_form('vals_soc_organisation_form');
        print drupal_render($f4);
    break;
}