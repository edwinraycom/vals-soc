function refreshTabs(json_data, args){
	var parent_type='administration';
	var targ = '';
	if (args.length == 0){
		alertdev('There are missing arguments to refresh the tabs');
		return;
	}
	if (arguments.length > 1 && args) {
		var type = args[0];
		targ = (args.length > 1) ? 'msg_'+ args[1] : '';
	}
	if(args.length > 2){
		parent_type = args[2];
	}
	if (json_data && (json_data.result !== 'error')){
		ajaxCall(parent_type, 'show', {type:type}, 'admin_container');
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
	var parent_type='administration';
	var target = '';
        var type = '';
	if (arguments.length > 1 && args && (args.length > 1)) {
                type = args[0];
		target = args[1];
	}
	if (! target){
		alertdev('No target supplied in target function');
                return false;
	}
	if(args.length > 2){//In some cases we might want to jump from administration_actions to timeline_actions for example
		parent_type = args[1];
	}
	//Get the id and the type
	var id = json_data.id;
	var type = json_data.type;
	if (json_data && (json_data.result !== 'error')){
		ajaxCall(parent_type, 'view', {id:id,type:type,target:target}, target);
	} else {
		ajaxError('msg_'+target, json_data.error);
	}
}

function formResult(data, args){
	var target = args[0];
	//todo: we want jquery to wait for the dom to be ready until ckeditor call
    if (Obj(target).html(data)){
    	CKEDITOR.replaceAll();
    }
}

function jsonFormResult(data, args){
	var target = args[0];
	if (data.result == "error") {
		ajaxAppend(data.error, target, 'error');
	} else {
		//todo: we want jquery to wait for the dom to be ready until ckeditor call
        if (Obj(target).html(data.result)){
        	CKEDITOR.replaceAll();
        }
	}     
}

function transform_into_rte(){
	CKEDITOR.replaceAll();
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

function handleDeleteResult(result, args){
	if (result){
		if (result.result == "error") {
			ajaxAppend(result.error, result.target, 'error', result.before);
		} else {
			if (is_string(result.result)) {
				ajaxInsert(result.result, result.target);
			} else {
				if (typeof result.msg != 'undefined') {
					if (result.replace_target) {
						alertdev('maakt target leeg '+ result.target);
						Obj(result.target).html('');
						before = '';
					}
					alertdev('append met '+ result.target + ' en '+ result.msg );
					ajaxAppend(result.msg, result.target, 'status', result.before);
				} else {
					alertdev('The action succeeded.');
				}
			}
			var row = $jq("tr[data-record='"+result.id+"']");
			if (row){
				row.remove();
			} else {
				alertdev('hij kon de row met data-record = '+result.id+ ' niet vinden');
			}
		}
	} else {
		alertdev('Not a valid result');
	}
}

function handleSaveResult(result, args){
	var args_valid = false;
	if (arguments.length > 1 && args && (args.length > 0)) {
		target = args[0];
		args_valid = true;
	} else {
		alertdev('No target supplied in target function');
	}

	if (!args_valid){
		alertdev('Something wrong with the arguments to handleSaveResult');
		return false;
	}
	if (result){
		if (result.result == "error") {
			ajaxAppend(result.error, target, 'error', 'vals-soc-proposal-form');
		} else {
			if (result.result == "OK") {
				getProposalDetail(result.id, target, result.msg);
			} else {
				alertdev('The action did not result in error but also not in OK. succeeded??');
			}
		}
	} else {
		alertdev('Not a valid result');
	}

}

function handleSubmitResult(result, args){
	var args_valid = false;
	if (arguments.length > 1 && args && (args.length > 0)) {
		target = args[0];
		args_valid = true;
	} else {
		alertdev('No target supplied in target function');
	}

	if (!args_valid){
		alertdev('Something wrong with the arguments to handleSubmitResult');
		return false;
	}
	if (result){
		if (result.result == "error") {
			ajaxAppend(result.error, target, 'error', 'vals-soc-proposal-form');
		} else {
			if (result.result == "OK") {
				Drupal.CTools.Modal.dismiss();
				//Right now the content div has no immediate child with an id at the top, so we let ajaxAppend
				//find out the first child to attach a message to
				ajaxAppend(result.msg, 'content', 'status');
			} else {
				alertdev('The action did not result in error but also not in OK. succeeded??');
			}
		}
	} else {
		alertdev('Not a valid result');
	}

}

function populateModal(result, fun, arg){
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

		Obj("modal-title").html("&nbsp;"); // doesnt render unless theres something there!
		Obj("modal-content").html(content);
		return true;
	} else {
		console.log('Some program error occured. We could not render the result');
		if (data){
			Obj("modal-title").html("&nbsp;"); // doesnt render unless theres something there!
			Obj("modal-content").html("<div class='messages error'>"+data.error+ "</div>");
		}
		return false;

	};
}

function generateAndPopulateModal(result, fun, arg){
	// TODO : work more on the formatting
	// and add other fields from DB
	Drupal.CTools.Modal.show();
	var return_result = populateModal(result, fun, arg);
	Drupal.attachBehaviors();
	return return_result;
}