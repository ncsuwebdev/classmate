<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>{$appTitle} - {$title}</title>
<link rel="stylesheet" type="text/css" media="all" href="{$sitePrefix}/public/css/layout.css" />
<link rel="stylesheet" type="text/css" media="all" href="{$sitePrefix}/public/css/Ot/common.css" />
{foreach from=$css item=c}
<link rel="stylesheet" type="text/css" media="all" href="{$sitePrefix}/public/css/{$c}" />
{/foreach}
<script type="text/javascript" src="{$sitePrefix}/public/scripts/mootools.v1.11.js"></script>
<script type="text/javascript" src="{$sitePrefix}/public/scripts/Autocompleter.js"></script>
<script type="text/javascript" src="{$sitePrefix}/public/scripts/Autocompleter.Remote.js"></script>
<script type="text/javascript" src="{$sitePrefix}/public/scripts/Observer.js"></script>
<script type="text/javascript" src="{$sitePrefix}/public/scripts/global.js"></script>
{if $showNews}
<script type="text/javascript" src="{$sitePrefix}/public/scripts/news.js"></script>
{/if}
{if $useInlineEditor}
<script type="text/javascript" src="{$sitePrefix}/public/scripts/moo.prompt.v1.js"></script>
<script type="text/javascript" src="{$sitePrefix}/public/scripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="{$sitePrefix}/public/scripts/iEdit.js"></script>
<script type="text/javascript" src="{$sitePrefix}/public/scripts/tinyMceConfig.js"></script>
{/if}
{foreach from=$javascript item=script}
<script type="text/javascript" src="{$sitePrefix}/public/scripts/{$script}"></script>
{/foreach}
</head>
<body>
    <input type="hidden" name="sitePrefix" id="sitePrefix" value="{$sitePrefix}" />
    <div class="content">
        <div class="header_right">
            <div class="top_info">
                <div class="top_info_right">
                    <p>

		            {if $loggedInUser == ''} 
		            <b>You are not Logged in!</b> <a href="{$sitePrefix}/login/">Log in</a> here.
		            {else}
		            Logged in as {$loggedInUser} via {$loggedInRealm}  &nbsp;|&nbsp;
		            {if $authManageLocally}
		            <a href="{$sitePrefix}/login/index/changePassword/">Change Password</a> &nbsp;|&nbsp;
		            {/if}
		            <a href="{$sitePrefix}/login/index/logout/">Sign Out</a>
		            {/if}                    
                    </p>                    
                </div>      
            </div>
                    
            <div class="bar">
                <ul>
                    <li class="slogan">Navigation:</li>
                    {foreach from=$tabs item=t name=tabs}
                    {if $branch eq $t.module}
                    {assign var=sectionTitle value=$t.display}
                    {/if}
                    <li{if $branch eq $t.module} class="active">{$t.display}{else}><a href="{$sitePrefix}/{if $t.module eq 'default'}index{else}{$t.module}{/if}/">{$t.display}</a>{/if}</li>
                    {/foreach}
                </ul>
            </div>
        </div>
            
        <div class="logo">
            <h1><a href="{$sitePrefix}/">Class<span class="red">Mate</span></a></h1>
            <p>It's that simple...</p>
        </div>
        
        <div class="searchBar">           
            <div class="search_field">
                <form method="get" action="{$sitePrefix}/index/search/">
                    <p><span class="grey">Search Example:</span> <span class="search">Excel</span>&nbsp;&nbsp; <input type="text" id="searchBox" name="search" class="search" /> <input type="submit" value="Search" class="button" /></p>
                </form>
            </div>
            <div class="newsletter">
                <p>Subscribe to our newsletter!</p>
            </div>
        </div>
        
        <div class="subheader">
            <p>        
            {foreach from=$subnav item=s name=subnav}
            {if $smarty.foreach.subnav.index != 0}
            &nbsp;|&nbsp;
            {/if}
            <a{if $subNavSelect eq $s.select} class="active"{/if} href="{$s.link}" target="{$s.target|empty_alt:'_self'}">{$s.display}</a> 
            {foreachelse}
            Welcome to {$appTitle}!
            {/foreach}
            </p>
            
        </div>
        
        {if $showNews}
        <div class="left">
            <div class="left_articles">
                <h2>{$title}</h2>
                <div id="actionTemplate">
                {$actionTemplate}
                </div>
            </div>
        </div>  
        <div class="right" id="newsDiv">
                        
            <p class="rt accToggler">New Excel Class</p>
            <div class="right_articles accElement">
                <p><img src="{$sitePrefix}/public/images/image.gif" alt="Image" title="Image" class="image" /><b>New Excel Class!</b><br />We are now teaching super advanced ultra cool classes or Excel!  <a href="#">Click here</a> to find a class now!.</p>
            </div>
            <p class="rt accToggler">Lorem Ipsom Dolor</p>
            <div class="right_articles accElement">    
                <p><img src="{$sitePrefix}/public/images/image.gif" alt="Image" title="Image" class="image" /><b>Lorem ipsum dolor sit amet</b><br />consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam <a href="#">erat volutpat</a>. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis <a href="#">nisl ut aliquip ex</a>.</p>
            </div>
            <p class="rt accToggler">Consectetuer</p>
            <div class="right_articles accElement">
                <p><img src="{$sitePrefix}/public/images/image.gif" alt="Image" title="Image" class="image" /><b>Lorem ipsum dolor sit amet</b><br />consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam <a href="#">erat volutpat</a>. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis <a href="#">nisl ut aliquip ex</a>.<br />consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam <a href="#">erat volutpat</a>. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis <a href="#">nisl ut aliquip ex</a>.</p>
            </div>
        </div>  
        {else}
        <div class="left full">
            <div class="left_articles">
                {if !$hideTitle}
                <h2>{$title}</h2>
                {/if}
                <div id="actionTemplate">
                {$actionTemplate}
                </div>
            </div>
        </div>         
        {/if}        

		<div id="oitocfooter">
		    <div class="left">
		        <a href="http://framework.zend.com" target="_blank"><img align="left" src="http://framework.zend.com/images/PoweredBy_ZF_4LightBG.png" width="127" height="25" alt="Powered By Zend Framework" /></a>
			
		    </div>
		    <div class="right">
		        <a href="http://itdapps.ncsu.edu/"><img src="http://itdapps5.ncsu.edu/htdocs/public/images/minilogo.png" width="128" height="21" alt="OIT - Outreach Technologies" /></a>
		    </div>
            <a href="#">RSS Feed</a> | <a href="#">Contact</a> | <a href="{$sitePrefix}/bug/add/">File Bug Report</a><br />
            &copy; {$copyrightDate} North Carolina State University
		</div>        

    </div>
</body>
</html>