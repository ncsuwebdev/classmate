You can create a new account here.  A password will be created for you and sent to 
the email address you provide.  If you already have an account for {$appTitle}, you can  
<a href="{$sitePrefix}/login/?realm={$realm}">click here</a> to log in.<br /><br />
<table width="100%">
    <tr>
        <td align="center">
		<div class="lt"></div>
		    <div class="lbox">
		    Enter your requested user ID below, as well as your email address.<br /><br />
			<form method="post" action="" id="signup" class="checkRequiredFields">
			   <input type="hidden" name="realm" value="{$realm}" />
				<table class="form">
                    <tr>
                        <td class="label"><label for="userId">Login Type:</label></td>
                        <td class="value">{$realmName}</td>
                    </tr>   				
				    <tr>
				        <td><label for="userId">User ID:</label></td>
				        <td><input type="text" id="userId" name="userId" value="" class="required" /></td>
				    </tr>
				    <tr>
				        <td><label for="email">Email Address:</label></td>
				        <td><input type="text" id="email" name="email" value="" class="required" /></td>
				    </tr>
				</table>
				<input type="submit" value="Sign Up Now!" />
			</form>
		</div>
		</td>
    </tr>
</table>