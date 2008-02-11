<div id="detailsRight">
    <div class="rightTitle">
        Event Information     
    </div>

    <div class="rightContent">
        <div class="event" id="event_{$e.eventId}">
            <span class="date">{$event.date|date_format:$config.longDateFormat}</span>
            {$event.startTime|date_format:$config.timeFormat} - {$event.endTime|date_format:$config.timeFormat} 
        </div> 
    </div>
</div>
<div id="detailsLeft">  
    <div id="tools">
        <ul class="mootabs_title">
            <li title="attendees">Class Attendees</li>
            <li title="evaluations">Evaluations</li>
            <li title="contact">Contact Attendees</li>
        </ul>
        <div id="attendees" class="mootabs_panel">
        attendee list
        </div>
        <div id="evaluations" class="mootabs_panel">
        eval stuff
        </div>
        <div id="contact" class="mootabs_panel">
        contact list
        </div>                
</div>