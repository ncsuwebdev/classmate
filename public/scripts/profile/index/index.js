window.addEvent('domready', function() {
    
    var accordion = new Accordion($$('.toggler'), $$('.element'), {
        
        opacity: false,
        show: 0,        
    });
    
    newsSlider = new Fx.Slide('toggler', {
            duration: 500
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