$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});
$(document).on('pageshow', '#profile_management', function(){ 
	var clientID = sessionStorage.getItem("clientID");
	var CliNo = decodeURIComponent(GetParameterValues('CliNo'));
	var ServiceID = decodeURIComponent(GetParameterValues('ServiceID'));
	
	$('#ServiceID').val(ServiceID);
	
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/profile_management.php',
			
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
			
			var result_html_current = '';
			var result_html_change = '';
			if(result.ret == true)
			{
				result_html_current += '<tr><td width="50%"><b>SNR (Signal to Noise Ratio)</b></td><td width="50%">'+ result.CurrentSNR +'</td></tr><tr><td><b>Interleaving / INP</b></td><td>'+ result.CurrentINP +'</td></tr>';
				
				$('#current_tbody').html(result_html_current);				
				$( "current_table" ).table( "refresh" );
				
				var SNROptions = result.SNROptions;
				var INPOptions = result.INPOptions;
				
				SNROptions = SNROptions.split("#*");
				INPOptions = INPOptions.split("#*");
				
				var SNRStr = '<option value="">Select SNR</option>';
				var INPStr = '<option value="">Select Interleaving / INP</option>';
				
				for(var i=0;i<SNROptions.length;i++)
				{
					var SNR = SNROptions[i];
					SNRStr += '<option value="'+SNR+'">'+SNR+'</option>';
				}
				
				for(var i=0;i<INPOptions.length;i++)
				{
					var INP = INPOptions[i];
					INPStr += '<option value="'+INP+'">'+INP+'</option>';
				}
				
				$('#SNRCombo').html(SNRStr);
				$("#SNRCombo").trigger("change");
				$('#INPCombo').html(INPStr);
				$('#INPCombo').trigger("change");
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
	
	$(document).on('click', '#submit_profiles', function() { // catch the form's submit event
	
		var clientID = sessionStorage.getItem("clientID");
		var FullName = sessionStorage.getItem("FullName");
		var SNR = $('#SNRCombo').val();
		var INP = $('#INPCombo').val();
		
		var ServiceID = $('#ServiceID').val();
		
		if(SNR == '')
		{
			showAlert('Please select SNR');
			return false;
		}
		
		if(INP == '')
		{
			showAlert('Please select Interleaving / INP');
			return false;
		}
		
		$("body").addClass('ui-disabled');
		$.ajax({url: global_url + 'ajaxfiles/profile_management.php',
			//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
			data:{CliNo : CliNo, ServiceID : ServiceID, inp_setto : INP, snr_setto : SNR, 'action' : 'submit_new', FullName : FullName}, 
			type: 'get',
			dataType: 'json',
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
				
				if(result.ret == true)			
				{
					var uuid = result.uuid;
					$('#msg').html('Your request was submitted successfully. Profile changes can take up to 5 minutes to take effect. Please <a data-ajax="true" rel="external" onclick="refreshData(\''+uuid+'\', \''+CliNo+'\')" class="ui-link">click here</a> to refresh.');
					$('#SNRCombo').val('');
					$("#SNRCombo").trigger("change");
					$('#INPCombo').val('');
					$('#INPCombo').trigger("change");
				}
				else if(result.ret == false)
				{
					showAlert(global_errormsg);
					$('#SNRCombo').val('');
					$("#SNRCombo").trigger("change");
					$('#INPCombo').val('');
					$('#INPCombo').trigger("change");
				}
				
			},
			error: function (request,error) {
				// This callback function will trigger on unsuccessful action                
				showError(global_errormsg);
				$('#SNRCombo').val('');
				$("#SNRCombo").trigger("change");
				$('#INPCombo').val('');
				$('#INPCombo').trigger("change");
			}
		});                   
		        
		return false; // cancel original event to prevent form submitting
	});
});

function refreshData(uuid, CliNo)
{
	
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url + 'ajaxfiles/profile_management.php',
			//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
			data:{CliNo : CliNo, uuid : uuid}, 
			type: 'get',
			dataType: 'json',
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
				
				$('#msg').attr('style', 'display:none;');
				$('#error_msg').attr('style', 'display:none;');
				if(result.ret == true)			
				{
					var result_html_current = '<tr><td width="50%"><b>SNR (Signal to Noise Ratio)</b></td><td width="50%">'+ result.CurrentSNR +'</td></tr><tr><td><b>Interleaving / INP</b></td><td>'+ result.CurrentINP +'</td></tr>';
				
					$('#current_tbody').html(result_html_current);				
					$( "current_table" ).table( "refresh" );
					showAlertWOTitle('Profile Change request processed successfully');
				}
				else if(result.ret == 'Fail')
				{
					$('#error_msg').attr('style', 'display:"";');
					$('#error_msg').html('Profile change request rejected - System fault encountered - please re-submit your request at a later time.');					
				}
				else
				{
					$('#error_msg').attr('style', 'display:"";');
					$('#error_msg').html('The data was not returned in the timeframe expected. Please <a data-ajax="true" rel="external" onclick="refreshData(\''+uuid+'\', \''+CliNo+'\')" class="ui-link">click here</a> to refresh.');					
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