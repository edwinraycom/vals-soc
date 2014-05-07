<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('DRUPAL_ROOT', realpath(getcwd().'/../../../../..'));

require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);//Used to be DRUPAL_BOOTSTRAP_SESSION
switch ($_GET['action']){
    case 'showmembers':
        include(DRUPAL_ROOT.'/sites/all/modules/vals_soc/includes/classes/Participants.php');
        include(DRUPAL_ROOT.'/sites/all/modules/vals_soc/includes/module/ui/participant.inc');     
        if ($_POST['type'] == 'group'){
            renderParticipants('student', '', $_POST['group_id'], $_POST['type']);
            //renderStudents($_POST['group_id']);
        } elseif ($_POST['type'] == 'institute'){
            $type = altSubValue($_POST, 'subtype', 'all');
            if ($type == 'student'){
                $participants = new Participants();
                $students = $participants->getAllStudents($_POST['institute_id']);
                renderStudents('', $students);
            } elseif ($type == 'supervisor'){
                $participants = new Participants();
                $teachers = $participants->getSupervisors($_POST['institute_id']);
                renderSupervisors('', $teachers);
            }
                
        } elseif ($_POST['type'] == 'organisation'){
           $type = altSubValue($_POST, 'subtype', 'all');
           renderParticipants($type, '', $_POST['organisation_id']);
        }
     break;
    case 'edit':
        include(DRUPAL_ROOT.'/sites/all/modules/vals_soc/includes/classes/Participants.php');
        if ($_POST['type'] == 'group'){
            renderParticipants('student', '', $_POST['group_id'], $_POST['type']);
            //renderStudents($_POST['group_id']);
        } elseif ($_POST['type'] == 'institute'){
            //$type = altSubValue($_POST, 'subtype', 'all');
            
//                $participants = new Participants();
                $inst_id = $_POST['id'];

                $inst = Participants::getOrganisation('institute', $inst_id);
                //print_r($inst);
                $f1 = drupal_get_form('vals_soc_institute_form', $inst);
                print drupal_render($f1);
//                $students = $participants->getAllStudents($_POST['institute_id']);
//                renderStudents('', $students);
            
                
        } elseif ($_POST['type'] == 'organisation'){
           $type = altSubValue($_POST, 'subtype', 'all');
           renderParticipants($type, '', $_POST['organisation_id']);
        }
        
    break;
    case 'save':
        include(DRUPAL_ROOT.'/sites/all/modules/vals_soc/includes/classes/Participants.php');
        $type = $_POST['type'];
        $id = $_POST['id'];
        //TODO do some checks here
        $organisation = Participants::filterPost($type, $_POST);
        $result = Participants::updateOrganisation($type, $organisation, $id);
        if ($result){
            echo json_encode(['result'=>TRUE]);
        } else {
            echo json_encode(['result'=> 0, 'msg'=> t('Something went wrong')]);
        }
        /*
         * 
        if ($_POST['type'] == 'group'){
            renderParticipants('student', '', $_POST['group_id'], $_POST['type']);
            //renderStudents($_POST['group_id']);
        } elseif ($_POST['type'] == 'institute'){
            //$type = altSubValue($_POST, 'subtype', 'all');
            
//                $participants = new Participants();
                $inst_id = $_POST['id'];
                echo "Binnengekregen $inst_id;";
                print_r($_POST);
//                $inst = Participants::getOrganisation('institute', $inst_id);
//                //print_r($inst);
//                $f1 = drupal_get_form('vals_soc_institute_form', $inst);
//                print drupal_render($f1);
//                $students = $participants->getAllStudents($_POST['institute_id']);
//                renderStudents('', $students);
            
                
        } elseif ($_POST['type'] == 'organisation'){
           $type = altSubValue($_POST, 'subtype', 'all');
           renderParticipants($type, '', $_POST['organisation_id']);
        }
         */
        
    break;
    default: echo "no such action";
}