<?php
include('include.php');
module_load_include('php', 'vals_soc', 'includes/functions/ajax_functions');
module_load_include('php', 'vals_soc', 'includes/classes/ThreadedComments');
module_load_include('php', 'vals_soc', 'includes/classes/ThreadUIBuilder');

switch ($_GET['action']){
	case 'save':
		$type = altSubValue($_POST, 'entity_type', '');
		$id = altSubValue($_POST, 'id', '');
		$entity_id = altSubValue($_POST, 'entity_id', '');
		$target = altSubValue($_POST, 'target', '');

		$properties = ThreadedComments::getInstance()->filterPostLite(ThreadedComments::getInstance()->getKeylessFields(), $_POST);
		$result = ThreadedComments::getInstance()->addComment($properties);
		$new = false;

		if ($result){
			echo json_encode(array(
					'result'=>TRUE,
					'id' => $result,
					'type'=> $type,
					'entity_id' => $entity_id,
					'msg'=> tt('You succesfully added a comment to this %1$s', t($type)). (_DEBUG ? showDrupalMessages(): '')
			));
		}
		else {
			echo jsonBadResult();
		}
		break;
	case 'view':
		$type = altSubValue($_POST, 'type');
		$id = altSubValue($_POST, 'id');
		$target = altSubValue($_POST, 'target', '');
		if (! ($id && $type && $target)){
			die(t('There are missing arguments. Please inform the administrator of this mistake.'));
		}
		$post = ThreadedComments::getInstance()->getPostById($id);
		if (! $post){
			echo tt('The post for this %1$s cannot be found', t($type));
		} else {
			$entity_id = $post['entity_id'];
			$entity_type = $post['entity_type'];
			$threaded_comments = new ThreadUIBuilder($entity_id, $entity_type);
			echo $threaded_comments->renderSingleComment($post);
		}
		break;
	case 'viewall':
		$type = altSubValue($_GET, 'type');
		$id = altSubValue($_GET, 'id');
		module_load_include('inc', 'vals_soc', 'includes/ui/comments/threaded_comments');
		$content = initComments($id, $type);
		echo $content;
		break;
	default: echo "No such action: ".$_GET['action'];
}