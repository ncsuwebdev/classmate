<form method="post" action=""  enctype="multipart/form-data" id="edit" class="checkRequiredFields">
    <input type="hidden" name="workshopCategoryId" value="{$workshopCategory.workshopCategoryId}" />
    <table class="form" enctype="multipart/form-data" id="edit" class="checkRequiredFields">
        <tbody>
        <tr>
            <td><label for="name">Name:</label></td>
            <td><input type="text" name="name" size="50" id="name" maxLength="64" class="required" value="{$workshopCategory.name}" /></td>
        </tr>
        <tr>
            <td><label for="description">Description:</label></td>
            <td><textarea rows="10" cols="80" name="description" id="description">{$workshopCategory.description}</textarea></td>
        </tr>
            <tr>
                <td><label for="largeIcon">Large Icon:</label></td>
                <td>
                <img src="{$sitePrefix}/index/image/?imageId={$workshopCategory.largeIconImageId}" alt="large icon" /><br />
                <input type="file" name="largeIcon" id="largeIcon" size="20" /><br />
                * This image will automatically be adjusted to be maximum 32 x 32 pixels
                </td>
            </tr>         
            <tr>
                <td><label for="smallIcon">Small Icon:</label></td>
                <td>
                <img src="{$sitePrefix}/index/image/?imageId={$workshopCategory.smallIconImageId}" alt="small icon" /><br />
                <input type="file" name="smallIcon" id="smallIcon" size="20" /><br />
                * This image will automatically be adjusted to be maximum 16 x 16 pixels
                </td>
            </tr>          
        </tbody>
    </table>
    <input type="submit" value="Save Category" name="submit" />
    <input type="button" value="Cancel" onclick="history.go(-1);" />
</form>