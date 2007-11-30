<div>
    {if $acl.add}
    <a href="{$sitePrefix}/admin/custom/add/?nodeId={$node.nodeId}"><img src="{$sitePrefix}/public/images/add.png" alt="Add Room"></a>
    <a href="{$sitePrefix}/admin/custom/add/?nodeId={$node.nodeId}">Add New Custom Attribute for {$node.nodeId}</a><br /><br />
    {/if}
    <table class="form">
        <tr>
            <td><label>Object:</label></td>
            <td>{$node.nodeId}</td>
        </tr>
        <tr>
            <td><label>Description:</label></td>
            <td>{$node.description}</td>
        </tr>
    </table>
    <div id="customAttributeOrder">
		<span id="listStatus">&nbsp;</span><br /><br />
		<span style="display: none;" id="parentIdName">nodeId</span>
		<span style="display: none;" id="parentIdValue">{$node.nodeId}</span>
		<span style="display: none;" id="sortUrl">{$sitePrefix}/admin/custom/orderAttributes/</span>
		<div id="list">
		{foreach from=$attributes item=a name=attributes}
		<table class="elm" id="{$a.attributeId}">
		    <tbody>
		    <tr>
		        <td class="order">{$smarty.foreach.attributes.iteration}</td>
		        <td class="description">
		            <div>
		                {if $acl.attributeDetails}
		                <a href="{$sitePrefix}/admin/custom/attributeDetails/?attributeId={$a.attributeId}">{$a.label}</a>
		                {else}
		                {$a.label}
		                {/if}
		                ({$a.type})
		            </div>
		        </td>
		        <td class="action">
                    {if $acl.edit}
                    <a href="{$sitePrefix}/admin/custom/edit/?attributeId={$a.attributeId}"><img src="{$sitePrefix}/public/images/edit.png" alt="edit" height="16" width"16" /></a>
                    {/if} 		        
		            {if $acl.delete}
		            <a href="{$sitePrefix}/admin/custom/delete/?attributeId={$a.attributeId}"><img src="{$sitePrefix}/public/images/delete.png" alt="delete" height="16" width"16" /></a>
		            {/if} 
		            
		        </td>
		    </tr>
		    </tbody>
		</table>
		{foreachelse}
		No custom attributes found for this object
		{/foreach}
		</div>
    </div>
</div>