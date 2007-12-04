    {if $acl.edit}
        <a href="{$sitePrefix}/profile/index/edit/?userId={$profile.userId}"><img src="{$sitePrefix}/public/images/edit.png" alt="Edit" /></a>
        <a href="{$sitePrefix}/profile/index/edit/?userId={$profile.userId}">Edit Profile</a><br /><br />
    {/if}
<table class="form">
    <tr>
        <td width="100"><label>User ID:</label></td>
        <td>{$displayUserId}</td>
    </tr>
    <tr>
        <td><label>Login Type:</label></td>
        <td>{$adapter.name}</td>
    </tr>
    <tr>
        <td><label>Name:</label></td>
        <td>{$profile.firstName} {$profile.lastName}</td>
    </tr>
    {if isset($email)}
    <tr>
        <td><label>Email:</label></td>
        <td>{$email}</td>
    </tr>    
    {/if}
    <tr>
        <td><label>User Type:</label></td>
        <td>
        {assign var=type value=$profile.type}
        {$types.$type|empty_alt:$profile.type}</td>
    </tr>    
    <tr>
        <td><label>Photo:</label></td>
        <td><img src="{$sitePrefix}/index/image/?imageId={$profile.picImageId}" border="0" /></td>
    </tr>           
</table>
<br />
{if count($custom) != 0}
<table class="form">
{foreach from=$custom item=c}
{$c.render}
{/foreach}
</table>
{/if}