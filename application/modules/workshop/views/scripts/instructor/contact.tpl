You can easily contact all users associated with your class.  Select who to send
the message to and ClassMate will take care of the rest!<br /><br />

<form method="POST">
<input type="hidden" name="eventId" value="{$event.eventId}" />
<table class="form">
    <tbody>
        <tr>
            <td><label>Send To:</label></td>
            <td>
                <input type="checkbox" name="attending" id="attending" value="ON" /> Attendees of the class<br />
                <input type="checkbox" name="waitlist" id="waitlist" value="ON" /> People on the waitlist<br />
                <input type="checkbox" name="instructors" id="instructors" value="ON" /> Instructors of this class<br />
            </td>
        </tr>
        <tr>
            <td><label>From:</label></td>
            <td>{$profile.firstName} {$profile.lastName} &lt;{$profile.emailAddress}&gt;</td>
        </tr>
        <tr>
            <td><label for="subject">Subject:</label></td>
            <td><input type="text" size="40" name="subject" label="subject" value="ClassMate: A Note About {$workshop.title}" /></td>
        </tr>
        <tr>
            <td><label for="message">Message:</label></td>
            <td><textarea name="message" id="message" rows="6" cols="60"></textarea></td>            
        </tr>
    </tbody>
</table>
<input type="submit" value="Queue Email for Sending" />
<input type="button" value="Cancel" onclick="javascript:history.go(-1)" />
</form>