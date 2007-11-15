<div id="adminCronToggle">
    <form method="POST" action="">
        <input type="hidden" name="path" value="{$path}" />
        <input type="hidden" name="status" value="{$status}" />
        Are you sure you want to <b>{$status}</b> <b>{$displayPath}</b>?
        <br /><br />
        <input type="submit" value="Yes" />
        <input type="button" value="No, Go Back" onclick="history.go(-1);" />
    </form>
</div>