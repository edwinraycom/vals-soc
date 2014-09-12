var debugging = false;
var console_Jquery_migrate_warnings_silent = true;
var baseUrl = '/vals/';
var moduleUrl = baseUrl + 'sites/all/modules/vals_soc/';
var event_counter = 1;

//Some settings for ajax calls
function getMouseXY(e) {
	
	if (document.all) { // grab the x-y pos.s if browser is IE
		tempX = event.clientX + document.body.scrollLeft;
		tempY = event.clientY + document.body.scrollTop;
	}
	else {  // grab the x-y pos.s if browser is NS
		
		tempX = e.pageX;
		tempY = e.pageY;
	}  
	if (tempX < 0){tempX = 0;}
	if (tempY < 0){tempY = 0;}  
	var arr = new Array(1);
	arr[0] = tempX;
	arr[1] = tempY;
	return arr;
}

function setStyleById(i, p, v) {
	var n = document.getElementById(i);
	n.style[p] = v;
}

function setStyleOnObject(o, p, v) {
	o.style[p] = v;
}

function startWait(event, counter, target){
	//arguments[0] = array(ajax_event, target, counter, action, xml_error_copy, tinyMceActive)
	
	var positions = getMouseXY(event);//get mouse position from event
	setWait(1, 'wait_'+counter, positions[0], positions[1], target);
}

function stopWait(counter){
	setWait(0, 'wait_'+counter);
}

function setWait(state, wait_name, x, y, target){
	
	if(state == 0){
		var waitobj = Obj(wait_name);
		if (waitobj) {
			waitobj.remove();
		}
	}else{
		waitingIcon(wait_name, x, y, target);
	}
}

function waitingIcon(wait_name, x, y, container){
	
	var obj = createDiv(wait_name, container, '', true);
	setStyleOnObject(obj, 'position', 'fixed');
	setStyleOnObject(obj, 'zIndex', 1000);
	setStyleOnObject(obj, 'top', (y - ajax_settings.ajax_waiting_half_width) +'px');
	setStyleOnObject(obj, 'left',(x - ajax_settings.ajax_waiting_half_height)  +'px');
	obj.innerHTML = ajax_settings.ajax_waiting_icon;
	
}
ajax_settings = {
		ajax_waiting_half_width:20,
		ajax_waiting_half_height:20,
		ajax_waiting_icon : "<img src='"+ moduleUrl+ "includes/js/resources/ajax-loader_old.gif'"+
		" width='20px'"  +
		" height='20px'"+
		"alt='waiting'></img>"
};


//Not appropriate at all, but here for testing the user form at the moment
function makeVisible(id){
	jQuery("#"+ id).removeClass('invisible');
}

function makeInvisible(id){
	jQuery("#"+ id).addClass('invisible');
}
