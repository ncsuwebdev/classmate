var sitePrefix;
var workshopBox, locationBox, workshopLengthHours, workshopLengthMinutes, workshopLength, workshopWidth;
var searchUrl;
var searchResultsContentBox;
var hoverDiv;
var baseTime;
var startTime, endTime;
var newEventStartTime, newEventEndTime;
var modeButton, currentMode;

window.addEvent('domready', function() {
    
    sitePrefix = $('sitePrefix').value;
    searchUrl = sitePrefix + "/workshop/schedule/search";
    createEventUrl = sitePrefix + "/workshop/schedule/createEvent";
    deleteEventUrl = sitePrefix + "/workshop/schedule/deleteEvent";
    
    searchResultsContentBox = $('workshopSearchResultsContent');
    
    baseTime = parseInt($('basetime').value);
    
    workshopBox = $('workshopId');
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
            $('workshopSearchWrapper').setStyle('display', 'none');
            $$('.delete').each (function(el){
                el.setStyle('visibility', 'visible');
            });
            currentMode = "edit";
            modeButton.value = "Switch to Add Mode";
        } else {
            currentMode = "add";
            $('workshopSearchWrapper').setStyle('display', 'block');
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
    
    workshopWidth = 93;
    
    hoverDiv = new Element('div');  
    hoverDiv.addClass('hoverDiv');
    hoverDiv.id = 'hoverDiv';
    hoverDiv.setStyle('width', workshopWidth);
    hoverDiv.setStyle('position', 'absolute');
    hoverDiv.setStyle('display', 'none');
    
    hoverDiv.addEvent('click', function(e) {
        
        if (workshopBox.value == 0) {
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
        
        if (detectCollision()) {
            alert("You cannot have the new event overlap with an existing event");
            return false;
        }
        
        var varStr = Object.toQueryString({
                        startTime: newEventStartTime, 
                        endTime: newEventEndTime, 
                        date: currentColumn.title,
                        workshopId: workshopBox.value,
                        locationId: locationBox.value,
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
                    alert('Workshop scheduled successfully!');
                    modeButton.fireEvent('click');
                    search();
                }
            }
        }).request();
        
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


var currentColumn;

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
            alert(el.id);
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
       
    var tmpLabel = workshopBox.options[workshopBox.options.selectedIndex].label.substring(0,25);
    if (tmpLabel != "") {
        tmpLabel += "...";    
    }
    
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
