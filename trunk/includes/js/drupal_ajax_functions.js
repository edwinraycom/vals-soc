(function($, Drupal){
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
				[tabTarget]
			);
		}
	};
}(jQuery, Drupal));