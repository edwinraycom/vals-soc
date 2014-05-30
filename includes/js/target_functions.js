/*function refreshTabs_oud(json_data, args){
	var targ = '';
	if (arguments.length > 1 && args) {
		var targ = (args.length > 1) ? 'msg_'+ args[1] : '';
	}
	if (json_data && (json_data.result !== 'error')){
		ajaxCall('administration', 'listgroups', {}, 'admin_container');
	} else {
		if (typeof json_data.error != 'undefined') {
			if ((! targ) || ! $jq('#'+targ).length){
				targ = 'ajax_error';
				alertdev('In refreshTabs You forgot to place an error div in the content of the tab: required msg_<target>. '+
					'Searched actually for '+ targ);
			}
			ajaxError(targ, json_data.error);
		} else {
			alertdev('Some error occured but no message was set.');
		}
	}
}*/

function refreshTabs(json_data, args){
	var targ = '';
	if (arguments.length > 1 && args) {
		targ = (args.length > 1) ? 'msg_'+ args[1] : '';
		var type = args[0];
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
	var before = (args.length > 1 ? args[1]: '');
	if (result){
		if (result.result == "html") {
			ajaxInsert(result.html, target);
		} else if (result.result == "error") {
			ajaxAppend(result.error, target, 'error', before);
		} else {
			if (typeof result.msg != 'undefined') {
				ajaxAppend(result.msg, target, 'status', before);
			} else {
				alertdev('The action '
						+ action
						+ ' succeeded.');
			}
		}
	} else {
		alertdev('Not a valid result');
	}
}
