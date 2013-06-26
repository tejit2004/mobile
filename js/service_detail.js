$(document).on('pageshow', '#service_detail', function(){ 
	var clientID = sessionStorage.getItem("clientID");
	var InventoryID = decodeURIComponent(GetParameterValues('ID'));
	var type = decodeURIComponent(GetParameterValues('type'));
	
	if(type != 'Connection')
	{
		$('#dslam_status').attr('style', 'display:none;');
		$('#line_usage').attr('style', 'display:none;');
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
				result_html += '<tr><td><b>Inventory ID : </b></td><td>'+ result.id +'</td></tr><tr><td><b>Order ID : </b></td><td>'+ result.orderID +'</td></tr><tr><td><b>Tel Number : </b></td><td>'+ result.tel_no +'</td></tr><tr><td><b>Name : </b></td><td>'+ result.name +'</td></tr><tr><td><b>Service Tag : </b></td><td>'+ result.service_tag +'</td></tr><tr><td><b>Manufacturer : </b></td><td>'+ result.manufacturer +'</td></tr><tr><td><b>Model : </b></td><td>'+ result.model +'</td></tr><tr><td><b>Description : </b></td><td>'+ result.productdesc +'</td></tr>';
				
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
				
				$('#manufacturer').val(result.manufacturer);
				$('#model').val(result.model);
				$('#name').val(result.name);
				$('#srno').val(result.tel_no);
				if(result.usageCap == '')
				{
					$('#line_usage').attr('style', 'display:none;');
				}
				
				$('#detail_tbody').html(result_html);				
				$( "service_detail_table" ).table( "refresh" );

			}
			else if(result.ret == false)
			{
				alert(result.error);	
			}
			
				
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			alert(global_errormsg);
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
	
	if(ParentChild == 'Child' && SupplierID == 'SR 0053')
	{
		alert('You cannot get the statistics on a secondary line in an LLU bonded set.');
		return false;
	}
	else if(OrderStage != 'Live')
	{
		alert('You can only get line statistics on lines that are Live.');
		return false;
	}
	else if(SupplierID != 'SR 0053' && TailProviderID != 'SR 0053')
	{
		alert('DSLAM stats and line profiles are not available for this type of service.');
		return false;
	}
	else
	{
		$.mobile.changePage( "dslam_status.html?gItemID="+gItemID+"&CliNo="+CliNo, { transition: "slide"}, true, true)						
	}
});

$(document).on('click', '#line_usage', function()
{	
	var gItemID = $('#gItemID').html();
	//$.mobile.changePage( "line_usage.html?gItemID="+gItemID, { transition: "slide"}, true, true)		
	document.location.href = "line_usage.html?gItemID="+gItemID;
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
