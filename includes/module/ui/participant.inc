<?php
/* 
 * This file shows the various lists and views on the possible Users to Semester of Code: organisations
 * instititutes, mentors, supervisors, students etc
 */

/**
 * Replaces the unordered list used show a member record
 * @param unknown $record - array of key => values (form)
 * @param string $member_type - used to show what type of entity is supposed to be used by the generated CODE
 * @return string - a bunch of HTML
 */
function formatMemberRecordNice($record, $type){
	$member_type='';
	switch ($type){
		case 'organisation': // organisations invite mentors
			$member_type = 'mentors';
			break;
		case 'institute': // institutes invite supervisors
			$member_type = 'supervisors';
			break;
		case 'studentgroup': // studentgroups invite students
			$member_type = 'students';
			break;
		default: $member_type = 'users';
	}
	
	$output='';
	$output .= '';
	$output .= '<dl class="view_record">';
	// check to see if there is an 'owner_id field'
	if(isset($record->owner_id)){
		$owner_details='';
		if(Users::getMyId() == $record->owner_id ){
			// owner is me
			$owner_details= t('You');
		}
		else{
			// else owner someone else, get their details
			$user = Users::getParticipantBasicSubset($record->owner_id);
			if (isset($user->fullname)){
				$owner_details= $user->fullname . ' (' . $user->mail . ')';
			}
			else{
				$owner_details= $user->name . ' (' . $user->mail . ')';
			}
		}
		$output .=  '<dt>';
		$output .=  t('Owner');
		$output .=  '</dt>';
		$output .=  '<span class="ui-icon ui-icon-arrowreturn-1-e"></span>';
		$output .=  '<dd>';
		$output .=  $owner_details;
		$output .=  '</dd>';
	}
	// just loop through the rest of the fields
	foreach($record as $key => $val){
		// don't show any field ending with '_id'
		if (substr_compare($key, '_id', -strlen('_id'))!= 0){
			$output .=  '<dt>';
			$output .=  ucfirst(str_replace('_', ' ', $key));
			$output .=  '</dt>';
			$output .=  '<span class="ui-icon ui-icon-arrowreturn-1-e"></span>';
			$output .=  '<dd>';
			$output .=  $val;
			if ($key=='code'){
				$output .= '<br/>';
				$output .= '<i>' . t('Use this code to invite your '. $member_type . ' to participate'). '</i>';
			}
			$output .=  '</dd>';
		}
	}
	$output .=  '</dl>';
	return $output;
}

function renderStudents($group_selection='', $students=''){

    if (!$students){
        //if we pass empty value to getStudents the current supervisor is assumed and we 
        //get all his students
        $students = Users::getStudents($group_selection);
    }
    if ($students && $students->rowCount()){
        $s = "<ul class='grouplist'>";
        foreach($students as $student){
            $s .= "<li>";
            $s .= $student->name.' : '. $student->mail;
            $s .= "</li>";
        }
        $s .= "</ul>";
        return $s;
    } else {
        return t('There are no students yet');
    }  
}

function renderSupervisors($group_selection='', $supervisors=''){
    if (!$supervisors){
        //if we pass empty value to getSupervisors the current institute_admin is assumed and we 
        //get all his supervisors in his/her institute
        $supervisors = Users::getSupervisors($group_selection);
    }
    if ($supervisors->rowCount()){
        $s = "<ul class='grouplist'>";
        foreach($supervisors as $supervisor){
            $s .= "<li>";
            $s .= $supervisor->name.' : '. $supervisor->mail;
            $s .= "</li>";
        }
        $s .= "</ul>";
        return $s;
    } else {
        return t('There are no supervisors yet in this institute');
    }
}
    
