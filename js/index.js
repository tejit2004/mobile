$(document).on('pagebeforeshow', '#login', function(){  
		var data = localStorage.getItem('username');		
		if(data != '' && data != 'null' && data != null)		
		{
			$.mobile.changePage("#list");
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
							var n=result.split("||");
							
							if(n[0] == "true")
							{
								localStorage.setItem('username', username);
								localStorage.setItem('password', password);
								$.mobile.changePage("#list");								
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

$(document).on('pagebeforeshow', '#second', function(){     
    $('#second [data-role="content"]').append('This is a result of form submition: ' + resultObject.formSubmitionResult);  
});

$(document).on('pagebeforeshow', '#efm_availability', function(){     
	$(document).on('click', '#submit_efm', function() { // catch the form's submit event
        if($('#efm_cli').val().length > 0 || $('#efm_postcode').val().length > 0){
            
				var efm_cli = $('#efm_cli').val();
				var efm_postcode = $('#efm_postcode').val();
				
				var postcode_pattern = /^([a-zA-Z0-9]{3,4}\s[a-zA-Z0-9]{3,4})$/;
				var cli_pattern = /^([0-9]{10,11})$/;
				var error = '';
				
				if(efm_cli != '' && !efm_cli.match(cli_pattern))
				{					
					error += 'Please Enter Proper Telephone number\n';
				}				
				
				if(efm_postcode != '' && !efm_postcode.match(postcode_pattern))
				{					
					error += 'Please Enter Proper Postcode\n';					
				}
				
				if(error == '')
				{
					$.ajax({url: 'http://nc2.cerberusnetworks.co.uk/mobile/ajaxfiles/check_efm_availability.php',
						//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
						data:{action : 'login', efm_cli : efm_cli, efm_postcode : efm_postcode}, 
							type: 'post',                   
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
								$.mobile.changePage("#efm_availability_result");
								var data = localStorage.getItem('username');						
								$('#efm_html').html(result);										
						},
						error: function (request,error) {
							// This callback function will trigger on unsuccessful action                
							alert('Network error has occurred please try again!');
						}
					});  
				}
				else
				{
					alert(error);
				}
        } else {
            alert('Please fill all necessary fields');
        }           
            return false; // cancel original event to prevent form submitting
        });    
});

$(document).on('pagebeforeshow', '#list', function(){     
	var data = localStorage.getItem('username');

	if(data == '' || data == 'null' || data == null)		
	{
		$.mobile.changePage("#login");
	}
});
      


var resultObject = {
    formSubmitionResult : null  
}