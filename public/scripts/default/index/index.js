var baseUrl = '';

$('document').ready(function() {
        
	baseUrl = $('#baseUrl').val();
	
	$('input#loginButton').click(function() {
		location.href = baseUrl + '/login';
	});
	
});