{if $noEvaluationsYet}
    <p style="font-size: 1.2em;">No evaluations have been submitted yet.</p>
{else}
<div id="questionPanes">
    <div class="questionContent">
        {foreach from=$evaluationResults item=q}
            {if $q.type == 'radio' || $q.type == 'ranking' || $q.type == 'select'}
                <div>
                    <div style="padding: 30px;">
                    <p class="questionTitle">{$q.label}</p>
                    <table id="question_{$q.attributeId}" class="graphQuestion" border="1">
                        <tr>
                            {foreach from=$q.options item=opt}
                            <th>{$opt}</th>
                            {/foreach}
                        </tr>
                        <tr>
                        {foreach from=$q.results item=r}
                            <td>{$r.answerCount}</td>
                        {/foreach}
                        </tr>
                    </table>
                    </div>
                </div>
            {elseif $q.type == 'text' || $q.type == 'textarea'}
                <div>
                    <div style="padding: 30px;">
                        <p class="questionTitle">{$q.label}</p>
                        <table id="question_{$q.attributeId}" width="100%">
                            <tbody>
                            {foreach from=$q.results item=r}
                                {if $r != ""}
                                <tr class="{cycle values='answerRow1,answerRow2'}">
                                    <td>{$r|nl2br}</td>
                                </tr>
                                {/if}
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>            
            {elseif $q.type == 'checkbox'}
                <div>
                    <div style="padding: 10px;">
                        <p class="questionTitle">{$q.label}</p>
                        <div id="question_{$q.attributeId}">
                            {assign var=total value=0}
                            {foreach from=$q.results item=r}
                                {if $r == 1}
                                    {assign var='total' value=`$total+1`}
                                {/if}
                            {/foreach}
                            <p>{$total} out of {$totalEvaluations} people checked this box.</p>
                        </div>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
</div>

<div id="evaluationControls">
    <img src="{$sitePrefix}/public/images/leftCircleArrow-orange.png" alt="Previous Question" id="previous" />
    <ul id="evaluationControlsButtons">
        {foreach name=questions from=$evaluationResults item=q}
        <li>{$smarty.foreach.questions.iteration}</li>
        {/foreach}
    </ul>
    <img src="{$sitePrefix}/public/images/rightCircleArrow-orange.png" alt="Next Question" id="next" />
</div>
{/if}