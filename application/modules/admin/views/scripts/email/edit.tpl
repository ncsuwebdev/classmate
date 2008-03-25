<div>
    You can alter this email template below.<br /><br />
    <form method="post" action="" id="edit" class="checkRequiredFields"">
        <input type="hidden" name="emailTemplateId" value="{$template.emailTemplateId}" />
        <input type="hidden" name="triggerId" value="{$template.triggerId}" /> 
        <table class="form">
            <tbody>
            <tr>
                <td><label for="name">Name:</label></td>
                <td><input name="name" id="name" size="35" maxlength="255" type="text" value="{$template.name}" class="required" />(internal use only)</td>
            </tr>
            <tr>
                <td><label for="emailTo">To:</label></td>
                <td><input name="emailTo" id="emailTo" size="35" maxlength="255" type="text" value="{$template.to}" class="required" /> (separate by commas)</td>
            </tr>
            <tr>
                <td><label for="emailSubject">Subject:</label></td>
                <td><input name="emailSubject" id="emailSubject" size="35" maxlength="255" type="text" value="{$template.subject}" class="required" /></td>
            </tr>
            <tr>
                <td><label for="emailBody">Body:</label></td>
                <td><textarea name="emailBody" id="emailBody" rows="15" cols="60" class="required" >{$template.body}</textarea></td>
            </tr>
            </tbody>
        </table>
        <input type="submit" value="Save Template" />
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