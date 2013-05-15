var fmsCheckOther_LastCustom = null;
var fmsCheckOther_LastCustomInput = null;
function fmsCheckOther(obj) {
	if($(obj).fieldValue()[0] == "__FMWS__CUSTOM__") {
		fmsCheckOther_LastCustom = obj;
		var input = $('<input type="text" name="'+obj.name+'">').get(0);
		fmsCheckOther_LastCustomInput = input;
		
		$(obj).attr("name", $(obj).attr("name")+"_DISABLED_");;
		$(input).attr("value","");
		if($(obj).attr("class")) $(input).attr("class",$(obj).attr("class"));
		
		$(obj).before(input);
		
		$(input).bind("blur",function() {
			var value = $(fmsCheckOther_LastCustomInput).fieldValue()[0];
			$(fmsCheckOther_LastCustom).show();
			$(fmsCheckOther_LastCustomInput).remove();
			$(fmsCheckOther_LastCustom).attr("name", $(fmsCheckOther_LastCustom).attr("name").substr(0,fmsCheckOther_LastCustom.name.length-10));
			
			if(fmsCheckOther_LastCustom.options[fmsCheckOther_LastCustom.options.length-1].value != '__FMWS__CUSTOM__') {
				fmsCheckOther_LastCustom.options[fmsCheckOther_LastCustom.options.length-1] = null;
			}
			fmsCheckOther_LastCustom.options[fmsCheckOther_LastCustom.options.length] = new Option(value,value);
			fmsCheckOther_LastCustom.selectedIndex = fmsCheckOther_LastCustom.options.length - 1;
			$(fmsCheckOther_LastCustom).change();
		});
		
		$(input).focus();
		$(obj).hide();
	}
}

function fmsLiveValueListChanged(valueList) {
	var groupId = valueList.id;
	var dropDown = true;
	if(valueList.nodeName != "SELECT") {
		groupId = $(valueList).parents().get(0).id;
		dropDown = false;
	}else{
		if($(valueList).fieldValue() == "__FMWS__CUSTOM__") {
			return;
		}
	}
	
	var name = valueList.name;
	if(name.substr(name.length-2) == '[]') name = name.substr(0,name.length-2);
	
	var action = window[groupId+'_ACTION'];

	var data = $('#'+groupId+',#'+groupId+' input').serialize();
	if(!data) data = name+'=';
	
	$.post(__FMS_Server_Engine, {__FMS_ACTION_ID:action, __FMS_ACTION_DATA:data, __FMS_ACTION_VIEW:'layout'}, function(data){
		fmsLiveValueListUpdateFromAction(action,data.value_lists,data);
	},"json");
}

var fmsLiveInputTextFieldFocus_Values = new Object();
function fmsLiveInputTextFieldFocus(input) {
	$(input).attr('has_focus','true');
	fmsLiveInputTextFieldFocus_Values[input.id] = input.value;
}

function fmsLiveInputTextFieldBlur(input) {
	$(input).attr('has_focus','false');
	var groupId = input.id;
	var name = input.name;
	if(input.value == fmsLiveInputTextFieldFocus_Values[input.id]) {
		return;
	}
	if(name.substr(name.length-2) == '[]') name = name.substr(0,name.length-2);
	
	var action = window[groupId+'_ACTION'];

	var data = $(input).serialize();
	
	$.post(__FMS_Server_Engine, {__FMS_ACTION_ID:action, __FMS_ACTION_DATA:data, __FMS_ACTION_VIEW:'layout'}, function(data){
		fmsLiveValueListUpdateFromAction(action,data.value_lists,data);
	},"json");
}

