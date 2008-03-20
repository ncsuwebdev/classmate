window.addEvent('domready', function() {
             
    $('instructorSelectButton').addEvent('click', function(e) {
        var event = new Event(e);
        event.stop();
        
        var box = $('instructorSelect');           
        var userId = box.options[box.options.selectedIndex].value;
        
        if (userId == "") {
            $$('.event').each(function (event) {
                event.setStyle('display', 'block');
            });
        } else {
        
            $$('.event').each(function (eventBox) {
            
                var display = false;
                        
                eventBox.getElements('span').each(function (el) {
                                
                    if (el.hasClass('instructor')) {
                        if (el.title == userId) {
                            display = true;
                        }
                    }
                });
                
                if (display == true) {
                    eventBox.setStyle('display', 'block');
                } else {
                    eventBox.setStyle('display', 'none');
                }
            });
        }
    });         
});