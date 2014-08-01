//var $jq = jquery.noConflict();
//These settings are now included in a general settings file
//var debugging = true;
//var console_Jquery_migrate_warnings = false;
//var baseUrl = '/vals/';
//var moduleUrl = baseUrl + 'sites/all/modules/vals_soc/';

var $jq = jQuery;

function alertdev(m) {
	if (debugging)
		alert(m);
}
function url(category, action) {
	return moduleUrl + "actions/"+category+"_actions.php?action="+ action;
}

function altSub(arr_obj, prop, def){
	if (arr_obj && (typeof arr_obj[prop] != 'undefined')){
		return arr_obj[prop];
	} else {
		return def ? def : '';
	}
}

function ajaxInsert(msg, target) {
	var tar = $jq('#' + target);
	if (tar.length) {
		tar.html(msg);
		Drupal.attachBehaviors();
		return true;
	} else {
		alertdev('Could not find target ' + target);
		return false;
	}
	
}

function isObject(mixed_var){
	if (mixed_var instanceof Array) {
		return false;
	} else {
		return (mixed_var !== null) && (typeof( mixed_var ) == 'object');
	}
}

function isArray(mixed_var){
	return (mixed_var instanceof Array) && (typeof( mixed_var ) == 'object');
}

function Obj(name_or_object, return_dom){
	if (isObject(name_or_object)){
		if (return_dom){
			return isJquery(name_or_object) ? name_or_object[0] : name_or_object;
		} else {
			return name_or_object;
		}
	} else
		var obj = $jq('#'+name_or_object);
	if (obj.length == 0){
		return false;
	} else {
		if (typeof return_dom == 'undefined') return_dom = false;
		return return_dom ? obj[0] : obj;
	}
}

function isJquery(obj){
	return obj && (typeof obj.jquery != 'undefined') && obj.jquery;
}

function createDiv(div_name, container){
	var div = Obj(div_name);
	if(div){
		return div;
	} else{
		var new_obj = document.createElement('div');
		new_obj.setAttribute("id", div_name);
		var cont_obj = Obj(container, true);
		if (cont_obj) {
			if (arguments.length > 2 && arguments[2] !== ''){
				//Insert before an element inside container
				//we use the normal objects, so we ask Obj to return a dom element
				var first_obj = Obj(arguments[2], true);
				cont_obj.insertBefore(new_obj, first_obj);
			} else {
				cont_obj.appendChild(new_obj);
			}
			return Obj(div_name);
		} else {
			alertdev('Could not find parent object '+ container);
			return null;
		}
	}
}

function is_string(arg){
	return (typeof arg) == 'string';
}

function ajaxAppend(msg, container, err, before){
	var t = '';
	if (typeof before == 'undefined') {
		var cont_obj = Obj(container);
		if (cont_obj){
			before = cont_obj[0].childNodes[0];
		} else {
			before = '';
			alertdev('Could not append message to container '+ container+ '. It could not be found.');
			return false;
		}
		t = createDiv(container+err, cont_obj[0], before);
	} else {
		t = createDiv(container+err, container, before);
	}

	if (t) {
		var msg2 = "<a href=javascript:void(0); onclick='var o = this.parentNode;$jq(o).html(\"\").removeClass(\"messages status\");'>"+
			msg+ "</a>";
		t.addClass('messages '+err);
		t.html(msg2);
	}

}

function ajaxError(targ, msg) {
	if (msg){
		var err_target = Obj(targ);
		var msg2 = "<a href=javascript:void(0); onclick='$jq(\"#" +targ+"\").html(\"\").removeClass(\"messages status\");'>"+
		msg+ "</a>";
		if (err_target.length){
			err_target.html(msg2);
			err_target.addClass('messages error');
		} else {
			alertdev('Target for error '+ targ+ ' could not be found.');
		}
	}
}

function ajaxMessage(targ, msg) {
	if (msg){
		var err_target = $jq('#'+targ);
		var msg2 = "<a href=javascript:void(0); onclick='$jq(\"#" +targ+"\").html(\"\").removeClass(\"messages status\");'>"+
		msg+ "</a>";
		if (err_target.length){
			err_target.html(msg2);
			err_target.addClass('messages status');
		} else {
			alertdev('Target for msg '+ targ+ ' could not be found.');
		}
	}
}

function sanitizeTarget(target){
	return target.replace(/[\(,\),;,\,]/g, '');
}

function isFunction(func){
	//Since func could also be a string of the form action=modules&sub=.... the eval might raise an error if we
	//do not enclose it in quotes. We just want to return false in that case
	//Doing return (func !== null) && (typeof( func ) == 'function'); does not work, so we have to use eval
	try {
		return eval("typeof " + sanitizeTarget(func) + " == 'function'");
	} catch (e){
		return false;
	}
}

