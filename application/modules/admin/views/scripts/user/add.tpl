<div id="userIndexAdd">
    Enter in the information below to add a user to the system.<br /><br />
    <form method="POST" action="">
        <table class="form">
            <tr>
                <td><label for="userId">User ID:</label></td>
                <td><input type="text" class="required" name="userId" id="userId" value="" size="8" maxlength="8" /></td>
            </tr>
            <tr>
                <td><label for="role">Role:</label></td>
                <td>{html_options name=role class=required id=role options=$roles selected=$role}</td>
            </tr>
        </table>
        <input type="submit" value="Add User" />
        <input type="button" value="Cancel" onclick="history.go(-1);" />

    </form>
</div>