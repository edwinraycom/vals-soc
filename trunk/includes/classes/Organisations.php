<?php
class Organisations {

	private static $instance;

	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}

	public function getOrganisations(){
		return db_query("SELECT o.org_id, o.name FROM soc_organisations o;");
	}
}