window.addEvent('domready', function() {
    /*
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
    */
    myTabs1 = new mootabs('classes', {
        height: '210px',
        changeTransition: 'none',
        mouseOverClass: '',
        });
});