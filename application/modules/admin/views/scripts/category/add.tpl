<div>
    Here you can add a new workshop category to the system.  This will be a selectable option
    when adding workshops.<br /><br />
    
    <form method="post" action="" id="addForm">
        <table class="form">
            <tbody>
            <tr>
                <td><label for="name">Name:</label></td>
                <td><input type="text" name="name" size="50" id="name" class="required" maxLength="64" /></td>
            </tr>
            <tr>
                <td><label for="description">Description</label></td>
                <td><textarea rows="10" cols="80" name="description" id="description"></textarea></td>
            </tr>
            </tbody>
        </table>
        <input type="submit" value="Add Category" name="submit" />
        <input type="button" value="Cancel" onclick="history.go(-1);" />
    </form>
</div>