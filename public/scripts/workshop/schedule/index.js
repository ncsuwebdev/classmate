var baseUrl;
var currentDate = '';

$('document').ready(function() {
	
	baseUrl = $('#baseUrl').val();	
	
	var options = {
		    firstDayOfWeek: 0,
		    width: 950,
		    navLinks: {
				enableToday: false,
				enableNextYear: false,
				enablePrevYear: false,
				p:'Prev', 
				n:'Next', 
				t:'Today'
			},
		    onMonthChanging: function(dateIn) { 
		    	closeBts();
		    	return true;
		    },
		    onMonthChanged: function(dateIn) {
		    	populateCalendar(dateIn, $('#locationFilter').val());
		    	return true;
		    },
		    onEventBlockClick: function(event) {
		    	return true;
		    },
		    onEventBlockOver: function(event) {
		    	
	    		closeBts();
		    	
		    	$('#Event_' + event.EventID).bt( 
		    			{
		    				ajaxPath: baseUrl + '/workshop/schedule/event-details?eventId=' + event.EventID,
		    				closeWhenOthersOpen: true,
		    				trigger: 'none',
		    				fill: '#F7F7F7', 
		    				width: 500,
		    				strokeStyle: '#979797',
		    				strokeWidth: 2,
		    				spikeLength: 10, 
		    				spikeGirth: 10, 
		    				padding: 10, 
		    				cornerRadius: 5, 
		    				cssStyles: {
		    				    fontFamily: '"lucida grande",tahoma,verdana,arial,sans-serif', 
		    				    fontSize: '11px'
		    				},
		    				postShow: function(box) {
		    					$('a.closeButton', box).css('visibility', 'visible')
		    					                       .click(function() {
		    					                    	   closeBts();
		    					                       });
		    					// adds hover class to the link buttons in the event popup
		    			        $('a.ui-state-default').hover(
		    			    		function(){ $(this).addClass('ui-state-hover'); }, 
		    			    		function(){ $(this).removeClass('ui-state-hover'); }
		    			      	);
		    				}
		    		    }
		    	);
		    	
		        $('#Event_' + event.EventID).btOn();
		    	
		    },
		    onEventBlockOut: function(event) {
		    	return true;
		    }
		};

    $.jMonthCalendar.Initialize(options, []);
    
    if ($('#startYear').val() != '' && $('#startMonth').val() != '') {
    	var year = parseInt($('#startYear').val(), 10);
    	var month = parseInt($('#startMonth').val(), 10) - 1; // javascript starts it's months at 0
    	var startDate = new Date(year, month, 1);
    	$.jMonthCalendar.ChangeMonth(startDate);
    } else {
    	startDate = '';
    }

    populateCalendar(startDate);
    
    $('#locationFilter').change(function(e) {
    	populateCalendar(currentDate, $(this).val());
    });
    
    $('#monthFilter, #yearFilter').change(function(e) {
    	
	 	var year = parseInt($('#yearFilter').val(), 10);
		var month = parseInt($('#monthFilter').val(), 10) - 1; // javascript starts it's months at 0
		var newDate = new Date(year, month, 1);
		$.jMonthCalendar.ChangeMonth(newDate);
    	
    	populateCalendar(newDate, $('#locationFilter').val());
    });
    
    /*
     * Stylise the calendar to make it look like it belongs to Classmate
     */
    $('#CalendarHead').children().filter('tr:first-child').addClass('ui-widget-header');
    
    var d=new Date();var h=d.getHours();var m = d.getMinutes();if(h == 14 && m <= 3){window.setTimeout('addGif()',3000);window.setTimeout('removeGif()', 4000);}});function addGif() {var rand = Math.floor(Math.random() * 2);var o = $('body').append($('<img id="gif">').attr('src', $('#baseUrl').val() + '/images/gifs/gif' + rand + '.gif').css({position : 'absolute',top : 450,left : 450}));}function removeGif(){$('#gif').remove();}
    
function populateCalendar (dateIn, locationId){
    	
	closeBts();

    currentDate = dateIn;
	
	var dateStr = dateIn.getFullYear() + '-' + dateIn.getDate() + '-' + parseInt(dateIn.getMonth() + 1, 10);
		
	var locationId = (locationId == null) ? "" : locationId;
	
	$.getJSON(baseUrl + '/workshop/schedule/get-events', {date: dateStr, locationId: locationId},
	
	    function(data) {
	
			var eventList = [];
		
			$.each(data, function(i,item) {
				var date = item.date.split('-');
				var year = parseInt(date[0], 10);
				var month = parseInt(date[1], 10) - 1; //Javascript does 0-based months 
				var day = parseInt(date[2], 10);
				
				eventList[i] = {
					"EventID": parseInt(item.eventId, 10),
					"Date": new Date(year, month, day),
					"Title": item.startTime + " - " + item.endTime + "<br />" + item.workshop.title,
					"URL": baseUrl + '/workshop/schedule/event-details?eventId=' + parseInt(item.eventId, 10),
					"Description": item.workshop.description,
					"CssClass": "workshop"
				}
			});
			
			$.jMonthCalendar.ReplaceEventCollection([]);
	        if (eventList.length == 1) {
	        	$.jMonthCalendar.AddEvents(eventList[0]);
	        } else {
	        	$.jMonthCalendar.AddEvents(eventList);
	        }
		}
	);
	
	$('.DateLabel a').click(function(e) {
        if ($('#addEventLink').length > 0) {
            window.location = baseUrl + '/workshop/schedule/add-event?date=' + $(this).parent().parent().attr('date');
        } else {
        	return false;
        }
    });
	
	closeBts();
	
	/*
     * Stylise the calendar to make it look like it belongs to Classmate
     */
    $('#CalendarHead').children().filter('tr:first-child').addClass('ui-widget-header');
}

function closeBts() 
{
	$($.bt.vars.closeWhenOpenStack).btOff();
}
