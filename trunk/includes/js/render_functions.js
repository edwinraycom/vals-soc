function renderProposalOverview(proposal){
	var content = "<h2>"+proposal.title+"</h2>";
	content += 'Submitted by student \'' + (proposal.student_name ? proposal.student_name: proposal.name) + '\'';
	content += '<br/>';
	content += '<ul>';
	content += '	<li>';
	content += '		This is the Overview tab, where you can also add comments to this proposal.';
	content += '	</li>'
	content += '	<li>';
	content += '		The \'Student Details\' tab lists both the students, the institution he/she is attending and his/her supervisor.';
	content += '	</li>';
	content += '	<li>';
	content += '	The \'Solution Summary\' tab is where the student was asked to provide a brief synopsis of the solution.';
	content += '	</li>';
	content += '	<li>';
	content += '		The \'Solution Description\' tab is where the student was asked to provide a more detailed description of the solution.';
	content += '	</li>';
	content += '	<li>';
	content += '		The \'Original Project\' tab is for your reference and links to the original project idea.';
	content += '	</li>';
	content += '</ul>';
	// comments
	content += "<div id=\"comments-proposal-"+proposal.proposal_id+"\"></div>";
	// go and get the comments asynch...
	getCommentsForEntity(proposal.proposal_id, 'proposal','comments-proposal-'+proposal.proposal_id);
	//
	
	return content;
}

function renderProject(project, apply_projects){

	var content = "<h2>"+project.title+"</h2>";
	content += project.description;
	if(project.url){
		content += "<br/><a target='_blank' class='external' href='" + project.url + "'>" + project.url + "</a>";
	}

	// comments
	content += "<div id=\"comments-project-"+project.pid+"\"></div>";
	// go and get the comments asych...
	getCommentsForEntity(project.pid, 'project','comments-project-'+project.pid);
	//
	if (apply_projects){
		content +="<div class=\"totheright\" style=\"display:none\">";
		//content +="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		//content +="<br/><br/><input id='vals-btn-cancel' type='button' onclick=\"Drupal.CTools.Modal.dismiss()\" value='Cancel' />";
		content +="<input id='vals-btn-submit-proposal' type='button' onclick='getProposalFormForProject("+project.pid+")' value='Submit proposal for this project'/>";
		content +="</div>";

		content +=
			"<script type='text/javascript'>"+
				"$jq.get( url('language','translate'), { words: ['Cancel','Submit proposal for this project'] }, function(result) {"+
				"if(result){"+
					"var parsed = JSON.parse(result);"+
					//"$jq('#vals-btn-cancel').prop('value', parsed[0]);"+
					"$jq('#vals-btn-submit-proposal').prop('value', parsed[1]);"+
				"}"+
				"$jq('.totheright').show();"+
				" });"+
			"</script>";
	}

	return content;
}

function renderOrganisation(org){
	var content = "<h1>"+org.name+"</h1>";
	content += "<h3>Information</h3>"+ org.description;
//	if (apply_projects){
//		content +="<div class=\"totheright\" style=\"display:none\">";
//		//content +="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
//
//		content +="<br/><br/><input id='vals-btn-cancel' type='button' onclick=\"Drupal.CTools.Modal.dismiss()\" value='Cancel' />";
//		content +="<input id='vals-btn-submit-proposal' type='button' onclick='getProposalFormForProject("+project.pid+")' value='Submit proposal for this project'/>";
//		content +="</div>";
//	}
	content += "<br/><h3>Website</h3><a href='"+org.url+"'>"+org.url+"</a>";
	return content;
}

function renderInstitute(ins){
	var content = "<h1>"+ins.name+"</h1>";
	content += "<h3>Contact information</h3>"+
	'<div style="padding-left:5px;">'+
	'Name:' + ins.contact_name + '<br/>'+
	'Email: '+ ins.contact_email + '</div>';
	return content;
}

