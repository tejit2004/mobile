$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});

var d_Date = '';
var a_Time = '';

$(document).on('pageshow', '#dslam_status_detail', function(){ 
	var gItemID = decodeURIComponent($.urlParam('gItemID'));	
	var CliNo = decodeURIComponent($.urlParam('CliNo'));
	var Network = decodeURIComponent($.urlParam('DSLNetwork'));
	var ServiceID = decodeURIComponent($.urlParam('Supplier_ServiceID'));
	var dslLineId = decodeURIComponent($.urlParam('dslLineId'));
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/dslam_status2.php',
		data:{ServiceID : ServiceID, CLI : CliNo, Network : Network, type_kbd : ''}, 
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
				/*result_html += '<tr><td><b>Line Length : </b></td><td>'+ result.line_length +'</td></tr><tr><td><b>Line Status : </b></td><td>'+ result.line_status +'</td></tr><tr><td><b>Download Speed : </b></td><td>'+ result.down_speed +'</td></tr><tr><td><b>Upload Speed : </b></td><td>'+ result.up_speed +'</td></tr><tr><td><b>Last Check : </b></td><td>'+ result.last_check +'</td></tr><tr><td><b>Received Bytes : </b></td><td>'+ result.received_bytes +'</td></tr><tr><td><b>Sent Bytes : </b></td><td>'+ result.sent_bytes +'</td></tr><tr><td><b>1-Day Retrains : </b></td><td>'+ result.day_rentain +'</td></tr><tr><td><b>Sent Bytes : </b></td><td>'+ result.sent_bytes +'</td></tr><tr><td><b>Loss of Frame : </b></td><td>'+ result.loss_frame +'</td></tr><tr><td><b>Error Seconds : </b></td><td>'+ result.error_seconds +'</td></tr><tr><td><b>Received Bytes Discarded : </b></td><td>'+ result.received_bytes_disc +'</td></tr><tr><td><b>Sent Bytes Discarded : </b></td><td>'+ result.sent_bytes_disc +'</td></tr><tr><td><b>Loss of Signal : </b></td><td>'+ result.loss_signal +'</td></tr><tr><td><b>Failed Inits : </b></td><td>'+ result.failed_inits +'</td></tr><tr><td><b>Severe Error Seconds : </b></td><td>'+ result.server_error_sec +'</td></tr>';*/
				
				count = counter = 35;
				
				$('#DSLID').html(dslLineId);
				
				d_Date = result.date_pass;
				a_Time = result.time_pass;
				
				result_html = '<tr><td><font color="green">Your request has been submitted successfully. The line data will be refreshed within <span id="count">'+count+'</span> seconds.</font></td></tr>';
				
				$('#dslam_detail_tbody').html(result_html);				
				$( "dslam_table" ).table( "refresh" );
				
				
				myVar = setInterval(function() {
					counter--;
					if (counter >= 0) {
					  span = document.getElementById("count");
					  span.innerHTML = counter;								 
					}
					if (counter === 0) {
						clearInterval(myVar);									
						refreshData(dslLineId);									
					}					
				  }, 1000);

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

function refreshData(DSLID)
{
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/dslam_status2.php',
		data:{DSLID : DSLID, d_Date : d_Date, a_Time : a_Time}, 
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
				result_html += '<tr><td><b>DateTime : </b></td><td>'+ result.DateC +'</td></tr><tr><td><b>Sync Speed (Down) : </b></td><td>'+ result.Down +'</td></tr><tr><td><b>Sync Speed (Up) : </b></td><td>'+ result.Up +'</td></tr><tr><td><b>Error Seconds (Down) : </b></td><td>'+ result.ErrorSecondDn +'</td></tr><tr><td><b>Error Seconds (Up) : </b></td><td>'+ result.ErrorSecondUp +'</td></tr><tr><td><b>SNR : </b></td><td>'+ result.SNR +'</td></tr><tr><td><b>INP : </b></td><td>'+ result.INP +'</td></tr>';
				
				$('#dslam_detail_tbody').html(result_html);				
				$( "dslam_table" ).table( "refresh" );				
			}
			else if(result.ret == false)
			{
				result_html = '<tr><td style="font-color:red;">The data was not returned in the timeframe expected. Please <a data-ajax="true" rel="external" onclick="refreshData(\''+DSLID+'\')" class="ui-link">click here</a> to refresh the page.</td></tr>';
				
				$('#dslam_detail_tbody').html(result_html);				
				$( "dslam_table" ).table( "refresh" );
			}	
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			showError(global_errormsg);
		}
	});		
}