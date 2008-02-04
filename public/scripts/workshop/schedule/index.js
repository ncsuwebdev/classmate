var sitePrefix;
var workshopBox, instructorListBox, instructorAddButton, locationBox;
var workshopLengthHours, workshopLengthMinutes, workshopLength;
var searchUrl, createEventUrl, deleteEventUrl, eventPopupUrl, updateEventUrl;
var searchResultsContentBox;
var hoverDiv;
var currentColumn;
var baseTime;
var startTime, endTime;
var newEventStartTime, newEventEndTime;
var modeButton, currentMode;

window.addEvent('domready', function() {
    
    sitePrefix = $('sitePrefix').value;
    searchUrl = sitePrefix + "/workshop/schedule/search";
    createEventUrl = sitePrefix + "/workshop/schedule/createEvent";
    deleteEventUrl = sitePrefix + "/workshop/schedule/deleteEvent";
    eventPopupUrl = sitePrefix + "/workshop/schedule/eventPopup";
    updateEventUrl = sitePrefix + "/workshop/schedule/updateEvent";
    editEventUrl = sitePrefix + "/workshop/schedule/editEvent";
    
    searchResultsContentBox = $('workshopSearchResultsContent');
    
    baseTime = parseInt($('basetime').value);
    
    workshopBox = $('workshopId');
    instructorListBox = $('instructorList');
    instructorAddButton = $('instructorAddButton');
    locationBox = $('locationId');
    workshopLengthHours = $('workshopLengthHours');
    workshopLengthMinutes = $('workshopLengthMinutes');
    workshopLength = (parseInt(workshopLengthHours.value) * 60) + parseInt(workshopLengthMinutes.value);
    
    locationBox.addEvent('change', function(e) {
        search();
    });
    
    modeButton = $('modeButton');

    modeButton.addEvent('click', function(e) {
        if (modeButton.value == "Switch to Edit Mode") {
            hideHoverDiv();
            $('workshopAddForm').setStyle('display', 'none');
            $$('.delete').each (function(el){
                el.setStyle('visibility', 'visible');
            });
            currentMode = "edit";
            modeButton.value = "Switch to Add Mode";
        } else {
            currentMode = "add";
            $('workshopAddForm').setStyle('display', 'block');
            $$('.delete').each (function(el){
                el.setStyle('visibility', 'hidden');
            });
            modeButton.value = "Switch to Edit Mode";
        }
    });
    
    workshopLengthHours.addEvent('change', function(e) {
        workshopLength = (parseInt(workshopLengthHours.value) * 60) + parseInt(workshopLengthMinutes.value);
    });
    
    workshopLengthMinutes.addEvent('change', function(e) {
        workshopLength = (parseInt(workshopLengthHours.value) * 60) + parseInt(workshopLengthMinutes.value);
    });
    
        
    hoverDiv = new Element('div');  
    hoverDiv.addClass('hoverDiv');
    hoverDiv.id = 'hoverDiv';
    hoverDiv.setStyle('position', 'absolute');
    hoverDiv.setStyle('display', 'none');
    
    hoverDiv.addEvent('click', function(e) {
        
        if (detectCollision()) {
            alert("You cannot have the new event overlap with an existing event");
            return false;
        }
               
        new StickyWinModal.Ajax({
            url: eventPopupUrl,
            onDisplay: initEventPopup,
            wrapWithStickyWinDefaultHTML: true,
            caption: 'Create Event',
            stickyWinHTMLOptions: {
                width: '600px',
                buttons: [
                    {
                        text: 'Cancel', 
                        onClick: function() {
                        }
                    },
                    {
                        text: 'Create This Event', 
                        onClick: function(e) {
                        
                            if ($('workshopId').value == 0) {
                                alert("You must select a workshop");
                                return false;
                            }
                            
                            if ($('workshopMinSize').value == "") {
                                alert("You must enter a minimum class size");
                                return false;
                            }
                           
                            if ($('workshopMaxSize').value == "") {
                                alert("You must enter a maximum class size");
                                return false;
                            }
                            
                            if ($('workshopWaitListSize').value == "") {
                                alert("You must enter a wait list size");
                                return false;
                            }
                                
                            var instructorStr = "";         
                            var tmpIList = $('instructors').childNodes; 
                            for (i=0; i < tmpIList.length; i++) {
                                if (instructorStr != "") {
                                    instructorStr += ": ";
                                }
                                
                                instructorStr += tmpIList[i].title;
                            }
                            
                            if (instructorStr == "") {
                                instructorStr = "none";
                            }
                                                        
                            var varStr = Object.toQueryString({
                                startTime: newEventStartTime, 
                                endTime: newEventEndTime, 
                                date: currentColumn.title,
                                workshopId: $('workshopId').value,
                                instructors: instructorStr,
                                locationId: $('locationId').value,
                                workshopMinSize: $('workshopMinSize').value,
                                workshopMaxSize: $('workshopMaxSize').value,
                                workshopWaitListSize: $('workshopWaitListSize').value
                            });
    
                            new Ajax(createEventUrl, {
                                method: 'post',
                                data: varStr,
                                onRequest: function() {
                                    $('workshopSearchResultsLoading').style.display = 'block';
                                },
                                onComplete: function(txtStr, xmlStr) {
                                    $('workshopSearchResultsLoading').style.display = 'none';
                                    if (txtStr != 0) {
                                        modeButton.fireEvent('click');
                                        search();
                                    } else {
                                        alert('Scheduling event failed');
                                    }
                                }
                            }).request();
                        }
                    }
                 ]
            }
        }).update();
    });
    
    $('workshopSearchResults').adopt(hoverDiv);

    search();
});

