<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>{$appTitle} - {$title}</title>
<link rel="stylesheet" type="text/css" media="all" href="{$sitePrefix}/public/css/site.css" />
<link rel="shortcut icon" type="image/x-icon" href="{$sitePrefix}/public/images/favicon.ico" />
<script language="javascript" type="text/javascript" src="{$sitePrefix}/public/scripts/mootools.v1.11.js"></script>
<script language="javascript" type="text/javascript" src="{$sitePrefix}/public/packages/calendar/calendar_stripped.js"></script>
<script language="javascript" type="text/javascript" src="{$sitePrefix}/public/packages/calendar/calendar-setup_stripped.js"></script>
<script language="javascript" type="text/javascript" src="{$sitePrefix}/public/packages/calendar/calendar-en.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="{$sitePrefix}/public/packages/calendar/calendar-red.css" />
{foreach from=$scripts item=script}
<script language="javascript" type="text/javascript" src="{$sitePrefix}/public/scripts/{$script}"></script>
{/foreach}
</head>
<body>

<div class="top"></div>
<div class="header">
    <div class="menu">
    {foreach from=$tabs item=t name=tabs}
    {if $branch eq $t.module}
    {assign var=sectionTitle value=$t.display}
    {/if}
    <div{if $branch eq $t.module} id="menuItemActive"{/if}><a href="{$sitePrefix}/{if $t.module eq 'default'}index{else}{$t.module}{/if}/">{$t.display}</a></div>
    {/foreach}
    {section name=loop start=$smarty.foreach.tabs.total loop=12}
    <div><a>&nbsp;</a></div>
    {/section}
	</div>
</div>
<div class="content_left">
	<div class="date">{$title}</div>
	<div class="newsitem">
	{$actionTemplate}
	</div>

</div>
<div class="content_right">
	<div class="links">
		<div class="title">{$sectionTitle} Navigation</div>
        {foreach from=$subnav item=s name=subnav}
            <a{if $subNavSelect eq $s.select} class="active"{/if} href="{$s.link}" target="{$s.target|empty_alt:'_self'}">{$s.display}</a>
            <div class="line"><span></span></div>
        {/foreach}
        <div class="title">Aerial</div>
        <a href="{$sitePrefix}/bug/add">Report a Bug</a>
        <div class="line"><span></span></div>

        <div class="line"><span></span></div>
	</div>
</div>
<div id="oitocfooter">
    <div class="left">
        <a href="http://framework.zend.com" target="_blank"><img align="left" src="http://framework.zend.com/images/PoweredBy_ZF_4DarkBG.png" width="127" height="25" alt="Powered By Zend Framework" /></a>

    </div>
    <div class="right">
        <a href="http://itdapps.ncsu.edu/"><img src="http://itdapps5.ncsu.edu/htdocs/public/images/minilogo.png" width="128" height="21" alt="OIT - Outreach Technologies" /></a>
    </div>
    &copy; {$copyrightDate} North Carolina State University
</div>
</body>
</html>
