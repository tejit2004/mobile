$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});

$(document).on('pageinit', '#settings', function(){ 
	$(document).on('click', '#logout', function()
	{ 
		showLogoutConfirm('Logout', 'Do you really want to logout?', 'Yes,No');	
		
		
		/*var txt;
var r = confirm("Press a button!");
if (r == true) {
    localStorage.clear();
	sessionStorage.clear();	
	$.mobile.changePage($(document.location.href="index.html"));
} else {
    txt = "You pressed Cancel!";
}*/
		
	});	
});
// process the confirmation dialog result
function onLogoutConfirm(buttonIndex) {
   if (buttonIndex == 1) 
   {
		
	localStorage.clear();
	sessionStorage.clear();	
	$.mobile.changePage($(document.location.href="index.html"));													
	/*$.mobile.changePage('index.html');		
	localStorage.clear();
	sessionStorage.clear();				*/
	
	
	//document.location.href = "index.html";
	
	//navigator.app.exitApp();				
		
   }
}

// Show a custom confirmation dialog

function showLogoutConfirm(title, message, buttons) {
	navigator.notification.confirm(
        message,  // message
        onLogoutConfirm,              // callback to invoke with index of button pressed
        title,            // title
        buttons          // buttonLabels
    );
}
