<div id="userIndexDelete">
&nbsp;
    <form method="POST" action="">
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
        </table><br />
                
        You have selected to delete the attribute <b>{$attribute.label}</b>.  Are you sure
        you want to do this?  This will remove all data associated with this attribute.
        <br /><br />
        <input type="submit" value="Yes, Delete Attribute" />
        <input type="button" value="No, Go Back" onclick="history.go(-1);" />
    </form>
</div>