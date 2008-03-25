window.addEvent('domready', function() {
       
    hideAllCategories();
    
    $('category_0').setStyle('display', '');
       
    $('categorySelectButton').addEvent('click', function(e) {
        var event = new Event(e);
        event.stop();
                   
        hideAllCategories();
        
        var box = $('categorySelect');
        
        var val = box.options[box.options.selectedIndex].value;
        
        if (val == "category_all") {
            
            $$('.catBox').each(function (el) {
                el.setStyle('display', '');
            });
            
            $('category_0').setStyle('display', 'none');
                            
        } else {
        
            $(val).setStyle('display', '');
        }
    });         
});

function hideAllCategories()
{
    $$('.catBox').each(function (el) {
        el.setStyle('display', 'none');
    });
}