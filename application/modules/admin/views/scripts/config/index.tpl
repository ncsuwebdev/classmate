<div>
    This is the current configuration of the application.<br /><br />

    {if $acl.edit}
    <a href="{$sitePrefix}/admin/config/edit/"><img src="{$sitePrefix}/public/images/edit.png" alt="Edit Application Configuration"></a>
    <a href="{$sitePrefix}/admin/config/edit/">Edit Application Configuration</a><br /><br />
    {/if}
    
    <table class="list">
        <tbody>
            <tr>
                <th width="250">Name</th>
                <th width="350">Value</th>
            </tr>
            {foreach from=$config item=c}
            <tr>
                <td>{$c.key}</td>
                {if $c.key eq "activeConference"}
                    <td>{$conference.name|empty_alt:"No Conference Selected"}</td>
                {else}
                <td>{$c.value}</td>
                {/if}
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>