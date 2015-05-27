$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});
$(document).on('pageshow', '#show_full_log', function(){ 
	var clientID = sessionStorage.getItem("clientID");
	var CliNo = decodeURIComponent(GetParameterValues('CliNo'));
	
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/show_full_log.php',
			
		data:{CliNo : CliNo}, 
		type: 'get',                   
		async: true,
		dataType: 'json',
		beforeSend: function() {
			$.mobile.loading( "show", {text: "Loading Please wait",textVisible: true,theme: "a",html: ""});			
		},
		complete: function() {
			$.mobile.loading( 'hide' );
			$("body").removeClass('ui-disabled');
		},
		success: function (result) {
			
			/*$( "table#my-table tbody" )           
            .html( result )           
            .closest( "table#my-table" )
            .table( "refresh" )           
            .trigger( "create" );*/
			
			if(result.ret == false)
			{
				$('#myData').html('<font color="red"><b>'+result.error+'</b></font>');
			}
			else if(result.ret == true)
			{
				
				if(result.data == '')
				{
					$('#myData').html('<font color="green"><b>No log found.</b></font>');
				}
				else
				{
					$('#myData').html('<pre>' + result.data + '</pre>');
				}
			}
				
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			showError(global_errormsg);			
		}
	});  
});