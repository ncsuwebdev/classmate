$('document').ready(function() {
	$('#checkAll').click(function(e) {
		$('form input:checkbox').attr('checked', true);
	});
	
	$('#uncheckAll').click(function(e) {
		$('form input:checkbox').attr('checked', false);
	});
});