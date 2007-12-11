We regularly teach a wide variety of workshops.  Here is the complete list of
what we teach.  Click on the title to see when we are offering it next!
<br /><br />

    {if $acl.add}
    <a href="{$sitePrefix}/workshop/index/add/"><img src="{$sitePrefix}/public/images/add.png" alt="Add"></a>
    <a href="{$sitePrefix}/workshop/index/add/">Add New Workshop</a><br /><br />
    {/if}
    <table class="list sortable">
    {foreach from=$workshops item=w name=workshops}
        {if $smarty.foreach.workshops.index % $config.headerRowRepeat == 0}
        <tr>
            <th width="450">Title</th>
            <th width="150">Category</th>
        </tr>
        {/if}
        <tr class="{cycle values="row1,row2"}">
            <td>
            {if $acl.details}
                <a href="{$sitePrefix}/workshop/index/details/?workshopId={$w.workshopId}">{$w.title}</a>
            {else}
                {$w.title}
            {/if}
            </td>
            <td style="text-align:center">{$w.category}</td>
        </tr>
    {foreachelse}
        <tr>
            <td class="noResults">No Workshops found</td>
        </tr>
    {/foreach}
    </table>