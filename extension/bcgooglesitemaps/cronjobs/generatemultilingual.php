<?php
/**
 * File containing the generatemultilingual.php cronjob
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2008 all2e GmbH. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package bcgooglesitemaps
 */

/**
 * Add a starting timing point tracking script execution time
 */
$srcStartTime = microtime( true );

/**
 * File contains an eZ Publish cronjob part (script) to automatically
 * fetch all the content of the eZ Publish siteaccess database content
 * tree content nodes, transform the nodes fetched into an xml based
 * sitemap and writes the sitemap to disk.
 *
 * Sitemap is based on custom extension settings (array of siteaccess name strings),
 * this script iterate over each siteaccess building an array of site languages
 * (site locale and site url), then iterating over site language information fetch
 * the root node of the content tree (settings based) in each language and then all
 * child nodes in each language. Next iterating over an array of all nodes in all
 * locales, for each node, generate the sitemap xml representing that node.
 *
 * Finally a valid xml sitemap file is written out to disk (settings based var/ dir root by default)
 *
 * File containing the bcgooglesitemaps siteaccess sitemap generator cronjob part
 *
 */

/**
 * BC: In testing multi-lingual sites with single language siteaccess installations
 * we have found it is tipically best for the cronjob mode to be disabled. Otherwise
 * content in all possible languages is returned in subtree results.
 */
eZContentLanguage::clearCronjobMode();

/**
 * Get a reference to eZINI. append.php will be added automatically.
 */
$ini = eZINI::instance( 'site.ini' );
$bcgooglesitemapsINI = eZINI::instance( 'bcgooglesitemaps.ini' );

/**
 * BC: Testing for settings required by the script and defining other variables required by the script
 */
if ( $bcgooglesitemapsINI->hasVariable( 'BCGoogleSitemapSettings', 'SitemapRootNodeID' ) &&
     $bcgooglesitemapsINI->hasVariable( 'BCGoogleSitemapSettings', 'Path' ) &&
     $bcgooglesitemapsINI->hasVariable( 'BCGoogleSitemapSettings', 'Filename' ) &&
     $bcgooglesitemapsINI->hasVariable( 'BCGoogleSitemapSettings', 'Filesuffix' ) &&
     $bcgooglesitemapsINI->hasVariable( 'BCGoogleSitemapSettings', 'Protocol' ) &&
     $bcgooglesitemapsINI->hasVariable( 'Classes', 'ClassFilterType' ) &&
     $bcgooglesitemapsINI->hasVariable( 'Classes', 'ClassFilterArray' ) &&
     $ini->hasVariable( 'SiteSettings', 'SiteURL' ) &&
     $ini->hasVariable( 'FileSettings', 'VarDir' )
     )
{
    /**
     * BC: Define root content tree node ID
     */
    $sitemapRootNodeID = $bcgooglesitemapsINI->variable( 'BCGoogleSitemapSettings', 'SitemapRootNodeID' );

    /**
     * BC: Define the sitemap basename and output file suffix
     */
    $sitemapName = $bcgooglesitemapsINI->variable( 'BCGoogleSitemapSettings', 'Filename' );
    $sitemapSuffix = $bcgooglesitemapsINI->variable( 'BCGoogleSitemapSettings', 'Filesuffix' );

    /**
     * BC: Define the sitemap base path, output file directory path. Path to directory to write out generated sitemaps
     */
    if( $bcgooglesitemapsINI->variable( 'BCGoogleSitemapSettings', 'Path' ) != false )
    {
        $sitemapPath = $bcgooglesitemapsINI->variable( 'BCGoogleSitemapSettings', 'Path' );
    }
    else
    {
        $sitemapPath = $ini->variable( 'FileSettings', 'VarDir' );
    }

    /**
     * BC: Define the sitemap link protocol. Default http
     */
    $sitemapLinkProtocol = $bcgooglesitemapsINI->variable( 'BCGoogleSitemapSettings', 'Protocol' );

    /**
     * BC: Define content tree node fetch class filter. Array of class identifiers and whether to include or exclude them.
     */
    $classFilterType = $bcgooglesitemapsINI->variable( 'Classes', 'ClassFilterType' );
    $classFilterArray = $bcgooglesitemapsINI->variable( 'Classes', 'ClassFilterArray' );
}
else
{
    /**
     * BC: Alert user of missing ini settings variables
     */
    $cli->output( 'Missing INI Variables in configuration block GeneralSettings.' );
    return;
}

/**
 * BC: Fetch the array of siteaccess names (multi siteaccess; multi language)
 * which should be used to fetch content for the sitemap or the default
 * siteaccess name (one siteaccess; one language) when the custom settings are unavailable
 */
