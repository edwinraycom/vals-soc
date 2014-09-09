<?php
class ThreadedComments extends AbstractEntity {
	
	private static $instance;
	
	public static $fields = array('id', 'parent_id', 'entity_id', 'entity_type', 'author', 
			'date_posted', 'description');
	
	public static function getInstance(){
		if (is_null ( self::$instance )){
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	public function getKeylessFields(){
		return array_slice(ThreadedComments::$fields, 1);
	}
	
	function getPostById($id, $fetch_style=PDO::FETCH_ASSOC){
		$query = db_select('soc_comments', 'c');
		$query->join('users', 'u', 'c.author = u.uid');
		$query->fields('c', self::$fields);
		$query->fields('u',  array('name'));
		$query->condition('c.id', $id);
		$post = $query->execute()->fetch($fetch_style);
		return $post;
	}
	/*
	function getPostById($id){
		$queryString = "SELECT s.*, u.name FROM soc_comments s, users u WHERE u.uid = s.author" .
		" AND s.id = ".$id .";";
		$post = db_query($queryString)->fetchAll(PDO::FETCH_ASSOC);
		return $post;
	}
	*/
	
	function getThreadsForEntity($entity_id, $entity_type){
		$queryString = "SELECT s.*, u.name FROM soc_comments s, users u WHERE entity_id=" . $entity_id .
			" AND entity_type='" . $entity_type . "' AND u.uid = s.author;";
		$result = db_query($queryString)->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	function addComment($props){
		if (! $props){
			drupal_set_message(t('Insert requested with empty (filtered) data set'), 'error');
			return false;
		}

		
		global $user;
		$txn = db_transaction();
		try {
			$uid = $user->uid;
			$props['author'] = $uid;
			$now = new DateTime();
			$props['date_posted'] = $now->format('Y-m-d H:i:s');
			// check for top level posts with an empty parent & set it to mysql null.
			if(!isset($props['parent_id']) || empty($props['parent_id'])) { 
				$props['parent_id'] = null;
			}
			$result = FALSE;
			$query = db_insert(tableName('comment'))->fields($props);
			$id = $query->execute();
			if ($id){
				$result = $id;
			}
			else {
				drupal_set_message(t('We could not add your comment'), 'error');
			}
		}
		catch (Exception $ex) {
			$txn->rollback();
			drupal_set_message(t('We could not add your comment. '). (_DEBUG? $ex->__toString(): ''), 'error');
		}
		return $result;
	}
}