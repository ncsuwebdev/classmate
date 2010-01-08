var baseUrl;

$('document').ready(function() {
	baseUrl = $('#baseUrl').val();
	
	$('.modalButton').click(function(e) {
		
		e.preventDefault();
		$('#editDialog').dialog('open');
		$('#modalLoading').css('display', 'block');
		
		var clicked = $(this);
		
    	$.ajax({
    		type: "get",
    		url: clicked.attr('href'),
    		success: function(data){
    			$('#editDialog').html(data);
    	        $('button#cancel').click(function(e) {
    	        	$('#editDialog').dialog('close');
    	        });
    	        
    	        $('input[type=submit], input[type=button], input[type=reset], button').addClass('ui-state-default ui-corner-all');

    	        // adds hover class to elements with the class state-default
    	        $('a.ui-state-default, input.ui-state-default, button.ui-state-default').hover(
    	    		function(){ $(this).addClass('ui-state-hover'); }, 
    	    		function(){ $(this).removeClass('ui-state-hover'); }
    	      	);    	        
    		},
    		error: function(msg) {
    			$('#editDialog').dialog('close');
    			alert(msg);
    		}
    	});
	});

	$('#editDialog').dialog({ 
        modal: true, 
        autoOpen: false,
        resizable: false,
        overlay: { 
            opacity: 0.5, 
            background: "black" 
        }, 
        width: 600,
        height: 400,
        close: function(event, ui) {
        	$(this).html('<div id="modalLoading"></div>');
        }
    }, "close");
    

});