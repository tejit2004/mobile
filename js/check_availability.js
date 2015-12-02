$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});
$(document).on('pagebeforeshow', '#efm_availability', function(){    	
	var data = localStorage.getItem('username');	
	
	if(data == '' || data == 'null' || data == null)		
	{
		$.mobile.changePage("index.html");
	}
	$(document).on('click', '#submit_efm', function() { // catch the form's submit event
        if($('#efm_cli').val().length > 0 || $('#efm_postcode').val().length > 0){
            
				var efm_cli = $('#efm_cli').val();
				var efm_postcode = $('#efm_postcode').val();
				
				efm_cli = efm_cli.replace(/\s/g, '');
				efm_postcode = $.trim(efm_postcode);
				
				var postcode_pattern = /^([a-zA-Z0-9]{3,4}\s*[a-zA-Z0-9]{3,4})$/;
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
					$("body").addClass('ui-disabled');
					$.ajax({url: global_url+'ajaxfiles/check_efm_availability.php',
						//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
						data:{efm_cli : efm_cli, efm_postcode : efm_postcode}, 
						type: 'post',                   
						async: true,
						beforeSend: function() {
							// This callback function will trigger before data is sent
							//$.mobile.showPageLoadingMsg(true); // This will show ajax spinner
							
							$.mobile.loading( "show", {text: "Loading Please wait",textVisible: true,theme: "a",html: ""});
							
						},
						complete: function() {
							// This callback function will trigger on data sent/received complete
							//$.mobile.hidePageLoadingMsg(); // This will hide ajax spinner
							$.mobile.loading( 'hide' );
							$("body").removeClass('ui-disabled');
						},
						success: function (result) {
								$.mobile.changePage("#efm_availability_result");								
								$('#efm_html').html(result);										
						},
						error: function (request,error) {
							// This callback function will trigger on unsuccessful action                
							showError(global_errormsg);
						}
					});  
				}
				else
				{
					showAlert(error);
				}
        } else {
            showAlert('Please fill all necessary fields');
        }           
            return false; // cancel original event to prevent form submitting
        });    
});
