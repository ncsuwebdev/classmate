var calendar;
var currMonth, currYear;
var nextMonth, nextYear;
var previousMonth, previousYear;
var sitePrefix;

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


function getCal(url, varStr) {
    
    new Ajax(url, {
        method: 'get',
        data: varStr,
        update: $('calendarWrapper'),
        onRequest: function() {
            $('loading').style.visibility = 'visible';
        },
        onComplete: function(htmlStr) {
            setDates();    
            initializeCalendar();
        
            $('loading').setStyle('visibility', 'hidden');
        }
    }).request();
}

function initializeCalendar()
{
    var tds = $ES('td','calendar') //gets all the tds in the calendar;
    
    tds.each(function(el){
        
        el.addEvent('click', function (e) {
        
            e = new Event(e).stop();
            
            var day = $ES('p', el).getText();
            var url = sitePrefix + "/calendar/index/getEvents";
            var varStr = Object.toQueryString({year: currYear, month: currMonth, day: day});
            
            new Ajax(url, {
                method: 'get',
                data: varStr,
                update: $('eventsWrapper')
            }).request();
        });
    });
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