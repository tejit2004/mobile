$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
});

$(document).on('pageinit', '#feedback', function(){    	
	
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
	
	$('#characterLeft').text('255 characters left');
	
	
	
	$('#add_feedback').keydown(function () {
		var max = 255;
		
		var len = $(this).val().length;
		if (len >= max) {
			$(this).val($(this).val().substring(0, max));
			$('#characterLeft').text(' you have reached the limit');
		} else {
			var ch = max - len;
			$('#characterLeft').text(ch + ' characters left');
		}
	});
});

$(document).on('click', '#submit_feedback', function() { // catch the form's submit event
	if($('#add_feedback').val().length > 0){
		
			var comment = $('#add_feedback').val();
			var CompanyName = sessionStorage.getItem("CompanyName");
			var FullName = sessionStorage.getItem("FullName");
			if($.trim(comment) == 'Comments')
			{
				showAlert('Please enter comment');
				return false;
			}
		
			$("body").addClass('ui-disabled');
			$.ajax({url: global_url + 'ajaxfiles/feedback.php',
				//data:{action : 'login', formData : $('#check-user').serialize()}, // Convert a form to a JSON string representation
				data:{comment : comment, CompanyName : CompanyName, FullName : FullName}, 
				type: 'post',
				dataType: 'json',	                   
				async: true,
				beforeSend: function() {
					$.mobile.loading( "show", {	text: "Loading Please wait",textVisible: true,theme: "a",html: ""});
				},
				complete: function() {
					$.mobile.loading( 'hide' );
					$("body").removeClass('ui-disabled');
				},
				success: function (result) {                							
						
						if(result.ret == true)
						{
							showAlertWOTitle('Thank you for submitting your feedback.');							
							$.mobile.changePage('list.html', {
							changeHash: true,
							dataUrl: "",    //the url fragment that will be displayed for the test.html page
							transition: "slide"  //if not specified used the default one or the one defined in the default settings
							});
						}
						else
						{
							showError(result.error);
						}
				},
				error: function (request,error) {
					// This callback function will trigger on unsuccessful action                
					showError(global_errormsg);
				}
			});                   
	} else {
		//do_alert(0,'Please fill all necessary fields.');
		showAlert('Please enter comment');
		//alert('Please fill all necessary fields');
	}           
		return false; // cancel original event to prevent form submitting
}); 

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


