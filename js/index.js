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

function GetParameterValues(param) 
{
	var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for (var i = 0; i < url.length; i++) 
	{
		var urlparam = url[i].split('=');
		if (urlparam[0] == param) 
		{
			return urlparam[1];
		}
	}
}

function isset () {  
  var a = arguments,
    l = a.length,
    i = 0,
    undef;

  if (l === 0) {
    throw new Error('Empty isset');
  }

  while (i !== l) {
    if (a[i] === undef || a[i] === null) {
      return false;
    }
    i++;
  }
  return true;
}


$(function() {
	var data = localStorage.getItem('username');	
	
	if(data == '' || data == 'null' || data == null)		
	{
		$.mobile.changePage("index.html");
	}
});

$.urlParam = function(name){	
	var results = new RegExp('[\\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
	return results[1] || 0;
}


function do_alert( theflag, thetext )
{
	var message ;

	
	if ($('#login_alert_box') )
		$('#login_alert_box').remove() ;

	if ( theflag )
		message = "<div class=\"outer\"><div id=\"login_alert_box\" class=\"info_good\" \">"+thetext+"</div></div>" ;
	else
		message = "<div class=\"outer\"><div id=\"login_alert_box\" class=\"info_error\" \">"+thetext+"</div></div>" ;

	$('body').append( message ) ;
	
	$('#login_alert_box').show().fadeOut("fast").fadeIn("fast").delay(2000).fadeOut("fast").hide() ;
	
}

