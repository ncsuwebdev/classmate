<div id="userIndexAdd">
    Role assignment for <b>{$displayUserId}</b>.  Select a new role below.<br /><br />
    <form method="POST" action="">
        <input type="hidden" name="userId" value="{$userId}" />
        <table class="form">
            <tr>
                <td><label for="realm">Login Type:</label></td>
                <td>
                {$adapter.name}<br />
                <span id="loginDescription">{$adapter.description}</span>
                </td>
            </tr>
            <tr>
                <td><label for="userId">User ID:</label></td>
                <td>{$displayUserId}</td>
            </tr>
            {if !$adapter.autoLogin}
            <tr id="emailRow">
                <td><label for="email">Email Address:</td>
                <td><input type="text" class="required" name="email" id="email" value="{$email}" size="30" maxlength="255" /></td>
            </tr>
            {/if}
            <tr>
                <td><label for="role">Role:</label></td>
                <td>{html_options name=role class=required id=role options=$roles selected=$role}</td>
            </tr>
        </table>
        <input type="submit" value="Save User" />
        <input type="button" value="Cancel" onclick="history.go(-1);" />

    </form>
</div>