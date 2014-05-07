<?php

class Participants {
    public function getGroups($supervisor='')
    {
        global $user;
        
        $supervisor = $supervisor ?: $user->uid;
        //todo: find out whether current user is supervisor
        
        if ($supervisor == 'all'){
            $groups = db_select('soc_groups')->fields('soc_groups')->execute()->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $groups = db_select('soc_groups')->fields('soc_groups')->condition('supervisor_id', $supervisor)->
                execute()->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $groups;
    }
    
    public function getStudents($group=''){
        global $user;
        
        $supervisor = $user->uid;
        //todo: find out whether current user is supervisor
       
        if ($group == 'all'){
            $students = db_query('select * from users as u left join  users_roles as ur on u.uid=ur.uid left join  role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'student'));
        } elseif ($group) {
            $students = db_query("SELECT * from users as u left join soc_user_membership as um".
                " on u.uid = um.uid WHERE um.type = 'group' AND um.oid = $group AND u.uid != $supervisor ");
        } else {
            $groups = db_query("SELECT oid from soc_user_membership um".
                " WHERE um.type = 'group' AND um.uid = $supervisor ")->fetchCol();
            if ($groups){
                $students = db_query("SELECT * from users as u left join soc_user_membership um ".
                    "on u.uid = um.uid WHERE um.type = 'group' AND um.oid IN (:groups) AND u.uid != $supervisor ", array(':groups' => $groups));
            } else {
                return NULL;
            }
        }
        
        return $students;
    }    
    
    //TRY OUT
    public function getParticipants($member_type, $group_type='', $group_id='', $id='')
    {
        global $user;
        
        $group_head = $user->uid;
        //todo: find out whether current user is institute_admin
        
        if ($group_id == 'all'){
            $members = db_query(
                'select * from users as u '.
                'left join  users_roles as ur on u.uid=ur.uid '.
                'left join role as r on r.rid=ur.rid '.
                'WHERE r.name=:role ', array(':role' => $member_type));
        } else {
            if ($id){
                $members = db_query(
                    "SELECT u.* from users as u".
                    "WHERE u.uid = '$id'");
                
            } else {
                if (!$group_id) {
                     //get the institute from the institute admin
                    $group_id = db_query(
                        "SELECT oid from soc_user_membership um".
                        " WHERE um.type = '$group_type' AND um.uid = $group_head ")
                        ->fetchColumn();
                }
                if ($group_id){
                    $members = db_query(
                    "SELECT u.* from users as u left join users_roles as ur ".
                    " on u.uid = ur.uid left join role as r ".
                    " on ur.rid = r.rid left join soc_user_membership as um ".
                    " on u.uid = um.uid ".
                    "WHERE r.name = '$member_type' AND um.type = '$group_type' AND um.oid = $group_id ");
                } else {
                    return NULL;
                }
            }
            
        }
        
        return $members;
    }
        
    public function getParticipant($id=''){
        return $this->getParticipants('', '', '', $id)->fetchObject();
    }
    
    public static function getOrganisations($org_type, $group_head_id='', $id='')
    {
        global $user;
        
        $group_head = $user->uid;
        //todo: find out whether current user is institute_admin
        
        if ($group_head_id == 'all'){
            $members = db_query("SELECT o.* from soc_$org_type as o");
        } else {
            $key_columns = array(
                'group' => 'group_id', 
                'institute' => 'inst_id', 
                'organisation' => 'org_id');
            $key_column = $key_columns[$org_type];
            if ($id){
                $members = db_query(
                    "SELECT o.*, c.code from soc_${org_type}s as o ".                   
                    "left join soc_codes as c ".
                    " on o.$key_column = c.org ".
                    "WHERE o.$key_column = $id ");
            } else {
                $group_head_id = $group_head_id ?: $group_head;

                $members = db_query(
                    "SELECT o.*, c.code from soc_${org_type}s as o ".
                    "left join soc_user_membership as um ".
                    " on o.$key_column = um.oid ".
                    "left join soc_codes as c ".
                    " on o.$key_column = c.org ".
                    "WHERE um.type = '$org_type' AND um.uid = $group_head_id ");
            }
        }
        
        return $members;
    }
    
    public function getOrganisation($org_type, $id='')
    {
        return Participants::getOrganisations($org_type, '', $id)->fetchObject();
    }
    
    public function getAllStudents($institute='')
    {
        global $user;
        
        $institute_admin = $user->uid;
        //todo: find out whether current user is institute_admin
        
        if ($institute == 'all'){
            $supervisors = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
                'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'student'));
        } else {
            if (!$institute) {
                 //get the institute from the institute admin
                $institute = db_query("SELECT oid from soc_user_membership um".
                    " WHERE um.type = 'institute' AND um.uid = $institute_admin ")->fetchColumn();
            }
            if ($institute){
                $students = db_query("SELECT u.* from users as u left join users_roles as ur ".
                " on u.uid = ur.uid left join role as r ".
                " on ur.rid = r.rid left join soc_user_membership as um ".
                " on u.uid = um.uid WHERE r.name = 'student' AND um.type = 'institute' AND um.oid = $institute ");
            } else {
                return NULL;
            }
            
        }
        
        return $students;
    }
    
    public function getSupervisors($institute='')
    {
        global $user;
        
        $institute_admin = $user->uid;
        //todo: find out whether current user is supervisor

        if ($institute == 'all'){
            $supervisors = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
                'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'supervisor'));
        } elseif ($institute) {
            $supervisors = db_query("SELECT u.* from users as u left join users_roles as ur ".
                " on u.uid = ur.uid left join role as r ".
                " on ur.rid = r.rid left join soc_user_membership as um ".
                " on u.uid = um.uid WHERE r.name = 'supervisor' AND um.type = 'institute' AND um.oid = $institute AND u.uid != $institute_admin ");
        } else {
            //get the institute from the institute_admin
            $institute = db_query("SELECT oid from soc_user_membership um".
                " WHERE um.type = 'institute' AND um.uid = $institute_admin ")->fetchColumn();
            if ($institute){
                $supervisors = db_query("SELECT u.* from users as u left join soc_user_membership um ".
                    "on u.uid = um.uid WHERE um.type = 'institute' AND um.oid = $institute AND u.uid != $institute_admin ");
            } else {
                return NULL;
            }
        }
        return $supervisors;
    }
    
    public function getMentors($organisation='')
    {
        global $user;
        
        $organisation_admin = $user->uid;
        //todo: find out whether current user is org admin
        
        //get organisations
        if ($organisation == 'all'){
            $mentors = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
                'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'mentor'));
        } elseif ($organisation) {
           
            $mentors = db_query("SELECT u.* from users as u left join soc_user_membership as um".
                " on u.uid = um.uid WHERE um.type = 'institute' AND um.oid = $organisation AND u.uid != $organisation_admin ");
        } else {
            //get the organisation
            $organisation = db_query("SELECT oid from soc_user_membership um".
                " WHERE um.type = 'organisation' AND um.uid = $organisation_admin ")->fetchColumn();
            if ($organisation){
                $mentors = db_query("SELECT u.* from users as u left join soc_user_membership um ".
                    "on u.uid = um.uid WHERE um.type = 'organisation' AND um.oid = $organisation AND u.uid != $organisation_admin ");
            } else {
                return NULL;
            }
        }
        
        return $mentors;
    }
    
    public function getInstituteAdmins($institute='')
    {
        global $user;
        
        $institute_admin = $user->uid;
        //todo: find out whether current user is supervisor
        
        if ($institute == 'all'){
            $admins = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
                'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'institute_admin'));
        } else {
            if (!$institute) {
                $institute = db_query("SELECT oid from soc_user_membership um".
                " WHERE um.type = 'institute' AND um.uid = $institute_admin ")->fetchColumn();
            }
            if ($institute){
                //Get all the admins from this institute (1?: all users with role institute_admin who are member of this institute
                $admins = db_query("SELECT u.* from users as u left join users_roles as ur ".
                    " on u.uid = ur.uid left join role as r ".
                    " on ur.rid = r.rid left join soc_user_membership as um ".
                    " on u.uid = um.uid WHERE r.name = 'institute_admin'  AND um.type = 'institute' AND um.oid = $institute ");
            } else {
                return NULL;
            }
        }
        
        return $admins;
    }
    
     public function getOrganisationAdmins($organisation='')
     { 
        global $user;
        
        $organisation_admin = $user->uid;
        //todo: find out whether current user is organisation_admin
    
        if ($organisation == 'all'){
            $admins = db_query('select u.* from users as u left join  users_roles as ur on u.uid=ur.uid left join '.
                'role as r on r.rid=ur.rid where r.name=:role ', array(':role' => 'organisation_admin'));
        } else {
            if (!$organisation) {
                $organisation = db_query("SELECT oid from soc_user_membership um".
                " WHERE um.type = 'organisation' AND um.uid = $organisation_admin ")->fetchColumn();
            }
            if ($organisation){
                //Get all the admins from this organisation (1?: all users with role organisation_admin who are member of this organisation
                $admins = db_query("SELECT u.* from users as u left join users_roles as ur ".
                    " on u.uid = ur.uid left join role as r ".
                    " on ur.rid = r.name = 'organisation_admin' rid left join soc_user_membership as um ".
                    " on u.uid = um.uid WHERE r.um.type = 'organisation' AND um.oid = $organisation ");
            } else {
                return NULL;
            }
        }
        
        return $admins;
    }
    
    function updateOrganisation($type, $organisation, $id)
    {
        $key_columns = array(
            'group' => 'group_id', 
            'institute' => 'inst_id', 
            'organisation' => 'org_id'
        );
        
        return 
            db_update("soc_${type}s")
            ->condition($key_columns[$type], $id)
            ->fields($organisation)
            ->execute();
    }
    
    function filterPost($type, $post){
        $input = array();
        //todo: get the db fields from schema and move foreach out of switch
        switch ($type){
            case 'institute':
                foreach (array('name', 'contact_name', 'contact_email') as $prop){
                    if (isset($post[$prop])){
                        $input[$prop] = $post[$prop];
                    }
                }
            break;
            //etc
        }
        return $input;
    }
}