function renderUsers($type='', $users='', $group_selection='', $group_type=''){
	//If no Users dataset is passed on, retrieve them based on the other arguments
    if (!$users){
    	$users = Users::getUsers($type, $group_type, $group_selection);
        if (!($users && $users->rowCount())){
        	$users = null;
        }
    }
    if ($users && $users->rowCount()){
        $s = "<ul class='grouplist'>";
        foreach($users as $member){
            $s .=  "<li>";
            $s .= ($member->fullname ?: $member->name).' : '. $member->mail;
            $s .= "</li>";
        }
        $s .= "</ul>";
        return $s; 
    } else {
    	$group_type = $group_type ?: 'studentgroup';
        return $group_selection ? tt('There is no %1$s yet in this %2$s', t($type), t($group_type)) :
        	tt('You have no %1$s yet.', t($type));
    }  
}

function renderGroups($supervisor_selection='', $groups=''){
	if (!$groups){
		//if we pass empty value to getGroups the current supervisor is assumed
		$groups = Groups::getGroups($supervisor_selection);
	}
	if ($groups){
		$s = "<ul class='grouplist'>";
		foreach($groups as $group){
			$s .= "<li>";
			// $member_url = "/vals/actions/group"
			$s .= "<a href='javascript: void(0);' onclick='ajaxCall(\"administration\", \"showmembers\", {studentgroup_id:${group['studentgroup_id']},type:\"group\"}, \"members_${group['studentgroup_id']}\");'>${group['name']}</a>: ${group['description']}";
			$s .= "<div id='members_${group['studentgroup_id']}'></div>";
			$s .= "</li>";
		}
		$s .= "</ul>";
		return $s;
	} else {
		return t('You have no groups yet');
	}
}

function renderOrganisations($type='', $organisations='', $organisation_head='', $target=''){
	//If no organisations dataset is passed on, retrieve them based on the other arguments
	if (!$organisations){
		$organisations = Groups::getGroups($type, $organisation_head);
		if (!($organisations && $organisations->rowCount())){
			$organisations = null;
		}
	}
	if ($organisations && $organisations->rowCount()){
		$key = Groups::keyField($type);
		$s = "<ul class='grouplist'>";
		foreach($organisations as $member){
			$id = $member->$key;
			$s .=  "<li>";
			$s .= "<a href='javascript:void(0);' onclick=\"ajaxCall('administration', 'view', {type:'$type', id:$id, target:'$target'}, '$target');\">".$member->name."</a>";
			$s .= "</li>";
		}
		$s .= "</ul>";
		return $s;
	} else {
		$type = $type ?: 'studentgroup';
		return $organisation_head ? tt('You have no %1$s yet.', t($type)):
			tt('There is no %1$s yet.', t($type));
	}
}

function renderOrganisation($type, $organisation='', $organisation_owner='', $target='', $show_buttons=true){
    if (!$organisation){
        $organisations = Groups::getGroups($type, $organisation_owner);
        $organisation = $organisations->fetchObject();
    }
    $key_name = Groups::keyField($type);
    $id = $organisation->$key_name;
    if ($organisation){
    	$s = '';
    	if ($show_buttons && user_access('vals admin register')){
	    	$pPath=request_path();
	    	$delete_action = "onclick='if(confirm(\"".tt('Are you sure you want to delete this %1$s?', t($type))."\")){ajaxCall(\"administration\", \"delete\", {type: \"$type\", id: $id, path: \"$pPath\", target: \"$target\"}, \"refreshTabs\", \"json\", [\"$type\", \"$target\", \"administration\"]);}'";
	    	$edit_action = "onclick='ajaxCall(\"administration\", \"edit\", {type: \"$type\", id: $id, path: \"$pPath\", target: \"$target\"}, \"formResult\", \"html\", \"$target\");'";

	    	$s .= "<input type='button' value='".t('edit')."' $edit_action/>";
	    	$s .= "<input type='button' value='".t('delete')."' $delete_action/><br/><br/>";
	    	//$sub_type_user = '';
    	}
        $s .= formatMemberRecordNice($organisation, $type);
        return $s;
    } else {
        return tt('You have no %1$s registered yet', $type);
    }  
}