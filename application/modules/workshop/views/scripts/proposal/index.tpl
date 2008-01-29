Do you think you have what it takes to be an instructor?  Do you have a class
in mind that you want to teach?  If so, tell us about it.
<br /><br />
<form method="POST" class="checkRequiredFields" id="add">
<table class="form">
    <tr>
        <td width="140"><label for="title">Workshop Title:</label></td>
        <td><input type="text" name="title" id="title" value="" class="required" size="30" maxlength="64" /></td>
    </tr>
    <tr>
        <td><label for="tags">Tag your workshop!
        <td>
            <input type="text" name="tags" id="tags" value="" size="30" /> <a href="{$sitePrefix}/help/?search=tags" target="_blank">What are tags?</a><br />
            Separate tags by commas.
        </td>
    </tr>        
    <tr>
        <td colspan="2"><label for="description">Briefly describe the content your class would cover:</label></td>
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
    <tr>
        <td colspan="2"><label for="softwareDependency">If your class needs specific software to be available, 
        please describe them below:</label></td>
    </tr>
    <tr>
        <td colspan="2"><textarea class="mceNoEditor" name="softwareDependency" id="softwareDependency" rows="8" cols="70"></textarea></td>
    </tr>                  
</table>
<input type="submit" value="Submit" />
<input type="button" value="Cancel" onclick="javascript:history.go(-1);" />
</form>