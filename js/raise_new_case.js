$(document).on('pageshow', '#raise_new_case', function(){ 
	
	$('input[id="subject"]').each(function()
	{ 
		this.value = $(this).attr('title');
		$(this).addClass('text-label');
	 
		$(this).focus(function(){
			if(this.value == $(this).attr('title')) {
				this.value = '';
				$(this).removeClass('text-label');
			}
		});
	 
		$(this).blur(function(){
			if(this.value == '') {
				this.value = $(this).attr('title');
				$(this).addClass('text-label');
			}
		});
	});
	
	$('textarea[id="add_description"]').each(function()
	{ 
		this.value = $(this).attr('title');
		$(this).addClass('text-label');
	 
		$(this).focus(function(){
			if(this.value == $(this).attr('title')) {
				this.value = '';
				$(this).removeClass('text-label');
			}
		});
	 
		$(this).blur(function(){
			if(this.value == '') {
				this.value = $(this).attr('title');
				$(this).addClass('text-label');
			}
		});
	});
	
	var InventoryID = decodeURIComponent(GetParameterValues('ID'));	
	var manufacturer = decodeURIComponent(GetParameterValues('manufacturer'));	
	var model = decodeURIComponent(GetParameterValues('model'));	
	var name = decodeURIComponent(GetParameterValues('name'));	
	var srno = decodeURIComponent(GetParameterValues('srno'));		
	
	var add_make_model = manufacturer + ' ' + model;
	
	$('#add_inventoryid').val(InventoryID);
	$('#add_make_model').val(add_make_model);
	$('#add_name').val(name);
	$('#add_srno').val(srno);
});	

$(document).on('click', '#submit_case', function() 
{ 

	if($('#add_inventoryid').val().length > 0 && $('#subject').val().length > 0 && $('#add_description').val().length > 0)
	{
		
			var username = localStorage.getItem('username');	
			var add_inventoryid = $('#add_inventoryid').val();				
			var subject = $('#subject').val();
			var add_description = $('#add_description').val();
			
			
			var clientID = sessionStorage.getItem("clientID");
			var error = '';
			if(add_inventoryid == 'Inventory ID' || subject == 'Case Name' || add_description == 'Fault Description')
			{
				error += 'Please fill all necessary fields\n';
			}
			
			if(error == '')
			{
				$("body").addClass('ui-disabled');
				$.ajax({url: global_url+'ajaxfiles/add_new_case.php',
					//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
					data:{type : 'save', add_inventoryid : add_inventoryid, subject : subject, add_description : add_description, clientID : clientID, username : username}, 
					type: 'post',                   
					async: true,
					dataType: 'json',	
					beforeSend: function() {
						// This callback function will trigger before data is sent
						//$.mobile.showPageLoadingMsg(true); // This will show ajax spinner
						
						$.mobile.loading( "show", {text: "Loading Please wait",textVisible: true,theme: "a",html: ""});
						
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
							
							/*$('#caseid_div').attr('style', 'display:"";');
							$('#caseid_span').html(result.caseID);*/
							$.mobile.changePage('case_detail.html?From=new&ID='+result.caseID, {
								changeHash: true,
								dataUrl: "",    //the url fragment that will be displayed for the test.html page
								transition: "slide"  //if not specified used the default one or the one defined in the default settings
							});
							
						}
						else
						{
							alert('Some problem occured');
						}
						
						
					},
					error: function (request,error) {
						// This callback function will trigger on unsuccessful action                
						alert('Network error has occurred please try again!');
					}
				});  
			}
			else
			{
				alert(error);
			}
	} 
	else 
	{
		alert('Please fill all necessary fields');
	}           
	return false; // cancel original event to prevent form submitting
});   

