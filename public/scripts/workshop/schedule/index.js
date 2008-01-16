var sitePrefix;
var workshopBox, locationBox, workshopLengthHours, workshopLengthMinutes, workshopLength, workshopWidth;
var searchUrl;
var searchResultsContentBox;
var searchButton;
var hoverDiv;
var baseTime;
var startTime, endTime;

window.addEvent('domready', function() {
    
    sitePrefix = $('sitePrefix').value;
    searchUrl = sitePrefix + "/workshop/schedule/search";
    
    searchResultsContentBox = $('workshopSearchResultsContent');
    
    baseTime = parseInt($('basetime').value);
    
    workshopBox = $('workshopId');
    locationBox = $('locationId');
    workshopLengthHours = $('workshopLengthHours');
    workshopLengthMinutes = $('workshopLengthMinutes');
    workshopLength = (parseInt(workshopLengthHours.value) * 60) + parseInt(workshopLengthMinutes.value);
    searchButton = $('searchButton');
    
    searchButton.addEvent('click', function(e) {
        search();
    });
    
    workshopLengthHours.addEvent('change', function(e) {
        workshopLength = (parseInt(workshopLengthHours.value) * 60) + parseInt(workshopLengthMinutes.value);
    });
    
    workshopLengthMinutes.addEvent('change', function(e) {
        workshopLength = (parseInt(workshopLengthHours.value) * 60) + parseInt(workshopLengthMinutes.value);
    });
    
    workshopWidth = 96;
    
    hoverDiv = new Element('div');  
    hoverDiv.addClass('hoverDiv');
    hoverDiv.setStyle('width', workshopWidth);
    hoverDiv.setStyle('position', 'absolute');
    hoverDiv.setStyle('display', 'none');    
    
    $('workshopSearchResults').adopt(hoverDiv);
    
    setTimeBlock();
    search();
});

function setTimeBlock()
{
    
}

function search()
{   
    var year = $('year').value;
    var week = $('week').value;
    
    var extraData = Object.toQueryString({year: year, week: week});

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

    $('workshopSearchResultsLoading').style.display = 'none';
    
    $('previousWeekButton').addEvent('click', function(e) {
        $('year').value = $('prevYear').value;
        $('week').value = $('prevWeek').value;
        search();
    });
    
    $('nextWeekButton').addEvent('click', function(e) {
        $('year').value = $('nextYear').value;
        $('week').value = $('nextWeek').value;
        search();
    });
    
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

  
    $$('.eventColumn').each(function (el) {
        el.addEvents({
        
            'mouseenter': function(e) {
            
                currentColumn = el;
            
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
            },
            
            'mousemove': function(e) {

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
            },
        })
    });
}


function setTime()
{
    var tmp = Math.round(((parseInt((hoverDiv.getTop() - currentColumn.getTop())/5))*5)*60);
    var topTime = new Date();
    var bottomTime = new Date();
    topTime.setTime((tmp + startTime) * 1000);
    bottomTime.setTime((tmp + startTime + workshopLength*60) * 1000);
    var tmpTop = formatTime(topTime.getHours(), topTime.getMinutes());
    var tmpBottom = formatTime(bottomTime.getHours(), bottomTime.getMinutes());
    hoverDiv.setHTML('<table height="100%" id="hoverDivTable" align="center"><tbody><tr><td class="top" valign="top">'+tmpTop+'</td></tr><tr><td class="top" valign="bottom">'+tmpBottom+'</td></tr></tbody></table>');
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
