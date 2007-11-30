<div>
    You can edit the application configuration from here. These changes will be made globally and immediately.<br /><br />
    <form method="post" action="">
    <table class="list">
        <tbody>
            <tr>
                <th width="250">Name</th>
                <th width="350">Value</th>
            </tr>
            {foreach from=$config item=c}
            <tr>
                <td>{$c.key}</td>
                <td>
                {if $c.key eq "timezone"}
                    
                    {html_options name=$c.key options=$timezoneList selected=$c.value}   
                    
                {elseif $c.key eq "activeConference"}
                
                    {html_options name=$c.key options=$conferences selected=$c.value}
                
                {else}
                    <input type="text" name="{$c.key}" size="40" value="{$c.value}" />
                {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    <input type="submit" value="Save" name="submit" />
    <input type="button" value="Cancel" onclick="history.go(-1);" />
    </form>
</div>