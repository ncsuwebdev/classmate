<div>
    This system allows for custom attributes to be added to a certain set of 
    objects within the system.  Select the object below to modify any attributes
    associated with it. <br /><br />

    <table class="list sortable">
    {foreach from=$nodes item=n name=nodes}
        {if $smarty.foreach.nodes.index % $config.headerRowRepeat == 0}
        <tr>
            <th width="200">Node</th>
            <th width="350">Description</th>
        </tr>
        {/if}
        <tr class="{cycle values="row1,row2"}">
            <td><a href="{$sitePrefix}/admin/custom/details/?nodeId={$n.nodeId}">{$n.nodeId}</a></td>
            <td>{$n.description}</td>
        </tr>
    {foreachelse}
        <tr>
            <td class="noResults">No objects found.</td>
        </tr>
    {/foreach}
    </table>
</div>