var debugging = true;
var console_Jquery_migrate_warnings_silent = true;
var baseUrl = '/vals/';
var moduleUrl = baseUrl + 'sites/all/modules/vals_soc/';

//Not appropriate at all, but here for testing the user form at the moment
function makeVisible(id){
	jQuery("#"+ id).removeClass('invisible');
}

function makeInvisible(id){
	jQuery("#"+ id).addClass('invisible');
}
