You can easily contact all users associated with your class.  Select who to send
the message to and ClassMate will take care of the rest!<br /><br />

<form method="POST">
<table class="form">
    <tbody>
        <tr>
            <td><label>To:</label></td>
            <td>
                <input type="checkbox" name="attendees" id="attendees" value="ON" /> Attendees of the class<br />
                <input type="checkbox" name="waitlist" id="waitlist" value="ON" /> People on the waitlist<br />
                <input type="checkbox" name="instructors" id="instructors" value="ON" /> Instructors of this class<br />
            </td>
        </tr>
        <tr>
            <td><label>From:</label></td>
            <td>from here</td>
        </tr>
        <tr>
            <td><label for="subject">Subject:</label></td>
            <td><input type="text" size="40" name="subject" label="subject" value="" /></td>
        </tr>
        <tr>
            <td><label for="message">Message:</label></td>
            <td><textarea name="message" id="message" rows="6" cols="60"></textarea></td>            
        </tr>
    </tbody>
</table>
<input type="submit" value="Send Email" />
<input type="button" value="Cancel" onclick="javascript:history.go(-1)" />
</form>