<p align="center">
    <input type="button" id="previousButton" value="&lsaquo; Previous" />
    <input type="button" id="viewWeekButton" value="Week" />
    <input type="button" id="viewMonthButton" value="Month" />
    <input type="button" id="nextButton" value="Next &rsaquo;" />
    <input type="hidden" id="currentView" value="month" />
    <input type="hidden" id="basetime" value="{$baseTime}" />
    <img id="loading" src="{$sitePrefix}/public/images/loading.gif" width="16" height="16" />
</p>

<textarea style="display: none;" id="popupDetails">
&lt;div class="infobox"&gt;
    &lt;div class="dis"&gt;
        &lt;h3 class="curve"&gt;
        &lt;div class="popupDetails"&gt;
          &lt;h2&gt;%title%&lt;/h2&gt;
          <!--&lt;img src="%thumbnail%" width="88" height="140" align="left"&gt;-->
          &lt;div style="float: left; width: 250px; overflow: auto;"&gt;
              &lt;p&gt;&lt;b&gt;&lt;a href="{$sitePrefix}/workshop/index/details?workshopId=%workshopId%"&gt;Click Here For More Info&lt;/a&gt;&lt;/b&gt;&lt;/p&gt;
              &lt;p&gt;&lt;b&gt;Time:&lt;/b&gt; %time%&lt;/p&gt;
              &lt;p&gt;&lt;b&gt;Description:&lt;/b&gt; %description%&lt;/p&gt;
          &lt;/div&gt;
        &lt;/div&gt;
        &lt;/h3&gt;
    &lt;div class="innerC"&gt;&lt;/div&gt;
    &lt;/div&gt;
    &lt;/div&gt;
</textarea>

<div id="workshopSearchResults">
    <div id="searchResultsTitle">Search Results</div>
    <div id="searchResultsContentWrapper">
        <div id="searchResultsContent">
            <input type="hidden" id="week" value="{$week}" />
            <input type="hidden" id="year" value="{$year}" />
            <input type="hidden" id="month" value="{$month}" />
        </div>
    </div>
</div>