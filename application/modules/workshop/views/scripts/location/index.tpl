<div>
    If you sign up for one of our workshops, chances are your class will be held
    in one of these locations.  Check out the details for directions
    <br /><br />

    {if $acl.add}
    <div class="add"><a href="{$sitePrefix}/workshop/location/add/">Add New Lab</a></div>
    {/if}
	
    {foreach from=$locations item=l name=locations}
	    {if ($l.status == 'disabled' && $acl.viewDisabled) || $l.status == 'enabled'}
	    <div class="location">
	        {if $l.status == 'enabled'}
	        <img src="{$sitePrefix}/public/images/network-idle.png" alt="{$location.name}" />
	        {else}
	        <img src="{$sitePrefix}/public/images/network-offline.png" alt="{$location.name}" />
	        {/if}
	        <div class="name">{if $l.status == 'disabled'}<B>DISABLED!</B> &nbsp; {/if}<a href="{$sitePrefix}/workshop/location/details/?locationId={$l.locationId}">{$l.name}</a></div>
	        {if $l.address != ''}
	        <div class="mapit"><a href="http://maps.google.com/maps?f=q&hl=en&geocode=&q={$l.address}&ie=UTF8&z=16&iwloc=addr" target="_blank">Map With Google Maps!</a></div>        
	        <div class="address">{$l.address}</div>
	        {else}
	        <div>No Address Provided</div>
	        {/if}
	    </div>
	    {/if}
    {foreachelse}
    No Locations Found
    {/foreach}
</div>