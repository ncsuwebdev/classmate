<div id="createEventPopup">
<table width="100%">
    <tbody>
        <tr>
            <td valign="top" width="50%">
                <table>
                    <tbody>
                        <tr>
                            <td>Location:</td>
                            <td id="locationDisplay"></td>
                        </tr>
                        <tr>
                            <td><label for="workshopId">Workshop:</label></td>
                            <td>{html_options id=workshopId name=workshopId options=$workshops selected=$workshopId}</td>
                        </tr>
                        <tr>
                            <td valign="top"><label for="instructors">Instructors:</label></td>
                            <td id="instructors">None Added</td>
                        </tr>
                        <tr>
                            <td><label for="workshopMinSize">Min Size:</label></td>
                            <td><input type="text" name="workshopMinSize" value="{$userConfig.defaultMinWorkshopSize.value}" id="workshopMinSize" /></td>
                        </tr>
                        <tr>
                            <td><label for="workshopMaxSize">Max Size:</label></td>
                            <td><input type="text" name="workshopMaxSize" value="{$userConfig.defaultMaxWorkshopSize.value}" id="workshopMaxSize" /></td>
                        </tr>
                        <tr>
                            <td><label for="workshopWaitListSize">Waitlist Size:</label></td>
                            <td><input type="text" name="workshopWaitListSize" value="{$userConfig.defaultWorkshopWaitListSize.value}" id="workshopWaitListSize" /></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td valign="top" align="center" width="50%">
                <div>Add Instructors</div>
                {html_options size=10 id=instructorList name=instructorList options=$instructors}
                <input type="button" id="instructorAddButton" value="&lsaquo; Add Selected Instructor" />                                
            </td>
        </tr>
    </tbody>
</table>
</div>