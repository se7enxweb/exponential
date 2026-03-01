<?php

$xrowsitemapINI = eZINI::instance( 'xrowsitemap.ini' );
if ( $xrowsitemapINI->hasVariable( 'SitemapSettings', 'RobotsPath' ) )
{
    $robotspath = $xrowsitemapINI->variable( 'SitemapSettings', 'RobotsPath' );
}
else
{
    $robotspath = 'robots.txt';
}
$content = "Sitemap: https://" . $_SERVER['HTTP_HOST'] . "/sitemaps/index\n";

if ( file_exists( $robotspath ) )
{
    $content .= file_get_contents( $robotspath );
}
else
{
    $content .= '';
}

// Set header settings
header( 'Content-Type: text/plain; charset=UTF-8' );
header( 'Content-Length: ' . strlen( $content ) );
header( 'X-Powered-By: eZ Publish' );

while ( @ob_end_clean() );

echo $content;

eZExecution::cleanExit();
