function renderProject(project, apply_projects){
	if (apply_projects){
		$jq.get( url('language','translate'), { words: ['Cancel','Submit proposal for this project'] }, function(result) {
	
			if(result){
				var parsed = JSON.parse(result);
				$jq("#vals-btn-cancel").prop('value', parsed[0]);
				$jq("#vals-btn-submit-proposal").prop('value', parsed[1]);
			}
			$jq('.totheright').show();
		});
	}
	var content = "<h2>"+project.title+"</h2>";
	content += project.description;
	if (apply_projects){
		content +="<div class=\"totheright\" style=\"display:none\">";
		//content +="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	
		content +="<br/><br/><input id='vals-btn-cancel' type='button' onclick=\"Drupal.CTools.Modal.dismiss()\" value='Cancel' />";
		content +="<input id='vals-btn-submit-proposal' type='button' onclick='getProposalFormForProject("+project.pid+")' value='Submit proposal for this project'/>";
		content +="</div>";
	}
	
	return content; 
}

function renderStudent(data){
	return data.name + 'is the name of the student. Fill this in later';
}

function renderProposalTabs(result, labels){
		var s = '<ol id="toc">';
		var count = labels.length;
		var target = '';
		for (var t=0; t < count;t++){
			target = labels[t].tab;
			s += '<li><a href="#tab_tab_'+ target +'" title="" ><span>'+labels[t].label+'</span></a></li>'; 

		}
		s += '</ol>';
		s += '<div class="tabs_container">';
		for (var t=0; t < count;t++){
			target = labels[t].tab;
			s += '<div id="tab_'+ target + '" class="content">';
			s += 	"<div id='msg_"+ target+ "'></div>";
			switch (target){
				case 'project': s += renderProject(result, false); 
					break;
				case 'student': s += renderStudent(result); 
					break;
				case 'cv': s += result.cv;
					break;
				case 'summary': s += result.solution_short;
					break;
				case 'solution': s += result.solution_long;
					break;
				case 'state': s += '?';
					break;
			}
			s += "</div>"; 

		}
		s += "</div>"; 
		return s;
		
	}