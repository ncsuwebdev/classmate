window.addEvent('domready', function() {
       
    hideAllCategories();
    
    $('category_0').setStyle('display', '');
       
    $('categorySelectButton').addEvent('click', function(e) {
        var event = new Event(e);
        event.stop();
                   
        hideAllCategories();
        
        var box = $('categorySelect');           
        
        $(box.options[box.options.selectedIndex].value).setStyle('display', '');
    });         
});

function hideAllCategories()
{
    $$('.catBox').each(function (el) {
        el.setStyle('display', 'none');
    });
}