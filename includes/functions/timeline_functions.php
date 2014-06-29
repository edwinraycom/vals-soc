<?php
include(_VALS_SOC_ROOT.'/includes/functions/tab_functions.php');

function showProgramTimelinePage(){
	$timeline = ProgramTimeline::getTimelineById($GLOBALS['user']->uid);
	if (! $timeline->rowCount()){
		echo t('You have no timeline set');
		$add_tab = '<h2>'.t('Add your timeline').'</h2>';

		$f3 = drupal_get_form('vals_soc_program_timeline_form', '', 'group_page-1');
		$add_tab .= drupal_render($f3);
		$data = array();
		$data[] = array(1, 'Add', 'add', 'timeline', null, "target=admin_container");
		echo renderTabs(1, null, 'inst_page-', 'timeline', $data, null, TRUE, $add_tab, 1,'timeline');
		?>
		<script type="text/javascript">
			activatetabs('tab_', ['inst_page-1']);
		</script><?php

	} else {
		$my_timeline = $timeline->fetchObject();
		echo sprintf('<h3>%1$s</h3>', t('Your timeline'));

        $nr = 2;
		$data = array();
		$tabs = array();
		$data[] = array(2, t('Your timeline'), 'view', 'timeline', $my_timeline->timeline_id);
		$data[] = array(1, 'Edit', 'edit', 'timeline', $my_timeline->timeline_id);
		$tabs = array("'inst_page-1'", "'inst_page-2'");

		//[number of tabs, label start, tab id start, type, data, id, render targets, active target content, active tab]
		echo renderTabs($nr, '', 'inst_page-', 'timeline', $data, $my_timeline->timeline_id, TRUE,
			renderTimeline('timeline', $my_timeline, null, "inst_page-1"), 1,'timeline');
		echo "<hr>";
		?>
		<script type="text/javascript">
			activatetabs('tab_', [<?php echo implode(',', $tabs);?>]);

		</script>
	    <?php
	}
}

function renderTimeline($type, $timeline='', $timeline_owner='', $target=''){
	if (!$timeline){
		$timelines = ProgramTimeline::getTimelineById($GLOBALS['user']->uid);
		$timeline = $timelines->fetchObject();
	}
	$key_name = ProgramTimeline::keyField($type);
	$id = $timeline->$key_name;

	if ($timeline){
		$pPath=request_path();
		$delete_action = "onclick='if(confirm(\"".t('Are you sure?')."\")){ajaxCall(\"timeline\", \"delete\", {type: \"$type\", id: $id, path: \"$pPath\"}, \"refreshTabs\", \"json\", [\"$type\", \"$target\"]);}'";
		$edit_action = "onclick='ajaxCall(\"timeline\", \"edit\", {type: \"$type\", id: $id, path: \"$pPath\", target: \"$target\"}, \"$target\");'";
		$s = '';
		//$s .=
		$s .= "<input type='button' value='".t('edit')."' $edit_action/>";
		$s .= "<input type='button' value='".t('delete')."' $delete_action/>";
		$s .= "<ul class='grouplist'>";
		foreach($timeline as $key => $val){

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
