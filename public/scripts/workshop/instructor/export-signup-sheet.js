$('document').ready(function() {
	
	$('#fileType').change(function() {
		if($(this).val() == 'csv') {
			$('#header').hide(1000);
			$('td.signature').hide(1000);
			$('#signature').hide(1000);
			$('#previewTable').width('30%');
		} else {
			$('#previewTable').width('90%');
			$('#header').show(1000);
			$('#signature').fadeIn(2000);
			$('td.signature').fadeIn(2000);
		}
	});
});