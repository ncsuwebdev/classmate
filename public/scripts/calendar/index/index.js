var calendar;
var sitePrefix;
var workshopBox, locationBox, workshopLengthHours, workshopLengthMinutes, workshopLength, workshopWidth;
var weekUrl, monthUrl, createEventUrl, eventsUrl, eventDetailsUrl;
var searchResultsContentBox;
var searchButton;
var hoverDiv;
var baseTime;
var startTime, endTime;
var locationId;
var newEventStartTime, newEventEndTime;
var currentMode;
var addMode;
var selectedDay;

Fx.Morph = Fx.Styles.extend({
 
    start: function(className){
 
        var to = {};
 
        $each(document.styleSheets, function(style){
            var rules = style.rules || style.cssRules;
            $each(rules, function(rule){
                if (!rule.selectorText.test('\.' + className + '$')) return;
                Fx.CSS.Styles.each(function(style){
                    if (!rule.style || !rule.style[style]) return;
                    var ruleStyle = rule.style[style];
                    to[style] = (style.test(/color/i) && ruleStyle.test(/^rgb/)) ? ruleStyle.rgbToHex() : ruleStyle;
                });
            });
        });
        return this.parent(to);
    }
 
});

Fx.CSS.Styles = ["backgroundColor", "color", "width", "height", "lineHeight", "textIndent", "opacity"];
 
Fx.CSS.Styles.extend(Element.Styles.padding);
Fx.CSS.Styles.extend(Element.Styles.margin);
 
Element.Styles.border.each(function(border){
    ['Width', 'Color'].each(function(property){
        Fx.CSS.Styles.push(border + property);
    });
});

window.addEvent('domready', function() {
    
    sitePrefix = $('sitePrefix').value;
    baseTime = parseInt($('basetime').value);
    
    startTime = "";
    endTime = "";
    locationId = "";
    
    addMode = false;
    
    // various urls to use for the AJAX functions
    monthUrl  = sitePrefix + "/calendar/index/getMonth";
    weekUrl = sitePrefix + "/calendar/index/getWeek";
    eventDetailsUrl = sitePrefix + "/calendar/index/getEventDetails";
    eventsUrl = sitePrefix + "/calendar/index/getEvents";
    createEventUrl = sitePrefix + "/calendar/index/createEvent";
    
    searchResultsContentBox = $('searchResultsContent');
  
    currentMode = $('currentView').value;
    
    // set up the next button's functionality
    var nextButton = $('nextButton');
    
    nextButton.addEvent('click', function (e) {
        
        e = new Event(e).stop();
        
        if (currentMode == "month") {
            $('month').value = $('nextMonth').value;
            $('year').value = $('nextYear').value;
            getMonth();
        } else {
            $('week').value = $('nextWeek').value;
            $('year').value = $('nextYear').value;
            getWeek();
        }
                
    });
    
    // set up the previous button's functionality
    var previousButton = $('previousButton');
    previousButton.addEvent('click', function (e) {
                
        e = new Event(e).stop();
        
        if (currentMode == "month") {
            $('month').value = $('prevMonth').value;
            $('year').value = $('prevYear').value;
            getMonth();
        } else {
            $('week').value = $('prevWeek').value;
            $('year').value = $('prevYear').value;
            getWeek();
        }        
    });
    
    var viewWeekButton = $('viewWeekButton');
    viewWeekButton.addEvent('click', function(e) {
        currentMode = "week";
        getWeek();
    });
    
    var viewMonthButton = $('viewMonthButton');
    viewMonthButton.addEvent('click', function(e) {
        currentMode = "month";
        getMonth();
    });
    
    workshopWidth = 94;
    
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
                $('loading').setStyle('visibility', 'visible');
            },
            onComplete: function(txtStr, xmlStr) {
                $('loading').setStyle('visibility', 'hidden');
                if (txtStr != 0) {
                    alert('Workshop scheduled successfully!');
                    hoverDiv.setStyle('display', 'none');
                    search();
                }
            }
        }).request();
        
    });
    
    $('workshopSearchResults').adopt(hoverDiv);
    
    if (currentMode = "month") {
        getMonth();
    } else {
        getWeek();
    }  
});


function getMonth()
{
    var year = $('year').value;
    var month = $('month').value;
    
    var varStr = Object.toQueryString({year: year, month: month});

    hoverDiv.setStyle('display', 'none');
    searchResultsContentBox.empty();    

    new Ajax(monthUrl, {
        method: 'get',
        data: varStr,
        update: searchResultsContentBox,
        onRequest: function() {
            $('loading').setStyle('visibility', 'visible');
        },
        onComplete: processMonthResults
    }).request();
}

