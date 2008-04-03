<form method="post" class="checkRequiredFields" id="add">
<table class="form">
    <tr>
        <td>
            <label for="workshopCategoryId">Category:</label>
        </td>
        <td>
            <select size="1" name="workshopCategoryId" id="workshopCategoryId">
                 {foreach from=$categories item=c}
                 <option value="{$c.workshopCategoryId}" style="background-image:url({$sitePrefix}/index/image/?imageId={$c.smallIconImageId})">{$c.name}</option>
                 {/foreach}
            </select>
        </td>
    </tr>
    <tr>
        <td width="100"><label for="title">Workshop Title:</label></td>
        <td><input type="text" name="title" id="title" value="" class="required" size="30" maxlength="64" /></td>
    </tr>
    <tr>
        <td><label for="tags">Tags:
        <td>
            <input type="text" name="tags" id="tags" value="" size="30" /><br />
            Separate tags by commas.
        </td>
    </tr>        
    <tr>
        <td colspan="2"><label for="description">Main description of the content of this workshop:</label></td>
    </tr>
    <tr>
        <td colspan="2"><textarea name="description" id="description" rows="15" cols="110" class="required" /></textarea></td>
    </tr>    
    <tr>
        <td colspan="2"><label for="prerequisites">What pre-requisites would the students of this class need?</label></td>
    </tr>    
    <tr>        
        <td colspan="2"><textarea name="prerequisites" id="prerequisites" rows="15" cols="110" class="required" /></textarea></td>
    </tr>                
</table>
<input type="submit" value="Submit" />
<input type="button" value="Cancel" onclick="javascript:history.go(-1);" />
</form>