function deleteEvent(eventId)
{

    if (confirm("Are you sure you want to remove this event?")) {

        var varStr = Object.toQueryString({eventId: eventId});
               
        new Ajax(deleteEventUrl, {
            method: 'post',
            data: varStr,
            onRequest: function() {
                $('workshopSearchResultsLoading').style.display = 'block';
            },
            onComplete: function(txtStr, xmlStr) {
                $('workshopSearchResultsLoading').style.display = 'none';
                search();
            }
        }).request();
    }
}

function hideHoverDiv()
{
    hoverDiv.setStyle('display', 'none');
}

function detectCollision()
{
    var events = $ES('.event',currentColumn);
    
    var hTop  = hoverDiv.getTop();
    var hBottom = hoverDiv.getCoordinates().bottom;
    
    var retVal = false;
    
    events.each(function(el) {
    
        var yScroll = $('weekViewWrapper').getSize().scroll.y;
            
        elTop = el.getTop() - yScroll;
        elBottom = el.getCoordinates().bottom - yScroll;
        
        if (hTop == elTop) { // hover div starts at the same time as an event
            retVal = true;
        } else if ((hTop < elTop) && (hBottom > elTop)) { // hover div ends inside an event
            retVal = true;
        } else if ((hTop >= elTop) && (hBottom <= elBottom)) { // hover div is inside an event 
            retVal = true;
        } else if ((hTop < elBottom) && (hBottom >= elBottom)) { // hover div starts inside and ends outside an event
            retVal = true;
        } else if ((hTop < elTop) && (hBottom > elBottom)) { // hover div encompasses an event
            retVal = true;
        }
    });
    
    return retVal;
}

function search()
{   
    var year = $('year').value;
    var week = $('week').value;
    
    var extraData = Object.toQueryString({year: year, week: week});

    hoverDiv.setStyle('display', 'none');
    searchResultsContentBox.empty();    

    new Ajax(searchUrl, {
        method: 'get',
        data: $('wsForm').toQueryString() + "&" + extraData,
        update: searchResultsContentBox,
        onRequest: function() {
            $('workshopSearchResultsLoading').style.display = 'block';
        },
        onComplete: processSearchResults
    }).request();
}