var lastMorph;
var lastEl;
function processMonthResults()
{
    var tds = $ES('td','calendar') //gets all the tds in the calendar;
    
    lastMorph = null;
    lastEl    = null;
    
    tds.each(function(el){
        
        el.addEvent('click', function (e) {
                              
            selectedDay = $ES('p', el).getText();
            $('week').value = $ES('input', el).getValue();
            
            var varStr = Object.toQueryString({year: $('year').value, month: $('month').value, day: selectedDay});
            
            new Ajax(eventsUrl, {
                method: 'get',
                data: varStr,
                onComplete: function(jsonStr, xmlStr) {
                    var events = Json.evaluate(jsonStr);
                    var tmpDiv = $ES('div', el);
                    if (events.length > 0) {
                        events.each(function (event) {
                            var link = new Element('p');
                            link.setText(event.workshopData.title);
                            
                            new PopupDetail($('popupDetails').getValue(), {
                              observer: link,
                              useAjax: true,
                              ajaxLink: eventDetailsUrl + "?workshopId=" + event.workshopId + '&eventId=' + event.eventId,
                              ajaxOptions: {method: 'get'},
                              stickyWinOptions: {
                                position: 'upperRight',
                                offset: {
                                  x: 5,
                                  y: -115
                                }
                              }
                            });
                            
                            tmpDiv.adopt(link);                                                          
                            
                        });
                    } else {
                        $ES('div', el).setHTML("No Events Found");
                    }
                }
            }).request();
            
            var calDayMorph = new Fx.Morph(el, {wait: false});
            if (lastMorph != null) {
                lastMorph.start('calendarDay');
            }
            
            if (lastEl != null) {
                $ES('div', lastEl).setText("");
            }
            
            calDayMorph.start('selectedCalendarDay');
            lastMorph = calDayMorph;
            lastEl    = el;             
        });
                
    });
    
    $('loading').setStyle('visibility', 'hidden');
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
            
        elTop = el.getTop();
        elBottom = el.getCoordinates().bottom;
        
        if (hTop == elTop) { // hover div starts at the same time as an event
            retVal = true;
        } else if ((hTop < elTop) && (hBottom >= elTop)) { // hover div ends inside an event
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

function getWeek()
{   
    var year = $('year').value;
    var week = $('week').value;
    
    var varStr = Object.toQueryString({year: year, week: week});
    
    var extraData = "";
    if (startTime != "") {
        extraData += "&" + Object.toQueryString({startTime: startTime});
    }
    
    if (endTime != "") {
        extraData += "&" + Object.toQueryString({endTime: endTime});
    }
    
    if (locationId != "") {
        extraData += "&" + Object.toQueryString({locationId: locationId});
    }

    hoverDiv.setStyle('display', 'none');
    searchResultsContentBox.empty();    

    new Ajax(weekUrl, {
        method: 'get',
        data: varStr + "&" + extraData,
        update: searchResultsContentBox,
        onRequest: function() {
            $('loading').setStyle('visibility', 'visible');
        },
        onComplete: processWeekResults
    }).request();
}


var currentColumn;
function processWeekResults()
{    
    hoverDiv.addEvents({
            
        'mousemove': function(e) {

            var e = new Event(e);            

            hoverDiv.setStyle('top', e.client.y + window.getScrollTop() - parseInt(workshopLength/2));
            
            if (hoverDiv.getTop() <= currentColumn.getTop()) {
                hoverDiv.setStyle('top', currentColumn.getTop());
            }
            
            if (hoverDiv.getCoordinates().bottom >= currentColumn.getCoordinates().bottom) {
                hoverDiv.setStyle('top', currentColumn.getCoordinates().bottom - workshopLength);
            }

            setTime();
        }
    });


    $$('.event').each(function (el) {
    
        el.addEvent('click', function (e) {
            alert(el.id);
        });
    });

  
    $$('.eventColumn').each(function (el) {
        el.addEvents({
        
            'mouseenter': function(e) {
            
                currentColumn = el;
            
                if (addMode == true) {
                    hoverDiv.setStyle('display', 'block');
                    hoverDiv.setStyle('height', workshopLength);
            
                    var e = new Event(e);
                    
                    hoverDiv.setStyle('top', e.client.y + window.getScrollTop() - parseInt(workshopLength/2));
                    hoverDiv.setStyle('left', this.getLeft());
                    
                    if (hoverDiv.getTop() <= this.getTop()) {
                        hoverDiv.setStyle('top', this.getTop());
                    }
                    
                    if (hoverDiv.getCoordinates().bottom >= this.getCoordinates().bottom) {
                        hoverDiv.setStyle('top', this.getCoordinates().bottom - workshopLength);
                    }
                }
            },
            
            'mousemove': function(e) {

                if (addMode == true) {

                    var e = new Event(e);
                    
                    hoverDiv.setStyle('top', e.client.y + window.getScrollTop() - parseInt(workshopLength/2));
                    hoverDiv.setStyle('left', this.getLeft());
                                   
                    if (hoverDiv.getTop() <= this.getTop()) {
                        hoverDiv.setStyle('top', this.getTop());
                    }
                    
                    if (hoverDiv.getCoordinates().bottom >= this.getCoordinates().bottom) {
                        hoverDiv.setStyle('top', this.getCoordinates().bottom - workshopLength);
                    }
    
                    setTime();
                }
            },
        })
    });
    
    $('loading').setStyle('visibility', 'hidden');
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

    var tmp = Math.round(((parseInt((hoverDiv.getTop() - currentColumn.getTop())/5))*5)*60);
    
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