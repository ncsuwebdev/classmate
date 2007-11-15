window.addEvent('domready', function() {

    var selectors = $$('.allAccess');
    
    selectors.each(function(el) {

        el.addEvent('change', function(e) {
            var disp = "none";
            if (el.value == 'some') {
                disp = "";
            } else {
                disp = "none";
            }
            
            var rows = $$('.' + el.id);
            
            rows.each(function(el) {
                el.style.display = disp;
            });            
        });
    });
        
    $('aclEditor').addEvent('submit', function(e) {

        selectors.each(function (el) {
            $$('.' + el.id + '_action').each(function(bx) {
                 if (!bx.checked) {   
                    if (el.value == 'some') {             
                        if (bx.value == 'allow') {
                            bx.value = 'deny';
                        } else {
                            bx.value = 'allow';
                        }
                    } else {
                        bx.value = el.value;
                    }
                    
                    bx.checked = true;
                }
                
            });
            
            if (el.value == 'some') {
                el.value = 'deny';
            }
            
        });
    });
});