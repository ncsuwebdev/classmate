<input type="hidden" id="startTime" value="{$startTime}" />
<input type="hidden" id="endTime" value="{$endTime}" />
<input type="hidden" id="thisYear" value="{$thisYear}" />
<input type="hidden" id="thisWeek" value="{$thisWeek}" />
<input type="hidden" id="startInAddMode" value="{$startInAddMode}" />
<input type="hidden" id="addModeWorkshopId" value="{$workshopId}" />
<input type="hidden" id="startInEditMode" value="{$startInEditMode}" />
<input type="hidden" id="editModeEventId" value="{$eventId}" />
<input type="hidden" id="basetime" value="{$baseTime}" />
<input type="hidden" id="today" value="{$today}" />

<div id="workshopSearchResults">
    <div id="searchResultsTitle">
        <form id="wsForm" style="float: left; display: inline; width: 50%;">
            <label for="locationId">Filter by Location:</label>
            {html_options id=locationId name=locationId options=$locations selected=$locationId}
        </form>
        <p style="float: right; margin-right: 10px;"><input id="modeButton" type="button" value="Switch to Add Mode" /></p>
            <br />
            <div id="workshopAddForm">
                Workshop Length:
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
            </div>
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
