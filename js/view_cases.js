$(document).bind('mobileinit', function(){
      $.mobile.metaViewportContent = 'width=device-width, user-scalable=yes';
	  
});

$(window).on("orientationchange",function(event){
  
  if(event.orientation == 'landscape')
  {
	  $('#table-column-toggle').attr('style', 'display:none;');	
	  $('#table-column-toggle1').attr('style', 'display:"";');	
	  $('.ui-table-columntoggle-btn').hide();
  }
  else
  {
	  $('#table-column-toggle1').attr('style', 'display:none;');	
	  $('#table-column-toggle').attr('style', 'display:"";');
	  $('.ui-table-columntoggle-btn').show();
  }
});



$(document).on('pagebeforeshow', '#view_cases', function(){    	
	
	var clientID = sessionStorage.getItem("clientID");
	
	  if(window.orientation == 90)
	  {
		  $('#table-column-toggle').attr('style', 'display:none;');	
		  $('#table-column-toggle').attr('data-mode', 'none');	
		  $('#table-column-toggle1').attr('style', 'display:"";');	
		  $('#table-column-toggle1').attr('data-mode', 'columntoggle');	
		  
		  $('.ui-table-columntoggle-btn').hide();
	  }
	  else
	  {
		  $('#table-column-toggle1').attr('style', 'display:none;');	
		   $('#table-column-toggle1').attr('data-mode', 'none');
		  $('#table-column-toggle').attr('style', 'display:"";');	
		  $('#table-column-toggle').attr('data-mode', 'columntoggle');	
		  
		  $('.ui-table-columntoggle-btn').show();
	  }
	  
	
	
	$.ajax({url: global_url+'ajaxfiles/view_cases_2.php',
			
		data:{clientID : clientID}, 
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
			//$('#view_cases_tbody').html(result);
			//$("#table-column-toggle").table("refresh");		
			//$("#table-column-toggle").table-columntoggle( "refresh" );	
			
			var table_rows_portrait = '';
			var table_rows_landscape = '';
			var json = $.parseJSON(result);
				$(json).each(function(i,val){
					if(i == "undefined" || i == undefined)
					{}
					else
					{
						$.each(val,function(k,v){
							
							if(v.ret == false)
							{
								table_rows_portrait += '<tr><td colspan="3">'+v.error+'</td></tr>';
							}
							else
							{
								var shortcaseName = v.CaseName;
								var longcaseName = v.CaseName;
								if(v.CaseName.length > 17)
								{
									shortcaseName = v.CaseName.substring(0, 17);
									shortcaseName += '<a title="'+v.CaseName+'" rel="tooltip" id="'+i+'"><em>...</em></a>';
								}
								
								if(v.CaseName.length > 60)
								{
									longcaseName = v.CaseName.substring(0, 60);
									longcaseName += '<a title="'+v.CaseName+'" rel="tooltip" id="'+i+'"><em>...</em></a>';
								}
								
								 table_rows_portrait += '<tr><td><a class="ui-link" href="case_detail.html?From=existing&ID='+v.ID+'" data-ajax="true" rel="external" data-transition="slide">'+v.ID+'</a></td><td data-priority="1">'+shortcaseName+'</td><td data-priority="2">'+v.SerialNumber+'</td></tr>';
								 
								 table_rows_landscape += '<tr><td><a class="ui-link" href="case_detail.html?From=existing&ID='+v.ID+'" data-ajax="true" rel="external" data-transition="slide">'+v.ID+'</a></td><td data-priority="1">'+longcaseName+'</td><td data-priority="2">'+v.SerialNumber+'</td></tr>';
							}
							
						});
					}
				});
			
			
			
			
			$( "table#table-column-toggle tbody" )
            // Append the new rows to the body
            .html( table_rows_portrait )
            // Call the refresh method
            .closest( "table#table-column-toggle" )
            .table( "refresh" )
            // Trigger if the new injected markup contain links or buttons that need to be enhanced
            .trigger( "create" );
			
			
			$( "table#table-column-toggle1 tbody" )
            // Append the new rows to the body
            .html( table_rows_landscape )
            // Call the refresh method
            .closest( "table#table-column-toggle1" )
            .table( "refresh" )
            // Trigger if the new injected markup contain links or buttons that need to be enhanced
            .trigger( "create" );
            
            eval(document.getElementById("runscript").innerHTML);
			
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			showError(global_errormsg);
		}
	});  
});

var resultObject = {
    formSubmitionResult : null  
}
