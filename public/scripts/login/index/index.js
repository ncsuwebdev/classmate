window.addEvent('domready', function() {

    var realm = $('realm');
    realm.addEvent('change', function() {
        var selected = realm.options.selectedIndex;
        
        var sel = $(realm.options[selected]);
        
        var login = $('loginForm');
        var manual = $('manual');
        
        if (sel.hasClass('autoLogin')) {
            login.style.display = 'none';
        } else {
            login.style.display = 'block';           
        }       
    
        if (sel.hasClass('signup')) {
            manual.style.display = 'inline';
        } else {
            manual.style.display = 'none';
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