<div id="userIndexAdd">
    Role assignment for <b>{$userId}</b>.  Select a new role below.<br /><br />
    <form method="POST" action="">
        <input type="hidden" name="userId" value="{$userId}" />
        <table class="form">
            <tr>
                <td><label for="role">Role:</label></td>
                <td>{html_options name=role class=required id=role options=$roles selected=$role}</td>
            </tr>
        </table>
        <input type="submit" value="Save User" />
        <input type="button" value="Cancel" onclick="history.go(-1);" />

    </form>
</div>