<form method="post" action="" id="editForm">
    <input type="hidden" name="workshopCategoryId" value="{$workshopCategory.workshopCategoryId}" />
    <table class="form">
        <tbody>
        <tr>
            <td><label for="name">Name:</label></td>
            <td><input type="text" name="name" size="50" id="name" maxLength="64" class="required" value="{$workshopCategory.name}" /></td>
        </tr>
        <tr>
            <td><label for="description">Description:</label></td>
            <td><textarea rows="10" cols="80" name="description" id="description">{$workshopCategory.description}</textarea></td>
        </tr>
        </tbody>
    </table>
    <input type="submit" value="Save Category" name="submit" />
    <input type="button" value="Cancel" onclick="history.go(-1);" />
</form>