function ajaxCall(handler_type, action, data, target, type, extra_args) {
	if (!type)
		type = 'html';// possible types are html, json, xml, text, script,
						// jsonp
	var call = {
		type : "POST",
		url : url(handler_type, action),
		data : data,
		dataType : type
	};
	// Handling a successfull call which can be a programmed returned error too.
	// As long as the returned val is corresponding with dataType and in time.
	// If the success function is not speicifed, a target is necessary to show
	// the result
	if (target) {
		if (isFunction(target)) {
			var args = [];
			if (arguments.length >= 6){
				if (isArray(extra_args))
					args = extra_args;
				else
					args = [extra_args];
			}
			call.success = function(msg){
					window[target](msg, args);
				};
		} else {
			call.success = function(msg) {
				if (type == 'json') {
					if (msg.result == "html") {
						ajaxInsert(msg.html, target);
					} else if (msg.result == "error") {
						ajaxInsert(msg.error, target);
					} else {
						if (typeof msg.msg != 'undefined') {
							ajaxInsert(msg.msg, target);
						} else {
							alertdev('The action '
									+ action
									+ ' succeeded. Specify a success message or some function');
						}
					}
				} else {// assume text or html, we don't care: all can be valid
					ajaxInsert(msg, target);
				}
			};
		}
	} else {
		alertdev('No target or function has been specified: see console for details.');
	}

	call.fail = function(jqXHR, textStatus, errorThrown) {
		console.log('AjaxCall failed with some error.Redirected to its fail function with: '
			+ errorThrown);
	};

	return $jq.ajax(call);
}

function ajaxFormCall(frm_selector, handler_type, action, data, target, type, args) {
	CKupdate();
	//testing is a global object with testing functions 
	if (testing && isFunction('testing.test_required_fields')){
		if (isFunction(target)){
			if (typeof args != ' undefined' && isArray(args) && typeof args[1] != ' undefined'){
				var msg_target = 'msg_'+ args[1];
			} else {
				var msg_target = 'msg_'+ target;
			}
		}
		if (!testing.test_required_fields(frm_selector, msg_target)) 
			return false;
	} else {
		//no test needs to be done or forgotten to include test_functions.js
	}
	//We assume the form is contained in a container with an id, or just the form id is passed (if it is unique)
	//this is possible otherwise we need a unique container (mostly the target where the form is put)
	var call_args = $jq('#' + frm_selector).serialize();
	if (data) {
		if (data instanceof Object) {
			var ds = '';
			for ( var k in data) {
				ds += (ds ? '&' : '') + k + '=' + data[k];
			}
			call_args = call_args.concat('&' + ds);
		} else {
			call_args = call_args.concat('&' + data);
		}
	}
	return ajaxCall(handler_type, action, call_args, target, type, args);

}

$jq(document)
		.ajaxError(
				function(event, jqxhr, settings, exception) {
					if (jqxhr.status === 0 || jqxhr.readyState === 0) {
						return false;
					}
					if (jqxhr.status == 404) {
						alertdev('Requested page not found. [404]');
					} else if (jqxhr.status == 500) {
						alertdev('Internal Server Error [500].');
					} else if (jqxhr.status == 302) {// error produced by
														// bjyauthorize
						// //The user returns to the page after a long time and
						// is logged out becauses of session expiration
						if (confirm('It seems you are no longer logged in. Do you want to log in now?')) {
							window.location.replace(baseUrl + "/user/login");
							return;
						} else {
							return;
						}
					} else if (exception === 'parsererror') {
						alertdev('Requested JSON parse failed.');
					} else if (exception === 'timeout') {
						alertdev('Time out error.');
					} else if (exception === 'abort') {
						alertdev('Ajax request aborted.');
					} else {
						alertdev('Uncaught Error. Probably Server execution aborted by die, exit or Fatal error.\n'
								+ jqxhr.responseText);
					}
					if (!(debugging && confirm("Do you want to open a window with some extra info?")))
						return;
					// Test on debugging status == TRUE, TODO more depending on
					// logged in status etc
					if (debugging) {
						myWindow = window.open('', '');
						myWindow.document.open();
						myWindow.focus();
						var txt = '';
						if (((typeof jqxhr.responseText) != 'undefined')) {
							try {
								var json_txt = decodeURIComponent(jqxhr.responseText);
								var parsed = JSON.parse(json_txt);
								txt = parsed.error;
							} catch (e) {
								txt = jqxhr.responseText;
								if (console) {
									console.error("Parsing error:", e);
								}
							}
						} else {
							txt = 'No response';
						}
						myWindow.document
								.write('Some error occured in the ajax call for '
										+ settings.url
										+ '<br>Exception:'
										+ exception + '<br>Response:' + txt);
						myWindow.document.close();
						myWindow.focus();
					}
				});

function inspect(arg){
	alertdev('Just inspecting the object two level deep');
	for (a in arg){
		if (typeof (arg[a]) == 'object'){
			alert('het is een object '+ a);
			for (b in arg[a]){
				if (typeof (arg[a][b]) == 'object'){
					alert('het is een object '+ b);

				} else
				alert('laat zien '+ b + arg[a][b]);
			}
		} else
		alert('laat zien '+ a + arg[a]);
	}
}
/* Returns a js object to process. Note that the json has been parsed by Jquery as the input comes in the
 * success function. I do the request with post as this seems slightly faster than get (1.38 s vs 1.7 s)*/
jQuery.extend({
	getJsonObject : function(category, action, data_obj) {
		var result = null;
		$jq.ajax({
			url : url(category, action),
			type : 'post',
			dataType : 'json',
			data : data_obj,
			async : false,
			success : function(data) {
				result = data;
			}
		});
		return result;
	}
});

/*
 * Used for ckeditor: the hidden textarea fields will be filled with the actual code just before sending
 * the ajax. This function will be called automatically for ajaxFormCall.
 */
function CKupdate(){
	if (CKEDITOR && ! jQuery.isEmptyObject(CKEDITOR.instances)){
		for (instance in CKEDITOR.instances){
	        CKEDITOR.instances[instance].updateElement();
	    }
	}
}