<div id="aclIndexAdd">
    Select the name that you would like to call the role.  This system also allows
    roles to inherit permission from an existing role.  Inhertance is optional
    but is useful in implementing a tiered access system.<br /><br />

    <form method="POST" action="{$sitePrefix}/admin/acl/{$action}">
        <input type="hidden" name="originalRoleName" id="originalRoleName" value="{$originalRoleName}" />
        <table class="form">
            <tr>
                <td><label for="roleName">Role Name:</label></td>
                <td><input type="text" class="required" name="roleName" id="roleName" value="{$roleName}" size="30" maxlength="126" /></td>
            </tr>
            <tr>
                <td><label for="role">Inherit From:</label></td>
                <td>{html_options name=inheritRoleName id=inheritRoleName options=$roles selected=$inheritRoleName}
                <input type="button" value="Pre-Populate" onclick="if (confirm('You will lose any changes you have made.')) location.href='{$sitePrefix}/admin/acl/{$action}/?roleName=' + document.getElementById('roleName').value + '&originalRoleName=' + document.getElementById('originalRoleName').value + '&inheritRoleName=' + document.getElementById('inheritRoleName').value; return false;" /></td>
            </tr>
        </table><br /><br />

        {if $action == 'edit' && count($children) != 0}
        <table class="form highlight">
            <tr>
                <td><b>CAUTION!</b><br /><br />
                Making changes to this role will affect the following roles which are
                inherited (directly or indirectly) from this role:
                <ul>
                {foreach from=$children item=c}
                <li><a href="{$sitePrefix}/acl/details/?originalRoleName={$c.name}">{$c.from}</li>
                {/foreach}
                </ul>
                </td>
            </tr>
        </table><br /><Br />
        {/if}
        <div id="accessList">
            {foreach from=$resources key=module item=controllers}
            <div class="aclSection">
                <table class="list">
                    <tr class="module">
                        <td colspan="3" width="460"><b>{$module|capitalize}</b></td>
                    </tr>
                    {foreach from=$controllers key=controller item=actions}
                    <tr class="controller">
                        <td class="td1" width="300">{$controller|capitalize}
                        </td>
                        <td width="80"><input type="radio" value="allow" onclick="toggle(this);" name="{$module}[{$controller}][all]" id="{$module}[{$controller}][all]"{if $actions.all.access} checked="checked"{/if} /> Allow All</td>
                        <td width="80"><input type="radio" value="deny" onclick="toggle(this);" name="{$module}[{$controller}][all]" id="{$module}[{$controller}][all]"{if !$actions.all.access} checked="checked"{/if} /> Deny All</td>
                    </tr>
                        {foreach from=$actions.part key=action item=access}
                    <tr class="action">
                        <td class="td1">{$action|capitalize}
                        </td>
                        <td><input type="radio" value="allow" name="{$module}[{$controller}][part][{$action}]" id="{$module}[{$controller}][part][{$action}]"{if $access.access} checked="checked"{/if}/> Allow</td>
                        <td><input type="radio" value="deny" name="{$module}[{$controller}][part][{$action}]" id="{$module}[{$controller}][part][{$action}]"{if !$access.access} checked="checked"{/if}/> Deny</td>
                    </tr>
                        {/foreach}
                    {/foreach}
                </table>
            </div>
            {/foreach}

        </div>
        <input type="submit" value="Set Permission" />
        <input type="button" value="Cancel" onclick="history.go(-1);" />
    </form>
</div>