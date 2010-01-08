$('document').ready(function() {
	baseUrl = $('#baseUrl').val();
	
    $('#search').autocomplete(baseUrl + '/search/index/tag', {
    	multiple: false,
    	minChars: 0
    });	        
});