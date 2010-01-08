var baseUrl = '';

$('document').ready(function() {
        
    $('#accountId').css('display', 'none');
    
    $('#accountId').parent().append('<input id="accountSearch" type="text" />');
    
    var accountData = new Array();
    $('#accountId option').each(function() {
    	accountData[accountData.length] = {id: $(this).attr('value'), name: $(this).text()};
    });
    
    $('#accountSearch').autocomplete(accountData, {
    	multiple: false,
    	minChars: 0,
    	matchContains: true,
    	formatItem: function(row, i, max) {
			return row.name;
		}
    }).result(function(event, data, formatted) {
    	$('#accountId option[value=' + data.id + ']').attr('selected', true);
    });	        
});