function renderStudent(data){
	var s = '<ol>';
	s += '<li>Name: '+(data.student_name ? data.student_name: data.name)+ '</li>';
	s += '<li>Email: '+data.mail+ '</li>';
	s += '<li>Institute: '+data.i_name+ '</li>';
	s += '<li>Supervisor: '+(data.supervisor_name ? data.supervisor_name: data.supervisor_user_name)+ '</li>';
	s += '<li>Supervisor email: '+data.supervisor_user_mail+ '</li>';
	s += '</ol>';
	return s;
}

/*This function renders the proposal as tabs and places it also in the right target */
function getProposalDetail(proposal_id, target, msg){
	var tabs = [
	            {tab: 'overview', label: 'Overview'},
				{tab: 'student', label: 'Student Details'},
				//{tab: 'cv', label: 'Cv'},
				{tab: 'summary', label: 'Solution Summary'},
				{tab: 'solution', label: 'Solution Description'},
				{tab: 'project', label: 'Project'}
				//{tab: 'modules', label: 'Modules and Libraries'}
			];
	var content_tabs = ['tab_overview',
	                    'tab_student',
	                    //'tab_cv',
	                     'tab_summary', 'tab_solution'
	                     ,'tab_project'
				      	//,'tab_modules'
	                    ];
	var url = moduleUrl + "actions/proposal_actions.php?action=proposal_detail&proposal_id=" +
		proposal_id;

  	if (window.view_settings.apply_projects){
  		tabs.push({tab: 'edit', label: 'Edit'});
		content_tabs.push('tab_edit');
		tabs.push({tab: 'delete', label: 'Delete'});
		content_tabs.push('tab_delete');
	}

	if ((typeof target == 'undefined')) {
		target = 'modal';
	}
	//Get the details and render with renderProposalTabs
	$jq.get(url, function (data,status){
		if (data.result == 'error'){
			alert('Could not retrieve well the saved data');
			return;
		}
		var msg_container = 'modal-content';
		var tabs_present = false;
		console.log('How about the target in get details '+target);
		switch (target){
			case 'modal':
				tabs_present = generateAndPopulateModal(data, renderProposalTabs, tabs);
				console.log('Creating and filling the modal window');
			break;
			case 'content' :
				var data2 = jQuery.parseJSON(data);
				if (data2.result == 'error' ){
					ajaxAppend(result.error, 'content', 'error');
				} else {
					var content = renderProposalTabs(data2.result, tabs, 'content');
					msg_container = 'content';
					if (Obj('content').html(content)) {
						console.log('doing the tabs first?');
						tabs_present = true;
						//activatetabs('tab_', content_tabs);
					};
					
				}
			break ;
			default: 
				var data2 = jQuery.parseJSON(data);
				if (data2.result == 'error' ){
					ajaxAppend(result.error, 'content', 'error');
				} else {
					var content = renderProposalTabs(data2.result, tabs, 'content');
					msg_container = 'content';
					if (Obj('content').html(content)) {
						console.log('doing the tabs first?');
						tabs_present = true;
						//activatetabs('tab_', content_tabs);
					};
				}
				//TODO this case should be covered too:  tabs_present = populateModal(data, renderProposalTabs, tabs);
		}
		if (tabs_present){
			//TODO: these activate tabs should also been done for the modal case
			console.log('doing the tabs second');
			console.log(content_tabs);
			activatetabs('tab_', content_tabs);
		}
		if (typeof msg != 'undefined' && msg){
      		 ajaxAppend(msg, msg_container, 'status', 'toc');
      	};

		});

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
			onclick = "\" onclick=\"ajaxCall('proposal', 'edit', {proposal_id:"+
				result.proposal_id+ ", target:'"+container+"'}, 'jsonFormResult', 'json', ['"+
				container+"']);\"";
		} else {
			onclick = '" ';
		}
		if (target == 'delete'){
			class_str = ' class="right"';
		} else {
			class_str = '';
		}
		s += '<li'+ class_str+'><a id="tab_tab_'+ target +'" href="#tab_tab_'+ target + onclick+
			'><span>'+labels[t].label+'</span></a></li>';

	}
	s += '</ol>';
	s += '<div class="tabs_container">';
	//TODO: translate this
	var ney = 'Nothing entered yet';
	for (var t=0; t < count;t++){
		target = labels[t].tab;
		s += '<div id="tab_'+ target + '" class="content">';
		s += 	"<div id='msg_"+ target+ "'></div>";

		switch (target){
			case 'project':
				//Paul - there was a bug here which meant that the project description
				// was being replaced by the organisation description. The value to use
				// from this resultset is 'pr_description' rather than 'description' which
				// is used in the renderProject() function above
				result.title = result.pr_title
				result.description = result.pr_description;
				result.url = result.pr_url;
				s += renderProject(result, false);
				break;
			case 'overview': s += renderProposalOverview(result);
				break;
			case 'student': s += renderStudent(result);
				break;
			case 'title': s += (result.title ? result.title : ney);
				break;
			case 'summary': s += (result.solution_short ? result.solution_short : ney);
				break;
			case 'solution': s += (result.solution_long ? result.solution_long : ney);
				break;
//			case 'modules': s += (result.modules ? result.modules : ney);
//				break;
			case 'edit': s += 'Wait please';
			break;
			case 'delete': s += 'Are you sure you want to delete this proposal?<br>'+
				'<input type="button" value="Yes" onclick="ajaxCall(\'proposal\', \'delete\', '+
				'{proposal_id:'+result.proposal_id+', target: \''+ container+ '\' }, '+
				'\'handleDeleteResult\', \'json\', [\'content\', \'proposal\', \'myproposal_page\']);"/>';
			break;
		}
		s += "</div>";

	}
	s += "</div>";
	return s;
}

