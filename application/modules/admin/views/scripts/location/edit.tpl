<form method="post" action="">
    <input type="hidden" name="locationId" value="{$location.locationId}" />
    <table class="list">
        <tbody>
        <tr>
            <td><label for="name">Name</label></td>
            <td><input type="text" name="name" size="50" id="name" maxLength="64" value="{$location.name}" /></td>
        </tr>
        <tr>
            <td><label for="description">Description</label></td>
            <td><textarea rows="20" cols="80" name="description" id="description">{$location.description}</textarea></td>
        </tr>
        <tr>
            <td><label for="capacity">Capacity</label></td>
            <td><input type="text" name="capacity" id="capacity" size="5" maxLength="5" value="{$location.capacity}" /> * Enter 0 for unlimited capacity</td>
        </tr>
        </tbody>
    </table>
    <input type="submit" value="Save Location" name="submit" />
</form>