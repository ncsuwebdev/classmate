<div id="detailsRight">
    <div class="rightTitle">Instructors</div>

    <div class="rightContent">
            {foreach from=$instructors item=i}
            <div class="instructor">
                {if $i.picImageId == 0}
                    <img src="{$sitePrefix}/public/images/system-users.png" border="0" width="32" height="32" />
                {else}
                    <img src="{$sitePrefix}/index/image/?imageId={$i.picImageId}" border="0" />
                {/if}
                <span class="name">{$i.firstName} {$i.lastName}</span>
                <br />
                <div class="email">
                    <a href="mailto:{$i.emailAddress}?subject=Question from ClassMate about {$workshop.title} on {$event.date|date_format:$config.longDateCompactFormat}    ">{$i.emailAddress}</a>
                </div>
            </div>
            {/foreach}                                 
    </div>
</div>

<div id="detailsLeft">
    <div id="workshopTitleContainer">
        <img src="{$sitePrefix}/index/image/?imageId={$category.largeIconImageId}" alt="{$category.name}" />
        <div id="wsTitle">Evaluate <a href="{$sitePrefix}/workshop/index/details/?workshopId={$workshop.workshopId}">{$workshop.title}</a></div>       
    </div>   
    <div class="event">
        <span class="date">{$event.date|date_format:$config.longDateCompactFormat}</span> |
        <span class="time">{$event.startTime|date_format:$config.timeFormat} - {$event.endTime|date_format:$config.timeFormat}</span> |
        <span class="location">{$location.name}</span>
    </div>
    
    <div>
    <form method="post" action="" id="evaluationForm" class="checkRequiredFields">
        <input type="hidden" name="eventId" value="{$eventId}" />
            
        <div id="questionPanes">
            <div class="questionContent">
                {foreach from=$custom item=c}
                <div>
                    <p>{$c.render}</p>
                </div>
                {/foreach}
                <div>
                    <div class="customRow">
                    <br />
                    <p style="font-size: 1.5em; margin-bottom: 30px;">Press the save button below to submit your evaluation.</p>
                        <p style="font-size: 1.3em;">Make sure you are satisfied with your answers because you 
                        can't change them once you've submitted your evaluation.</p>
                        <br /><br />
                        <input type="submit" value="Save Evaluation" />
                        <input type="button" value="Cancel" onclick="javascript:history.go(-1);" />
                     </div>
                </div>
            </div>
        </div>
    </form>
    </div>
    <div id="evaluationControls">
        <img src="{$sitePrefix}/public/images/leftCircleArrow-orange.png" alt="Previous Question" id="previous" />
        <ul id="evaluationControlsButtons">
            {foreach name=customAttrs from=$custom item=c}
            <li>{$smarty.foreach.customAttrs.iteration}</li>
            {/foreach}
            <li>{math equation="x + y" x=1 y=$smarty.foreach.customAttrs.iteration}</li>
        </ul>
        <img src="{$sitePrefix}/public/images/rightCircleArrow-orange.png" alt="Next Question" id="next" />
    </div>
</div>