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

</div>