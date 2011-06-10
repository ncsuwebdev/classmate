$('document').ready(function() {
	var status;
	
	$('tr.location').hover(function(){
		status = $(this).find('td.status').hasClass('enabled') ? 'Enabled' : 'Disabled';
		$(this).addClass('rowHover' + status);
	},
	function() {
		$(this).removeClass('rowHover' + status);
	}
	);
});