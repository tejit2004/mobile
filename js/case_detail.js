$(document).on('pageinit', '#case_detail', function(){    	
	
	var clientID = sessionStorage.getItem("clientID");
	CaseID = decodeURIComponent($.urlParam('ID'));
	
	$.ajax({url: global_url+'ajaxfiles/case_detail.php',
			
		data:{clientID : clientID, CaseID : CaseID}, 
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
			
			$( "table#case_detail_table tbody" )
            // Append the new rows to the body
            .html( result )
            // Call the refresh method
            .closest( "table#case_detail_table" )
            .table( "refresh" )
            // Trigger if the new injected markup contain links or buttons that need to be enhanced
            //.trigger( "create" );
			
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			alert('Network error has occurred please try again!');
		}
	});  
});

var resultObject = {
    formSubmitionResult : null  
}