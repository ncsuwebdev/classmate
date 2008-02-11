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
    
    $$('.arrow').each(function(el) {
        el.addEvent('click', function(e) { 
            hideAllMenus();          
            var menu = $('menu_' + el.id.replace(/^[^_]*_/, ''));
                        
            menu.setPosition({
                relativeTo: el,
                position: 'bottomRight',
                edge: 'topRight'
            });
            
            menu.style.display = 'block';
            el.addClass('arrowSelect');
            
        });
    });  
    
});

document.addEvent('click', function(e) {
    var e = new Event(e);
    
    if (!$(e.target).hasClass('arrow')) {
        hideAllMenus();
    }
    
    return true;
});

function hideAllMenus() {
    $$('.sub_menu').each(function (el) {
        el.style.display = 'none';
        $('tab_' + el.id.replace(/^[^_]*_/, '')).removeClass('arrowSelect');
    });
}