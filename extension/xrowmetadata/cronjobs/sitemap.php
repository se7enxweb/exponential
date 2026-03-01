<?php

$ini = eZINI::instance( 'site.ini' );
$xrowsitemapINI = eZINI::instance( 'xrowsitemap.ini' );
$hostArrayWares = array();

//getting custom set site access or default access
if ( $xrowsitemapINI->hasVariable( 'SitemapSettings', 'AvailableSiteAccessList' ) )
{
    $siteAccessArray = $xrowsitemapINI->variable( 'SitemapSettings', 'AvailableSiteAccessList' );
}
else
{
    $siteAccessArray = array(
        $ini->variable( 'SiteSettings', 'DefaultAccess' )
    );
}

if ( $xrowsitemapINI->hasVariable( 'Settings', 'ExcludeSiteaccess' ) )
{
    $exclude_array = $xrowsitemapINI->variable( 'Settings', 'ExcludeSiteaccess' );
    foreach ($siteAccessArray as $key=>$siteaccess_item)
    {
        if (in_array($siteaccess_item, $exclude_array))
        {
            unset($siteAccessArray[$key]);
        }
    }
}

if ( $xrowsitemapINI->hasVariable( 'SitemapSettings', 'HostUriMatchMapItems' ) )
{
    $hostArrays = $xrowsitemapINI->variable( 'SitemapSettings', 'HostUriMatchMapItems' );
}

foreach($hostArrays as $hostArray)
{
    $hostArrayTemp=explode(";",$hostArray);
    if(!(in_array($hostArrayTemp[0],$hostArrayWares)))
    {
        array_push($hostArrayWares,$hostArrayTemp[0]);
    }
}

if ( $xrowsitemapINI->variable( 'Settings', 'Sitemap' ) == 'enabled' )
{
    if ( ! $isQuiet )
    {
        $cli->output( "Generating Regular Sitemaps...\n" );
    }
    xrowSitemapTools::siteaccessCallFunction( $siteAccessArray, 'xrowSitemapTools::createSitemap' );
}

foreach($hostArrayWares as $hostArrayWare)
{
    $cli->output( "Submit Sitemap $hostArrayWare to Google and Bing.....\n" );
    xrowSitemapTools::ping($hostArrayWare);
}
