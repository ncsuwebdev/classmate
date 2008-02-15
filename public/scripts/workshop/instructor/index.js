var myStickyWin = null;

window.addEvent('domready', function() {
    //

    var onWidth = '100px', offWidth = '16px';
    
    var attendance = $$(".attendance");
    
    var eventId = $('eventId').value;
    var sitePrefix = $('sitePrefix').value;
    
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
			        			        
	                new Ajax(sitePrefix + '/workshop/instructor/attendance/', {
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
    
    $$('.removeAttendee').each(function (el) {
        el.addEvent('click', function (e) {
            var name = $E('span', el.getParent()).innerHTML;
            
            if (confirm('Are you sure you want to remove ' + name + ' from the class role?')) {
                var userId = el.id.replace(/^[^_]*\_/, '');
                
                location.href=sitePrefix + '/workshop/instructor/deleteAttendee/?userId=' + userId + '&eventId=' + eventId;
            }
        });
    });
    
    if ($('addAttendee')) {
        // we need to store the html in a variable and then remove the whole thing from the
        // dom so that it doesn't interfere when we make it the content in the modal popup
        eventPopupHtml = $('createEventPopup').innerHTML;
        $('createEventPopup').remove();
            
        $('addAttendee').addEvent('click', function(e) {
        
            myStickyWin = new StickyWinModal({
                modalOptions: {
                   hideOnClick: false
                },            
                onDisplay: initEventPopup,
                content: stickyWinHTML('Add Attendees to Class', eventPopupHtml, {
                    width: '500px',
                    buttons: [
                        {
                            text: 'Cancel', 
                            onClick: function() {
                                myStickyWin.destroy();
                            }
                        },
                        {
                            text: 'Save', 
                            onClick: function(e) {
                                $('addAttendeeForm').submit();
                            }
                        }
                     ]
                })
            });
        });  
    }      
});

function initEventPopup()
{    
    $$('.closeButton').each(function(el) {
        el.remove();
    });
    
    $('attendeeList').setStyle('visibility', 'visible');
    $('attendeeList').setStyle('opacity', '100');
    $('attendeeList').setStyle('width', '200'); 
    
    var tmpListBox = $('attendeeList');
    for (var i=0; i < tmpListBox.options.length; i++) {
        if (tmpListBox[i].selected) {
            
            var tmpBox = new Element('div');
            tmpBox.title = tmpListBox.options[i].value;
            tmpBox.addClass('attendeeName');
            
            var hidden = new Element('input');
            hidden.type = 'hidden';
            hidden.name = 'editor[]';
            hidden.value = tmpListBox.options[i].value;
            
            var tmpLeft = new Element('p');
            tmpLeft.addClass('left');
                                    
            var tmpRight = new Element('p');
            tmpRight.addClass('right');
            
            var tmpCloseBtn = new Element('p');
            tmpCloseBtn.innerHTML = "&nbsp;";
            tmpCloseBtn.title = i;
            tmpCloseBtn.addClass('closeBtn');
            
            tmpCloseBtn.addEvent('click', function(e) {
                this.parentNode.remove();
                
                if ($('attendees').innerHTML == "") {
                    $('attendees').innerHTML = "None Added";
                }
            });
            
            var tmpP = new Element('a');
            tmpP.innerHTML = tmpListBox.options[i].label;
            tmpP.addClass('content');
            
            tmpBox.adopt(hidden);
            tmpBox.adopt(tmpLeft);
            tmpBox.adopt(tmpRight);
            tmpBox.adopt(tmpCloseBtn);
            tmpBox.adopt(tmpP);
            
            if($('attendees').innerHTML == "None Added") {
                $('attendees').empty();
            }
            
            $('attendees').adopt(tmpBox);                       
            
            tmpListBox.options[i].selected = false;
        }
    }
    
    tmpListBox.multiple = false;
    
    $('attendeeAddButton').addEvent('click', function(e) {
        var tmpListBox = $('attendeeList');
        
        if (tmpListBox.options.selectedIndex >= 0) {
            var tmpChildren = $('attendees').getChildren();
            var found = false;
            for (var i = 0; i < tmpChildren.length; i++) {
                if (tmpChildren[i].title == tmpListBox.options[tmpListBox.options.selectedIndex].value) {
                    found = true;
                }
            }
            
            if (!found) {        
	            var tmpBox = new Element('div');
	            tmpBox.title = tmpListBox.options[tmpListBox.options.selectedIndex].value;
	            tmpBox.addClass('attendeeName');
	            
	            var hidden = new Element('input');
	            hidden.type = 'hidden';
	            hidden.name = 'userId[]';
	            hidden.value = tmpListBox.options[tmpListBox.options.selectedIndex].value;
	                        
	            var tmpLeft = new Element('p');
	            tmpLeft.addClass('left');
	                                    
	            var tmpRight = new Element('p');
	            tmpRight.addClass('right');
	            
	            var tmpCloseBtn = new Element('p');
	            tmpCloseBtn.innerHTML = "&nbsp;";
	            tmpCloseBtn.title = tmpListBox.options.selectedIndex;
	            tmpCloseBtn.addClass('closeBtn');
	            
	            tmpCloseBtn.addEvent('click', function(e) {
	                this.parentNode.remove();
	                
	                if ($('attendees').innerHTML == "") {
	                    $('attendees').innerHTML = "None Added";
	                }
	            });
	            
	            
	            var tmpP = new Element('a');
	            tmpP.innerHTML = tmpListBox.options[tmpListBox.options.selectedIndex].label;
	            tmpP.addClass('content');
	            
	            tmpBox.adopt(hidden);
	            tmpBox.adopt(tmpLeft);
	            tmpBox.adopt(tmpRight);
	            tmpBox.adopt(tmpCloseBtn);
	            tmpBox.adopt(tmpP);
	            
	            if($('attendees').innerHTML == "None Added") {
	                $('attendees').empty();
	            }
	            
	            $('attendees').adopt(tmpBox);                       
            }
        }
    });
}