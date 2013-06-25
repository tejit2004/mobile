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
				$('#monthly_overusage_data').html(result.monthly_overusage_data);				
				$('#monthly_dates').html(result.monthly_dates);
				
				$('#daily_data').html(result.daily_data);
				$('#daily_dates').html(result.daily_dates);
				
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
	
	var daily_data = $('#daily_data').html();
	var daily_dates = $('#daily_dates').html();
	
	var data_daily = daily_data.split(',');
	var dates_daily = daily_dates.split(',');
	
	seriesData_daily = new Array();
	
	for (i=0; i<data_daily.length; i++) 
	{
    	seriesData_daily.push(parseFloat(data_daily[i]));
	}
	
	DrawDailyGraph(dates_daily, seriesData_daily, 'daily');
	
});

$(document).on('click', '#daily_graph', function()
{
	document.getElementById('result_graph_monthly').style.display = 'none';
	document.getElementById('result_graph_daily').style.display = '';
	
	var daily_data = $('#daily_data').html();
	var daily_dates = $('#daily_dates').html();
	
	var data_daily = daily_data.split(',');
	var dates_daily = daily_dates.split(',');
	
	seriesData_daily = new Array();
	
	for (i=0; i<data_daily.length; i++) 
	{
    	seriesData_daily.push(parseFloat(data_daily[i]));
	}
	
	DrawDailyGraph(dates_daily, seriesData_daily);	
});

$(document).on('click', '#monthly_graph', function()
{
	document.getElementById('result_graph_monthly').style.display = '';
	document.getElementById('result_graph_daily').style.display = 'none';
	
	var monthly_data = $('#monthly_data').html();
	var monthly_dates = $('#monthly_dates').html();	
	var monthly_overusage_data = $('#monthly_overusage_data').html();
	
	var data = monthly_data.split(',');
	var dates = monthly_dates.split(',');
	var overusge_data = monthly_overusage_data.split(',');
	
	seriesData = new Array();
	overusageData = new Array();
	
	for (i=0; i<data.length; i++) 
	{
    	seriesData.push(parseFloat(data[i]));
	}
	
	for (i=0; i<overusge_data.length; i++) 
	{
    	overusageData.push(parseFloat(overusge_data[i]));
	}
	
	DrawMonthlyGraph(dates, seriesData, overusageData);
});

function DrawMonthlyGraph(dates, seriesData, overusageData)
{
	$('#result_graph_monthly').highcharts({
        chart: {
                type: 'bar'
            },
		title: {
			text: 'Monthly Usage'
		},
		credits: {
            enabled: false
        },
		colors: [		   
		   '#910000',		   
		   '#2f7ed8'		   
		],
		xAxis: {
			categories: dates
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Value'
			}
		},
		legend: {
			backgroundColor: '#FFFFFF',
			reversed: true
		},
		plotOptions: {
			series: {
				stacking: 'normal'
			}
		},
			series: [{
			name: 'Overusage',
			data: overusageData
		},{
			name: 'Usage Data',
			data: seriesData
		}]
    });	
}

function DrawDailyGraph(dates, seriesData)
{
	$('#result_graph_daily').highcharts({
        chart: {
                type: 'bar'
            },
		title: {
			text: 'Daily Usage'
		},
		credits: {
            enabled: false
        },
		colors: [		   
		   '#2f7ed8',
		   '#910000'		   
		],
		xAxis: {
			categories: dates
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Value'
			}
		},
		legend: {
			backgroundColor: '#FFFFFF',
			reversed: true
		},
		plotOptions: {
			series: {
				stacking: 'normal'
			}
		},
		series: [{
			name: 'Usage Data',
			data: seriesData			
		}]
    });	
}
