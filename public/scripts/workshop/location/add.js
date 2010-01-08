var baseUrl;

$('document').ready(function() {
	baseUrl = $('#baseUrl').val();
	
	$('#description').wysiwyg({
    	controls : {
        	separator03 : { visible : true },
        	separator00 : { visible : true },
        	insertOrderedList : { visible : true },
        	insertUnorderedList : { visible : true },
        	justifyLeft: { visible : true },
        	justifyCenter: { visible : true },
        	justifyRight: { visible : true }
    	}
    });
});