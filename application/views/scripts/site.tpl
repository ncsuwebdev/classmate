<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>{$appTitle} - {$title}</title>
<link rel="stylesheet" type="text/css" media="all" href="{$sitePrefix}/public/css/site.css" />
<script type="text/javascript" src="{$sitePrefix}/public/scripts/mootools.v1.11.js"></script>
{foreach from=$javascript item=script}
<script type="text/javascript" src="{$sitePrefix}/public/scripts/{$script}"></script>
{/foreach}
</head>
<body>
    <div class="content">
        <div class="header_right">
            <div class="top_info">
                <div class="top_info_right">
                    <p>

		            {if $loggedInUser == ''} 
		            <b>You are not Logged in!</b> <a href="{$sitePrefix}/login/">Log in</a>  or <a href="#">register</a> to take classes!
		            {else}
		            Logged in as {$loggedInUser}  &nbsp;|&nbsp;
		            <a href="{$sitePrefix}/profile/index/edit/">Edit Profile</a>  &nbsp;|&nbsp;
		            <a href="{$sitePrefix}/login/index/changePassword/">Change Password</a> &nbsp;|&nbsp;
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
            <h1><a href="#">Class<span class="red">Mate</span></a></h1>
            <p>Do it to it...</p>
        </div>
        
        <div class="search_field">
            <form method="post" action="?">
                <p><span class="grey">Search Example:</span> <span class="search">Excel</span>&nbsp;&nbsp; <input type="text" name="search" class="search" /> <input type="submit" value="Search" class="button" /></p>
            </form>
        </div>
        
        <div class="newsletter">
            <p>Subscribe for Newsletter!</p>
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
                <h2><a href="#">{$title}</a></h2>
                <div id="actionTemplate">
                {$actionTemplate}
                </div>
            </div>
        </div>  
        <div class="right">
                        
            <div class="rt"></div>
            <div class="right_articles">
                <p><img src="{$sitePrefix}/public/images/image.gif" alt="Image" title="Image" class="image" /><b>New Excel Class!</b><br />We are now teaching super advanced ultra cool classes or Excel!  <a href="#">Click here</a> to find a class now!.</p>
            </div>
            <div class="rt"></div>
            <div class="right_articles">
                <p><img src="images/image.gif" alt="Image" title="Image" class="image" /><b>Lorem ipsum dolor sit amet</b><br />consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam <a href="#">erat volutpat</a>. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis <a href="#">nisl ut aliquip ex</a>.</p>
            </div>
            <div class="rt"></div>
            <div class="right_articles">
                <p><img src="images/image.gif" alt="Image" title="Image" class="image" /><b>Lorem ipsum dolor sit amet</b><br />consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam <a href="#">erat volutpat</a>. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis <a href="#">nisl ut aliquip ex</a>.</p>
            </div>
        </div>  
        {else}
        <div class="left full">
            <div class="left_articles">
                <h2><a href="#">{$title}</a></h2>
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