<?php

$ini = eZINI::instance( 'site.ini' );
$xrowsitemapINI = eZINI::instance( 'xrowsitemap.ini' );

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

// adding the mobile sitemap site accesses
if ( $xrowsitemapINI->hasVariable( 'MobileSitemapSettings', 'AvailableSiteAccessList' ) )
{
    $siteAccessArray = array_merge( $siteAccessArray, $xrowsitemapINI->variable( 'MobileSitemapSettings', 'AvailableSiteAccessList' ) );
}

$Module = $Params['Module'];
$access = $GLOBALS['eZCurrentAccess']['name'];

if ( is_array( $siteAccessArray ) && count( $siteAccessArray ) > 0 )
{
    if ( ! in_array( $access, $siteAccessArray ) )
    {
        return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

$index = new xrowSitemapIndex();

$dirArray = array(
    eZSys::storageDirectory() . '/sitemap/' . xrowSitemapTools::domain() ,
    eZSys::storageDirectory() . '/sitemap/' . xrowSitemapTools::domain() . '/' . xrowSitemapTools::FILETYP_ARCHIVE ,
    eZSys::storageDirectory() . '/sitemap/' . xrowSitemapTools::domain() . '/' . xrowSitemapTools::FILETYP_STANDARD
);

foreach ( $dirArray as $item )
{
    addFiles( $index, $item, $dirArray );
}

function addFiles( &$index, $dirname, $dirArray )
{
    try
    {
        $dir = new eZClusterDirectoryIterator( $dirname );
    }
    catch( Exception $e )
    {
        if( $e instanceof UnexpectedValueException )
        {
            eZDebug::writeDebug( "Cannot add $dirname to the sitemaps index because it does not exist" );
            return;
        }
    }

    foreach ( $dir as $file )
    {
        $f = eZClusterFileHandler::instance( $file->name() );
        if ( $f->exists() )
        {
            $exists = true;
            break;
        }
    }
    if ( false != $exists )
    {
        foreach ( $dir as $file )
        {

            if ( in_array( $file->name(), $dirArray ) )
            {
                continue;
            }
            if ( $file->size() > 50 )
            {
                $date = new xrowSitemapItemModified($file->mtime());
                $loc = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $file->name();
                if ( !in_array( $loc, $GLOBALS['loc'] ) )
                {
                    $GLOBALS['loc'][] = $loc;
                    $index->add( $loc, array(
                        $date
                    ) );
                }
            }
        }
    }

}

// Append foreign Sitemaps
if ( $ini->hasVariable( 'Settings', 'AddSitemapIndex' ) )
{
    $urlList = $ini->variable( 'Settings', 'AddSitemapIndex' );
    foreach ( $urlList as $loc )
    {
        $index->add( $loc, array(
            $date
        ) );
    }
}
unset($GLOBALS['loc']);
$content = $index->saveXML();

// Set header settings
header( 'Content-Type: text/xml; charset=UTF-8' );
header( 'Content-Length: ' . strlen( $content ) );
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-cache, must-revalidate' );
header( 'Pragma: no-cache' );

while ( @ob_end_clean() );

echo $content;

eZExecution::cleanExit();
