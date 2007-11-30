You may change your own password by filling in the fields below.<br /><br />
If this is the first time logging into the system, you must change your password
to proceed.<br /><br />
<form method="post" action="" id="add" class="checkRequiredFields">
<table class="form">
    <tr>
        <td width="150">
            <label for="name">Old Password:</label>
        </td>
        <td width="250">
            <input type="password" maxlength="64" style="width:150px" name="oldPassword" id="oldPassword" class="required" />

        </td>
    </tr>
    <tr>
        <td width="150">
            <label for="name">New Password:</label>
        </td>
        <td width="250">
            <input type="password" maxlength="64" style="width:150px" name="newPassword1" id="newPassword1" class="required" />

        </td>
    </tr>
    <tr>
        <td width="150">
            <label for="name">New Password Again:</label>
        </td>
        <td width="250">
            <input type="password" maxlength="64" style="width:150px" name="newPassword2" id="newPassword2" class="required" />

        </td>
    </tr>
    <tr>
        <td width="400" colspan="2">
            <input type="submit" name="submit" value="Change Password" />
        </td>
    </tr>
</table>
</form>