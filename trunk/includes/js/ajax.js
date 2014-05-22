//var $jq = jquery.noConflict();
var debugging = true;
var baseUrl = '/vals/';

var $jq = jQuery;

function alertdev(m) {
	if (debugging)
		alert(m);
}
function url(category, action) {
	return baseUrl
			+ "sites/all/modules/vals_soc/actions/"+category+"_actions.php?action="
			+ action;
}

function ajaxInsert(msg, target) {
	var tar = $jq('#' + target);
	if (tar.length) {
		tar.html(msg);
	} else {
		alertdev('Could not find target ' + target);
	}
}

function ajaxError(targ, msg) {
	if (msg){
		var err_target = $jq('#'+targ);	
	
		if (err_target.length){
			err_target.html(msg);
			err_target.addClass('messages error');
		} else {
			alertdev('Target for error '+ targ+ ' could not be found.');
		}
	}
}

function ajaxMessage(targ, msg) {
	if (msg){
		var err_target = $jq('#'+targ);	
	
		if (err_target.length){
			err_target.html(msg);
			err_target.addClass('messages status');
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

function ajaxCall(category, action, data, target, type, args) {
	if (!type)
		type = 'html';// possible types are html, json, xml, text, script,
						// jsonp
	var call = {
		type : "POST",
		url : url(category, action),
		data : data,
		dataType : type
	};
	// Handling a successfull call which can be a programmed returned error too.
	// As
	// long as the returned val is corresponding with dataType and in time.
	// If the success function is not speicifed, a target is necessary to show
	// the result

	if (target) {
		if (isFunction(target)) {
			if (arguments.length < 6){
				var args = [];
				
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
		console
				.log('AjaxCall failed with some error.Redirected to its fail function with: '
						+ errorThrown);
	};
		
	
	return $jq.ajax(call);
}

function ajaxFormCall(frm, category, action, data, target, type, args) {
	var call_args = $jq('#' + frm).serialize();
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
	return ajaxCall(category, action, call_args, target, type, args);

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
						if (confirm('U bent niet langer ingelogd. Wilt u nu inloggen?')) {
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
						alertdev('Uncaught Error. Probaly Server execution aborted by die, exit or Fatal error.\n'
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

jQuery.extend({
	getJsonObject : function(url, data_obj) {
		var result = null;
		$jq.ajax({
			url : url,
			type : 'get',
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