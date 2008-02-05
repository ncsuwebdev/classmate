<div id="createEventPopup">
<input type="hidden" name="eventId" id="eventId" value="{$eventId}" />
<input type="hidden" name="originalLocationId" id="originalLocationId" value="{$event.locationId}" />
<input type="hidden" name="eventStartTime" id="eventStartTime" value="{$event.startTime}" />
<input type="hidden" name="eventEndTime" id="eventEndTime" value="{$event.endTime}" />
<input type="hidden" name="eventDate" id="eventDate" value="{$event.date}" />
<table width="100%">
    <tbody>
        <tr>
            <td valign="top" width="50%">
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