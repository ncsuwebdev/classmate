<div>
    Here you can add, edit, and delete the workshop categories available to assign workshops to.<br /><br />

    {if $acl.add}
    <a href="{$sitePrefix}/admin/category/add/"><img src="{$sitePrefix}/public/images/add.png" alt="Add New Category"></a>
    <a href="{$sitePrefix}/admin/category/add/">Add New Category</a><br /><br />
    {/if}
    <table class="list">
    {foreach from=$wcs item=w name=wcs}
        {if $smarty.foreach.wcs.index % $config.headerRowRepeat == 0}
        <tr>
            <th width="300">Category Name</th>
            {if $acl.edit}
            <th width="50">Edit</th>
            {/if}
            {if $acl.delete}
            <th width="50">Delete</th>
            {/if}
        </tr>
        {/if}
        <tr class="{cycle values="row1,row2"}">
            <td>{$w.name}</td>
            {if $acl.edit}
            <td align="center">
                <a href="{$sitePrefix}/admin/category/edit/?workshopCategoryId={$w.workshopCategoryId}"><img src="{$sitePrefix}/public/images/edit.png" alt="Edit {$w.name}" width="16" height="16" /></a>
            </td>
            {/if}
            {if $acl.delete}
            <td align="center">
                <a onclick="return confirm('Are you sure you want to remove {$w.name}?');" href="{$sitePrefix}/admin/category/delete/?workshopCategoryId={$w.workshopCategoryId}"><img src="{$sitePrefix}/public/images/delete.png" alt="Delete {$w.name}" width="16" height="16" /></a>
            </td>
            {/if}
        </tr>
        {foreachelse}
        <tr>
            <td class="noResults">No Categories Found</td>
        </tr>
        {/foreach}
    </table>
</div>