var Arrinventory = new Array();
var ArrinventoryName = new Array();
var Arrmake_model = new Array();
var Arrname = new Array();
var Arrsrno = new Array();
var Arrcli = new Array();

$(document).on('pageinit', '#add_new_case', function(){    	
	
	$('input[type="text"]').each(function()
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
	
	var clientID = sessionStorage.getItem("clientID");
	
	$.ajax({url: global_url+'ajaxfiles/add_new_case.php',
		//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
		
		data:{clientID : clientID, type : 'fetch'}, 
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
			
			var output = '';
			output =+ '<option value="">Select Inventory</option>';	
			
			var lines = result.split('#*');
			
			$.each(lines, function(key, line) 
			{
				var parts = line.split('||');
				if(parts.length == 6)
				{
					$.each(parts, function(key_part, part) {
						value = trim(part);
						if(key_part == 0)
							Arrinventory.push(value);
						else if(key_part == 1)	
							ArrinventoryName.push(value);
						else if(key_part == 2)	
							Arrmake_model.push(value);
						else if(key_part == 3)	
							Arrname.push(value);
						else if(key_part == 4)	
							Arrsrno.push(value);
						else if(key_part == 5)	
							Arrcli.push(value);
					});	
				}
			});					
			
			$.each(Arrinventory, function(id, id_val) {
				
				if(id_val != '')
				{
					var inventory_name = ArrinventoryName[id];			
					output += '<option value="'+id_val+'">'+id_val+' - '+inventory_name+'</option>';		
				}
			});
			
			
			
			if(output != '')
			{
				$("#add_inventoryid").html(output);
			}
			
				
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			alert('Network error has occurred please try again!');
		}
	});  
});
	
$(document).on('change', '#add_inventoryid', function(){ 	
	
	var inventory_val = $("#add_inventoryid").val();		
	
	for(i in Arrinventory)
	{		
		if(inventory_val == Arrinventory[i])
		{	
			$("#add_make_model").val(Arrmake_model[i]);
			$("#add_make_model").removeClass('text-label');
			$("#add_name").val(Arrname[i]);
			$("#add_name").removeClass('text-label');
			$("#add_srno").val(Arrsrno[i]);				
			$("#add_srno").removeClass('text-label');
			break;
		}		
	}
	  
});	

$(document).on('pageinit', '#add_new_case', function(){ 	

	$(document).on('click', '#submit_case', function() { 
	
		if($('#add_inventoryid').val().length > 0 && $('#subject').val().length > 0 && $('#add_description').val().length > 0){
            
				
				var add_inventoryid = $('#add_inventoryid').val();
				var add_make_model = $('#add_make_model').val();
				var add_name = $('#add_name').val();
				var add_srno = $('#add_srno').val();
				var subject = $('#subject').val();
				var add_description = $('#add_description').val();
				
				var clientID = sessionStorage.getItem("clientID");
				
				if(add_inventoryid == 'Enter Name' || subject == 'Case Name' || add_description == 'Description')
				{
					error += 'Please fill all necessary fields\n';
				}
				
				if(error == '')
				{
					$.ajax({url: global_url+'ajaxfiles/add_new_case.php',
						//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
						data:{type : 'save', add_inventoryid : add_inventoryid, add_make_model : add_make_model, add_name : add_name, add_srno : add_srno, subject : subject, add_description : add_description, clientID : clientID}, 
						type: 'post',                   
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
							$('#add_inventoryid').val('');
							$('#add_make_model').val('');
							$('#add_name').val('');
							$('#add_srno').val('');
							$('#subject').val('');
							$('#add_description').val('');
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
        } else {
            alert('Please fill all necessary fields');
        }           
            return false; // cancel original event to prevent form submitting
        });    
});