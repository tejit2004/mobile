$(document).on('pageshow', '#dslam_status_detail', function(){ 
	var gItemID = decodeURIComponent($.urlParam('gItemID'));	
	var CliNo = decodeURIComponent($.urlParam('CliNo'));
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/dslam_status.php',
			
		data:{gItemID : gItemID, CliNo : CliNo}, 
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
			
			var result_html = '';
			if(result.ret == true)
			{
				result_html += '<tr><td><b>Line Length : </b></td><td>'+ result.line_length +'</td></tr><tr><td><b>Line Status : </b></td><td>'+ result.line_status +'</td></tr><tr><td><b>Download Speed : </b></td><td>'+ result.down_speed +'</td></tr><tr><td><b>Upload Speed : </b></td><td>'+ result.up_speed +'</td></tr><tr><td><b>Last Check : </b></td><td>'+ result.last_check +'</td></tr><tr><td><b>Received Bytes : </b></td><td>'+ result.received_bytes +'</td></tr><tr><td><b>Sent Bytes : </b></td><td>'+ result.sent_bytes +'</td></tr><tr><td><b>1-Day Retrains : </b></td><td>'+ result.day_rentain +'</td></tr><tr><td><b>Sent Bytes : </b></td><td>'+ result.sent_bytes +'</td></tr><tr><td><b>Loss of Frame : </b></td><td>'+ result.loss_frame +'</td></tr><tr><td><b>Error Seconds : </b></td><td>'+ result.error_seconds +'</td></tr><tr><td><b>Received Bytes Discarded : </b></td><td>'+ result.received_bytes_disc +'</td></tr><tr><td><b>Sent Bytes Discarded : </b></td><td>'+ result.sent_bytes_disc +'</td></tr><tr><td><b>Loss of Signal : </b></td><td>'+ result.loss_signal +'</td></tr><tr><td><b>Failed Inits : </b></td><td>'+ result.failed_inits +'</td></tr><tr><td><b>Severe Error Seconds : </b></td><td>'+ result.server_error_sec +'</td></tr>';
				
				/*$( "table#service_detail_table tbody" )				
				.html( html )				
				.closest( "table#service_detail_table" )
				.table( "refresh" )*/
				
				$('#dslam_detail_tbody').html(result_html);				
				$( "dslam_table" ).table( "refresh" );

			}
			else if(result.ret == false)
			{
				showError(result.error);	
			}	
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			showError(global_errormsg);
		}
	});  
});