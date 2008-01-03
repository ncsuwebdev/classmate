window.addEvent('domready', function() {
    
    // search box stuff
    var searchBox = $('searchBox');
    var sitePrefix = $('sitePrefix').value;

    var completer = new Autocompleter.Ajax.Json(searchBox, sitePrefix + '/index/autoSuggest/', {
        'baseHref': sitePrefix + '/public/css/', 
        
        'postVar': 'search',
        
        'onRequest': function(el) {
            //indicator2.setStyle('display', '');
        },
        'onComplete': function(el) {
            //indicator2.setStyle('display', 'none');
        }
    });   
});