<fieldset id="loginInfoContainer">
    <legend>Login Information</legend>
    <p>Total number of logins to date: {$loginCount}</p>
</fieldset>

<br /><br />

<fieldset id="upcomingEventsContainer">
    <legend>Upcoming Events - {$upcomingEventCounts.totalNumber} events</legend>
    <div align="center">
        <table id="upcomingEventsPieChart" class="list">
            <tr>
                <th>Open - {$upcomingEventCounts.open}</th>
                <th>Closed - {$upcomingEventCounts.closed}</th>
                <th>Canceled - {$upcomingEventCounts.canceled}</th>
            </tr>
            <tr>
                <td>{$upcomingEventCounts.open}</td>
                <td>{$upcomingEventCounts.closed}</td>
                <td>{$upcomingEventCounts.canceled}</td>
            </tr>
        </table>
    </div>
</fieldset>

<br /><br />

<fieldset id="pastEventsContainer">
    <legend>Past Events - {$pastEventCounts.totalNumber} events</legend>
    <div align="center">
        <table id="pastEventsPieChart" class="list">
            <tr>
                <th>Open - {$pastEventCounts.open}</th>
                <th>Closed - {$pastEventCounts.closed}</th>
                <th>Canceled - {$pastEventCounts.canceled}</th>
            </tr>
            <tr>
                <td>{$pastEventCounts.open}</td>
                <td>{$pastEventCounts.closed}</td>
                <td>{$pastEventCounts.canceled}</td>
            </tr>
        </table>
    </div>
</fieldset>