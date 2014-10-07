/*
 * Some form testing functions -only defined once.
 * by having a #vals_soc_attached attribute in the form we can set extra tests in the variable
 * vals_extra_tests like: 
 * $form['#vals_soc_attached']['js'] = array(
		array(
			'type'  => 'file',
			'data' => '/includes/js/test_functions.js',
		),
// 		array(
// 			'type' => 'direct',	
// 			'data'=> 'vals_extra_tests = ["test2"];',
// 		),
	);
 */
var testing = testing || {
	default_tests : ['test_required_fields'],
	//msgs : '',
	msgs : [],
	warning_style : {'border-style'  :'solid', 'border-color' : 'orange' },
	none_style : {'border-style'  :'', 'border-color' : '' },
	run : function (env, msg_target){
		var tests = this.default_tests;
		var all_good = true;
		if (typeof vals_extra_tests != 'undefined'){
			tests = tests.concat(vals_extra_tests);
		}
		var test = '';
		for (var i = 0; i < tests.length; i++) {
		    test = tests[i];
			all_good = all_good && testing[test](env, msg_target);
		}
		if (all_good === false) {
			if (!this.msgs){
				this.msgs = ['Your form is not correct'];
			}
			ajaxError(msg_target, this.msgs.join("<br/>"));
			var message_div = document.getElementById(msg_target);
			if (message_div){
				message_div.scrollIntoView(true);
			} else {
				console.log('could not find '+ msg_target);
				alertdev('could not find '+ msg_target);
			}
			console.log('Testing:'+ this.msgs.join("\n"));
		}
		this.msgs = [];
		return all_good;
	},
	test2 : function (env, targ){
		this.msgs.push(' Test 2 done');
		return true;
	},
	test_required_fields : function (form_selector, msg_target){	
		var all_good = true;
		var self = this;
		$jq("#" + form_selector+ " .required").each(function( index ) {
			if ($jq(this).prop("tagName") == 'TEXTAREA'){
				var rte_id = '#cke_' + $jq(this).prop("id");
				if (self.isBlank($jq(this).val())){
					$jq(rte_id).css(self.warning_style);
					all_good = false;
				} else {
					$jq(rte_id).css(self.none_style);
				}
			} else {
				if (self.isBlank($jq( this ).val())){
					$jq(this).css(self.warning_style);
					all_good = false;
				} else {
					$jq(this).css(self.none_style);
				}
			}
			
		});
		if (all_good === false) {
			this.msgs.push('You have left some required fields open.');
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