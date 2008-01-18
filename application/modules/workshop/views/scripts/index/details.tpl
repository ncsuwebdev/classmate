<div id="detailsRight">
    <div class="rightTitle">
        Upcoming Schedule
    </div>

    <div class="rightContent">
        {if $acl.addEvent}
        <span id="addEvent" class="add">Add New Event</span>
        {/if}      
	    {foreach from=$events item=e name=events}
	        <div class="event" id="event_{$e.eventId}">
	            <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
	            {$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}
	            <span class="status">
                {if $e.status == 'instructor'}
                    <a href="{$sitePrefix}/workshop/signup/instructor/?eventId={$e.eventId}"></a>Instructor Options
                {elseif $e.status == 'attending'}
                    <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a>You are attending. Cancel...
                {elseif $e.status == 'waitlist'}
                    <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a>You're on the waitlist.  Cancel...
                {elseif $e.status == 'restricted'}
                    <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a><span class="restricted">Attendance Restricted</span>
                {else}
                    {if $e.roleSize < $e.maxSize}
                        <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a>{math equation="x-y" x=$e.maxSize y=$e.roleSize} seats remaining. Register...
                    {else}
                        {if $e.waitlistSize != 0 && $e.waitlistSize > $e.waitlistTotal}
                            <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a>Class is full.  Signup to waitlist...
                        {else}
                            This class is full.
                        {/if}                  
                    {/if}
                {/if}
                </span>
	        </div>
	    {foreachelse}
	    No events are scheduled at this time.
	    {/foreach}  
    </div>  
    <div style="display: none" class="toolboxItem notify">Notify me when this is scheduled</div>
    <div class="rightTitle">
        Online Resources
    </div>
    
    <div class="rightContent">
        {if $acl.addLink}
        <span id="addLink" class="add inlineEdit" target="addLinkForm">Add Resource</span>
        <div id="addLinkForm" rel="type=link&size=20&url={$sitePrefix}/workshop/index/addLink/&response=response"><a href="http://"></a></div>
        {/if}      
        <div id="links">      
	        {foreach from=$links item=l}
	        <div class="linkPackage" id="linkPackage_{$l.workshopLinkId}">
	            {if $acl.editLink}
		        <div id="editLink" class="inlineEdit" target="link_{$l.workshopLinkId}"></div>
		        {/if}
		        {if $acl.deleteLink}
		        <div class="delete"></div>
		        {/if}
		        <div class="link" id="link_{$l.workshopLinkId}" rel="type=link&size=20&url={$sitePrefix}/workshop/index/editLink/&response=response">
		            <a href="{$l.url}" target="_blank">{$l.name|truncate:30:'...':true}</a>	            
		        </div>
		    </div>
	        {foreachelse}
	        <div class="noLinks">No Resources Found</div>
	        {/foreach}
        </div>
    </div>
</div>
<div id="detailsLeft">
	<div id="response">&nbsp;</div> 
	<input type="hidden" name="workshopId" id="workshopId" value="{$workshop.workshopId}" class="postArgs description wsTitle prerequisites" />   
	<div id="workshopTitleContainer">
	    {if $acl.edit}
		<div id="editTitle" class="inlineEdit" target="wsTitle"></div>
		{/if}
		<div id="wsTitle" rel="type=input&size=40&url={$sitePrefix}/workshop/index/edit/&response=response">{$workshop.title}</div>
	</div>
    {if $acl.edit}
    <div id="editTags" class="inlineEdit" target="taglist"></div>
    {/if}
    <div id="tags">
        <div id="taglist" rel="type=tags&size=40&url={$sitePrefix}/workshop/index/edit/&response=response">
        {foreach from=$tags item=t}
        <a href="{$sitePrefix}/index/search/?search={$t.name}">{$t.name}</a>
        {foreachelse}
        None
        {/foreach}
        </div>
    </div>  	
	{if $acl.edit}
	<div id="editDescription" class="inlineEdit" target="description"></div>
	<div id="description" rel="type=textarea&cols=80&rows=30&url={$sitePrefix}/workshop/index/edit/&response=response">{$workshop.description}</div>
	{else}
	<div id="description">{$workshop.description|empty_alt:"No Description Provided"}</div>
	{/if}
	
    {if $acl.edit}
    <div id="editPrerequisites" class="inlineEdit" target="prerequisites"></div>
    {/if}
    <div id="prereqs">
        <b class="sectionTitle">Pre-Requisites:</b>
        <span id="prerequisites" rel="type=input&size=40&url={$sitePrefix}/workshop/index/edit/&response=prerequisitesResponse">{$workshop.prerequisites}</span>
    </div>	
    {if $acl.addDocuments || count($documents) != 0}
    <div id="tabDocuments">   
        {if $acl.addDocuments}
        <div id="addDocument" class="add">Add Documents</div>
        {/if}   
        <b class="sectionTitle">Handouts:</b>   
        Click the icon to download the file.<br />
        {if $acl.addDocuments}
        <div id="addDocumentForm">
            Click "Browse" to upload new handouts.  Mutliple files can be uploaded at one time
            by clicking the "Browse" button again.<br /><br />
            <form id="uploadForm" method="POST" enctype="multipart/form-data" action="{$sitePrefix}/workshop/index/addDocuments/">
                <input type="hidden" name="attributeId" value="{$workshop.workshopId}" />
                <input type="hidden" name="attributeName" value="workshopId" />
                <input type="file" name="uploadDocuments"><br clear="all"/>
                <input type="submit" value="Upload Files" />
            </form>              
        </div>
        {/if}        
		<div id="documents">
		    {foreach from=$documents item=d}
		    <div class="document" title="{$d.description}" id="document_{$d.documentId}">
		        {if $acl.deleteDocument}
		        <div class="delete"></div>
		        {/if}
		        <a href="{$sitePrefix}/{$d.path}">
		            <div class="icon docType-{$d.type}"></div>
			    </a>
			    <div class="data">
			        <div><span class="name">{$d.name|truncate:50}</span> (<span class="filesize">{$d.filesize} bytes</span>)</div>
			        {if $acl.editDocument}
			        <input type="hidden" name="documentId" id="documentId_{$d.documentId}" value="{$d.documentId}" class="postArgs documentTitle_{$d.documentId}" />
			        <span class="title editable inlineEdit" target="documentTitle_{$d.documentId}" id="documentTitle_{$d.documentId}" rel="type=input&size=40&url={$sitePrefix}/workshop/index/editDocument/&response=response" title="Click here to edit">{$d.title|empty_alt:"Click here to add a title"}</span>
			        {else}
			        <span class="title">{$d.title|empty_alt:"No Description Provided"}</span>
			        {/if}
			    </div>
		    </div>
		    {/foreach}
		</div>
	</div>	
	{/if}
</div>