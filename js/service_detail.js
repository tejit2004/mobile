$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});
$(document).on('pageshow', '#service_detail', function(){ 
	var clientID = sessionStorage.getItem("clientID");
	var InventoryID = decodeURIComponent(GetParameterValues('ID'));
	var type = decodeURIComponent(GetParameterValues('type'));
	
	if(type != 'Connection')
	{
		$('#dslam_status').attr('style', 'display:none;');
		$('#line_usage').attr('style', 'display:none;');
		$('#login_log').attr('style', 'display:none;');
	}
	
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/service_detail.php',
			
		data:{clientID : clientID, InventoryID : InventoryID, product_type : type}, 
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
				if(type == 'Connection')
				{
					var caption = 'Order Ref';
					var value = result.ClientOrderRef;
				}
				else
				{
					var caption = 'Service Tag';
					var value = result.service_tag;
				}
				
				result_html += '<tr><td><b>Inventory ID</b></td><td>'+ result.id +'</td></tr><tr><td><b>Order ID</b></td><td>'+ result.orderID +'</td></tr><tr><td><b>Tel Number</b></td><td>'+ result.tel_no +'</td></tr><tr><td><b>Name</b></td><td>'+ result.name +'</td></tr><tr><td><b>'+ caption +'</b></td><td>'+ value +'</td></tr><tr><td><b>Manufacturer</b></td><td>'+ result.manufacturer +'</td></tr><tr><td><b>Model</b></td><td>'+ result.model +'</td></tr>';
				
				/*$( "table#service_detail_table tbody" )				
				.html( html )				
				.closest( "table#service_detail_table" )
				.table( "refresh" )*/
				
				$('#gItemID').html(result.gItemID);
				$('#CliNo').html(result.tel_no);
				
				$('#ParentChild').html(result.parent_child);
				$('#SupplierID').html(result.supplierID);
				$('#TailProviderID').html(result.tailproviderID);
				$('#OrderStage').html(result.orderStage);
				$('#PPP_Username').html(result.PPP_Username);
				$('#DSLNetwork').html(result.DSLNetwork);
				$('#Supplier_ServiceID').html(result.Supplier_ServiceID);
				$('#dslLineId').html(result.dslLineId);
				
				
				$('#manufacturer').val(result.manufacturer);
				$('#model').val(result.model);
				$('#name').val(result.name);
				$('#srno').val(result.tel_no);
				
				
				if(result.usageCap == '')
				{
					$('#line_usage').attr('style', 'display:none;');
				}
				
				if(result.PPP_Username != '' && result.supplierID == 'SR 0000') {}
				else
				{
					$('#login_log').attr('style', 'display:none;');					
				}
				
				if(type != 'Connection')
				{
					$('#full_log').attr('style', 'display:none;');
					$('#dslam_status').attr('style', 'display:none;');
					$('#line_profile').attr('style', 'display:none;');
					$('#reset_port').attr('style', 'display:none;');
				}
				
				if(type == 'Connection')
				{
					var settings_html = '';
					$('#settings_id').attr('style','display:"";');
					settings_html += '<tr><td><b>Username</b></td><td>'+ result.username +'</td></tr><tr><td><b>Password</b></td><td>'+ result.password +'</td></tr><tr><td><b>IP Subnet</b></td><td>'+ result.ip_subnet +'</td></tr>';
					
					$('#settings_tbody').html(settings_html);
					$( "settings_table" ).table( "refresh" );
					
				}
				
				$('#detail_tbody').html(result_html);				
				$( "service_detail_table" ).table( "refresh" );


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

$(document).on('click', '#dslam_status', function()
{	
	var gItemID = $('#gItemID').html();
	var CliNo = $('#CliNo').html();
	
	var ParentChild = $('#ParentChild').html();
	var SupplierID = $('#SupplierID').html();
	var TailProviderID = $('#TailProviderID').html();
	var OrderStage = $('#OrderStage').html();
	var DSLNetwork = $('#DSLNetwork').html();
	var Supplier_ServiceID = $('#Supplier_ServiceID').html();
	var dslLineId = $('#dslLineId').html();
	
	if(ParentChild == 'Child' && SupplierID == 'SR 0053')
	{
		showAlert('You cannot get the statistics on a secondary line in an LLU bonded set.');
		return false;
	}
	else if(OrderStage != 'Live')
	{
		showAlert('You can only get line statistics on lines that are Live.');
		return false;
	}
	else if(SupplierID != 'SR 0053' && TailProviderID != 'SR 0053' && TailProviderID != 'SR 0006' && TailProviderID != 'SR 0860')
	{
		showAlert('DSLAM stats and line profiles are not available for this type of service.');
		return false;
	}
	else if(TailProviderID == 'SR 0860' && (DSLNetwork != 'WBC' && DSLNetwork != 'FC'))
	{
		alert('DSLAM stats and line profiles are not currently available for this type of service.');
		return false;
	}
	else
	{
		$.mobile.changePage( "dslam_status.html?gItemID="+gItemID+"&CliNo="+CliNo+"&DSLNetwork="+DSLNetwork+"&Supplier_ServiceID="+Supplier_ServiceID+"&dslLineId="+dslLineId, { transition: "slide"}, true, true)						
	}
});

$(document).on('click', '#line_usage', function()
{	
	var gItemID = $('#gItemID').html();
	//$.mobile.changePage( "line_usage.html?gItemID="+gItemID, { transition: "slide"}, true, true)		
	document.location.href = "line_usage.html?gItemID="+gItemID;
});

$(document).on('click', '#login_log', function()
{	
	var PPP_Username = $('#PPP_Username').html();
	//$.mobile.changePage( "line_usage.html?gItemID="+gItemID, { transition: "slide"}, true, true)		
	document.location.href = "show_radius_log.html?PPP_Username="+PPP_Username;
});

$(document).on('click', '#full_log', function()
{	
	var CliNo = $('#CliNo').html();
	//$.mobile.changePage( "line_usage.html?gItemID="+gItemID, { transition: "slide"}, true, true)		
	document.location.href = "show_full_log.html?CliNo="+CliNo;
});

$(document).on('click', '#raise_case', function()
{	
	var InventoryID = decodeURIComponent(GetParameterValues('ID'));	
	var manufacturer = $('#manufacturer').val();
	var model = $('#model').val();
	var name = $('#name').val();
	var srno = $('#srno').val();	
	
	document.location.href = "raise_new_case.html?ID="+InventoryID+"&manufacturer="+manufacturer+"&model="+model+"&name="+name+"&srno="+srno;
	//$.mobile.changePage("raise_new_case.html?ID="+InventoryID+"&manufacturer="+manufacturer+"&model="+model+"&name="+name+"&srno="+srno, { transition: "slide"}, true, true)	
});

$(document).on('click', '#line_profile', function()
{	
	var type = decodeURIComponent(GetParameterValues('type'));
	var CliNo = $('#CliNo').html();
	var Flag_EditLineProfile = sessionStorage.getItem("Flag_EditLineProfile");
	var Supplier_ServiceID = $('#Supplier_ServiceID').html();
	var TailProviderID = $('#TailProviderID').html();
	var DSLNetwork = $('#DSLNetwork').html();
	
	if(Flag_EditLineProfile != 'Yes')
	{
		showAlert('You do not have privileges to make this change.');
		return false;
	}
	if(Supplier_ServiceID == '')
	{
		showAlert('Line profiles are not available for this type of service.');
		return false;
	}
	if(type != 'Connection' && TailProviderID != 'SR 0860')
	{
		showAlert('Line profiles are not available for this type of service.');
		return false;
	}
	if(TailProviderID == 'SR 0860' && (DSLNetwork != 'WBC' && DSLNetwork != 'FC'))
	{
		showAlert('Line profiles are not currently available for this type of service.');
		return false;
	}
	
	document.location.href = "profile_management.html?CliNo="+CliNo+"&ServiceID="+Supplier_ServiceID;
	
});

$(document).on('click', '#reset_port', function()
{	
	var type = decodeURIComponent(GetParameterValues('type'));
	var CliNo = $('#CliNo').html();
	var Flag_EditLineProfile = sessionStorage.getItem("Flag_EditLineProfile");
	var Supplier_ServiceID = $('#Supplier_ServiceID').html();
	var TailProviderID = $('#TailProviderID').html();
	var DSLNetwork = $('#DSLNetwork').html();
	var SupplierID = $('#SupplierID').html();
	
	showPortResetConfirm('Confirm', 'Are you sure you wish to reset the data port for this connection? This will temporarily disconnect the service?', 'Yes,No');	
	
	if(Flag_EditLineProfile != 'Yes')
	{
		showAlert('You do not have privileges to make this change.');
		return false;
	}
	if(Supplier_ServiceID == '')
	{
		showAlert('Reset Data Port not available for this type of service.');
		return false;
	}
	if(type != 'Connection' && TailProviderID != 'SR 0860')
	{
		showAlert('Reset Data Port not available for this type of service.');
		return false;
	}
	if(TailProviderID == 'SR 0860' && (DSLNetwork != 'WBC' && DSLNetwork != 'FC'))
	{
		showAlert('Reset Data Port currently not available for this type of service.');
		return false;
	}
	
	//document.location.href = "reset_dataport.html?CliNo="+CliNo+"&ServiceID="+Supplier_ServiceID+"&SID="+TailProviderID;
	
});

// process the confirmation dialog result
function onPortResetConfirm(buttonIndex) {
	var CliNo = $('#CliNo').html();
	var Supplier_ServiceID = $('#Supplier_ServiceID').html();
	var TailProviderID = $('#TailProviderID').html();
   if (buttonIndex == 1) 
   {
   		document.location.href = "reset_dataport.html?CliNo="+CliNo+"&ServiceID="+Supplier_ServiceID+"&SID="+TailProviderID;
   }
   else {
        return false;
    }
}

// Show a custom confirmation dialog

function showPortResetConfirm(title, message, buttons) {
	navigator.notification.confirm(
        message,  // message
        onPortResetConfirm,              // callback to invoke with index of button pressed
        title,            // title
        buttons          // buttonLabels
    );
}
