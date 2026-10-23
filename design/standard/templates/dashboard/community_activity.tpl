{def $forumsRssUrl='https://share.exponential.earth/rss/feed/all_forums'
     $versionString=fetch( 'setup', 'version' )}
<div class="community-block-title">
    <h2>{'What\'s happening in the eZ Community'|i18n( 'design/admin/dashboard/community' )}</h2>
    <span class="first"><a href="https://share.exponential.earth/get-involved/exchange#RSS?{concat( 'utm_content=eZ+Publish+Community+Project+', $versionString , '&utm_source=eZ+Publish+Community+Project+Dashboard&utm_medium=eZ+Publish+Community+Project+Dashboard&utm_campaign=eZ+Publish+Community+Project+Dashboard' )}"><img width="16px" height="16px" src={"rss-icon.gif"|ezimage} /></a></span>
    <span><a href="https://share.exponential.earth/get-involved/exchange#twitter?{concat( 'utm_content=eZ+Publish+Community+Project+', $versionString , '&utm_source=eZ+Publish+Community+Project+Dashboard&utm_medium=eZ+Publish+Community+Project+Dashboard&utm_campaign=eZ+Publish+Community+Project+Dashboard' )}"><img width="16px" height="16px" src={"twitter-icon.png"|ezimage} /></a></span>
    <span><a href="https://share.exponential.earth/get-involved/exchange#googleplus?{concat( 'utm_content=eZ+Publish+Community+Project+', $versionString , '&utm_source=eZ+Publish+Community+Project+Dashboard&utm_medium=eZ+Publish+Community+Project+Dashboard&utm_campaign=eZ+Publish+Community+Project+Dashboard' )}"><img width="16px" height="16px" src={"googleplus-icon.png"|ezimage} /></a></span>
</div>
<div class="break"></div>
<div class="community-activity">
    <div class="loading">
        {'Loading the latest forum posts...'|i18n( 'design/admin/dashboard/community' )}
        <img src={'2/loader.gif'|ezimage} />
    </div>
    <div id="feed-elements">


    </div>
</div>

{* The feed is fetched by javascript/dashboard_community_activity.js (a file of its own, for a Content Security
   Policy without 'unsafe-inline'), with the values of #community-activity-config *}
<div id="community-activity-config" hidden
     data-number-of-items="{$block.number_of_items|wash}"
     data-feed-url="{$forumsRssUrl|wash}"
     data-version="{$versionString|wash}"
     data-failed-text="{'The latest forum posts could not be loaded.'|i18n( 'design/admin/dashboard/community' )|wash}"></div>
{ezscript( array( 'dashboard_community_activity.js' ) )}