if( $bcgooglesitemapsINI->hasVariable( 'SiteAccessSettings', 'SiteAccessArray' ) )
{
    $siteAccessArray = $bcgooglesitemapsINI->variable( 'SiteAccessSettings', 'SiteAccessArray' );
}
else
{
    $siteAccessArray = array( $ini->variable( 'SiteSettings', 'DefaultAccess' ) );
}

/**
 * BC: Array to store all siteacces related information
 */
$siteaccesses = array();

/** Display of execution time **/

function executionTimeDisplay( $srcStartTime, $cli, $isQuiet )
{
    /** Add a stoping timing point tracking and calculating total script execution time **/
    $srcStopTime = microtime( true );
    $startTimeCalc = $srcStartTime;
    $stopTimeCalc = $srcStopTime;

    /** Calculate execution time **/
    $executionTime = $srcStopTime - $srcStartTime;
    list( $executionTimeInSeconds, $executionTimeInUSeconds ) = explode('.', $executionTime );
    $executionTimeInUSeconds = str_replace( "0.", ".", $executionTimeInUSeconds );
    $executionTimeInUSecondsRounded = round( $executionTimeInUSeconds, 2 );

    /** Get and set timezone setting for time calculation date format display **/
    $previousTimeZone = date_default_timezone_get();
    date_default_timezone_set( 'UTC' );

    /** Format calculated execution time **/
    $executionTimeFormattedInHours = date( "H \\h\\o\\u\\r\\s", $executionTimeInSeconds );
    $executionTimeFormattedInMinutes = date( "i \m\i\\n\u\\t\\e\\s", $executionTimeInSeconds );
    $executionTimeFormattedInSeconds = date( "s \\s\\e\\c\\o\\n\\d\\s", $executionTimeInSeconds );

    /** Restore previous timezone setting **/
    date_default_timezone_set( $previousTimeZone );

    /** Trim formatted calculated execution time **/
    if( $executionTimeFormattedInHours > 0 )
    {
        $executionTimeFormattedInHours = ltrim( $executionTimeFormattedInHours, 0 );
    }
    if( $executionTimeFormattedInMinutes > 0 )
    {
        $executionTimeFormattedInMinutes = ltrim( $executionTimeFormattedInMinutes, 0 );
    }
    if( $executionTimeFormattedInSeconds > 0 )
    {
        $executionTimeFormattedInSeconds = ltrim( $executionTimeFormattedInSeconds, 0 );
    }

    /** Alert the user to how long the script execution took place **/
    if ( !$isQuiet )
    {
        if( $executionTimeInSeconds <= 60 )
        {
            $cli->output( "This script execution completed in " . $executionTimeFormattedInSeconds . ".\n" );
        }
        elseif( $executionTimeInSeconds < 3600 )
        {
            $cli->output( "This script execution completed in " . $executionTimeFormattedInMinutes . ' and ' . $executionTimeFormattedInSeconds . " \n" );
        }
        elseif( $executionTimeInSeconds >= 3600 )
        {
            $cli->output( "This script execution completed in " . $executionTimeFormattedInHours . ', ' . $executionTimeFormattedInMinutes . ' and ' . $executionTimeFormattedInSeconds . " \n" );
        }
    }
}

/**
 * BC: Iterate over each siteaccess and collect siteaccess local settings (site languages)
 */
foreach( $siteAccessArray as $siteAccess )
{
    /**
     * BC: Fetch siteaccess settings and locale
     */
    $siteAccessINI = eZINI::instance( 'site.ini.append.php', 'settings/siteaccess/' . $siteAccess  );
    $siteacessLocale = $siteAccessINI->variable( 'RegionalSettings', 'Locale' );

    /**
     * BC: Fetch siteaccess site url
     */
    $siteURL = $siteAccessINI->variable( 'SiteSettings', 'SiteURL' );

    if( substr( $siteURL, -1) != '/' ) {
        $siteURL .= '/';
    }

    /**
     * BC: Fetch siteaccess site languages list and build array of siteaccess specific settings
     */
    if ( $siteAccessINI->hasVariable( 'RegionalSettings', 'SiteLanguageList' ) )
    {
        $siteaccessLanguages = $siteAccessINI->variable( 'RegionalSettings', 'SiteLanguageList' );

        if( !in_array( $siteacessLocale, $siteaccessLanguages ) )
        {
            array_push( $siteacessLocale, $siteaccessLanguages );
        }

        array_push( $siteaccesses, array( 'siteaccess' => $siteAccess,
                                          'locale' => $siteacessLocale,
                                          'siteaccessLanguages' => $siteaccessLanguages,
                                          'siteurl' => $siteURL ) );
    }
    else
    {
        array_push( $siteaccesses, array( 'siteaccess' => $siteAccess,
                                          'locale' => $siteacessLocale,
                                          'siteaccessLanguages' => array( $siteacessLocale ),
                                          'siteurl' => $siteURL ) );
    }
}

/**
 * Prepare new xml document
 */
