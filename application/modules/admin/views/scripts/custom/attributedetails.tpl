<div>
    {if $acl.edit || $acl.delete}
        {if $acl.edit}
	    <a href="{$sitePrefix}/admin/custom/edit/?attributeId={$attribute.attributeId}"><img src="{$sitePrefix}/public/images/edit.png" alt="edit"></a>
	    <a href="{$sitePrefix}/admin/custom/edit/?attributeId={$attribute.attributeId}">Edit</a> &nbsp; &nbsp;
	    {/if}
	    {if $acl.delete}
	    <a href="{$sitePrefix}/admin/custom/delete/?attributeId={$attribute.attributeId}"><img src="{$sitePrefix}/public/images/delete.png" alt="Delete"></a>
        <a href="{$sitePrefix}/admin/custom/delete/?attributeId={$attribute.attributeId}">Delete</a>
	    {/if}
	    <br /><br />
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
    </table><br /><br />
    <table class="form">
        <tr>
            <td><label>Label:</label></td>
            <td>{$attribute.label}</td>
        </tr>
        <tr>
            <td><label>Type:</label></td>
            <td>{$attribute.type}</td>
        </tr>
        <tr>
            <td><label>Required:</label></td>
            <td>{if $attribute.required}Yes{else}No{/if}</td>
        </tr>
        <tr>
            <td><label>Display Direction:</label></td>
            <td>{$attribute.direction|capitalize}</td>
        </tr>  
        {if $attribute.type == 'radio' || $attribute.type == 'select'}   
        <tr>
            <td><label>Options:</label></td>
            <td>
            {foreach from=$attribute.options item=option}
            {$option}<br />
            {foreachelse}
            No Options
            {/foreach}
            </td>
        </tr>                 
        {/if}
    </table>
    <br /><br />
    <b>Preview</b>
    <div class="preview">
        <table class="form">
            {$attribute.render}
        </table>
    </div>
</div>