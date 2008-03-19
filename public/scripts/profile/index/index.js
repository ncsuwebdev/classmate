window.addEvent('domready', function() {

    if ($('reservations')) {
        myTabs1 = new mootabs('reservations', {
            height: '225px',
            changeTransition: 'none',
            mouseOverClass: ''
            });
    }
        
    if ($('teaching')) {
        myTabs2 = new mootabs('teaching', {
            height: '225px',
            changeTransition: 'none',
            mouseOverClass: ''
            });
    }        
});