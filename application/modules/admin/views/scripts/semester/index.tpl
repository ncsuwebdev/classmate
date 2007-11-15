<div id="adminSemesterIndex">
    This interface provides management of the semesters.  Highlighted row
    represents the current semester.<br /><br />

    {if $acl.add}
    <a href="{$sitePrefix}/admin/semester/add/"><img src="{$sitePrefix}/public/images/add.png" alt="Add Semester"></a>
    <a href="{$sitePrefix}/admin/semester/add/">Add New Semester</a><br /><br />
    {/if}
    <table class="list sortable">
    {foreach from=$semesters item=s name=semesters}
        {if $smarty.foreach.semesters.index % $config.headerRowRepeat == 0}
        <tr>
            <th width="150">Name</th>
            <th width="130">Start Date</th>
            <th width="130">Activate Date</th>
            <th width="130">GC Open Date</th>
            <th width="130">ComTech Expire Date</th>
            {if $acl.edit}
            <th width="50">Edit</th>
            {/if}
        </tr>
        {/if}
        <tr class="{cycle values="row1,row2"}{if $s.semesterId == $current} highlight{/if}">
            <td>{$s.name}</td>
            <td style="text-align:center">{$s.startDate|date_format:$config.dateFormat}</td>
            <td style="text-align:center">{$s.activeDt|date_format:$config.dateFormat}</td>
            <td style="text-align:center">{$s.gcOpenDt|date_format:$config.dateFormat}</td>
            <td style="text-align:center">{$s.ctExpireDt|date_format:$config.dateFormat}</td>
            {if $acl.edit}
            <td style="text-align:center">
                <a href="{$sitePrefix}/admin/semester/edit/?semesterId={$s.semesterId}"><img src="{$sitePrefix}/public/images/edit.png" alt="Edit" /></a>
            </td>
            {/if}
        </tr>
    {foreachelse}
        <tr>
            <td class="noResults">No Semesters found</td>
        </tr>
    {/foreach}
    </table>
</div>