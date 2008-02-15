{if count($messages) != 0}
<div class="messageContainer">
	<div class="message">
	{foreach from=$messages item=m}
	{$m}<br />
	{/foreach}
	</div>
</div>
{/if}
Select the way you would like to log in to ClassMate below.  
<br /><br />
<table width="100%">
    <tr>
        <td align="center">
		<div class="lt"></div>
        <div class="lbox">
			<form action="" method="post" id="login" class="checkRequiredFields">	
			<label for="realm">Select Login Type:</label>
            <select size="1" name="realm" id="realm">
                {foreach from=$authAdapters item=a}
                <option value="{$a.realm}" title="{$a.description}" class="{if $a.autoLogin}autoLogin{else}manualLogin{/if} {if $a.signup}signup{else}noSignup{/if}">{$a.name}</option>
                {/foreach}
            </select>
            <span id="loginDescription"></span>
			<table class="form" id="loginForm">
			    <tbody>
			    <tr>
			        <td class="label"><label for="userId">User ID:</label></td>
			        <td class="value"><input type="text" id="userId" name="userId" value="" class="required" size="15" /></td>
			    </tr>
			    <tr>
			        <td class="label"><label for="password">Password:</label></td>
			        <td class="value"><input type="password" id="password" name="password" class="required" size="15" />
			        <a href="javascript:goto('{$sitePrefix}/login/index/forgot/')">I Forget...Help!</a>
			        </td>
			    </tr>
			    </tbody>
			</table>
			<input type="submit" value="Login" />
			<span id="manual"> - or -  
			<input type="button" value="Sign-up Now!" onclick="javascript:goto('{$sitePrefix}/login/index/signup/')"  /></span>
			</form>            
        </div>
        </td>
    </tr>
</table>		