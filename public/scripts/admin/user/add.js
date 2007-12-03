window.addEvent('domready', function() {

    var realm = $('realm');
    realm.addEvent('change', function() {
        var sel = realm.options[realm.options.selectedIndex];
        
        var login = $('emailRow');
        
        if (sel.hasClass('autoLogin')) {
            login.style.display = 'none';
        } else {
            login.style.display = '';           
        }       
        
        $('loginDescription').setHTML(sel.title);
    });
    
    realm.fireEvent('change');
});

function goto(link)
{
    if (link.indexOf('?') != -1) {
        link += '&';
    } else {
        link += '?';
    }
    
    link += 'realm=' + $('realm').value;

    location.href=link;
}