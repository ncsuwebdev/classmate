<div>
    You can create a new email template below.<br /><br />
    <form method="post" action="" id="add" class="checkRequiredFields">
        <input type="hidden" name="triggerId" value="{$triggerId}" /> 
        <table class="form">
            <tbody>
            <tr>
                <td><label for="name">Name:</label></td>
                <td><input name="name" id="name" size="35" maxlength="255" class="required" type="text" />(internal use only)</td>
            </tr>
            <tr>
                <td><label for="emailTo">To:</label></td>
                <td><input name="emailTo" id="emailTo" size="35" maxlength="255" class="required" type="text" /> (separate by commas)</td>
            </tr>
            <tr>
                <td><label for="emailSubject">Subject:</label></td>
                <td><input name="emailSubject" id="emailSubject" size="35" class="required" maxlength="255" type="text" /></td>
            </tr>
            <tr>
                <td><label for="emailBody">Body:</label></td>
                <td><textarea name="emailBody" id="emailBody" rows="15" class="required" cols="60"></textarea></td>
            </tr>
            </tbody>
        </table>
        <input type="submit" value="Add Template" />
        <input type="button" value="Cancel" onclick="history.go(-1);" />
    </form>
    <br />
    <b>Available Template Variables</b>
    <table class="list">
    <tbody>
        <tr>
            <th width="200">Variable</th>
            <th width="300">Description</th>
        </tr>
        {foreach from=$templateVars item=t}
        <tr>
            <td>[[{$t.variable}]]</td>
            <td>{$t.description}</td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="2">No variables available</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
</div>