<div id="adminSemesterIndex">
    This interface provides the ability to enable and disable the execution
    of any cron jobs that run to maintain data within Cyclone.<br /><br />

    {if $acl.add}
    <a href="{$sitePrefix}/admin/cron/add/"><img src="{$sitePrefix}/public/images/add.png" alt="Add Cron Job"></a>
    <a href="{$sitePrefix}/admin/cron/add/">Add Cron Job</a>
    {/if}

    {if $acl.toggle}
    <a href="{$sitePrefix}/admin/cron/toggle/?path=all&status=disabled"><img src="{$sitePrefix}/public/images/enabled.png" alt="Enable All"></a>
    <a href="{$sitePrefix}/admin/cron/toggle/?path=all&status=disabled" style="color:#0C0">Enable All</a> &nbsp; &nbsp;

    <a href="{$sitePrefix}/admin/cron/toggle/?path=all&status=enabled"><img src="{$sitePrefix}/public/images/disabled.png" alt="Disable All"></a>
    <a href="{$sitePrefix}/admin/cron/toggle/?path=all&status=enabled" style="color:#C00">Disable All</a><br /><br />
    {/if}
    <table class="list sortable">
    {foreach from=$cronjobs item=c name=cronjobs}
        {if $smarty.foreach.cronjobs.index % $config.headerRowRepeat == 0}
        <tr>
            <th width="250">Cron Job</th>
            <th width="130">Status</th>
            <th width="150">Last Execution</th>
            {if $acl.edit}
            <th width="50">Edit</th>
            {/if}
        </tr>
        {/if}
        <tr class="{cycle values="row1,row2"}">
            <td>{$c.path}</td>
            <td style="text-align:center" class="{$c.status}">
            {if $acl.toggle}
            <a href="{$sitePrefix}/admin/cron/toggle/?path={$c.path}">
            {/if}
            {if $c.status == 'enabled'}
            Enabled
            {else}
            Disabled
            {/if}
            {if $acl.toggle}
            </a>
            {/if}
            </td>
            <td align="center">
                {$c.lastRunDt|date_format:$config.dateTimeFormat}
            </td>
            {if $acl.edit}
            <td style="text-align:center">
                <a href="{$sitePrefix}/admin/cron/edit/?path={$c.path}"><img src="{$sitePrefix}/public/images/edit.png" alt="Edit" /></a>
            </td>
            {/if}
        </tr>
    {foreachelse}
        <tr>
            <td class="noResults">No Cron Jobs found</td>
        </tr>
    {/foreach}
    </table>
</div>