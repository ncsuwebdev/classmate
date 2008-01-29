<input type="hidden" id="startTime" value="{$startTime}" />
<input type="hidden" id="endTime" value="{$endTime}" />
<input type="hidden" id="startInAddMode" value="{$startInAddMode}" />

<div id="workshopSearchWrapper">
    <div id="workshopAddFormTitle"><p>New Workshop Options</p></div>
    <div id="workshopAddForm">
    <table>
        <tbody>
            <tr>
                <td><label for="workshopId">Workshop:</label></td>
                <td>{html_options id=workshopId name=workshopId options=$workshops selected=$workshopId}</td>
            </tr>
            <tr>
                <td>Workshop Length:</td>
                <td>
                    <select name="workshopLength[Time_Hour]" id="workshopLengthHours">
                        <option label="00" value="00">00</option>
                        <option label="01" value="01" selected="selected">01</option>
                        <option label="02" value="02">02</option>
                        <option label="03" value="03">03</option>
                        <option label="04" value="04">04</option>
                        <option label="05" value="05">05</option>
                        <option label="06" value="06">06</option>
                        <option label="07" value="07">07</option>
                        
                        <option label="08" value="08">08</option>
                        <option label="09" value="09">09</option>
                        <option label="10" value="10">10</option>
                        <option label="11" value="11">11</option>
                        <option label="12" value="12">12</option>
                    </select> hours
                    
                    <select name="workshopLength[Time_Minute]" id="workshopLengthMinutes">
                        <option label="00" value="00" selected="selected">00</option>
                        <option label="5" value="5">5</option>
                        <option label="10" value="10">10</option>
                        <option label="15" value="15">15</option>
                        <option label="20" value="20">20</option>
                        <option label="25" value="15">25</option>
                        <option label="30" value="30">30</option>
                        <option label="35" value="35">35</option>
                        <option label="40" value="40">40</option>
                        <option label="45" value="45">45</option>
                        <option label="50" value="50">50</option>
                        <option label="55" value="55">55</option>
                    </select> minutes
                </td>
            </tr>
            <tr>
                <td><label for="workshopMinSize">Min Size:</label></td>
                <td><input type="text" name="workshopMinSize" value="{$userConfig.defaultMinWorkshopSize}" id="workshopMinSize" /></td>
            </tr>
            <tr>
                <td><label for="workshopMaxSize">Max Size:</label></td>
                <td><input type="text" name="workshopMaxSize" value="{$userConfig.defaultMaxWorkshopSize}" id="workshopMaxSize" /></td>
            </tr>
            <tr>
                <td><label for="workshopWaitListSize">Waitlist Size:</label></td>
                <td><input type="text" name="workshopWaitListSize" value="{$userConfig.defaultWorkshopWaitListSize}" id="workshopWaitListSize" /></td>
            </tr>
        </tbody>
    </table>
    </div>
</div>
&nbsp;
<input type="hidden" id="basetime" value="{$baseTime}" />

<div id="workshopSearchResults">
    <div id="searchResultsTitle">
        <form id="wsForm" style="float: left; display: inline; width: 50%;">
            <label for="locationId">Filter by Location:</label>
            {html_options id=locationId name=locationId options=$locations}
        </form>
        <p style="float: right; margin-right: 10px;"><input id="modeButton" type="button" value="Switch to Add Mode" /></p>
    </div>
    <div id="searchResultsContentWrapper">
        <div id="workshopSearchResultsLoading">
            <img width="32" height="32" src="{$sitePrefix}/public/images/loading_big.gif" />
        </div>
        <div id="workshopSearchResultsContent">
            <input type="hidden" id="week" value="{$week}" />
            <input type="hidden" id="year" value="{$year}" />
        </div>
    </div>
</div>
