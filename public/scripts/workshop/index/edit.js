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
    $('#prerequisites').wysiwyg({
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
    $('#tags').autocomplete(baseUrl + '/search/index/tag', {
    	multiple: true,
    	minChars: 0
    });
    
    $('#editors').parent().after('<div id="editorDisplay"></div>');
    
    $('#editors option:selected').each(function() {
    	addEditor($(this).attr('value'), $(this).text());
    });
    
    var editorData = new Array();
    $('#editors option').each(function() {
    	editorData[editorData.length] = {id: $(this).attr('value'), name: $(this).text()};
    });
    
    $('#editors').css('display', 'none');
    
    $('#editors').parent().append('<input id="editorSearch" type="text" />');
    
    $('#editorSearch').autocomplete(editorData, {
    	multiple: false,
    	minChars: 0,
    	matchContains: true,
    	formatItem: function(row, i, max) {
			return row.name;
		}

    }).result(function(event, data, formatted) {
    	$('#editors option[value=' + data.id + ']').attr('selected', true);
    	$('#editorSearch').val('');
    	
    	addEditor(data.id, data.name);
    });	
});

var editors = new Array();

function addEditor(id, display)
{
	if ($.inArray(id, editors) == -1) {
    	var html = '<div class="editor" id="editor_' + id + '">'
    	         + '<a class="ui-state-default ui-corner-all linkButtonNoText removeEditor"><span class="ui-icon ui-icon-minusthick"/></a>'
    	         + '<span>' + display + '</span>'
    	         + '</div>';
    	
    	$('#editorDisplay').append(html).css('display', 'block');
    	
    	editors[id] = id;
    	
    	$('#editor_' + id + ' a').click(function(e) {
    		e.preventDefault();
    		
    		var editorId = $(this).parent().attr('id').replace(/^[^_]*_/, '');
    		
    		$('#editors option[value=' + editorId + ']').attr('selected', false);
    		
    		$(this).parent().remove();
    		editors[editorId] = undefined;
    		if ($('#editorDisplay').children().length == 0) {
    			$('#editorDisplay').css('display', 'none');
    		}
    	});

	}
}