<div id="aclIndexDelete">
    <form method="POST" action="">
        <input type="hidden" name="userId" value="{$userId}" />
        You have selected to delete the code for <b>{$userId}</b>.
        <br /><br />
        Are you sure you want to do this?
        <br /><br />
        <input type="submit" value="Yes, Delete API Code" />
        <input type="button" value="No, Go Back" onclick="history.go(-1);" />
    </form>
</div>