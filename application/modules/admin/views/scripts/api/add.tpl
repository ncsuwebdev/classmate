Enter user ID to add code for.<br /><br />
<form method="POST" action="">
    <table class="form">
        <tr>
            <td><label for="userId">User:</label></td>
            <td><input type="text" name="userId" id="userId" class="required" maxlength="16" /></td>
        </tr>
    </table>

    <input type="submit" value="Create Code" />
    <input type="button" value="Cancel" onclick="history.go(-1);" />
</form>