$(document).on('pagebeforeshow', '#adsl_availability', function(){    	
	
	$('input[type="text"]').each(function()
	{ 
		this.value = $(this).attr('title');
		$(this).addClass('text-label');
	 
		$(this).focus(function(){
			if(this.value == $(this).attr('title')) {
				this.value = '';
				$(this).removeClass('text-label');
			}
		});
	 
		$(this).blur(function(){
			if(this.value == '') {
				this.value = $(this).attr('title');
				$(this).addClass('text-label');
			}
		});
	});
	
	$(document).on('click', '#submit_adsl', function() { // catch the form's submit event
        if($('#telephone').val().length > 0){
            
				
				var telephone = $('#telephone').val();
				var cli_pattern = /^([0-9]{10,11})$/;
				var error = '';
				
				if(telephone == 'Enter CLI/Telephone')
				{
					error += 'Please fill all necessary fields\n';
				}
				
				else if(telephone != '' && !telephone.match(cli_pattern))
				{					
					error += 'Please Enter Proper Telephone number\n';
				}				
				
				if(error == '')
				{
					$("body").addClass('ui-disabled');
					$.ajax({url: global_url+'ajaxfiles/check_adsl_availability.php',
						//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
						data:{telephone : telephone}, 
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
								$.mobile.changePage("#adsl_availability_result");								
								$('#adsl_html').html(result);		
								$( "my-table" ).table( "refresh" );								
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