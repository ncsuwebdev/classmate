<div>
    Here you can add, edit, and delete the locations available in which classes can be held.<br /><br />

    {if $acl.add}
    <a href="{$sitePrefix}/admin/location/add/"><img src="{$sitePrefix}/public/images/add.png" alt="Add Access Location"></a>
    <a href="{$sitePrefix}/admin/location/add/">Add New Location</a><br /><br />
    {/if}
    <table class="list">
    {foreach from=$locations item=l name=locations}
        {if $smarty.foreach.locations.index % $config.headerRowRepeat == 0}
        <tr>
            <th width="300">Location Name</th>
            {if $acl.edit}
            <th width="50">Edit</th>
            {/if}
            {if $acl.delete}
            <th width="50">Delete</th>
            {/if}
        </tr>
        {/if}
        <tr class="{cycle values="row1,row2"}">
            <td>{$l.name}</td>
            {if $acl.edit}
            <td align="center">
                <a href="{$sitePrefix}/admin/location/edit/?locationId={$l.locationId}"><img src="{$sitePrefix}/public/images/edit.png" alt="Edit {$l.name}" width="16" height="16" /></a>
            </td>
            {/if}
            {if $acl.delete}
            <td align="center">
                <a onclick="return confirm('Are you sure you want to remove {$l.name}?');" href="{$sitePrefix}/admin/location/delete/?locationId={$l.locationId}"><img src="{$sitePrefix}/public/images/delete.png" alt="Delete {$l.name}" width="16" height="16" /></a>
            </td>
            {/if}
        </tr>
        {foreachelse}
        <tr>
            <td class="noResults">No Locations Found</td>
        </tr>
        {/foreach}
    </table>
</div>