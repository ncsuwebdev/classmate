{if $acl.add}
<a href="{$sitePrefix}/admin/api/add"><img src="{$sitePrefix}/public/images/add.png" alt="Add" /></a>
<a href="{$sitePrefix}/admin/api/add">Add New Code</a><br /><br />
{/if}
<table class="list sortable">
{foreach from=$codes item=c name=codes}
    {if $smarty.foreach.codes.index % $config.headerRowRepeat == 0}
    <tr>
        <th width="150">User</th>
        <th width="300">Code</th>
        {if $acl.delete}
        <th width="60">Delete</th>
        {/if}
    </tr>
    {/if}
    <tr class="{cycle values="row1,row2"}">
        <td>
            {$c.userId}
        </td>
        <td style="text-align: center">{$c.code}</td>
        {if $acl.delete}
        <td style="text-align: center">
          <a href="{$sitePrefix}/admin/api/delete/?userId={$c.userId}"><img src="{$sitePrefix}/public/images/delete.png" alt="Delete"/></a>
        </td>
        {/if}
    </tr>
{foreachelse}
    <tr>
        <td class="noResults">No Codes Found</td>
    </tr>
{/foreach}
</table>