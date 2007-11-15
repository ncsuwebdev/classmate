<div id="bugAdd">
    If you are experiencing an issue with Aerial, you can file a bug report here.  If you
    have a feature request, you should contact your administrator directly.<br /><br />
    <form method="POST" action="">
    <table class="form">
        <tr>
            <td><label>Reproducibility:</label></td>
            <td>{html_options class=required name=reproducibility id=reproducibility options=$reproducibilityTypes}</td>
        </tr>
        <tr>
            <td><label>Severity:</label></td>
            <td>{html_options class=required name=severity id=severity options=$severityTypes}</td>
        </tr>
        <tr>
            <td><label>Priority:</label></td>
            <td>{html_options class=required name=priority id=priority options=$priorityTypes}</td>
        </tr>
        <tr>
            <td><label>Description:</label></td>
            <td><textarea rows="5" class='required' cols="100" name="description"></textarea></td>
        </tr>
    </table>
    <input type="submit" value="File Bug Report" />
    <input type="button" value="Cancel" onclick="history.go(-1);" />
    </form>
</div>