var testing = testing || {
	test_required_fields : function (form_selector, msg_target){		
		var msgs = '';
		var all_good = true;
		var warning_style = {'border-style'  :'solid', 'border-color' : 'orange' };
		var none_style = {'border-style'  :'', 'border-color' : '' };
		var self = this;
		$jq("#" + form_selector+ " .required").each(function( index ) {
			if ($jq(this).prop("tagName") == 'TEXTAREA'){
				var rte_id = '#cke_' + $jq(this).prop("id");
				if (self.isBlank($jq( this ).val())){
					$jq(rte_id).css(warning_style);
					all_good = false;
				} else {
					$jq(rte_id).css(none_style);
				}
			} else {
				if (self.isBlank($jq( this ).val())){
					$jq(this).css(warning_style);
					all_good = false;
				} else {
					$jq(this).css(none_style);
				}
			}
			
		});
		if (all_good === false) {
			msgs = 'You have left some required fields open';
			ajaxError(msg_target, msgs);
			console.log(msgs);
//			all_good = true;
//			return false;
		} else {
			//console.log('all is good');
		}
		return all_good;
	},
	//For checking if a string is empty, null or undefined I use:
	
	isEmpty : function (str) {
	    return (!str || 0 === str.length);
	},
	
	//For checking if a string is blank, null or undefined I use:
	
	isBlank : function (str) {
	    return (!str || /^\s*$/.test(str));
	}
};