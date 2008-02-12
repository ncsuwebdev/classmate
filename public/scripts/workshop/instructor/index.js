window.addEvent('domready', function() {
    //

    var onWidth = '100px', offWidth = '16px';
    
    var attendance = $$(".attendance");
    
    var eventId = $('eventId').value;
    
    attendance.each(function (main, i) {
    
        var togglers = $ES('div', main);
        
        var fx = new Fx.Elements(togglers, {wait: false, duration: 300, transition: Fx.Transitions.Back.easeOut});
        
        togglers.each(function (el, i) {
            if (el.hasClass('on')) {
                el.setStyle('width', onWidth);
            } else {
                el.setStyle('width', offWidth);
            }
            
            el.addEvent('click', function (e) {
                if (!el.hasClass('on')) {
 
                    var userId = main.id.replace(/^[^_]*\_/, '');
                    
	                el.toggleClass('off');
	                el.toggleClass('on');
	                
	                var attended = el.hasClass('present');
	                
			        var o = {};
			        o[i] = {width: [el.getStyle("width").toInt(), onWidth]}
			        togglers.each(function(other, j) {
			            if(i != j) {
			                var w = other.getStyle("width").toInt();
			                if(w != offWidth) o[j] = {width: [w, offWidth]};
			                other.toggleClass('on');
			                other.toggleClass('off');
			             }
			        });
			        fx.start(o);
			        
			        var updateEl = $('update_' + userId);
			        			        
	                new Ajax($('sitePrefix').value + '/workshop/instructor/attendance/', {
	                    method: 'post',
	                    postBody: 'eventId=' + eventId + '&userId=' + userId + '&attended=' + attended,
	                    update: updateEl,
	                    onComplete: function(){
	                        var responseFade = new Fx.Style(updateEl.getProperty('id'), 'opacity', {
	                            duration:2000
	                        });
	                        responseFade.start.pass([1,0],responseFade).delay(1000);
	                    }
	               }).request();			        
			    }	                              
            });
        });
    });
});