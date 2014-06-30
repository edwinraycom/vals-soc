<?php
/*Expects data for every tab:
 * [translate, label, action, type, id, extra GET arguments]
* translate can be: 0=label with sequence nr, 1=translate the text, 2=use the name etc as label (untranslated)
*/
function renderTabs($count, $tab_label, $target_label, $type, $data, $id=0,
	$render_targets=false, $active_content='', $active_tab=1, $parent_type='administration'){?>
	<ol id="toc"><?php
	$label_start = t($tab_label);
	$title = "";
	$label_nr = 1;
	for ($t=0; $t < $count;$t++){
		$target = $target_label.($t + 1); ?>
		<li><a href="#tab_<?php echo $target;?>" <?php
		//title
		if ($data[$t][0] == 1){
			$link_text = t($data[$t][1]);
			$title = "";
		} elseif ($data[$t][0] == 0) {
			$link_text = "$label_start $label_nr";
			$label_nr++;
			$title = " title = '".$data[$t][1]."' ";
		} else {
			$link_text = $data[$t][1];
			$title = "";
		}
		echo $title;

		//onclick action
		if (isset($data[$t][2])){
				$action = $data[$t][2];
				$type = isset($data[$t][3]) ? $data[$t][3] : $type;
				$id =  isset($data[$t][4]) ? $data[$t][4] : $id;
			if (isset($data[$t][5])){
				$action	.= "&".$data[$t][5];
			}
			echo "onclick=\"ajaxCall('$parent_type', '$action', {type:'$type', id:$id, target:'$target'}, '$target');\"";
		}

		?>><span><?php echo $link_text;?></span></a>
    	</li>
	<?php
	}?>
	</ol><?php
	if ($render_targets){
		for ($i=1; $i<= $count;$i++){
			echo "<div id='$target_label$i' class='content'>".
				"<div id='msg_$target_label$i'></div>".
				(($i == $active_tab) ? $active_content : '')."</div>";
		}
	}

}