function initEventPopup()
{
    $('workshopId').setStyle('visibility', 'visible');
    $('workshopId').setStyle('opacity', '100');
    $('workshopId').setStyle('width', '225');
    
    $('instructorList').setStyle('visibility', 'visible');
    $('instructorList').setStyle('opacity', '100');
    $('instructorList').setStyle('width', '200'); 
    
    $('locationDisplay').setText(locationBox.options[locationBox.options.selectedIndex].label);
    
    var tmpListBox = $('instructorList');
    for (var i=0; i < tmpListBox.options.length; i++) {
        if (tmpListBox[i].selected) {
            
            var tmpBox = new Element('div');
            tmpBox.title = tmpListBox.options[i].value;
            tmpBox.addClass('instructorName');
            
            var tmpLeft = new Element('p');
            tmpLeft.addClass('left');
                                    
            var tmpRight = new Element('p');
            tmpRight.addClass('right');
            
            var tmpCloseBtn = new Element('p');
            tmpCloseBtn.innerHTML = "&nbsp;";
            tmpCloseBtn.title = i;
            tmpCloseBtn.addClass('closeBtn');
            
            tmpCloseBtn.addEvent('click', function(e) {
                $('instructorList').options[this.title].setStyle('display', '');
                this.parentNode.remove();
                
                if ($('instructors').innerHTML == "") {
                    $('instructors').innerHTML = "None Added";
                }
            });
            
            var tmpP = new Element('a');
            tmpP.innerHTML = tmpListBox.options[i].label;
            tmpP.addClass('content');
            
            tmpBox.adopt(tmpLeft);
            tmpBox.adopt(tmpRight);
            tmpBox.adopt(tmpCloseBtn);
            tmpBox.adopt(tmpP);
            
            if($('instructors').innerHTML == "None Added") {
                $('instructors').empty();
            }
            
            $('instructors').adopt(tmpBox);                       
            
            tmpListBox.options[i].selected = false;
            tmpListBox.options[i].setStyle('display', 'none');
        }
    }
    
    tmpListBox.multiple = false;
    
    $('instructorAddButton').addEvent('click', function(e) {
        var tmpListBox = $('instructorList');
        
        if (tmpListBox.options.selectedIndex >= 0) {
            var tmpBox = new Element('div');
            tmpBox.title = tmpListBox.options[tmpListBox.options.selectedIndex].value;
            tmpBox.addClass('instructorName');
            
            var tmpLeft = new Element('p');
            tmpLeft.addClass('left');
                                    
            var tmpRight = new Element('p');
            tmpRight.addClass('right');
            
            var tmpCloseBtn = new Element('p');
            tmpCloseBtn.innerHTML = "&nbsp;";
            tmpCloseBtn.title = tmpListBox.options.selectedIndex;
            tmpCloseBtn.addClass('closeBtn');
            
            tmpCloseBtn.addEvent('click', function(e) {
                $('instructorList').options[this.title].setStyle('display', '');
                this.parentNode.remove();
                
                if ($('instructors').innerHTML == "") {
                    $('instructors').innerHTML = "None Added";
                }
            });
            
            
            var tmpP = new Element('a');
            tmpP.innerHTML = tmpListBox.options[tmpListBox.options.selectedIndex].label;
            tmpP.addClass('content');
            
            tmpBox.adopt(tmpLeft);
            tmpBox.adopt(tmpRight);
            tmpBox.adopt(tmpCloseBtn);
            tmpBox.adopt(tmpP);
            
            if($('instructors').innerHTML == "None Added") {
                $('instructors').empty();
            }
            
            $('instructors').adopt(tmpBox);                       
            
            tmpListBox.options[tmpListBox.options.selectedIndex].setStyle('display', 'none');
        }
    });
}


