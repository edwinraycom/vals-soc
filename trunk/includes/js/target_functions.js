function refreshTabs(json_data, args){
	var targ = '';
	if (args.length == 0){
		alertdev('There are missing arguments to refresh the tabs');
		return;
	}
	if (arguments.length > 1 && args) {
		var type = args[0];
		targ = (args.length > 1) ? 'msg_'+ args[1] : '';	
	}
	if (json_data && (json_data.result !== 'error')){
		ajaxCall('administration', 'show', {type:type}, 'admin_container');
	} else {
		if (typeof json_data.error != 'undefined') {
			if ((! targ) || ! $jq('#'+targ).length){
				targ = 'ajax_error';
				alertdev('You forgot to place an error div in the content of the tab: required msg_<target>. Searched actually for '+ targ);
			}
			ajaxError(targ, json_data.error);
		} else {
			alertdev('Some error occured but no message was set.');
		}
	}
}

function refreshSingleTab(json_data, args){
	var target = '';
	if (arguments.length > 1 && args && (args.length > 0)) {
		target = args[0];
	}
	if (! target){
		alertdev('No target supplied in target function');
	}
	//Get the id and the type
	var id = json_data.id;
	var type = json_data.type;
	if (json_data && (json_data.result !== 'error')){
		ajaxCall('administration', 'view', {id:id,type:type,target:target}, target);
	} else {
		ajaxError('msg_'+target, json_data.error);
		//$jq('#error_'+target).html(json_data.error);
	}
}

function handleResult(result, args){
	var target = args[0];
	var before = (args.length > 1 ? args[1] : '');
	var replace_target = (args.length > 2 ? args[2]: false);
	if (result){
		if (result.result == "error") {
			ajaxAppend(result.error, target, 'error', before);
		} else {
			if (is_string(result.result)) {
				ajaxInsert(result.result, target);
			} else {
				if (typeof result.msg != 'undefined') {
					if (replace_target) {
						alertdev('maakt target leeg');
						Obj(target).html('');
						before = '';
					}
					alertdev('append met '+ target + ' en '+ result.msg );
					ajaxAppend(result.msg, target, 'status', before);
				} else {
					alertdev('The action succeeded.');
				}
			}
		}
	} else {
		alertdev('Not a valid result');
	}
}

function generateAndPopulateModal(result, fun, arg){
	// TODO : work more on the formatting
	// and add other fields from DB
	var data = jQuery.parseJSON(result);
	if (data && data.result !== 'error'){
		var content = '';
		if (typeof fun == 'function'){
			content = fun(data.result, arg);
		} else {
			content = data.result;
		}
		Drupal.CTools.Modal.show();
		Obj("modal-title").html("&nbsp;"); // doesnt render unless theres something there!
		Obj("modal-content").html(content);
		Drupal.attachBehaviors();
		return true;
	} else {
		console.log('Some program error occured. We could not render the result');
		if (data){
			Drupal.CTools.Modal.show();
			Obj("modal-title").html("&nbsp;"); // doesnt render unless theres something there!
			Obj("modal-content").html("<div class='messages error'>"+data.error+ "</div>");
			Drupal.attachBehaviors();
		}
		return false;
		
	}
}
