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
	var s = '<ol>';
	s += '<li>name: '+(data.student_name ? data.student_name: data.name)+ '</li>';
	s += '<li>email: '+data.mail+ '</li>';
	s += '<li>Institute: '+data.i_name+ '</li>';
	s += '<li>Supervisor: '+(data.supervisor_name ? data.supervisor_name: data.supervisor_user_name)+ '</li>';
	s += '<li>Supervisor email: '+data.supervisor_user_mail+ '</li>';	
	s += '</ol>';
	return s;
}

/*This function renders the proposal as tabs and places it also in the right target */
function getProposalDetail(proposal_id, target, msg){
	var tabs = [{tab: 'project', label: 'Project'},
				{tab: 'student', label: 'Student'},
				{tab: 'cv', label: 'Cv'},
				{tab: 'summary', label: 'Solution Summary'},
				{tab: 'solution', label: 'Solution'},
				{tab: 'state', label: 'State'}];
	var content_tabs = ['tab_project', 'tab_student', 'tab_cv', 'tab_summary', 'tab_solution', 
				      	'tab_state'];

  	if (window.view_settings.apply_projects){
  		tabs.push({tab: 'edit', label: 'Edit'});
		content_tabs.push('tab_edit');
	}
	var url = moduleUrl + "actions/proposal_actions.php?action=proposal_detail&proposal_id=" + proposal_id;
	if ((typeof target == 'undefined')) {
		target = 'modal';
	}
	$jq.get(url,function(data,status){
		
		if (data.result == 'error'){
			alert('Could not retrieve well the saved data');
			return;
		}
		var msg_container = 'modal-content';
		//var msg_before = 'toc';
		
		switch (target){
		
			case 'modal': generateAndPopulateModal(data, renderProposalTabs, tabs);break;
			case 'content' :
				var data2 = jQuery.parseJSON(data);
				console.log(data2.result);
				var content = renderProposalTabs(data2.result, tabs, 'content');
				msg_container = 'content';
				//msg_before = 'vals-soc-proposal-form';
				Obj('content').html(content);			
			break ;
			default: populateModal(data, renderProposalTabs, tabs);
		}
		activatetabs('tab_', content_tabs);
      	if (typeof msg != 'undefined' && msg){
      		 ajaxAppend(msg, msg_container, 'status', 'toc');
      	};
		
		});
	
}		

function getProposalFormForProject(projectId){
	Drupal.CTools.Modal.dismiss();
	//With formResult it will turn all textareas in rte fields and with handleResult, it just copies the
	//form and places everything in the target content
	//possible forms: ajaxCall(module, action, data, handleResult, json, args)
	// ajaxCall(module, action, data, target, html)
	//Note that formResult and jsonFormResult store the call in the target and convert the textareas
	ajaxCall("student", "proposal", {id: projectId, target:'content'}, "formResult", 'html', 'content');
	//ajaxCall("student", "proposal", {id: projectId, target:'content'}, 'content');
}

function renderProposalTabs(result, labels, container){
	var s = '<ol id="toc">';
	var count = labels.length;
	var target = '';
	var onclick = '';
	if (typeof container == 'undefined'){
		container = 'tab_edit';
	}
	for (var t=0; t < count;t++){
		target = labels[t].tab;
		if (target == 'edit'){
			onclick = "onclick=\"ajaxCall('proposal', 'proposal_edit', {proposal_id:"+
				result.proposal_id+ ", target:'"+container+"'}, 'jsonFormResult', 'json', ['"+container+"']);\"";
		} else {
			onclick = '';
		}
		s += '<li><a id="tab_tab_'+ target +'" href="#tab_tab_'+ target +'" title="" '+onclick+'><span>'+labels[t].label+'</span></a></li>'; 

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
			case 'edit': s += '';
			break;
		}
		s += "</div>"; 

	}
	s += "</div>"; 
	return s;
}

//function getProposalFormForProject(projectId){
//	//TODO is the call to dismiss necessary?
//	Drupal.CTools.Modal.dismiss();
//	ajaxCall("student", "proposal", {id: projectId, target: target_id}, "content");
//}

function getProjectDetail(projectId){	
	var url = moduleUrl + "actions/project_actions.php?action=project_detail&project_id=" + projectId;
	//TODO: currently the apply projects is passed around as global. not so elegant
	$jq.get(url,function(data,status){
		generateAndPopulateModal(data, renderProject, window.view_settings.apply_projects);
	});
}
	
function testTagInput() {
	var filter = /^[a-z0-9+_.\s]+$/i;
	if (filter.test($jq("#tags").val()) || $jq("#tags").val()=="") {
		$jq("#tags").removeClass("error");
		$jq("#infotext").removeClass("error");
		$jq("#infotext").text("");
		return true;
	}
	else {
		$jq("#tags").addClass("error");
		$jq("#infotext").addClass("error");
		$jq("#infotext").text("Invalid character/s entered");
		return false;
	}
}