(function($, Drupal){
/*
	Drupal.behaviors.myBehavior = {
			attach: function (context, settings) {
				//Drupal.attachBehaviors();
			}
	}
*/
	Drupal.ajax.prototype.commands.jsEntitySaveCallback = function(ajax, response, status){
		var key = response.key;
		var tabTarget = response.tabTarget;
		var entityType = response.entityType;
		//var target_fun = 'refreshSingleTab';
		if(key==''){
			key = 0;
			ajaxFormCall("vals-soc-" + entityType + "-form", 
				"administration", 
				"save",
				{
					type: entityType, 
					id:0
				}, 
				"refreshTabs",
				"json",
				[entityType, tabTarget]
			);
		} else {
			ajaxFormCall("vals-soc-" + entityType + "-form",
				"administration",
				"save",
				{
					type: entityType,
					id: key,
					target: tabTarget 
				},
				"refreshSingleTab",
				"json",
				[entityType, tabTarget]
			);
		}
	};
	// TODO - restructure & refine this OR remove if newer version of ajaxformcall plays nicer with drupal
	Drupal.ajax.prototype.commands.timelineSaveCallback = function(ajax, response, status){
		var key = response.key;
		var tabTarget = response.tabTarget;
		var entityType = response.entityType;
		//var target_fun = 'refreshSingleTab';
		if(key==''){
			key = 0;
			ajaxFormCall("vals-soc-" + entityType + "-form", 
				"timeline", 
				"save",
				{
					type: entityType, 
					id:0
				}, 
				"refreshTabs",
				"json",
				[entityType, tabTarget, "timeline"]
			);
		} else {
			ajaxFormCall("vals-soc-" + entityType + "-form",
				"timeline",
				"save",
				{
					type: entityType,
					id: key,
					target: tabTarget 
				},
				"refreshSingleTab",
				"json",
				[tabTarget, "timeline"]
			);
		}
	};
	/*
	Drupal.ajax.prototype.commands.timelineSaveCallback = function(ajax, response, status){
		var entityType="program_timeline";
		var call_args = $jq('#' + "vals-soc-" + entityType + "-form").serialize();
		console.log(call_args);
		$jq.post( url("timeline","save"), call_args);
	}
	*/
}(jQuery, Drupal));