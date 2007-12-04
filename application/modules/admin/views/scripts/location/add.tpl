<div>
    Here you can add a new location to the system.  This will be a selectable option
    when scheduling classes.<br /><br />
    
    <form method="post" action="">
        <table class="list">
            <tbody>
            <tr>
                <td><label for="name">Name</label></td>
                <td><input type="text" name="name" size="50" id="name" maxLength="64" /></td>
            </tr>
            <tr>
                <td><label for="description">Description</label></td>
                <td><textarea rows="15" cols="60" name="description" id="description"></textarea></td>
            </tr>
            <tr>
                <td><label for="capacity">Capacity</label></td>
                <td><input type="text" name="capacity" id="capacity" size="5" maxLength="5" /> * Enter 0 for unlimited capacity</td>
            </tr>
            </tbody>
        </table>
        <input type="submit" value="Add Location" name="submit" />
    </form>
</div>