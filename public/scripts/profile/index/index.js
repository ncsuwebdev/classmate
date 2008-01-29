window.addEvent('domready', function() {
    
    var accordion = new Accordion($$('.toggler'), $$('.element'), {
        
        opacity: false,
        show: 0,     
        onActive: function(el) {
            el.removeClass('inactive');
            el.addClass('active');
        },   
        onBackground: function(el) {
            el.removeClass('active');
            el.addClass('inactive');
        },
    });

});

var newsSlider;
function toggleNews()
{   

    newsSlider.toggle();
    
    var tmp = $('toggleNewsLink');
    if (tmp.getText() == "Hide News") {
        tmp.setHTML("Show News");
    } else {
        tmp.setHTML("Hide News");
    }       
}