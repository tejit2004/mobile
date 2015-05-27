$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});
$(document).on('pagebeforeshow', '#domain_availability', function(){    	
	
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
	
	$(document).on('click', '#submit_domain', function() { // catch the form's submit event
        if($('#domain_name').val().length > 0){
            
				var domain_name = $('#domain_name').val();
				var clientID = sessionStorage.getItem("clientID");
			
				var array = domain_name.split(".");
				$('#domain_name').val(array[0]);
				var error = '';
				
				domain_name = array[0];
				
				domain_name = array[0].replace(/\s/g, '');
				
				if(domain_name == 'Domain (without any suffix)')
				{
					error += 'Please fill all necessary fields\n';
				}
				
				if(error == '')
				{
					$("body").addClass('ui-disabled');
					$.ajax({url: global_url+'ajaxfiles/domain_availability.php',
						//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
						data:{domain : domain_name, clientID:clientID}, 
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
								$('#result_id').attr('style','display:"";');
					
								$( "#result_id" ).trigger( "expand" );
								
								$( "#search_id" ).trigger( "collapse" );
								
								$( "table#view_result tbody" )
								// Append the new rows to the body
								.html( result )
								// Call the refresh method
								.closest( "table#view_result" )
								.table( "refresh" )
								// Trigger if the new injected markup contain links or buttons that need to be enhanced
								.trigger( "create" );
								
								eval(document.getElementById("runscript").innerHTML);			
															
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
