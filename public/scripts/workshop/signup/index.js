window.addEvent('domready', function() {
   
    $$('.event').each(function(el) {
        el.addEvent('click', function() {
            if ($E('a', el)) {
                location.href=$E('a', el).href;
            }
        });
    });
}); 
