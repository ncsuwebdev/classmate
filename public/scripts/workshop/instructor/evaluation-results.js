$('document').ready(function() {

	var totalEntries = parseInt($('#totalEntries').val(), 10);
	
	$('.graph').each(function() {
		var labels = new Array();
		var values = new Array();
		
		$(this).find('thead tr th').each(function() {
			labels[labels.length] = $(this).text();
		});
				
		$(this).find('tbody tr td').each(function() {
			var val = parseInt($(this).text(), 10);
			
			labels[values.length] += ' (' + val + ')';
			values[values.length] = val;
		});
		
		var thisId = $(this).attr('id');
		
		$(this).parent().gchart({
			dataLabels: labels,
			width: 600,
			height: 150,
			chartColor: 'FEFDFA',
			series: [$.gchart.series('answers_' + thisId, values, 'red')]
		});
	});
});