function processSearchResults()
{

    startTime = parseInt($('startTime').value);
    endTime   = parseInt($('endTime').value);
    
    if (modeButton.value == "Switch to Edit Mode") {
        currentMode = "add";
    } else {
        currentMode = "edit";
    }

    $('workshopSearchResultsLoading').style.display = 'none';
    
    $('previousWeekButton').addEvent('click', function(e) {
        $('year').value = $('prevYear').value;
        $('week').value = $('prevWeek').value;
        hoverDiv.setStyle('display', 'none');
        search();
    });
    
    $('nextWeekButton').addEvent('click', function(e) {
        $('year').value = $('nextYear').value;
        $('week').value = $('nextWeek').value;
        hoverDiv.setStyle('display', 'none');
        search();
    });
    
    hoverDiv.addEvents({
            
        'mousemove': function(e) {

            var e = new Event(e);            

            var weekViewWrapperBottom = $('weekViewWrapper').getCoordinates().bottom;            
                                                     
            hoverDiv.setStyle('top', e.client.y + window.getScrollTop() - parseInt(workshopLength/2));
            
            if (hoverDiv.getTop() <= currentColumn.getTop()) {
                hoverDiv.setStyle('top', currentColumn.getTop());
            }
            
            if (hoverDiv.getCoordinates().bottom >= currentColumn.getCoordinates().bottom || hoverDiv.getCoordinates().bottom >= weekViewWrapperBottom) {
                hoverDiv.setStyle('top', weekViewWrapperBottom - workshopLength);
            }

            setTime();
        }
    });
    
    $('weekViewWrapper').scrollTo(0, 480); // scroll to 8:00 AM

    $$('.event').each(function (el) {
    
        el.addEvent('dblclick', function (e) {
            
            new StickyWinModal.Ajax({
            url: editEventUrl + "?eventId=" + el.id,
            onDisplay: initEventPopup,
            wrapWithStickyWinDefaultHTML: true,
            caption: 'Edit Event',
            stickyWinHTMLOptions: {
                width: '600px',
                buttons: [
                    {
                        text: 'Cancel', 
                        onClick: function() {
                        }
                    },
                    {
                        text: 'Save Event', 
                        onClick: function(e) {
                        
                            if ($('workshopId').value == 0) {
                                alert("You must select a workshop");
                                return false;
                            }
                            
                            if ($('workshopMinSize').value == "") {
                                alert("You must enter a minimum class size");
                                return false;
                            }
                           
                            if ($('workshopMaxSize').value == "") {
                                alert("You must enter a maximum class size");
                                return false;
                            }
                            
                            if ($('workshopWaitListSize').value == "") {
                                alert("You must enter a wait list size");
                                return false;
                            }
                                
                            var instructorStr = "";         
                            var tmpIList = $('instructors').childNodes; 
                            for (i=0; i < tmpIList.length; i++) {
                                if (instructorStr != "") {
                                    instructorStr += ":";
                                }
                                
                                instructorStr += tmpIList[i].title;
                            }
                            
                            if (instructorStr == "") {
                                instructorStr = "none";
                            }
                                                        
                            var varStr = Object.toQueryString({
                                eventId: $('eventId').value,
                                workshopId: $('workshopId').value,
                                instructors: instructorStr,
                                workshopMinSize: $('workshopMinSize').value,
                                workshopMaxSize: $('workshopMaxSize').value,
                                workshopWaitListSize: $('workshopWaitListSize').value
                            });
    
                            new Ajax(editEventUrl, {
                                method: 'post',
                                data: varStr,
                                onRequest: function() {
                                    $('workshopSearchResultsLoading').style.display = 'block';
                                },
                                onComplete: function(txtStr, xmlStr) {
                                    $('workshopSearchResultsLoading').style.display = 'none';
                                    if (txtStr != 0) {
                                        search();
                                    } else {
                                        alert('Updating event failed');
                                    }
                                }
                            }).request();
                        }
                    }
                 ]
              }
            }).update();
        });
    
    });

  
    $$('.eventColumn').each(function (el) {
        el.addEvents({
        
            'mouseenter': function(e) {
            
                currentColumn = el;
            
                if (currentMode == "add") {
                    hoverDiv.setStyle('display', 'block');
                    hoverDiv.setStyle('height', workshopLength);
            
                    var e = new Event(e);
                    
                    var weekViewWrapperBottom = $('weekViewWrapper').getCoordinates().bottom;
                                        
                    hoverDiv.setStyle('top', e.client.y + window.getScrollTop() - parseInt(workshopLength/2));
                    
                    hoverDiv.setStyle('left', this.getLeft());
                    
                    if (hoverDiv.getTop() <= this.getTop()) {
                        hoverDiv.setStyle('top', this.getTop());
                    }
                    
                    if (hoverDiv.getCoordinates().bottom >= this.getCoordinates().bottom || hoverDiv.getCoordinates().bottom >= weekViewWrapperBottom) {
                        hoverDiv.setStyle('top', weekViewWrapperBottom - workshopLength);
                    }
                }
            },
            
            'mousemove': function(e) {

                if (currentMode == "add") {

                    var e = new Event(e);
                    
                    var weekViewWrapperBottom = $('weekViewWrapper').getCoordinates().bottom;
                                                                             
                    hoverDiv.setStyle('top', e.client.y + window.getScrollTop() - parseInt(workshopLength/2));
                    hoverDiv.setStyle('left', this.getLeft());
                                   
                    if (hoverDiv.getTop() <= this.getTop()) {
                        hoverDiv.setStyle('top', this.getTop());
                    }
                    
                    if (hoverDiv.getCoordinates().bottom >= this.getCoordinates().bottom || hoverDiv.getCoordinates().bottom >= weekViewWrapperBottom) {
                        hoverDiv.setStyle('top', weekViewWrapperBottom - workshopLength);
                    }
    
    	            setTime();
                }
            }
        });
    });
    
    if ($('startInAddMode').value == 1) {
        modeButton.fireEvent('click');   
    }
    
    $('startInAddMode').value = 0;
}


