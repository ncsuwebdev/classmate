<div id="adminemailDetails">
    <table class="form">
        <tr>
            <td><label>Status:</label></td>
            <td>
            {if $acl.changeStatus}
            <form method="post" action="{$sitePrefix}/bug/changeStatus">
                <input type="hidden" name="bugId" value="{$bug.bugId}" />
                {html_options name=status id=status options=$statusTypes selected=$bug.status}
                <input type="submit" value="Set Status">
            </form>
            {else}
            {$bug.status|capitalize}
            {/if}
            </td>
        </tr>
        <tr>
            <td><label>Submitted By:</label></td>
            <td>{$bug.submittedByUserId}</td>
        </tr>
        <tr>
            <td><label>Submit Date:</label></td>
            <td>{$bug.submitDt|date_format:$config.dateTimeFormat}</td>
        </tr>
        <tr>
            <td><label>Reproducibility:</label></td>
            <td>{$bug.reproducibility|capitalize}</td>
        </tr>
        <tr>
            <td><label>Severity:</label></td>
            <td>{$bug.severity|capitalize}</td>
        </tr>
        <tr>
            <td><label>Priority:</label></td>
            <td>{$bug.priority|capitalize}</td>
        </tr>
        <tr>
            <td><label>Description:</label></td>
            <td>{$bug.description|nl2br}</td>
        </tr>
    </table>
</div>