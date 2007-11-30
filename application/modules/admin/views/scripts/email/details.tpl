<div>
    You can manage the email templates for the email triggers from here.<br /><br />
    
    {if $acl.add}
    <a href="{$sitePrefix}/admin/email/add/?triggerId={$triggerId}"><img src="{$sitePrefix}/public/images/add.png" alt="Add Email Template"></a>
    <a href="{$sitePrefix}/admin/email/add/?triggerId={$triggerId}">Add New Template</a><br /><br />
    {/if}
    
    <table class="list">
        <tbody>
            <tr>
                <th width="200">Trigger ID</th>
                <th width="300">Name</th>
                {if $acl.edit}
                <th width="50">Edit</th>
                {/if}
                {if $acl.delete}
                <th width="50">Delete</th>
                {/if}
            </tr>
            {foreach from=$templates item=t}
            <tr>
                <td>{$t.triggerId}</td>
                <td>{$t.name}</td>
                {if $acl.edit}
                <td align="center"><a href="{$sitePrefix}/admin/email/edit/?emailTemplateId={$t.emailTemplateId}"><img src="{$sitePrefix}/public/images/edit.png" alt="Edit Email Template"></a></td>
                {/if}
                {if $acl.delete}
                <td align="center"><a href="{$sitePrefix}/admin/email/delete/?emailTemplateId={$t.emailTemplateId}&amp;triggerId={$t.triggerId}"><img src="{$sitePrefix}/public/images/delete.png" alt="Delete Email Template"></a></td>
                {/if}
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>