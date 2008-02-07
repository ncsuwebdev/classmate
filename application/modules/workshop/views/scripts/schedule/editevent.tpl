<div id="createEventPopup">
<table width="100%">
    <tbody>
        <tr>
            <td valign="top" width="50%">
                <form id="editEventForm">
                    <input type="hidden" name="eventId" id="eventId" value="{$eventId}" />
                <table>
                    <tbody>
                        <tr>
                            <td>Location:</td>
                            <td>{html_options id=editLocationId name=editLocationId options=$locations selected=$event.locationId}</td>
                        </tr>
                        <tr>
                            <td><label for="workshopId">Workshop:</label></td>
                            <td>{html_options id=workshopId name=workshopId options=$workshops selected=$event.workshopId}</td>
                        </tr>
                        <tr>
                            <td><label for="eventDate">Date:</label></td>
                            <td>{html_select_date class='eventDate' time=$event.date field_array='eventDate'}</td>
                        </tr>
                        <tr>
                            <td><label for="eventStartTime">Start Time:</label></td>
                            <td class="eventStartTime">{html_select_time display_seconds=false use_24_hours=false minute_interval=5 time=$event.startTime field_array='eventStartTime'}</td>
                        </tr>
                        <tr>
                            <td><label for="eventEndTime">End Time:</label></td>
                            <td class="eventEndTime">{html_select_time display_seconds=false use_24_hours=false minute_interval=5 time=$event.endTime field_array='eventEndTime'}</td>
                        </tr>
                        <tr>
                            <td valign="top"><label for="instructors">Instructors:</label></td>
                            <td id="instructors">None Added</td>
                        </tr>
                        <tr>
                            <td><label for="workshopMinSize">Min Size:</label></td>
                            <td><input type="text" name="workshopMinSize" value="{$event.minSize}" id="workshopMinSize" /></td>
                        </tr>
                        <tr>
                            <td><label for="workshopMaxSize">Max Size:</label></td>
                            <td><input type="text" name="workshopMaxSize" value="{$event.maxSize}" id="workshopMaxSize" /></td>
                        </tr>
                        <tr>
                            <td><label for="workshopWaitListSize">Waitlist Size:</label></td>
                            <td><input type="text" name="workshopWaitListSize" value="{$event.waitlistSize}" id="workshopWaitListSize" /></td>
                        </tr>
                    </tbody>
                </table>
                </form>
            </td>
            <td valign="top" align="center" width="50%">
                <div>Add Instructors</div>
                {html_options size=10 multiple=true id=instructorList name=instructorList options=$instructors selected=$currentInstructors}
                <input type="button" id="instructorAddButton" value="&lsaquo; Add Selected Instructor" />                                
            </td>
        </tr>
    </tbody>
</table>
</div>