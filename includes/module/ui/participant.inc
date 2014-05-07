<?php
 //drupal_add_js(drupal_get_path('module', 'vals_soc') .'/includes/js/jquery-1.10.2.js');
 
// drupal_add_library('system', 'drupal.ajax');
// drupal_add_library('system', 'jquery.form');
/* 
 * This file shows the various lists and views on the possible particpants to Semester of Code: organisations
 * instititutes, mentors, supervisors, students etc
 */
     
function EdwinCall(){
    $result = array('#type' => 'ajax');
/**
* ajax_command_replace
* ajax_command_insert
*/
$result['#commands'][] = ajax_command_html( '#ajax-response-goes-here', '<b>form html here</b>' );
# result will be blaced to <b>#add-new-comments-form-box</b>
return $result;
}

function some_callback_function(){
    return "DIT KOMT UIT DE CALLBACK";
    /*
     * //  echo "test<BR>";
//  $link = array(
//  '#type' => 'link',
//  '#title' => t('something'),
//  '#href' => '#',
//  '#ajax' => array(
//    'callback' => 'some_callback_function',
//    'wrapper' => 'ajax-response-goes-here',
//    'method' => 'replace',
//    'effect' => 'fade',
//  
//  ),
//);
//  echo "<div id='ajax-response-goes-here'></div>Some HTML and stuff" . drupal_render($link);

     */
}
 function renderGroups($supervisor_selection='', $groups=''){
    if (!$groups){
        $particpants = new Participants();
         //if we pass empty value to getGroups the current supervisor is assumed
        $groups = $particpants->getGroups(30);//$supervisor_selection););//TODO:  tijdelijk even 30
    }
    if ($groups){
        echo "<ul class='grouplist'>";
        foreach($groups as $group){
            echo "<li>";
           // $member_url = "/vals/actions/group" 
            echo "<a href='javascript: void(0);' onclick='ajaxCall(\"vals_soc\", \"showmembers\", {group_id:${group['group_id']},type:\"group\"}, null, null,\"html\", \"members_${group['group_id']}\");'>${group['name']}</a>: ${group['description']}";
            echo "<div id='members_${group['group_id']}'></div>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo t('You have no groups yet');
    }
}

function renderStudents($group_selection='', $students=''){

    if (!$students){
        $particpants = new Participants();
        //if we pass empty value to getStudents the current supervisor is assumed and we 
        //get all his students
        $students = $particpants->getStudents($group_selection);//$groups);//TODO: $groups tijdelijk even 30
    }
    if ($students->rowCount()){
        echo "<ul class='grouplist'>";
        foreach($students as $student){
            echo "<li>";
            echo $student->name.' : '. $student->mail;
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo t('There are no students yet in this group');
    }  
}

function renderSupervisors($group_selection='', $supervisors=''){
    if (!$supervisors){
        $particpants = new Participants();
        //if we pass empty value to getSupervisors the current institute_admin is assumed and we 
        //get all his supervisors in his/her institute
        $supervisors = $particpants->getSupervisors($group_selection);//$groups);//TODO: $groups tijdelijk even 30
    }
    if ($supervisors->rowCount()){
        echo "<ul class='grouplist'>";
        foreach($supervisors as $supervisor){
            echo "<li>";
            echo $supervisor->name.' : '. $supervisor->mail;
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo t('There are no supervisors yet in this institute');
    }
}
    
function renderParticipants($type='', $participants='', $group_selection='', $group_type=''){
    if (!$participants){
        $particpant_obj = new Participants();
        $participants = $particpant_obj->getParticipants($type, $group_type, $group_selection);
    }
    if ($participants->rowCount()){
        echo "<ul class='grouplist'>";
        foreach($participants as $member){
            echo "<li>";
            echo $member->name.' : '. $member->mail;
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo sprintf (t('There is no %1$s yet in this group'), $type);
    }  
}

function renderOrganisation($type, $organisation='', $organisation_owner=''){
    if (!$organisation){
        $organisations = Participants::getOrganisations($type, $organisation_owner);
        $organisation = $organisations->fetchObject();       
    }
    
    if ($organisation){
        echo "<ul class='grouplist'>";
        foreach($organisation as $key => $val){
            
            echo "<li>";
            echo $key.' : '. $val;
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo sprintf (t('You have no %1$s registered yet'), $type);
    }  
}