<p>
Aerial is a collection of utilities designed to manage the classroom technology on campus at NC State University.
</p>

{if $flickrImage}
<div id="flickr">
    <a href="{$flickrImage.clickUri}" target="_blank">
        <img src="{$flickrImage.uri}" width="{$flickrImage.width}" height="{$flickrImage.height}" alt="Flickr Image" />
    </a>
    <br /><br />
    Random image provided by <a target="_blank" href="http://flickr.com/search/?q=nc+state"><img src="{$sitePrefix}/public/images/flickr.png" width="50" height="50" alt="flickr" /></a>
</div>
{/if}