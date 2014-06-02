(function($, Drupal){
	Drupal.ajax.prototype.commands.jsOrganisationSaveCallback = function(ajax, response, status){
		var orgId = response.orgId;
		var tabTarget = response.tabTarget;
		if(orgId==''){
			ajaxFormCall("vals-soc-organisation-form", 
					"administration",
					"save",
					{
						type: "organisation", 
						id: 0
					},
					"refreshTabs", 
					"json", 
					["organisation", tabTarget]
			);
		}
		else{
			ajaxFormCall("vals-soc-organisation-form",
					"administration", 
					"save",
					{
						type: "organisation", 
						id: orgId,
						target: tabTarget 
					}, 
					"refreshSingleTab", 
					"json", 
					[tabTarget]
			);
		}
	};
}(jQuery, Drupal));