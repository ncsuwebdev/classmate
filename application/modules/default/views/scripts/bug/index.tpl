<div id="adminSemesterIndex">
    These are the bugs which have been filed.<br /><br />

    {if $acl.add}
    <a href="{$sitePrefix}/bug/add/"><img src="{$sitePrefix}/public/images/add.png" alt="Add Semester"></a>
    <a href="{$sitePrefix}/bug/add/">New Bug Report</a><br /><br />
    {/if}
    <table class="list sortable">
    {foreach from=$bugs item=b name=bugs}
        {if $smarty.foreach.bugs.index % $config.headerRowRepeat == 0}
        <tr>
            <th width="450">Bug Description</th>
            <th width="150">Submit Info</th>
            <th width="60">Status</th>
        </tr>
        {/if}
        <tr class="{cycle values="row1,row2"}">
            <td>
            {if $acl.details}
                <a href="{$sitePrefix}/bug/details/?bugId={$b.bugId}">{$b.description|truncate:70}</a>
            {else}
                {$b.description|truncate:70}
            {/if}
            </td>
            <td style="text-align:center">{$b.submittedByUserId} ({$b.submitDt|date_format:$config.dateFormat})</td>
            <td style="text-align:center">{$b.status|capitalize}</td>
        </tr>
    {foreachelse}
        <tr>
            <td class="noResults">No Bugs found</td>
        </tr>
    {/foreach}
    </table>
</div>