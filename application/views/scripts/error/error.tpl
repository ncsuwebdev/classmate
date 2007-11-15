<div class="error">
<b>{$messageTitle}</b><br />
<ul>
{if $message == ''}
    <li>No error message passed</li>
{else}
    <li>{$message}</li>
{/if}
</ul>
<br />
<form id="groupForm">
    <input type="button" value="Back" onclick="history.go(-1);" />
</form>
</div>
<span id="error"></span>