<div id="adinEmailqueueIndex">
    The following are the emails that have been sent or are queued to be sent.<br /><br />

    <form method="get" action="">
        <table class="form">
            <tr>
                <td><label for="status">Status:</label></td>
                <td>{html_options name=status id=status options=$statusTypes selected=$status}</td>
            </tr>
            <tr>
                <td><label>Queue Date:</label></td>
                <td>
                    {jscalendar name='queueBeginDt' value=$queueBeginDt|date_format:$config.dateTimeFormat}
                    <b>- to -</b>
                    {jscalendar name='queueEndDt' value=$queueEndDt|date_format:$config.dateTimeFormat}
                </td>
            </tr>
            <tr>
                <td><label>Sent Date:</label></td>
                <td>
                    {jscalendar name='sentBeginDt' value=$sentBeginDt|date_format:$config.dateTimeFormat}
                    <b>- to -</b>
                    {jscalendar name='semtEndDt' value=$semtEndDt|date_format:$config.dateTimeFormat}
                </td>
            </tr>
            <tr>
                <td><label>Attribute:</label></td>
                <td>
                    <input type="text" name="attributeName" id="attributeName" value="{$attributeName}" />
                    <input type="text" name="attributeId" id="attributeId" value="{$attributeId}" />
                </td>
            </tr>
            <tr>
                <td><label for="callId">Call ID:</td>
                <td><input type="text" name="callId" id="callId" value="{$callId}" /></td>
            </tr>
        </table>
        <input type="submit" value="Filter Results" />
    </form>
    <br /><br />

    <table class="list sortable">
    {foreach from=$emails item=e name=emails}
        {if $smarty.foreach.emails.index % $config.headerRowRepeat == 0}
        <tr>
            <th width="150">To</th>
            <th width="300">Subject</th>
            <th width="90">Status</th>
            <th width="90">Call ID</th>
            <th width="50">Details</th>
        </tr>
        {/if}
        <tr class="{cycle values="row1,row2"}">
            <td>{$e.msg.to}</td>
            <td>{$e.msg.subject}</td>
            <td style="text-align: center">{$e.status|capitalize}</td>
            <td style="text-align: center">{$e.callId|empty_alt:'None'}</td>
            <td style="text-align: center">
                <a href="{$sitePrefix}/admin/emailqueue/details/?queueId={$e.queueId}"><img src="{$sitePrefix}/public/images/details.png" alt="Details" /></a>
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td class="noResults">No Emails found</td>
        </tr>
        {/foreach}
    </table>
</div>