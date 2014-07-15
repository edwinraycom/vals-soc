function test_required_fields(form_id, msg_target){
	var msgs = '';
	var all_good = true;
	var warning_style = {'border-style'  :'solid', 'border-color' : 'orange' };
	var none_style = {'border-style'  :'', 'border-color' : '' };
	$jq("#" + form_id+ " .required").each(function( index ) {
		console.log(' we zien voor '+ index + ' : '+ $jq(this).prop("tagName"));
		if ($jq(this).prop("tagName") == 'TEXTAREA'){
			var rte_id = '#cke_' + $jq(this).prop("id");
			if (isBlank($jq( this ).val())){
				$jq(rte_id).css(warning_style);
				all_good = false;
			} else {
				$jq(rte_id).css(none_style);
			}
		} else {
			if (isBlank($jq( this ).val())){
				$jq(this).css(warning_style);
				all_good = false;
			} else {
				$jq(this).css(none_style);
			}
		}
		
	});
	msgs = 'You have left some required fields open';
	ajaxError(msg_target, msgs);
	console.log(msgs);
	return all_good;
}
//From 
//For checking if a string is empty, null or undefined I use:

function isEmpty(str) {
    return (!str || 0 === str.length);
}

//For checking if a string is blank, null or undefined I use:

function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}