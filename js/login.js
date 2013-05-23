$(document).on('pagebeforeshow', '#login', function(){  
		var data = localStorage.getItem('username');	
	
		if(data == '' || data == 'null' || data == null)		
		{
			$.mobile.changePage("index.html");
		}
        $(document).on('click', '#submit', function() { // catch the form's submit event
        if($('#username').val().length > 0 && $('#password').val().length > 0){
            // Send data to server through ajax call
            // action is functionality we want to call and outputJSON is our data
				var username = $('#username').val();
				var password = $('#password').val();
				
				$.ajax({url: 'http://nc2.cerberusnetworks.co.uk/mobile/ajaxfiles/check.php',
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
                                alert('Incorrect Username or password');
							}
                    },
                    error: function (request,error) {
                        // This callback function will trigger on unsuccessful action                
                        alert('Network error has occurred please try again!');
                    }
                });                   
        } else {
            alert('Please fill all necessary fields');
        }           
            return false; // cancel original event to prevent form submitting
        });    
});


$(document).on('click', '#logout', function(){ 	
	
	$.ajax({url: global_url+'ajaxfiles/check.php',
			data:{action : 'logout'}, // Convert a form to a JSON string representation
			//data:{action : 'login', username : username, password : password}, 
			type: 'get',                   
			async: true,
			beforeSend: function() {
				// This callback function will trigger before data is sent
				$.mobile.showPageLoadingMsg(true); // This will show ajax spinner
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete
				$.mobile.hidePageLoadingMsg(); // This will hide ajax spinner
			},
			success: function (result) {
					resultObject.formSubmitionResult = result;
					localStorage.clear();
					sessionStorage.clear();	
					//$.mobile.changePage("index.html");															
					$.mobile.changePage('index.html', {
								changeHash: true,
								dataUrl: "",    //the url fragment that will be displayed for the test.html page
								transition: "flip"  //if not specified used the default one or the one defined in the default settings
								});
			},
			error: function (request,error) {
				// This callback function will trigger on unsuccessful action                
				alert('Network error has occurred please try again!');
			}
		}); 
	  
});	

/*$(document).on('pagebeforeshow', '#logout', function(){  
		
		$.ajax({url: global_url+'ajaxfiles/check.php',
			data:{action : 'logout'}, // Convert a form to a JSON string representation
			//data:{action : 'login', username : username, password : password}, 
			type: 'get',                   
			async: true,
			beforeSend: function() {
				// This callback function will trigger before data is sent
				$.mobile.showPageLoadingMsg(true); // This will show ajax spinner
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete
				$.mobile.hidePageLoadingMsg(); // This will hide ajax spinner
			},
			success: function (result) {
					resultObject.formSubmitionResult = result;
					localStorage.clear();
					sessionStorage.clear();	
					$.mobile.changePage("index.html");															
			},
			error: function (request,error) {
				// This callback function will trigger on unsuccessful action                
				alert('Network error has occurred please try again!');
			}
		});   
});*/

/*$(document).on('pagebeforeshow', '#list', function(){  
		
		$(document).on('click', '#adsl', function()
		{
			$.mobile.changePage('check_availability_adsl.html', 
			{
				changeHash: false,
				dataUrl: "",    //the url fragment that will be displayed for the test.html page
				transition: "slide"  //if not specified used the default one or the one defined in the default settings
			});
		});
	
});*/

/*$(document).on('pagebeforeshow', '#list', function(){     
	var data = localStorage.getItem('username');

	if(data == '' || data == 'null' || data == null)		
	{
		$.mobile.changePage("index.html");
	}
});*/
      


var resultObject = {
    formSubmitionResult : null  
}