function getProposalsForProject(projectId){
	ajaxCall("proposal", "render_proposals_for_id", {id: projectId, target:'content'}, "formResult", 'html', 'content');
}

function getProposalsForStudent(studentId){
	ajaxCall("institute", "render_proposals_for_student", {id: studentId, target:'content'}, "formResult", 'html', 'content');
}

function getProposalFormForProject(projectId){
	Drupal.CTools.Modal.dismiss();
	//With formResult it will turn all textareas in rte fields and with handleResult, it just copies the
	//form and places everything in the target content
	//possible formats:
	//   ajaxCall(module, action, data, handleResult, json, args)
	//   ajaxCall(module, action, data, target, html)
	//Note that formResult and jsonFormResult store the call in the target and convert the textareas
	ajaxCall("student", "proposal", {id: projectId, target:'content'}, "formResult", 'html', 'content');
}

function getProjectDetail(projectId){
	var url = moduleUrl + "actions/project_actions.php?action=project_detail&project_id=" + projectId;
	//TODO: currently the apply projects is passed around as global. not so elegant
	$jq.get(url, function(data,status){
		generateAndPopulateModal(data, renderProject, window.view_settings.apply_projects);
	});
}

function getCommentsForEntity(id, entityType, target){
	var url = moduleUrl + "actions/comment_actions.php?action=viewall&id=" + id + "&type=" + entityType;
	$jq.get(url,function(data,status){
		ajaxInsert(data, target);
	});
}

function getOrganisationDetail(org_id){
	var url = moduleUrl + "actions/organisation_actions.php?action=organisation_detail&orgid=" + org_id;
	$jq.get(url,function(data,status){
		generateAndPopulateModal(data, renderOrganisation);
	});
}

function getInstituteDetail(id){
	var url = moduleUrl + "actions/institute_actions.php?action=detail&instid=" + id;
	$jq.get(url,function(data,status){
		generateAndPopulateModal(data, renderInstitute);
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