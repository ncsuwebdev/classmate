<a href="{$sitePrefix}/workshop/index/">&lt; &lt; Back to All Workshops</a><br />
{if $acl.options}
<div id="createEventPopup" style="position: absolute; left: -10000px;">
<form method="POST" action="{$sitePrefix}/workshop/index/options" id="workshopOptionForm">
<input type="hidden" name="workshopId" value="{$workshop.workshopId}">
<table width="450" class="form">
    <tbody>
        <tr>
            <td width="25%">
                <label for="workshopCategoryId">Category:</label>
            </td>
            <td colspan="2" width="55%">
	            <select size="1" name="workshopCategoryId" id="workshopCategoryId">
	                 {foreach from=$categories item=c}
	                 <option value="{$c.workshopCategoryId}" style="background-image:url({$sitePrefix}/index/image/?imageId={$c.smallIconImageId})"{if $c.workshopCategoryId == $workshop.workshopCategoryId} selected="selected"{/if}>{$c.name}</option>
	                 {/foreach}
	            </select>
            </td>
            <td></td>
         </tr>
         <tr>
            <td width="25%">
                <label for="status">Status:</label>
            </td>
            <td colspan="2" width="50%">
                <input type="radio" name="status" id="status_enabled" value="enabled"{if $workshop.status == 'enabled'} checked="checked"{/if} /> Enabled &nbsp; &nbsp; 
                <input type="radio" name="status" id="status_disabled" value="disabled"{if $workshop.status == 'disabled'} checked="checked"{/if} /> Disabled
            </td>
         </tr>
         <tr>
            <td width="25%">
                <label for="featured">Featured:</label>
            </td>
            <td colspan="2" width="50%">
                <input type="checkbox" name="featured" value="1" id="featured"{if $workshop.featured == 1} checked="checked"{/if} /> 
            </td>
            <td></td>
         </tr>         
         <tr>
            <td colspan="4">
                <label for="instructors">Workshop Editors:</label>
            </td>
         </tr>
         <tr>
            <td colspan="2" width="50%" id="editors">None Added</td>
            <td align="center" colspan="2" width="50%">
                {html_options size=10 multiple=true id=editorList name=editorList options=$users selected=$currentEditors}
                <br />
                <input type="button" id="editorAddButton" value="&lsaquo; Add Selected User" />              
            </td>
         </tr>
     </tbody>
