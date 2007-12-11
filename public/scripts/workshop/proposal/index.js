window.addEvent('domready', function() {
    
    // search box stuff
    var box = $('tags');
    var sitePrefix = $('sitePrefix').value;
    
    var myCompleter = new Autocompleter.Ajax.Json(box, sitePrefix + '/index/autoSuggest/', {
        'postVar' : 'search',
	
        'multi' : true,
        
    });   
});