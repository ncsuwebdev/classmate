<div>
    {if $notice}
    <table class="highlight form">
        <tbody>
            <tr>
                <td>        
			    Before you can signup for classes or use any of the other new features of ClassMate,
			    we need to get some information from you!  
			    Please fill out this form and you will gain access to signup for classes.
                </td>
            </tr>
        </tbody>
    </table>
    {/if}
    Enter in the information below to change your profile.<br /><br />
    <form method="post" action="" enctype="multipart/form-data" id="edit" class="checkRequiredFields">
        <input type="hidden" name="userId" value="{$profile.userId}" />
        <table class="form">
            <tr>
                <td><label>First Name:</label></td>
                <td><input type="text" name="firstName" id="firstName" class="required" value="{$profile.firstName}" size="20" /></td>
            </tr>
            <tr>
                <td><label>Last Name:</label></td>
                <td><input type="text" name="lastName" id="lastName" class="required" value="{$profile.lastName}" size="20" /></td>
            </tr>  
            <tr>
                <td><label>User Type</label></td>
                <td>{html_options options=$types selected=$profile.type name=type id=type}</td>
            </tr>
		    <tr>
		        <td><label for="pic">Photo:</label></td>
		        <td>
		        <img src="{$sitePrefix}/index/image/?imageId={$profile.picImageId}" alt="{$userId}" /><br /><br />
		        <input type="file" name="pic" id="pic" size="20" />
		        </td>
		    </tr>            
        </table><br />
        <table class="form">
        {foreach from=$custom item=c}
        {$c.render}
        {/foreach}
        </table>
        <input type="submit" value="Save Profile" />
        <input type="button" value="Cancel" onclick="history.go(-1);" />

    </form>
</div>