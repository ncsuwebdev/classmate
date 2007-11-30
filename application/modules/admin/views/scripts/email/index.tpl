<div>
    These are all the available email triggers in the system.<br /><br />
    
    <table class="list">
        <tbody>
            <tr>
                <th width="250">Name</th>
                <th width="350">Description</th>
            </tr>
            {foreach from=$emailTriggers item=et}
            <tr>
                <td>
                    {if $acl.details}
                    <a href="{$sitePrefix}/admin/email/details/?triggerId={$et.triggerId}">
                    {/if}
                    {$et.triggerId}
                    {if $acl.details}
                    </a>
                    {/if}
                </td>
                <td>{$et.description}</td>                
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>