$xmlRoot = "urlset";
$xmlNode = "url";

/**
 * Define XML Child Nodes
 */
$xmlSubNodes = array( 'loc', 'lastmod', 'changefreq', 'priority' );

/**
 * Create the DOMnode
 */
$dom = new DOMDocument( '1.0', 'UTF-8' );

/**
 * Create DOM-Root (urlset)
 */
$root = $dom->createElement( $xmlRoot );
$root->setAttribute( 'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9' );
$root = $dom->appendChild( $root );

/**
 * BC: Iterate over each siteaccess locals
 */
foreach( $siteaccesses as $key => $siteaccess )
{
    /**
     * BC: Alert user of the generation of the sitemap for the current language siteacces (name)
     */
    if ( !$isQuiet )
        $cli->output( "Generating sitemap content for siteaccess " . $siteaccess["siteaccess"] . " with locale " . implode( ', ', $siteaccess['siteaccessLanguages'] ) . " \n" );

    /**
     * Get the Sitemap's root node
     */
    $rootNode = eZContentObjectTreeNode::fetch( $sitemapRootNodeID, $siteaccess['locale'] );
    $siteaccessNodesArray[] = $rootNode;

    /**
     * Test for content object fetch (above) failure to return a valid object.
     * Alert the user and terminate execution of script
     */
    if ( !is_object( $rootNode ) )
    {
        $cli->output( "Invalid SitemapRootNodeID in configuration block GeneralSettings; OR SitemapRootNodeID does not not have language translation for current siteaccess language.\n" );
        return;
    }

    /**
     * Change siteaccess
     */
    eZSiteAccess::change( array("name" => $siteaccess["siteaccess"], "type" => eZSiteAccess::TYPE_URI ) );

    /**
     * Fetch the content tree nodes (children) of the above root node (in a given locale)
     */
    $siteaccessSubtreeNodesArray = $rootNode->subTree( array( 'Language' => $siteaccess['siteaccessLanguages'],
                                                              'ClassFilterType' => $classFilterType,
                                                              'ClassFilterArray' => $classFilterArray ) );

    $siteaccessNodesArray = array_merge( $siteaccessNodesArray, $siteaccessSubtreeNodesArray );

    /**
     * BC: Generate XML sitemap compatible data file contents
     * based on array of arrays containing content tree nodes
     * in each language for a given sitaccess or array of siteaccesses
    */

    /**
     * BC: Iterate over siteaccess language nodes
     */
    foreach( $siteaccessNodesArray as $subTreeNode )
    {
        /**
         * BC: Site node url alias (calculation)
         */
        $siteURL = $siteaccesses[ $key ]['siteurl'];
        $urlAlias = $sitemapLinkProtocol . '://' . $siteURL . $subTreeNode->attribute( 'url_alias' );

        /**
         * BC: Fetch node's object
         */
        $object = $subTreeNode->object();

        /**
         * $depth = $subTreeNode->attribute( 'depth' );
         */

        /**
         * BC: Fetch object's modified date
         */
        $modified = date( "c" , $object->attribute( 'modified' ) );

        /**
         * Create new url element
         */
        $node = $dom->createElement( $xmlNode );

        /**
         * Append to root node
         */
        $node = $root->appendChild( $node );

        /**
         * Create new page url subnode
         */
        $subNode = $dom->createElement( $xmlSubNodes[0] );
        $subNode = $node->appendChild( $subNode );

        /**
         * Set text node with data
         */
        $date = $dom->createTextNode( $urlAlias );
        $date = $subNode->appendChild( $date );

        /**
         * BC: Create 'modified' subnode and append child data to xml document being generated
         */
        $subNode = $dom->createElement( $xmlSubNodes[1] );
        $subNode = $node->appendChild( $subNode );

        /**
         * BC: Create 'lastmod' node and append child data to the xml document being generated
         */
        $lastmod = $dom->createTextNode( $modified );
        $lastmod = $subNode->appendChild( $lastmod );
    }
} // BC: End foreach( $siteaccesses as $siteaccess )


/**
 * BC: Build output xml data file name
 */
$xmlDataFile = $sitemapPath . '/' . $sitemapName . $sitemapSuffix;

/**
 * BC: Write sitemap xml file to disk
 */
$dom->save( $xmlDataFile );
$fileHandler = eZClusterFileHandler::instance();
$fileHandler->fileStore( $xmlDataFile, 'sitemap', true, 'text/xml' );

/**
 * BC: Alert user of script completion
 */
if ( !$isQuiet )
{
    /**
     * @TODO: Extend message displayed to include more details of the content and context of the results written to disk
     */
    $cli->output( "Sitemap for site has been generated. See: $xmlDataFile\n" );
}

/** Call for display of execution time **/

executionTimeDisplay( $srcStartTime, $cli, $isQuiet );

/**
 * Terminate execution and exit system normally
 */

?>