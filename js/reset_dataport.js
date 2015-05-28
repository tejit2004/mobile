$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});
$(document).on('pageshow', '#reset_dataport', function(){ 
	var clientID = sessionStorage.getItem("clientID");
	var CliNo = decodeURIComponent(GetParameterValues('CliNo'));
	var ServiceID = decodeURIComponent(GetParameterValues('ServiceID'));
	var SID = decodeURIComponent(GetParameterValues('SID'));
	
	var FullName = sessionStorage.getItem("FullName");
	
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/reset_dataport.php',
			
		data:{CliNo : CliNo, ServiceID : ServiceID, FullName : FullName, SID : SID}, 
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
				result_html += '<tr><td style="text-align:center;color:green;"><b>Your request has been submitted successfully. The status will be updated within <span id="count">15</span> seconds.</b></td></tr>';
				
				$('#dataport_tbody').html(result_html);				
				$( "dataport_table" ).table( "refresh" );
				
												
				counter = 15;							
				
				myVar = setInterval(function() 
				{
					counter--;
					if (counter >= 0) {
					  span = document.getElementById("count");
					  span.innerHTML = counter;								 
					}
					if (counter === 0) {
						clearInterval(myVar);									
						refreshPortData(result.uuid);
					}					
				  }, 1000);
				
			}
			else if(result.ret == false)
			{
				result_html += '<tr><td style="text-align:center;color:red;"><b>'+global_errormsg+'</b></td></tr>';
				
				$('#dataport_tbody').html(result_html);				
				$( "dataport_table" ).table( "refresh" );
				
				showError(global_errormsg);
			}
			
				
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			showError(global_errormsg);
		}
	});  
});

function refreshPortData(flag)
{
	var FullName = sessionStorage.getItem("FullName");
	
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/reset_dataport.php',
			
		data:{ID : flag}, 
		type: 'get',                   
		async: true,
		dataType: 'json',	 
		beforeSend: function() {
			$.mobile.loading( "show", {text: "Loading Please wait",textVisible: true,theme: "a",html: ""});	
			$('#dataport_tbody').html('');			
		},
		complete: function() {
			$.mobile.loading( 'hide' );
			$("body").removeClass('ui-disabled');
		},
		success: function (result) {
			
			var result_html = '';

			  if(result.ret == true)
			  {
				result_html += '<tr><td style="text-align:center;color:green;"><b>Reset Data Port was successful</b></td></tr>';
				
				$('#dataport_tbody').html(result_html);				
				$( "dataport_table" ).table( "refresh" );
				showAlert('Reset Data Port was successful');
			  }
			  else if(result.ret == false)
			  {
			  	  if(result.error == 'failed')
				  {
					  result_html += '<tr><td style="text-align:center;color:red;"><b>Reset Data Port failed. Please contact the Support team for assistance on 0845 257 1335 or at supprt@cerberusnetworks.co.uk.</b></td></tr>';
				
					  $('#dataport_tbody').html(result_html);				
					  $( "dataport_table" ).table( "refresh" );
					  showAlert('Reset Data Port failed. Please contact the Support team for assistance on 0845 257 1335 or at supprt@cerberusnetworks.co.uk');
					 
				  }
				  else
				  {
					  result_html += '<tr><td style="text-align:center;color:red;"><b>The data was not returned in the timeframe expected. Please <a data-ajax="true" rel="external" onclick="refreshPortData(\''+flag+'\')" class="ui-link">click here</a> to refresh the result.</b></td></tr>';
				
					  $('#dataport_tbody').html(result_html);				
					  $( "dataport_table" ).table( "refresh" );
				  }
			  }
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			showError(global_errormsg);
		}
	}); 
}
