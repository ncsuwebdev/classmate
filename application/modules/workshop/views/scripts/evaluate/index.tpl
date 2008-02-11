<form method="post" action="" id="evaluationForm" class="checkRequiredFields">
    <input type="hidden" name="eventId" value="{$eventId}" />
    <table class="form">
        {foreach from=$custom item=c}
        {$c.render}
        {/foreach}
    </table>
    <input type="submit" value="Save Evaluation" />
    <input type="button" value="Cancel" onclick="history.go(-1);" />
</form>