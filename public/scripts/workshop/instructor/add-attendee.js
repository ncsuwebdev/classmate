$('document').ready(function() {

	$('#users').parent().after('<div id="userDisplay"></div>');
    
    $('#users option:selected').each(function() {
    	addUser($(this).attr('value'), $(this).text());
    });
    
    var userData = new Array();
    $('#users option').each(function() {
    	userData[userData.length] = {id: $(this).attr('value'), name: $(this).text()};
    });
    
    $('#users').css('display', 'none');
    
    $('#users').parent().append('<input id="userSearch" type="text" />');
    
    $('#userSearch').autocomplete(userData, {
    	multiple: false,
    	minChars: 0,
    	matchContains: true,
    	formatItem: function(row, i, max) {
			return row.name;
		}

    }).result(function(event, data, formatted) {
    	$('#users option[value=' + data.id + ']').attr('selected', true);
    	$('#userSearch').val('');
    	
    	addUser(data.id, data.name);
    });
});

var users = new Array();

function addUser(id, display)
{
	if ($.inArray(id, users) == -1) {
    	var html = '<div class="user" id="editor_' + id + '">'
    	         + '<a class="ui-state-default ui-corner-all linkButtonNoText removeUser"><span class="ui-icon ui-icon-minusthick"/></a>'
    	         + '<span>' + display + '</span>'
    	         + '</div>';
    	
    	$('#userDisplay').append(html).css('display', 'block');
    	
    	users[id] = id;
    	
    	$('#user_' + id + ' a').click(function(e) {
    		e.preventDefault();
    		
    		var userId = $(this).parent().attr('id').replace(/^[^_]*_/, '');
    		
    		$('#users option[value=' + userId + ']').attr('selected', false);
    		
    		$(this).parent().remove();
    		users[userId] = undefined;
    		if ($('#userDisplay').children().length == 0) {
    			$('#userDisplay').css('display', 'none');
    		}
    	});
	}
}