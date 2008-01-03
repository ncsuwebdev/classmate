var calendar;
var currMonth, currYear;
var nextMonth, nextYear;
var previousMonth, previousYear;
var weekLayout;
var sitePrefix;

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
    
    // set up the next button's functionality
    var nextButton = $('nextButton');
    
    nextButton.addEvent('click', function (e) {
        
        e = new Event(e).stop();
 
        var url = sitePrefix + "/calendar/index/getCal";
        var varStr = Object.toQueryString({year: nextYear, month: nextMonth});
        
        getCal(url, varStr);
                
    });
    
    // set up the previous button's functionality
    var previousButton = $('previousButton');
    
    previousButton.addEvent('click', function (e) {
                
        e = new Event(e).stop();
 
        var url = sitePrefix + "/calendar/index/getCal";
        var varStr = Object.toQueryString({year: previousYear, month: previousMonth});
        
        getCal(url, varStr);
        
    });
    
    setDates();
    initializeCalendar();
    
});


function getCal(url, varStr)
{
    
    new Ajax(url, {
        method: 'get',
        data: varStr,
        update: $('calendarWrapper'),
        onRequest: function() {
            $('loading').style.visibility = 'visible';
        },
        onComplete: setupCal
    }).request();
}

function setupCal()
{
    setDates();    
    initializeCalendar();
    $('loading').setStyle('visibility', 'hidden');
}

var lastMorph;
var lastEl;

function initializeCalendar()
{
    var tds = $ES('td','calendar') //gets all the tds in the calendar;
    
    lastMorph = null;
    lastEl    = null;
    
    tds.each(function(el){
        
        el.addEvent('click', function (e) {
            
            if (!el.hasClass('weekNum')) { // the cell is a day of the month   
                   
                var day = $ES('p', el).getText();
                var url = sitePrefix + "/calendar/index/getEvents";
                var varStr = Object.toQueryString({year: currYear, month: currMonth, day: day});
                
                new Ajax(url, {
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
                                  ajaxLink: sitePrefix + "/calendar/index/getEventDetails?workshopId=" + event.workshopId,
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
                
            } else { // the cell is a week number
                
                var weekNum = $ES('p', el).getText();
                var url = sitePrefix + "/calendar/index/weekView";
                var varStr = Object.toQueryString({year: currYear, week: weekNum, newWindow: 1});
                
                new StickyWinModal.Ajax({
                    url: url + "?" + varStr,
                    width: 1000,
                    height: 550,
                    className: 'weekViewWrapper',
                    onDisplay: initializeWeekView,
                    
                }).update();
            }
             
        });
                
    });    
}

function initializeWeekView()
{          
    var nextWeekButton = $('nextWeekButton');
    var prevWeekButton = $('prevWeekButton');
    
    var url = sitePrefix + "/calendar/index/weekView";
    
    nextWeekButton.addEvent('click', function (e) {
        var varStr = Object.toQueryString({year: $('weekViewNextYear').value, week: $('weekViewNextWeekNum').value, newWindow: 0});
        
        new Ajax(url, {
            method: 'get',
            data: varStr,
            update: $('weekViewData'),
            onRequest: function() {
                $('weekLoading').style.visibility = 'visible';
            },
            onComplete: function() {
                $('weekLoading').setStyle('visibility', 'hidden');
            }
        }).request();
    });
    
    prevWeekButton.addEvent('click', function (e) {
        var varStr = Object.toQueryString({year: $('weekViewPrevYear').value, week: $('weekViewPrevWeekNum').value});
        
        new Ajax(url, {
            method: 'get',
            data: varStr,
            update: $('weekViewData'),
            onRequest: function() {
                $('weekLoading').style.visibility = 'visible';
            },
            onComplete: function() {
                $('weekLoading').setStyle('visibility', 'hidden');
            }
        }).request();
    });
    
    $('weekLoading').setStyle('visibility', 'hidden');
}

function setDates()
{
    // the calendar
    var calendar = $('calendar');
    
    var currCalDate = calendar.title;
        
    currCalDate = currCalDate.split('_');
       
    currMonth = parseInt(currCalDate[0]);
    currYear  = parseInt(currCalDate[1]);
    
    if (currMonth == 12) {
        nextMonth = 1;
    } else {
        nextMonth = currMonth + 1;
    }
    
    nextYear = currYear;
    if (nextMonth == 1) {
        nextYear = currYear + 1;
    }
    
    if (currMonth == 1) {
        previousMonth = 12;
    } else {
        previousMonth = currMonth - 1;
    }
    
    previousYear = currYear;
    if (currYear == 1970) {
        previousYear = 1970;
    } else if (previousMonth == 12) {
        previousYear = currYear - 1;
    }
}