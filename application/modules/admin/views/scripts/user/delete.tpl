<div id="userIndexDelete">
    <form method="POST" action="">
        <input type="hidden" name="userId" value="{$userId}" />
        You have selected to delete the user <b>{$displayUserId} ({$role})</b> who logs in via <b>{$realmName}</b>.  Are you sure
        you want to do this?
        <br /><br />
        <input type="submit" value="Yes, Delete User" />
        <input type="button" value="No, Go Back" onclick="history.go(-1);" />
    </form>
</div>