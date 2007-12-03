<div style="text-align:left">
If you have forgotten your password, we can reset it for you and email it
to the address we have on file for you.
</div>
<br />

<table width="100%">
    <tr>
        <td align="center">
        <div class="lt"></div>
            <div class="lbox">
            Enter the user ID you registered with to reset your password.<br /><br />
			<form action="" method="post" id="forgot" class="checkRequiredFields">
			<input type="hidden" name="realm" value="{$realm}" />
			    <table class="form">
			        <tbody>
                    <tr>
                        <td class="label"><label for="userId">Login Type:</label></td>
                        <td class="value">{$realmName}</td>
                    </tr>			        
			        <tr>
			            <td class="label"><label for="userId">User Id:</label></td>
			            <td class="value"><input type="text" id="userId" name="userId" value="" class="required" /></td>
			        </tr>
			        </tbody>
			    </table>
			    <br />
			    <input type="submit" value="Reset My Password" /><br /><br />
			</form>
        </div>
        </td>
    </tr>
</table>