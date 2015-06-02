$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});

var d_Date = '';
var a_Time = '';
var linkEnabled = true;

$(document).on('pageshow', '#dslam_status_detail', function(){ 
	var gItemID = decodeURIComponent($.urlParam('gItemID'));	
	var CliNo = decodeURIComponent($.urlParam('CliNo'));
	var Network = decodeURIComponent($.urlParam('DSLNetwork'));
	var ServiceID = decodeURIComponent($.urlParam('Supplier_ServiceID'));
	var dslLineId = decodeURIComponent($.urlParam('dslLineId'));
	$("body").addClass('ui-disabled');
	
	$('#DSLID').html(dslLineId);
	$.ajax({url: global_url+'ajaxfiles/dslam_status2.php',
		data:{DSLID : dslLineId}, 
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
				result_html += '<tr><td width="50%"><b>Date/Time : </b></td><td width="50%">'+ result.DateC +'</td></tr><tr><td><b>Sync Speed (Down) : </b></td><td>'+ result.Down +'</td></tr><tr><td><b>Sync Speed (Up) : </b></td><td>'+ result.Up +'</td></tr><tr><td><b>Error Seconds (Down) : </b></td><td>'+ result.ErrorSecondDn +'</td></tr><tr><td><b>Error Seconds (Up) : </b></td><td>'+ result.ErrorSecondUp +'</td></tr><tr><td><b>SNR : </b></td><td>'+ result.SNR +'</td></tr><tr><td><b>INP : </b></td><td>'+ result.INP +'</td></tr>';
				
				$('#dslam_detail_tbody').html(result_html);				
				$( "dslam_table" ).table( "refresh" );				
			}
			else if(result.ret == false)
			{
				result_html = '<tr><td style="font-color:red;">There are no historical data available.</td></tr><tr>';
				
				$('#dslam_detail_tbody').html(result_html);				
				$( "dslam_table" ).table( "refresh" );
			}	
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			showError(global_errormsg);
		}
	});		  
});


$(document).on('click', '#update_dsl_status', function(){ 

	if(!linkEnabled){
		return;
	}
	
	linkEnabled = false;

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
				
				count = counter = 35;
				
				d_Date = result.date_pass;
				a_Time = result.time_pass;
				
				var result_html = '<tr><td colspan="2" id="success"><font color="green">Your request has been submitted successfully. The line data will be refreshed within <span id="count">'+count+'</span> seconds.</font></td></tr>';
				
				result_html = result_html + $('#dslam_detail_tbody').html();
				
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
				linkEnabled = true;
				showError(result.error);	
			}	
		},
		error: function (request,error) {
			linkEnabled = true;
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
			linkEnabled = true;
		},
		success: function (result) {
			
			var result_html = '';
			if(result.ret == true)
			{
				result_html += '<tr><td width="50%"><b>Date/Time : </b></td><td width="50%">'+ result.DateC +'</td></tr><tr><td><b>Sync Speed (Down) : </b></td><td>'+ result.Down +'</td></tr><tr><td><b>Sync Speed (Up) : </b></td><td>'+ result.Up +'</td></tr><tr><td><b>Error Seconds (Down) : </b></td><td>'+ result.ErrorSecondDn +'</td></tr><tr><td><b>Error Seconds (Up) : </b></td><td>'+ result.ErrorSecondUp +'</td></tr><tr><td><b>SNR : </b></td><td>'+ result.SNR +'</td></tr><tr><td><b>INP : </b></td><td>'+ result.INP +'</td></tr>';
				
				$('#dslam_detail_tbody').html(result_html);				
				$( "dslam_table" ).table( "refresh" );	
				
				showAlertWOTitle('DSL Status updated successfully.');			
			}
			else if(result.ret == false)
			{
				result_html = '<tr><td colspan="2" id="error">The data was not returned in the timeframe expected. Please <a data-ajax="true" rel="external" onclick="refreshData(\''+DSLID+'\')" class="ui-link">click here</a> to refresh the page.</td></tr>';
				
				$('#success').remove();		
				$('#error').remove();		
				result_html = result_html + $('#dslam_detail_tbody').html();
				
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

function showAlertWOTitle(message) {
    navigator.notification.alert(
        message,  // message
        alertWODismissed,         // callback
        'NetCONNECT',            // title
        'OK'                  // buttonName
    );
}

// alert dialog dismissed
function alertWODismissed() {    
}