$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});
$(document).on('pageshow', '#show_radius_log', function(){ 
	var clientID = sessionStorage.getItem("clientID");
	var PPP_Username = decodeURIComponent(GetParameterValues('PPP_Username'));
	
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/show_radius_log.php',
			
		data:{username : PPP_Username}, 
		type: 'get',                   
		async: true,
		beforeSend: function() {
			$.mobile.loading( "show", {text: "Loading Please wait",textVisible: true,theme: "a",html: ""});			
		},
		complete: function() {
			$.mobile.loading( 'hide' );
			$("body").removeClass('ui-disabled');
		},
		success: function (result) {
			
			$( "table#table-column-toggle tbody" )
            // Append the new rows to the body
            .html( result )
            // Call the refresh method
            .closest( "table#table-column-toggle" )
            .table( "refresh" )
            // Trigger if the new injected markup contain links or buttons that need to be enhanced
            .trigger( "create" );
				
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			showError(global_errormsg);			
		}
	});  
});