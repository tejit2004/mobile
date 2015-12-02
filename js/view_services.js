$(document).on('pageinit', '#view_services', function(){    	
	
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
	
	
	$(document).on('click', '#submit', function() { // catch the form's submit event
	
		var clientID = sessionStorage.getItem("clientID");
		var type = $('#type').val();
		var srno = $('#srno').val();
		var name = $('#name').val();
		
		/*if(type != '')
		{*/
			if(name == 'Name or Order Ref')
			{
				name = '';
			}
			
			if(srno == 'Sr.No or CLI or IP address')
			{
				srno = '';
			}
			
			$("body").addClass('ui-disabled');
			$.ajax({url: global_url + 'ajaxfiles/view_services.php',
				//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
				data:{clientID : clientID, name : name, srno : srno, type : type}, 
				type: 'post',				
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
					
					$('#result_id').attr('style','display:"";');
					
					$( "#result_id" ).trigger( "expand" );
					
					$( "#search_id" ).trigger( "collapse" );
					
					$( "table#view_services_table tbody" )
					// Append the new rows to the body
					.html( result )
					// Call the refresh method
					.closest( "table#view_services_table" )
					.table( "refresh" )				
					
				},
				error: function (request,error) {
					// This callback function will trigger on unsuccessful action                
					showError(global_errormsg);
				}
			});                   
		/*} else {
			showAlert('Please select Service Type');
		} */          
		return false; // cancel original event to prevent form submitting
	});    
});
