window.addEvent('domready', function() {

    var realm = $('realm');
    realm.addEvent('change', function() {
        var sel = realm.options[realm.options.selectedIndex];
        
        var login = $('loginForm');
        var manual = $('manual');
        if (sel.hasClass('auto')) {
            login.style.display = 'none';
            manual.style.display = 'none';
        } else {
            login.style.display = 'block';
            manual.style.display = 'inline';
        }
        
        $('loginDescription').setHTML(sel.title);
    });
    
    realm.fireEvent('change');
});