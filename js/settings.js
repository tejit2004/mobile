$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});

$(document).on('pageinit', '#settings', function(){ 
	$(document).on('click', '#logout', function()
	{ 
		showLogoutConfirm('Logout', 'Do you really want to logout?', 'Yes,No');	
	});	
});
// process the confirmation dialog result
function onLogoutConfirm(buttonIndex) {
   if (buttonIndex == 1) 
   {
		
	localStorage.clear();
	sessionStorage.clear();	
	//$.mobile.changePage("index.html");															
	$.mobile.changePage('index.html', {
				changeHash: true,
				dataUrl: "",    //the url fragment that will be displayed for the test.html page
				transition: "flip",  //if not specified used the default one or the one defined in the default settings
				reloadPage:true
				});		
	localStorage.clear();
	sessionStorage.clear();				
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
