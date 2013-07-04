$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});

$(document).on('pagebeforeshow', '#login', function(){  
		var username = localStorage.getItem('username');	
		var password = localStorage.getItem('password');	
	
		if(username == '' || username == 'null' || username == null)		
		{
			$.mobile.changePage("index.html");
		}
		
		if(username != '' && password != '' && username != null && password != null && username != 'null' && password != 'null')
		{			
			$("body").addClass('ui-disabled');
			
			$('.check-user').html('');
			$.ajax({url: global_url + 'ajaxfiles/check.php',
				//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
				data:{action : 'login', username : username, password : password}, 
				type: 'post',
				dataType: 'json',	                   
				async: true,
				beforeSend: function() {
					// This callback function will trigger before data is sent
					//$.mobile.showPageLoadingMsg(true); // This will show ajax spinner
					//$.mobile.loading( 'show' );
					$.mobile.loading( "show", {	text: "Loading Please wait",textVisible: true,theme: "a",html: ""});
				},
				complete: function() {
					// This callback function will trigger on data sent/received complete
					//$.mobile.hidePageLoadingMsg(); // This will hide ajax spinner						
					$.mobile.loading( 'hide' );
					$("body").removeClass('ui-disabled');
				},
				success: function (result) {                							
						resultObject.formSubmitionResult = result;
						if(result.ret == true)
						{
							localStorage.setItem('username', username);
							localStorage.setItem('password', password);
							
							sessionStorage.setItem("contactID", result.contactID);
							sessionStorage.setItem("clientID", result.clientID);
							sessionStorage.setItem("CompanyName", result.CompanyName);
							sessionStorage.setItem("FullName", result.FullName);								
							//$.mobile.changePage("index.html#list");	
							$.mobile.changePage('list.html', {
							changeHash: true,
							dataUrl: "",    //the url fragment that will be displayed for the test.html page
							transition: "flip"  //if not specified used the default one or the one defined in the default settings
							});
							
						}
						else
						{
							showAlert('Incorrect Username or password');
						}
				},
				error: function (request,error) {
					// This callback function will trigger on unsuccessful action                
					showError(global_errormsg);
				}
			});                   			
		}
		
        $(document).on('click', '#submit', function() { // catch the form's submit event
        if($('#username').val().length > 0 && $('#password').val().length > 0){
            // Send data to server through ajax call
            // action is functionality we want to call and outputJSON is our data
				var username = $('#username').val();
				var password = $('#password').val();
				$("body").addClass('ui-disabled');
				$.ajax({url: global_url + 'ajaxfiles/check.php',
                    //data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
					data:{action : 'login', username : username, password : password}, 
                    type: 'post',
					dataType: 'json',	                   
                    async: true,
                    beforeSend: function() {
                        // This callback function will trigger before data is sent
                        //$.mobile.showPageLoadingMsg(true); // This will show ajax spinner
						//$.mobile.loading( 'show' );
						$.mobile.loading( "show", {	text: "Loading Please wait",textVisible: true,theme: "a",html: ""});
                    },
                    complete: function() {
                        // This callback function will trigger on data sent/received complete
                        //$.mobile.hidePageLoadingMsg(); // This will hide ajax spinner						
						$.mobile.loading( 'hide' );
						$("body").removeClass('ui-disabled');
                    },
                    success: function (result) {                							
							resultObject.formSubmitionResult = result;
							if(result.ret == true)
							{
								localStorage.setItem('username', username);
								localStorage.setItem('password', password);
								
								sessionStorage.setItem("contactID", result.contactID);
								sessionStorage.setItem("clientID", result.clientID);
								sessionStorage.setItem("CompanyName", result.CompanyName);
								sessionStorage.setItem("FullName", result.FullName);								
								//$.mobile.changePage("index.html#list");	
								$.mobile.changePage('list.html', {
								changeHash: true,
								dataUrl: "",    //the url fragment that will be displayed for the test.html page
								transition: "flip"  //if not specified used the default one or the one defined in the default settings
								});
								
							}
							else
							{
                                showError('Incorrect Username or password');
							}
                    },
                    error: function (request,error) {
                        // This callback function will trigger on unsuccessful action                
                        showError(global_errormsg);
                    }
                });                   
        } else {
            //do_alert(0,'Please fill all necessary fields.');
			showAlert('Please fill all necessary fields');
			//alert('Please fill all necessary fields');
        }           
            return false; // cancel original event to prevent form submitting
        });    
		
	/*$(document).on('click', '#logout', function()
	{		
		showConfirm('Confirm NetConnect Logout', 'Are you sure you want to logout?', 'Yes,No');		
	});*/
	
	$(document).on('click', '#view_services', function()
	{	
		  $.mobile.changePage('view_services.html', {
									changeHash: true,
									dataUrl: "",    //the url fragment that will be displayed for the test.html page
									transition: "slide"  //if not specified used the default one or the one defined in the default settings
									});
	});		
});


$(document).on('click', '#logout', function()
{ 
	
	showConfirm('Logout & Exit', 'Do you really want to logout and exit?', 'Yes,No');	
});	
	
	
$(document).on('click', '#view_services', function()
{	
	  $.mobile.changePage('view_services.html', {
								changeHash: true,
								dataUrl: "",    //the url fragment that will be displayed for the test.html page
								transition: "slide"  //if not specified used the default one or the one defined in the default settings
								});
});
var resultObject = {
    formSubmitionResult : null  
}

// process the confirmation dialog result
function onConfirm(buttonIndex) {
   if (buttonIndex == 1) 
   {
		
	localStorage.clear();
	sessionStorage.clear();	
	//$.mobile.changePage("index.html");															
	$.mobile.changePage('index.html', {
				changeHash: true,
				dataUrl: "",    //the url fragment that will be displayed for the test.html page
				transition: "flip"  //if not specified used the default one or the one defined in the default settings
				});
	localStorage.clear();
	sessionStorage.clear();			
	navigator.app.exitApp();				
		
   }
}

// Show a custom confirmation dialog

function showConfirm(title, message, buttons) {
    navigator.notification.confirm(
        message,  // message
        onConfirm,              // callback to invoke with index of button pressed
        title,            // title
        buttons          // buttonLabels
    );
}