function setTime()
{

    if (detectCollision()) {
        hoverDiv.removeClass('hoverDiv');
        hoverDiv.addClass('hoverDivOverlap');
    } else {
        hoverDiv.removeClass('hoverDivOverlap');
        hoverDiv.addClass('hoverDiv');
        
    }
    
    var yScroll = $('weekViewWrapper').getSize().scroll.y;
    
    var tmp = Math.round(((parseInt((hoverDiv.getTop() + yScroll - currentColumn.getTop())/5))*5)*60);
    
    var topTime = new Date();
    var bottomTime = new Date();
    
    topTime.setTime((tmp + startTime) * 1000);
    bottomTime.setTime((tmp + startTime + workshopLength*60) * 1000);
    
    newEventStartTime = topTime.getHours() + ":" + topTime.getMinutes() + ":00";
    newEventEndTime   = bottomTime.getHours() + ":" + bottomTime.getMinutes() + ":00";
    
    var tmpTop = formatTime(topTime.getHours(), topTime.getMinutes());
    var tmpBottom = formatTime(bottomTime.getHours(), bottomTime.getMinutes());
      
    var tmpLabel = "";
    //var tmpLabel = workshopBox.options[workshopBox.options.selectedIndex].label.substring(0,25);
    //if (tmpLabel != "") {
    //    tmpLabel += "...";    
    //}
    
    hoverDiv.setHTML('<table height="100%" id="hoverDivTable" align="center"><tbody><tr><td class="top" valign="top">'
                     + tmpTop + '</td></tr><tr><td class="middle" valign="top">' 
                     + tmpLabel
                     + '</td></tr><tr><td class="bottom" valign="bottom">' 
                     + tmpBottom + '</td></tr></tbody></table>'
                    );
}

function formatTime(hours, minutes)
{
    var meridian = "PM";

    if (hours < 12) {
        meridian = "AM";
    }

    if (hours == 0) {
        hours = 12;
    } else if (hours > 12) {
        hours -= 12;
    }

    minutes = minutes + "";

    if (minutes.length == 1) {
        minutes = "0" + minutes;
    }

    return hours + ":" + minutes + " " + meridian;
}