function fmsLiveValueListUpdateFromAction(action, value_lists,data) {
	var fields = __FMS_VL_LOOKUP[action];
	
	for(var i in fields) {
		var field = fields[i];
		var area = $('#'+field[0]).get(0);
		var value_list = value_lists[field[1]];
		if(area.nodeName == "SELECT") {
			var current = null;
			var found = false;
			var orig_values = "";
			for(var oi=0;oi<area.options.length;oi++) orig_values+='&'+area.options[oi].value+':'+area.options[oi].text;
			
			if(area.selectedIndex>=0) current = area.options[area.selectedIndex].value;
			var current_is_custom = false;
			if(area.options.length > 2) {
				if(area.selectedIndex>1 && area.selectedIndex == area.options.length-1) {
					if(area.options[area.selectedIndex-1].value == "__FMWS__CUSTOM__") {
						current_is_custom = true;
					}
				}
			}
			area.options.length = 0;
			if(field[2] !== null) {
				area.options[area.options.length] = new Option(field[2],"");
				if(current == "") {
					area.selectedIndex = area.options.length-1;
					found = true;
				}
			}
			for(var x in value_list) {
				area.options[area.options.length] = new Option(value_list[x][1],value_list[x][0]);
				if(value_list[x][0] == current) {
					area.selectedIndex = area.options.length-1;
					found = true;
				}
			}
			if(field[3] !== null) {
				area.options[area.options.length] = new Option(field[3],"__FMWS__CUSTOM__");
				if(current == "__FMWS__CUSTOM__") {
					found = true;
				}
				if(current_is_custom) {
					area.options[area.options.length] = new Option(current,current);
					area.selectedIndex = area.options.length-1;
					found = true;
				}
			}
			if(current != null && !found) {
				if(area.options.length) {
					$(area).change();
				}
			}
			var new_values = "";
			for(var oi=0;oi<area.options.length;oi++) new_values+='&'+area.options[oi].value+':'+area.options[oi].text;
			
			if(orig_values != new_values) $(area).effect("highlight", {}, 750);
		}else if($(area).hasClass('__FMS_LiveValueList_StaticText')) {
			var newValue = data.record[field[6]];
			var oldValue = $(area).text();
			$(area).text(newValue);
			if(oldValue!=newValue) $(area).effect("highlight", {}, 750);
		}else if(area.nodeName == "INPUT"){
			if($(area).attr('has_focus') == 'true') continue;
			var newValue = data.record[field[6]];
			var oldValue = area.value;
			$(area).attr('value',newValue);
			if(oldValue!=newValue) $(area).effect("highlight", {}, 750);
		}else if(area.nodeName == "TEXTAREA"){
			if($(area).attr('has_focus') == 'true') continue;
			var newValue = data.record[field[6]];
			var oldValue = area.value;
			area.value = newValue;
			if(oldValue!=newValue) $(area).effect("highlight", {}, 750);
		}else{
			var values = $(area).find("input").fieldValue();
			var start = $(area).text().replace(/\s/g,"");
			$(area).empty();
			for(var x in value_list) {
				var input = null;
				if(field[5] == 'checkbox') {
					input = $('<input type="checkbox" name="'+field[6]+'[]">').get(0);
				}else{
					input = $('<input type="radio" name="'+field[6]+'">').get(0);
				}
				jQuery.each(values,function(index,item){
					if(item == value_list[x][0]) $(input).attr("checked","checked");
				});
				$(input).attr("value",value_list[x][0]);
				$(area).append(input);
				$(area).append('&nbsp;');
				$(input).identify();
				$(input).bind("click",function() {fmsLiveValueListChanged(this); });
				var label_for = $(input).attr("id");
				var label = $('<label for="'+label_for+'">'+value_list[x][1]+'</label>').appendTo(area);
				if(field[4] !== null && x < value_list.length-1) {
					$($('<span></span>').html(field[4])).appendTo(area);
				}
			}
			var end= $(area).text().replace(/\s/g,"");
			if(start!=end) $(area).effect("highlight", {}, 750);
		}
	}
	
}

$(document).ready(function () {
	$("select:has(option:[value=__FMWS__CUSTOM__])").bind("change", function() { fmsCheckOther(this); });
	
	$("select.__FMS_LiveValueList").bind("change",function() {fmsLiveValueListChanged(this); });
	
	$("input.__FMS_LiveValueList,textarea.__FMS_LiveValueList").bind("focus",function() {fmsLiveInputTextFieldFocus(this); });
	$("input.__FMS_LiveValueList,textarea.__FMS_LiveValueList").bind("blur",function() {fmsLiveInputTextFieldBlur(this); });


	$("span.__FMS_LiveValueList input").bind("click",function() {fmsLiveValueListChanged(this); });
});


jQuery.fn.identify = function(prefix) {
	if(prefix == undefined) {
		prefix = "jq_anon";
	}
    var i = 0;
    return this.each(function() {
        if($(this).attr('id')) return;
        do { 
            i++;
            var id = prefix + '_' + i;
        } while($('#' + id).length > 0);            
        $(this).attr('id', id);            
    });
};


/*
var start = new Date().getTime();
	var i = 1;
	for(var x = 0; x<1000; x++) {
	//fmsCheckOther_TraverseAndAttach(document);
	}
	var step = (new Date().getTime() - start);
    alert(step);
	
	var start = new Date().getTime();
	var i = 1;
	for(var x = 0; x<1; x++) {
	fmsCheckOther_TraverseAndAttach2();
	}
	var step = (new Date().getTime() - start);
    alert(step);
*/
