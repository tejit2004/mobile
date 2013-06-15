$(document).on('pageshow', '#line_usage', function(){ 
	var gItemID = decodeURIComponent($.urlParam('gItemID'));
	$("body").addClass('ui-disabled');
	$.ajax({url: global_url+'ajaxfiles/bandwidth.php',
			
		data:{gItemID : gItemID}, 
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
				
				/*$("table#daily_table tbody" )				
				.html( result.daily_table )				
				.closest( "table#daily_table" )
				.table( "refresh" )*/
				
				//alert(result.daily_table)
				
				$('#monthly_data').html(result.monthly_data);
				$('#monthly_dates').html(result.monthly_dates);
				
				$('#daily_detail_tbody').html(result.daily_table);				
				$( "daily_table" ).table( "refresh" );
				
				$('#monthly_detail_tbody').html(result.monthly_table);				
				$( "monthly_table" ).table( "refresh" );

			}
			else if(result.ret == false)
			{
				alert(result.error);	
			}	
		},
		error: function (request,error) {
			alert('Network error has occurred please try again!');
		}
	});  
});

$(document).on('click', '#monthly', function()
{
	document.getElementById('result_monthly').style.display = '';
	document.getElementById('result_daily').style.display = 'none';
	document.getElementById('result_graph').style.display = 'none';
});

$(document).on('click', '#daily', function()
{
	document.getElementById('result_monthly').style.display = 'none';
	document.getElementById('result_daily').style.display = '';
	document.getElementById('result_graph').style.display = 'none';
});

$(document).on('click', '#graph', function()
{
	document.getElementById('result_monthly').style.display = 'none';
	document.getElementById('result_daily').style.display = 'none';
	document.getElementById('result_graph').style.display = '';
	
	var monthly_data = $('#monthly_data').html();
	var monthly_dates = $('#monthly_dates').html();
	
	var data = monthly_data.split(',');
	var dates = monthly_dates.split(',');
	
	seriesData = new Array();
	
	for (i=0; i<data.length; i++) 
	{
    	seriesData.push(parseFloat(data[i]));
	}
	
	$('#result_graph').highcharts({
        chart: {
            type: 'bar'
        },
		title: {
			text: 'Monthly Usage',
			x: -20 //center
		},
        
		xAxis: {
			categories: dates
		},
		yAxis: {
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},		
		
        legend: {
            layout: 'vertical',
            floating: true,
            backgroundColor: '#FFFFFF',
            align: 'right',
            verticalAlign: 'top',
            y: 60,
            x: -60
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.series.name +'</b><br/>'+
                    this.x +': '+ this.y;
            }
        },
		credits: {
		enabled: false
	  },
        series: [{
            data: seriesData,
            name: 'Usage data'
        }]
    });
	
	
});