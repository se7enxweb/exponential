<?php

$ini = eZINI::instance( 'site.ini' );
$xrowsitemapINI = eZINI::instance( 'xrowsitemap.ini' );

//getting custom set site access or default access
if ( $xrowsitemapINI->hasVariable( 'MobileSitemapSettings', 'AvailableSiteAccessList' ) )
{
    $siteAccessArray = $xrowsitemapINI->variable( 'MobileSitemapSettings', 'AvailableSiteAccessList' );
}
else
{
    $siteAccessArray = array(
        $ini->variable( 'SiteSettings', 'DefaultAccess' )
    );
}

if ( $xrowsitemapINI->variable( 'Settings', 'MobileSitemap' ) == 'enabled' )
{
    if ( ! $isQuiet )
    {
        $cli->output( "Generating Mobile Sitemaps...\n" );
    }
    xrowSitemapTools::siteaccessCallFunction( $siteAccessArray, 'xrowSitemapTools::createMobileSitemap' );
}

xrowSitemapTools::ping();
