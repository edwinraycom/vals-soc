<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
module_load_include('php', 'vals_soc', 'includes/classes/Timeline'); // old version
module_load_include('php', 'vals_soc', 'includes/functions/timeline_functions');
module_load_include('php', 'vals_soc', 'includes/classes/ProgramTimeline'); //new version
switch ($_GET['action']){
	case 'delete':
		$type = altSubValue($_POST, 'type', '');
		$id = altSubValue($_POST, 'id', '');
		$result = ProgramTimeline::removeTimeline($type, $id);
		echo $result ? jsonGoodResult() : jsonBadResult();
		break;
	case 'edit':
		$type = altSubValue($_POST, 'entity_type', '');
		$id = altSubValue($_POST, 'id', '');
		$target = altSubValue($_POST, 'target', '');

		$obj = ProgramTimeline::getTimeline($GLOBALS['user']->uid);
		$originalPath = false;
		if(isset($_POST['path'])){
			$originalPath = $_POST['path'];
		}
		unset($_POST);

		$form = render(drupal_get_form("vals_soc_program_timeline_form", $obj, $target));
		// Generate the settings:
		$settings = FALSE;
		$javascript = drupal_add_js(NULL, NULL);
		if(isset($javascript['settings'], $javascript['settings']['data'])){
			$settings = '<script type="text/javascript">jQuery.extend(Drupal.settings, ';
			$settings .= drupal_json_encode(call_user_func_array('array_merge_recursive', $javascript['settings']['data']));
			$settings .=  ');</script>';
		}
		// Return the rendered form and the settings
		 die($form . $settings);
	break;
	case 'save':
		$type = altSubValue($_POST, 'entity_type', '');
		$id = altSubValue($_POST, 'id', '');
		$properties = ProgramTimeline::getInstance()->filterPostLite(ProgramTimeline::getInstance()->getFields(), $_POST);
		
		if (!$id){
			$result = ProgramTimeline::getInstance()->addTimeline($properties);
		} 
		else {
			$result = ProgramTimeline::getInstance()->changeTimeline($properties, $id);
		}
		if ($result){
			echo json_encode(array(
				'result'=>TRUE,
				'id' => $id,
				'type'=> $type,
				'msg'=>
				($id ? tt('You succesfully changed the data of your %1$s', t($type)):
					tt('You succesfully added your %1$s', t($type))).
				(_DEBUG ? showDrupalMessages(): '')
				));
		}
		else {
			echo jsonBadResult();
		}
		break;
	case 'view':
		$timelines = ProgramTimeline::getTimelineById($GLOBALS['user']->uid);
		$timeline = $timelines->fetchObject();
		$output = renderTimeline('timeline', $timeline, '', 'inst_page-1');
		echo $output;
		break;
	case 'show':
		showProgramTimelinePage(getRole());
		break;
	// everything below is from the old single instance timeline	
	case 'setdate':
		if(isset($_POST['date'])){
			$now = Timeline::getInstance($_POST['date'])->getNow();
			echo $now->format('F j, Y, g:i a');
		}
		else{
			echo t("No date submitted!");
		}
		break;
	default: echo "No such action: ".$_GET['action'];
}
