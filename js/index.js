var global_url = 'http://nc2.cerberusnetworks.co.uk/mobile/';

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
function ltrim(stringToTrim) {
	return stringToTrim.replace(/^\s+/,"");
}
function rtrim(stringToTrim) {
	return stringToTrim.replace(/\s+$/,"");
}

$(function() {
	var data = localStorage.getItem('username');	
	
	if(data == '' || data == 'null' || data == null)		
	{
		$.mobile.changePage("index.html");
	}
});



