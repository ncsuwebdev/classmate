<div id="detailsRight">
    <div class="rightTitle">Event Information</div>

    <div class="rightContent">
    <table width="100%">
        <tbody>
        <tr>
            <td valign="top"><label>Workshop:</label></td>
            <td valign="top">{$workshop.title}</td>
        </tr>
        <tr>
            <td><label>Date:</label></td>
            <td>{$event.date|date_format:$config.longDateCompactFormat}</td>
        </tr>        
        <tr>
            <td width="65"><label>Time:</label></td>
            <td>{$event.startTime|date_format:$config.timeFormat} - {$event.endTime|date_format:$config.timeFormat}</td>
        </tr>
        <tr>
            <td valign="top"><label>Instructors:</label></td>
            <td valign="top">
                {foreach from=$instructors item=i}
                    {$i.firstName} {$i.lastName}<br />
                {/foreach}
            </td>
        </tr>                             
        </tbody>
    </table>    
    </div>
</div>

<div>
<form method="post" action="" id="evaluationForm" class="checkRequiredFields">
    <input type="hidden" name="eventId" value="{$eventId}" />
        
    <div id="questionPanes">
        <div id="questionContent">
            {foreach from=$custom item=c}
            <div>
                <p>{$c.render}</p>
            </div>
            {/foreach}
            <div>
                <p style="font-size: 1.5em; margin-bottom: 30px;">Press the save button below to submit your evaluation.</p>
                    <p style="font-size: 1.3em;">Make sure you are satisfied with your answers because you 
                    can't change them once you've submitted your evaluation.</p>
                    <br /><br />
                    <input type="submit" value="Save Evaluation" />
                    <input type="button" value="Cancel" onclick="javascript:history.go(-1);" />
            </div>
        </div>
    </div>
    
    <div id="evaluationHeading">
        <img src="{$sitePrefix}/public/images/leftCircleArrow-orange.png" alt="Previous Question" id="previous" />
        <ul id="evaluationHeadingButtons">
            {foreach name=customAttrs from=$custom item=c}
            <li>{$smarty.foreach.customAttrs.iteration}</li>
            {/foreach}
            <li>{math equation="x + y" x=1 y=$smarty.foreach.customAttrs.iteration}</li>
        </ul>
        <img src="{$sitePrefix}/public/images/rightCircleArrow-orange.png" alt="Next Question" id="next" />
    </div>
</form>
</div>