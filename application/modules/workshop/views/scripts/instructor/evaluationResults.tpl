<div id="questionPanes">
    <div class="questionContent">
        {foreach from=$evaluationResults item=q}
        <div>
            <div style="padding: 10px;">
            <p class="questionTitle">{$q.label}</p>
            <table id="question_{$q.attributeId}" class="question" border="1">
                <tr>
                    <td></td>
                    {foreach from=$q.options item=opt}
                    <th>{$opt}</th>
                    {/foreach}
                </tr>
                <tr>
                {foreach from=$q.results item=r}
                    <th>{$r.answerLabel}</th>
                    <td>{$r.answerCount}</td>
                {/foreach}
                </tr>
            </table>
            </div>
        </div>
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