</table>
</form>
</div>
{/if}
<div id="detailsRight">
    {if $acl.options}
    <input type="button" id="manageWorkshop" value="Manage Workshop Options" />
    {/if}
    <div class="rightTitle">
        {if $acl.addEvent}
        <span id="addEvent" class="add"><a href="{$sitePrefix}/workshop/schedule/?workshopId={$workshop.workshopId}"></a>&nbsp;</span>
        {/if}     
        Upcoming Schedule
    </div>

    <div class="rightContent">     
	    {foreach from=$events item=e name=events}
	        <div class="event" id="event_{$e.eventId}">
	            <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
	            {$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}
	            <span class="status">
                {if $e.status == 'instructor'}
                    <a href="{$sitePrefix}/workshop/instructor/?eventId={$e.eventId}"></a>Instructor Tools...
                {elseif $e.status == 'attending'}
                    <a href="{$sitePrefix}/workshop/signup/cancel/?eventId={$e.eventId}"></a>You are attending.
                    <br />
                    Cancel...
                {elseif $e.status == 'waitlist'}
                    <a href="{$sitePrefix}/workshop/signup/cancel/?eventId={$e.eventId}"></a>You're on the waitlist.
                    <br />
                    Cancel...
                {elseif $e.status == 'restricted'}
                    <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a><span class="restricted">Attendance Restricted</span>
                {else}
                    {if $e.roleSize < $e.maxSize}
                        <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}">{math equation="x-y" x=$e.maxSize y=$e.roleSize} seats remaining.</a>
                        <br />
                        Register...
                    {else}
                        {if $e.waitlistSize != 0 && $e.waitlistSize > $e.waitlistTotal}
                            <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a>Class is full.
                            <br />Signup to waitlist...
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
        {if $acl.addLink}
        <span id="addLink" class="add inlineEdit" target="addLinkForm"></span>
        {/if}     
        Online Resources
    </div>
    
    <div class="rightContent">     
    <div id="addLinkForm" rel="type=link&size=20&url={$sitePrefix}/workshop/index/addLink/&response=response"><a href="http://"></a></div>
        {if $acl.reorderLink}
        Drag the icon to reorder the links.<br /><br />
        <span style="display: none;" id="sortUrl">{$sitePrefix}/workshop/index/saveLinkOrder/</span>
        {/if}
        <div id="links">      
        
	        {foreach from=$links item=l}
	        <div class="linkPackage" id="linkPackage_{$l.workshopLinkId}">
	            {if $acl.editLink}
		        <div id="editLink" class="inlineEdit" target="link_{$l.workshopLinkId}"></div>
		        {/if}
                {if $acl.reorderLink}
                <div class="order">&nbsp;</div>
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

    {if $workshop.featured == 1}
    <div class="featured_badge"></div>
    {/if}

	<div id="response">&nbsp;</div> 
	<input type="hidden" name="workshopId" id="workshopId" value="{$workshop.workshopId}" class="postArgs taglist description wsTitle prerequisites" />   
	<div id="workshopTitleContainer">
	    {if $acl.edit}
		<div id="editTitle" class="inlineEdit" target="wsTitle"></div>
		{/if}
        <img src="{$sitePrefix}/index/image/?imageId={$category.largeIconImageId}" alt="{$category.name}" />		
        {if $workshop.status == 'disabled'}
        <img id="disabledImage" src="{$sitePrefix}/public/images/cross.png" />
        <span id="disabled">DISABLED!</span>
        {/if}		
		<span id="wsTitle" rel="type=input&size=40&url={$sitePrefix}/workshop/index/edit/&response=response">{$workshop.title}</span>
	</div>
    {if $acl.edit}
    <div id="editTags" class="inlineEdit" target="taglist"></div>
    {/if}
    <div id="tags">
        <b>Tags:</b>
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
	<div id="description" rel="type=textarea&cols=80&rows=20&url={$sitePrefix}/workshop/index/edit/&response=response">{$workshop.description|empty_alt:"No Description Provided"}</div>
	{else}
	<div id="description">{$workshop.description|empty_alt:"No Description Provided"}</div>
	{/if}
    
    <div id="prereqs" class="sectionBar">
        <p class="left"></p>
        <p class="content">Workshop Pre-Requisites</p>
    </div>
    
    {if $acl.edit}
    <div id="editPrerequisites" class="inlineEdit" target="prerequisites"></div>
    {/if}
    <div id="prerequisites" rel="type=textarea&cols=80&rows=10&url={$sitePrefix}/workshop/index/edit/&response=prerequisitesResponse">{$workshop.prerequisites|empty_alt:"There are no pre-requisites for this workshop"}</div>

    
    {if $acl.addDocuments || count($documents) != 0}
    <div id="tabDocumentsHeader" class="sectionBar">   
        <p class="left"></p>
            {if $acl.addDocuments}
                <div id="addDocument" class="add"></div>
            {/if}        
        <p class="content">
	        Workshop Handouts
        </p>
    </div>
    <div id="tabDocuments"> 
        {if $acl.addDocuments}
        <div id="addDocumentForm">
            Click "Browse" to upload new handouts.  Mutliple files can be uploaded at one time
            by clicking the "Browse" button again.<br /><br />
            <form id="uploadForm" method="POST" enctype="multipart/form-data" action="{$sitePrefix}/workshop/index/addDocuments/">
                <input type="hidden" name="attributeId" value="{$workshop.workshopId}" />
                <input type="hidden" name="attributeName" value="workshopId" />
                <input type="file" name="uploadDocuments"><br clear="all"/>
                <input type="button" value="Cancel" onclick="javascript:toggleDocForm()" />
                <input type="submit" value="Upload Files" />
            </form>              
        </div>
        {/if}      
	    Click the icon to download the file.          
		<div id="documents">
		    {foreach from=$documents item=d}
		    <div class="document" title="{$d.description}" id="document_{$d.documentId}">
		        {if $acl.deleteDocument}
		        <div class="delete"></div>
		        {/if}
		        <a href="{$sitePrefix}/workshop/index/downloadDocument/?documentId={$d.documentId}">
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