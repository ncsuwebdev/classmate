<div>
    Enter in the information below to add custom attribute to this object.<br /><br />
    <form method="post" action="" id="edit" class="checkRequiredFields">
        <input type="hidden" name="attributeId" value="{$attribute.attributeId}" />
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
                <td><input type="text" name="label" id="label" value="{$attribute.label}" size="20" class="required" /></td>
            </tr>
            <tr>
                <td><label>Type:</label></td>
                <td>{html_options options=$types name=type id=type class=required selected=$attribute.type}
                <div id="opt"{if $attribute.type == 'select' || $attribute.type == 'radio'} style="display:block"{/if}>
                    {foreach from=$attribute.options item=o}
                        <input type="checkbox" value="{$o}" name="opt_delete[]" /> <b>Delete &quot;{$o}&quot;</b><br />
                    {/foreach}
                    <br /><br />
                    Add New Options:
                    <table class="form" id="options">
                        <tr id="optionRow">
                            <td width="50"><label for="option[]">Option:</label></td>
                            <td width="120"><input type="text" maxlength="128" size="20" name="option[]" value="" /></td>
                        </tr>
                    </table>
                    <a href="javascript:addNewRow('options', 'optionRow')"><img src="{$sitePrefix}/public/images/add.png" width="16" height="16" alt="Add Row" /></a>
                    <a href="javascript:addNewRow('options', 'optionRow')">Add Row</a>
        
                    &nbsp;&nbsp;
                    <a href="javascript:removeRow('options')"><img src="{$sitePrefix}/public/images/subtract.png" width="16" height="16" alt="Remove Row" /></a>
                    <a href="javascript:removeRow('options')">Remove Row</a>
                </div>
                </td>
            </tr>
            <tr>
                <td><label>Required:</label></td>
                <td><input type="checkbox" value="1" name="required" id="required" {if $attribute.required}checked="checked"{/if}/></td>
            </tr>
            <tr>
                <td><label>Display Direction:</label></td>
                <td>
                    <label for="direction"><input type="radio" value="vertical" name="direction" {if $attribute.direction eq 'vertical'}checked="checked"{/if}/>Vertical</label>
                    <br />
                    <label for="direction"><input type="radio" value="horizontal" name="direction" {if $attribute.direction eq 'horizontal'}checked="checked"{/if}/>Horizontal</label>
                </td>
            </tr>  
        </table>
        <input type="submit" value="Save Custom Attribute" />
        <input type="button" value="Cancel" onclick="history.go(-1);" />

    </form>
</div>