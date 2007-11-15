<div id="userIndexIndex">
    Here you can add users and grant them roles.  Roles are defined by the applicaiton
    administrator and can be set <a href="{$sitePrefix}/admin/acl/">here</a>.<br /><br />

    {if $acl.add}
    <a href="{$sitePrefix}/admin/user/add/"><img src="{$sitePrefix}/public/images/add.png" alt="Add User"></a>
    <a href="{$sitePrefix}/admin/user/add/">Add New User</a><br /><br />
    {/if}
    <table class="list sortable">
    {foreach from=$users item=u name=users}
        {if $smarty.foreach.users.index % $config.headerRowRepeat == 0}
        <tr>
            <th width="70">User ID</th>
            <th width="200">Access Role</th>
            {if $acl.log}
            <th width="50">Logs</th>
            {/if}
            {if $acl.edit}
            <th width="50">Edit</th>
            {/if}
            {if $acl.delete}
            <th width="50">Delete</th>
            {/if}
        </tr>
        {/if}
        <tr class="{cycle values="row1,row2"}">
            <td>{$u.userId}</td>
            <td align="center">{$u.role}</td>
            {if $acl.log}
            <td align="center">
                <a href="{$sitePrefix}/admin/log/?userId={$u.userId}"><img src="{$sitePrefix}/public/images/log.png" alt="Logs for {$u.userId}" /></a>
            </td>
            {/if}
            {if $acl.edit}
            <td align="center">
                <a href="{$sitePrefix}/admin/user/edit/?userId={$u.userId}"><img src="{$sitePrefix}/public/images/edit.png" alt="Edit {$u.userId}" /></a>
            </td>
            {/if}
            {if $acl.delete}
            <td align="center">
                <a href="{$sitePrefix}/admin/user/delete/?userId={$u.userId}"><img src="{$sitePrefix}/public/images/delete.png" alt="Delete {$u.userId}" /></a>
            </td>
            {/if}
        </tr>
    {foreachelse}
        <tr>
            <td class="noResults">No Users found</td>
        </tr>
    {/foreach}
    </table>
</div>