<?php
/* 
 * This file shows the various lists and views on the possible Users to Semester of Code: organisations
 * instititutes, mentors, supervisors, students etc
 */

function renderStudents($group_selection='', $students=''){

    if (!$students){
        //if we pass empty value to getStudents the current supervisor is assumed and we 
        //get all his students
        $students = Users::getStudents($group_selection);
    }
    if ($students->rowCount()){
        $s = "<ul class='grouplist'>";
        foreach($students as $student){
            $s .= "<li>";
            $s .= $student->name.' : '. $student->mail;
            $s .= "</li>";
        }
        $s .= "</ul>";
        return $s;
    } else {
        return t('There are no students yet in this group');
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

function renderProjects($organisation_selection='', $projects='', $target=''){
	if (!$projects){
		//if we pass empty value to getGroups the current supervisor is assumed
		$projects = Groups::getGroups($organisation_selection);
	}
	$target_set = ! empty($target);
	if ($projects){
		$s = "<ul class='projectlist'>";
		foreach($projects as $project){
			$project = objectToArray($project);
			$s .= "<li>";
			// $member_url = "/vals/actions/project"
			if (!$target_set) {
				$target = "show_${project['pid']}";
			}
			$s .= "<a href='javascript: void(0);' onclick='".
			//($target_set ? "" : "\$jq(\"#$target\").toggle();").
			"ajaxCall(\"administration\", \"view\", {id:${project['pid']},type:\"project\", target:\"$target\"}, \"$target\");'>${project['title']}</a>";
			if (! $target_set) {
				$s .= "<div id='$target' ></div>";
			}
			$s .= "</li>";
		}
		$s .= "</ul>";
		return $s;
	} else {
		return t('You have no projects yet');
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

function renderOrganisation($type, $organisation='', $organisation_owner='', $target=''){
    if (!$organisation){
        $organisations = Groups::getGroups($type, $organisation_owner);
        $organisation = $organisations->fetchObject();
    }
    
    $key_name = Groups::keyField($type);
    $id = $organisation->$key_name;
    if ($organisation){
    	$pPath=request_path();
    	$delete_action = "onclick='if(confirm(\"".t('Are you sure?')."\")){ajaxCall(\"administration\", \"delete\", {type: \"$type\", id: $id, path: \"$pPath\"}, \"refreshTabs\", \"json\", [\"$type\", \"$target\"]);}'";
    	$edit_action = "onclick='ajaxCall(\"administration\", \"edit\", {type: \"$type\", id: $id, path: \"$pPath\", target: \"$target\"}, \"$target\");'";
    	$s = '';
    	//$s .= 
    	$s .= "<input type='button' value='".t('edit')."' $edit_action/>";
    	$s .= "<input type='button' value='".t('delete')."' $delete_action/>";
        $s .= "<ul class='grouplist'>";
        foreach($organisation as $key => $val){
            
            $s .=  "<li>";
            $s .=  $key.' : '. $val;
            $s .=  "</li>";
        }
        $s .=  "</ul>";
        return $s;
    } else {
        return tt('You have no %1$s registered yet', $type);
    }  
}