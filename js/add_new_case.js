/*var Arrinventory = new Array();
var ArrinventoryName = new Array();
var Arrmake_model = new Array();
var Arrname = new Array();
var Arrsrno = new Array();
var Arrcli = new Array();*/

var findinventory_div;
var inventoryresult_div;

$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});

function fillDetail(id)
{
	var inventory_id = 'inventory_'+id;
	var manufacturer_id = 'manufacturer_'+id;
	var model_id = 'model_'+id;
	var name_id = 'name_'+id;
	var srno_id = 'srno_'+id;
	
	var inventory = document.getElementById(inventory_id).getAttribute('title');
	var manufacturer = document.getElementById(manufacturer_id).getAttribute('title');
	var model = document.getElementById(model_id).getAttribute('title');
	var name = document.getElementById(name_id).getAttribute('title');
	var srno = document.getElementById(srno_id).getAttribute('title');	
	
	var manufacturer_model = manufacturer + ' ' + model;
 	
	$('#add_inventoryid').val(inventory);							
	$("#add_inventoryid").removeClass('text-label');
	
	$('#add_make_model').val(manufacturer_model);							
	$("#add_make_model").removeClass('text-label');
	
	$('#add_name').val(name);	
	$("#add_name").removeClass('text-label');
	
	$('#add_srno').val(srno);	
	$("#add_srno").removeClass('text-label');
	
	$('#inventoryResult').popup("close");
	
}

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
	
	$('textarea').each(function()
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
	
	/*var clientID = sessionStorage.getItem("clientID");
	
	$.ajax({url: global_url+'ajaxfiles/add_new_case.php',
		
		
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
			output += '<option value="0">Select Inventory</option>';	
			
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
	}); */ 
});


$(document).on('click', '#find_inventory', function(){ 

	var val = trim($("#find_inventory_input").val());
	var clientID = sessionStorage.getItem("clientID");
	
	if(val != '')
	{
		$("body").addClass('ui-disabled');
		findinventory_div.remove();
		$.ajax({url: global_url+'ajaxfiles/findinventory.php',				
			data:{clientID : clientID, search_text : val}, 
			type: 'get',                   
			async: true,
			beforeSend: function() {
				$.mobile.loading( "show", {text: "Finding Inventories",textVisible: true,theme: "a",html: ""});			
			},
			complete: function() {
				$.mobile.loading( 'hide' );
				$("body").removeClass('ui-disabled');
			},
			success: function (result) {
				
				
				
				$('#detail_tbody').html(result);	
				$( "findinventory_table" ).table( "refresh" );
				
				$('#inventoryResult').popup("open")
				
				
				/*var $popUp = $("<div/>").popup({
					dismissible: false,
					theme: "c",
					overlyaTheme: "a",
					transition: "pop"
				}).on("popupafterclose", function () {
					//remove the popup when closing
					$(this).remove();
				});
				
				//create a message for the popup
				$("<div/>", {
					text: result,
					"data-role": "popup",
					"data-theme": "c"
				}).appendTo($popUp);
				
				//create a back button
				$("<a>", {        
					"data-rel": "back",
					"data-role": "button",
					"data-theme": "c",
					"data-icon": "delete",
					"data-iconpos": "notext",
					"class": "ui-btn-right"
				}).appendTo($popUp);
			
				$popUp.popup('open').trigger("create");
				//$popUp.popup('open');		*/	
					
			},
			error: function (request,error) {
				// This callback function will trigger on unsuccessful action                
				showError(global_errormsg);
			}
		}); 
	}
	else
	{
		showAlert('Please enter search criteria.');
	}
	
});


$(document).on('click', '#create', function(){ 	
    //create a div for the popup
    findinventory_div = $("<div/>").popup({
        dismissible: false,
        theme: "c",
        overlyaTheme: "a",		
        transition: "pop",
        positionTo: "window",
        tolerance: "5,0"	
    }).on("popupafterclose", function () {
        //remove the popup when closing
        $(this).remove();
    });
    //create a title for the popup
    $("<h4/>", {
        text: "Please enter the Device information"
    }).appendTo(findinventory_div);

    //create a message for the popup
    $("<div/>", {
        text: "Manufacturer, Model, Name and Serial Number OR Exact InventoryID"
    }).appendTo(findinventory_div);

    //create a form for the pop up
    $("<form>").append($("<input/>", {
        type: "text",
        name: "find_inventory_input",
		id: "find_inventory_input"        
    })).appendTo(findinventory_div);

    //Create a submit button(fake)
    
	
    $("<form>").append($("<input/>", {
        type: "button",
        name: "find_inventory",
		id: "find_inventory",
		value: "Search",
		"data-theme": "b"
		
    })).appendTo(findinventory_div);
	
	//create a back button
    $("<a>", {        
        "data-rel": "back",
		"data-role": "button",
		"data-theme": "c",
		"data-icon": "delete",
		"data-iconpos": "notext",
		"class": "ui-btn-right"
    }).appendTo(findinventory_div);

    findinventory_div.popup('open').trigger("create");
    //$popUp.popup('open');
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
            
				var username = localStorage.getItem('username');	
				var add_inventoryid = $('#add_inventoryid').val();
				var add_make_model = $('#add_make_model').val();
				var add_name = $('#add_name').val();
				var add_srno = $('#add_srno').val();
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
						data:{type : 'save', add_inventoryid : add_inventoryid, add_make_model : add_make_model, add_name : add_name, add_srno : add_srno, subject : subject, add_description : add_description, clientID : clientID, username : username}, 
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
								$('#caseid_span').html(result.caseID);
								
								$('#add_inventoryid').val('Inventory ID');
								$('#add_inventoryid').attr('title', 'Inventory ID');								
								$("#add_inventoryid").addClass('text-label');
								
								$('#add_make_model').val('Device Make and Model');
								$('#add_make_model').attr('title', 'Device Make and Model');								
								$("#add_make_model").addClass('text-label');
								
								$('#add_name').val('Name');
								$('#add_name').attr('title', 'Name');
								$("#add_name").addClass('text-label');
								
								$('#add_srno').val('Serial Number');
								$('#add_srno').attr('title', 'Serial Number');
								$("#add_srno").addClass('text-label');
								
								$('#subject').val('Case Name');
								$("#subject").addClass('text-label');
								
								$('#add_description').val('Description');					
								$("#add_description").addClass('text-label');*/
								
								$.mobile.changePage('case_detail.html?From=new&ID='+result.caseID, {
									changeHash: true,
									dataUrl: "",    //the url fragment that will be displayed for the test.html page
									transition: "slide"  //if not specified used the default one or the one defined in the default settings
								});
								
							}
							else
							{
                                showError('Some problem occured');
							}
							
							
						},
						error: function (request,error) {
							// This callback function will trigger on unsuccessful action                
							showError(global_errormsg);
						}
					});  
				}
				else
				{
					showAlert(error);
				}
        } else {
            showAlert('Please fill all necessary fields');
        }           
            return false; // cancel original event to prevent form submitting
        });    
});
