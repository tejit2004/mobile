$(document).on('pageinit', '#update_case', function(){    	
	
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
});


$(document).on('click', '#submit_addnote', function() { // catch the form's submit event
	
	var subject = trim($('#subject').val());
	var comment = trim($('#comment').val());
	
	if(subject.length > 0 && comment.length > 0)
	{		
		$.ajax({url: global_url + 'ajaxfiles/add_comment.php',
			//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
			data:{subject : subject, comment : comment}, 
			type: 'post',
			dataType: 'json',	                   
			async: true,
			beforeSend: function() {
				// This callback function will trigger before data is sent
				//$.mobile.showPageLoadingMsg(true); // This will show ajax spinner
				//$.mobile.loading( 'show' );
				$.mobile.loading( "show", {	text: "Updating Case",textVisible: true,theme: "a",html: ""});
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete
				//$.mobile.hidePageLoadingMsg(); // This will hide ajax spinner						
				$.mobile.loading( 'hide' );
			},
			success: function (result) {                							
					resultObject.formSubmitionResult = result;
					if(result.ret == true)
					{		
						$( ".update_case" ).dialog( "close" );					
					}
					else
					{
						alert('Incorrect Username or password');
					}
			},
			error: function (request,error) {
				// This callback function will trigger on unsuccessful action                
				alert('Network error has occurred please try again!');
			}
		});
	}
});

$(document).on('pageinit', '#case_detail', function(){    	
	
	var clientID = sessionStorage.getItem("clientID");
	CaseID = decodeURIComponent($.urlParam('ID'));
	
	$.ajax({url: global_url+'ajaxfiles/case_detail.php',
			
		data:{clientID : clientID, CaseID : CaseID}, 
		type: 'get',                   
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
		},
		success: function (result) {						
			//$('#view_cases_tbody').html(result);
			//$("#table-column-toggle").table("refresh");		
			//$("#table-column-toggle").table-columntoggle( "refresh" );	
			
			
			$('#summary').html(result.summary);	
			
			var html = '';
			if(result.ret == true)
			{
				html += '<tr><td><b>Case ID : </b></td><td>'+ result.id +'</td></tr><tr><td><b>Status : </b></td><td>'+ result.status +'</td></tr><tr><td><b>Case Name : </b></td><td>'+ result.subject +'</td></tr><tr><td><b>Created By : </b></td><td>'+ result.created_by +'</td></tr><tr><td><b>Created Date : </b></td><td>'+ result.created_date +'</td></tr><tr><td><b>Owner : </b></td><td>'+ result.owner +'</td></tr><tr><td><b>Case Type : </b></td><td>'+ result.case_type +'</td></tr><tr><td><b>Contract : </b></td><td>'+ result.contract +'</td></tr><tr><td><b>Description : </b></td><td>'+ result.desc +'</td></tr>';
			}
			
			$('.summary').html(result.summary);
			$( "table#case_detail_table tbody" )
            
            .html( html )
            
            .closest( "table#case_detail_table" )
            .table( "refresh" )
            // Trigger if the new injected markup contain links or buttons that need to be enhanced
            //.trigger( "create" );
			
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			alert('Network error has occurred please try again!');
		}
	});  
});

var resultObject = {
    formSubmitionResult : null  
}