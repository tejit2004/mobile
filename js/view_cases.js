$(document).on('pagebeforeshow', '#view_cases', function(){    	
	
	var clientID = sessionStorage.getItem("clientID");
	
	$.ajax({url: global_url+'ajaxfiles/view_cases.php',
			
		data:{clientID : clientID}, 
		type: 'get',                   
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
		},
		success: function (result) {						
			//$('#view_cases_tbody').html(result);
			//$("#table-column-toggle").table("refresh");		
			//$("#table-column-toggle").table-columntoggle( "refresh" );	
			
			$( "table#table-column-toggle tbody" )
            // Append the new rows to the body
            .html( result )
            // Call the refresh method
            .closest( "table#table-column-toggle" )
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
});

var resultObject = {
    formSubmitionResult : null  
}
