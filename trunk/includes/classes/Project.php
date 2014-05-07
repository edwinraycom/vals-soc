<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Project {
	
	private static $instance;
	
	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}
    
    public function getProjects(){
    	$projects = db_select('soc_projects')->fields('soc_projects')->execute()->fetchAll(PDO::FETCH_ASSOC);
    	return $projects;
    }
}
