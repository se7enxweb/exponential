<?php

class xrowSitemapTools
{
    /* max. amount of links in 1 sitemap */
    const MAX_PER_FILE = 49998;
    const DEFAULT_LIMIT = 250;
    const SITEDATA_ARCHIVE_KEY = 'xrowSitemapArchiveTimestamp';
    const FILETYP_STANDARD = 'standard';
    const FILETYP_ARCHIVE = 'archive';
    const FILETYP_NEWS = 'news';
    const FILETYP_MOBILE = 'mobile';

    public static $excludes = null;

    public static function changeAccess( array $access )
    {
        eZSiteAccess::load( $access );
        unset( $GLOBALS['eZContentObjectDefaultLanguage'] );
        eZContentLanguage::expireCache();
        eZContentObject::clearCache();
    }

    public static function siteaccessCallFunction( $siteaccesses = array(), $fnc = null )
    {
        $old_access = $GLOBALS['eZCurrentAccess'];
        $ini = eZINI::instance( 'site.ini' );
        foreach ( $siteaccesses as $siteaccess )
        {
            /* Change the siteaccess */
            self::changeAccess( array(
                "name" => $siteaccess ,
                "type" => eZSiteAccess::TYPE_URI
            ) );

            if( $ini->hasVariable( 'UserSettings', 'AnonymousUserID' ) )
            {
                $user_id = $ini->variable( 'UserSettings', 'AnonymousUserID' );
                $user = eZUser::fetch( $user_id );
                if ( $user instanceof eZUser )
                {
                    $user->loginCurrent();
                    try{
                        call_user_func( $fnc );
                    }catch( Exception $e ){
                        eZCLI::instance()->output( "Skipping access $siteaccess" );
                        continue;
                    }
                }else{
                    continue;
                }
            }
        }
        self::changeAccess( $old_access );
    }

    public static function ping( $hostname = null )
    {
        if ( $hostname === null )
        {
            $hostname = self::domain();
        }
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        // send a ping to google?
        if ( ( $ini->hasVariable( 'Settings', 'Ping' ) and $ini->variable( 'Settings', 'Ping' ) == 'true' ) or ! $ini->hasVariable( 'Settings', 'Ping' ) )
        {
            $uri = '/sitemaps/index';
            eZURI::transformURI( $uri );
            $link = 'https://' . $hostname . $uri;
            // google
            $url = "https://www.google.com/webmasters/tools/ping?sitemap=" . $link;
            file_get_contents( $url );
            // bing
            $url = "https://www.bing.com/webmaster/ping.aspx?siteMap=" . $link;
            file_get_contents( $url );
        }
    }

    public static function language()
    {
        $specificINI = eZINI::instance( 'site.ini' );
        $localestr = $specificINI->variable( 'RegionalSettings', 'ContentObjectLocale' );
        $local = new eZLocale( $localestr );
        return $local->LanguageCode;
    }

    public static function domain()
    {
        $ini = eZINI::instance( 'site.ini' );
        $domain = preg_split( '/[\/\:]/i', $ini->variable( 'SiteSettings', 'SiteURL' ), 2 );
        if ( is_array( $domain ) )
        {
            $domain = $domain[0];
            $domain2 = preg_split( '/[\/]/i', $domain, 2 );
            if ( is_array( $domain2 ) )
            {
                $domain = $domain2[0];
            }

        }
        else
        {
            $domain = preg_split( '/[\/]/i', $ini->variable( 'SiteSettings', 'SiteURL' ), 2 );
            if ( is_array( $domain ) )
            {
                $domain = $domain[0];
            }
            else
            {
                $domain = $siteURL;
            }
        }
        return $domain;
    }

    public static function getNewsConverter()
    {
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        if ( $ini->hasVariable( 'Settings', 'NewsConverter' ) )
        {
            $converter = eZExtension::getHandlerClass( new ezpExtensionOptions( array(
                'iniFile' => 'xrowsitemap.ini' ,
                'iniSection' => 'Settings' ,
                'iniVariable' => 'NewsConverter'
            ) ) );
        }
        if ( ! $converter )
        {
            $converter = new xrowSitemapConverter();
        }
        return $converter;
    }

    public static function getVideoConverter()
    {
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        if ( $ini->hasVariable( 'Settings', 'VideoConverter' ) )
        {
            $converter = eZExtension::getHandlerClass( new ezpExtensionOptions( array(
                'iniFile' => 'xrowsitemap.ini' ,
                'iniSection' => 'Settings' ,
                'iniVariable' => 'VideoConverter'
            ) ) );
        }
        if ( ! $converter )
        {
            $converter = new xrowSitemapConverter();
        }
        return $converter;
    }

    public static function getImageConverter()
    {
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        if ( $ini->hasVariable( 'Settings', 'ImageConverter' ) )
        {
            $converter = eZExtension::getHandlerClass( new ezpExtensionOptions( array(
                'iniFile' => 'xrowsitemap.ini' ,
                'iniSection' => 'Settings' ,
                'iniVariable' => 'ImageConverter'
            ) ) );
        }
        if ( ! $converter )
        {
            $converter = new xrowSitemapConverter();
        }
        return $converter;
    }

    public static function fetchImages( eZContentObjectTreeNode $node )
    {

        $images = array();
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        $params = array(
            'Limit' => 999 ,  #Google doesn`t allow more as 1000
            'ClassFilterType' => 'include' ,
            'ClassFilterArray' => $ini->variable( 'SitemapSettings', 'ImageClasses' )
        );

        $nodeArray = $node->subTree( $params );
        foreach ( $nodeArray as $imageNode )
        {
            $imageadd = self::getImageConverter()->addImage( $imageNode );
            if ( ! empty( $imageadd ) )
            {
                $images = array_merge( $images, $imageadd );
            }
        }

        return $images;
    }

    public static function excludeNode( $node )
    {
        if ( self::$excludes === null )
        {
            $ini = eZINI::instance( 'xrowsitemap.ini' );
            if ( $ini->hasVariable( 'SitemapSettings', 'ExcludeNodes' ) and $ini->hasVariable( 'SitemapSettings', 'ExcludeNodes' ) )
            {
                self::$excludes = $ini->variable( 'SitemapSettings', 'ExcludeNodes' );
            }
            else
            {
                self::$excludes = array();
            }
        }

        if ( ! empty( self::$excludes ) )
        {
            $result = array();
            $result = array_intersect( self::$excludes, $node->pathArray() );
            if ( count( $result ) > 0 )
            {
                return true;
            }
        }
        return false;
    }

    public static function addNode( xrowSitemap $sitemap, eZContentObjectTreeNode $node )
    {
        $site_ini = eZINI::instance( 'site.ini' );
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        if ( self::excludeNode( $node ) )
        {
            return false;
        }
        $extensions = array();

        $meta = xrowMetaDataFunctions::fetchByNode( $node );

        if ( $meta and $meta->sitemap_use == '0' )
        {
            return false;
        }
        elseif ( $meta === false and $ini->variable( 'Settings', 'AlwaysAdd' ) == 'disabled' )
        {
            return false;
        }

        /*if ( $ini->hasVariable( 'ExtraAttributeFilter_1', 'Value' ) )
        {
            $offlineNummer = $ini->variable( 'ExtraAttributeFilter_1', 'Value' );
            if(in_array($offlineNummer,$node->attribute('object')->attribute('state_id_array')))
            {
                return false;
            }
        }*/

        if ( $ini->hasVariable( 'SitemapSettings', 'GalleryClasses' ) and $node->attribute( 'parent' ) instanceof eZContentObjectTreeNode and in_array( $node->attribute( 'parent' )->attribute( 'class_identifier' ), $ini->variable( 'SitemapSettings', 'GalleryClasses' ) ) and in_array( $node->attribute( 'class_identifier' ), $ini->variable( 'SitemapSettings', 'ImageClasses' ) ) )
        {
            return false;
        }
        $extensions[] = new xrowSitemapItemModified( $node->attribute( 'modified_subnode' ) );

        $url = $node->attribute( 'url_alias' );

        // Remove PathPrefix if needed
        if ($ini->hasVariable( 'SitemapSettings', 'PathPrefix' ) )
        {
            $pathPrefixs = $ini->variable( 'SitemapSettings', 'PathPrefix' );
            foreach ( $pathPrefixs as $pathPrefix_item )
            {
                $pathPrefix_to_access = explode( ';', $pathPrefix_item );
                $accessname_item = $pathPrefix_to_access[1];
                $pathPrefix_temp = $pathPrefix_to_access[0];
                if ( $accessname_item == $GLOBALS['eZCurrentAccess']['name'] )
                {
                    $pathPrefix = $pathPrefix_temp;
                    break;
                } else {
                    $pathPrefix = "";
                }
            }
            if ( $url != '' && $pathPrefix != '' )
            {
                $pathPrefix .= '/';
                if ( strncmp( $url, $pathPrefix, strlen( $pathPrefix ) ) == 0 )
                {
                    $url = substr( $url, strlen( $pathPrefix ) );
                }
            }
        }
        $urlAlias = $url;

        //Ticket #10114 -  correction URLs
        if ( $ini->hasVariable( 'SitemapSettings', 'HostUriMatchMapItems' ) )
        {
            $host_uri = self::getUri($ini->variable( 'SitemapSettings', 'HostUriMatchMapItems' ));
        } elseif ( $site_ini->hasVariable( 'SiteAccessSettings', 'HostUriMatchMapItems' ))
        {
            $host_uri = self::getUri($site_ini->variable( 'SiteAccessSettings', 'HostUriMatchMapItems' ));
        }
        if ( $host_uri !== '') {
            $url = $host_uri . '/' . $url;
        }

        // $urlAlias is kept 'as is' to be able to generate these links
        eZURI::transformURI( $urlAlias, true );
        if ( $ini->hasVariable( 'SitemapSettings', 'CreateAlternateLink' ) )
        {
            if ( $ini->hasVariable( 'SitemapSettings', 'MobileSiteAccessName' ) &&
            $ini->hasVariable( 'SitemapSettings', 'MobileSiteAccessName' ) != '' )
            {
                $mobileSiteAccess = $ini->variable( 'SitemapSettings', 'MobileSiteAccessName' );
                $mobileURL = 'https://' . self::domain() . '/' . $mobileSiteAccess . $urlAlias;
            }
            if ( $ini->hasVariable( 'SitemapSettings', 'MobileDomainName' ) &&
            $ini->hasVariable( 'SitemapSettings', 'MobileDomainName' ) != '' )
            {
                $mobileDomain = $ini->variable( 'SitemapSettings', 'MobileDomainName' );
                $mobileURL = 'https://' . $mobileDomain . $urlAlias;
            }
            $extensions[] = new xrowSitemapItemAlternateLink( $mobileURL );
        }

        $transformURIMode = eZURI::getTransformURIMode();
        // force URL to be generated in 'full mode' if MatchOrder != uri
        if( $site_ini->variable( 'SiteAccessSettings', 'MatchOrder' ) != 'uri' )
        {
            $transformURIMode = 'full';
        }
        eZURI::transformURI( $url, true, $transformURIMode );

        if($transformURIMode == 'full')
        {
            $url = preg_replace("(^https?)", "https", $url );
        }

        // only the URI mode is fully compatible with this $url generation
        if( $site_ini->variable( 'SiteAccessSettings', 'MatchOrder' ) == 'uri' )
        {
            if ( $site_ini->variable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess' ) == 'enabled' or $ini->variable( 'Settings', 'HideSiteaccessAlways' ) == 'true' )
            {
                $url = 'https://' . self::domain() . $url;
            }
            else
            {
                $url = 'https://' . self::domain() . '/' . $GLOBALS['eZCurrentAccess']['name'] . $url;
            }
        }
        $url = htmlspecialchars_decode($url, ENT_QUOTES);
        if ( $ini->hasVariable( 'SitemapSettings', 'GalleryClasses' ) and in_array( $node->attribute( 'class_identifier' ), $ini->variable( 'SitemapSettings', 'GalleryClasses' ) ) )
        {
            $imageextensions = self::fetchImages( $node );
            if ( ! empty( $imageextensions ) )
            {
                $extensions = array_merge( $extensions, $imageextensions );
            }
        }
        if ( $ini->hasVariable( 'SitemapSettings', 'VideoClasses' ) and in_array( $node->attribute( 'class_identifier' ), $ini->variable( 'SitemapSettings', 'VideoClasses' ) ) )
        {
            $extensions[] = self::getVideoConverter()->addVideo( $node );
        }
        if ( $meta and $meta->change )
        {
            $extensions[] = new xrowSitemapItemFrequency( $meta->change );
        }

        if ( $meta and $meta->priority !== null )
        {
            $extensions[] = new xrowSitemapItemPriority( $meta->priority );
        }
        elseif ( self::addPriority() )
        {
            $rootDepth = self::rootNode()->attribute( 'depth' );
            $prio = 1 - ( ( $node->attribute( 'depth' ) - $rootDepth ) / 10 );
            if ( $prio > 0 )
            {
                $extensions[] = new xrowSitemapItemPriority( $prio );
            }
        }
        $sitemap->add( $url, $extensions );
    }
    public static $addPriority = null;

    public static function addPriority()
    {
        if ( self::$addPriority === null )
        {
            $ini = eZINI::instance( 'xrowsitemap.ini' );
            if ( $ini->hasVariable( 'Settings', 'AddPriorityToSubtree' ) and $ini->variable( 'Settings', 'AddPriorityToSubtree' ) == 'true' )
            {
                self::$addPriority = true;
            }
            elseif ( ! $ini->hasVariable( 'Settings', 'AddPriorityToSubtree' ) )
            {
                self::$addPriority = true;
            }
            else
            {
                self::$addPriority = false;
            }
        }
        return self::$addPriority;
    }

    static public function cleanDir( $dirname )
    {
        $dir = new eZClusterDirectoryIterator( $dirname );
        foreach ( $dir as $file )
        {
            echo "$file\n";
            if ( $file->exists() )
            {
                $file->delete();
            }
        }
        unset( $dir );
    }

    public static $rootNode;
    /**
     * Get the Sitemap's root node
     */
    public static function rootNode()
    {
        $node_id = eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
        if( self::$rootNode === null || $node_id != self::$rootNode )
        {
            $rootNode = eZContentObjectTreeNode::fetch( $node_id );
            if ( ! $rootNode instanceof eZContentObjectTreeNode )
            {
                throw new Exception( "Invalid RootNode ".$node_id." for Siteaccess " . $GLOBALS['eZCurrentAccess']['name'] . " \n" );
            }
            self::$rootNode = $rootNode;
        }
        return self::$rootNode;
    }

    public static function createArchiveSitemap()
    {
        self::createSitemap( true );
    }

    public static function createSitemap( $archive = false )
    {
        $cli = eZCLI::instance();

        $xrowsitemapINI = eZINI::instance( 'xrowsitemap.ini' );
        if ( ! $xrowsitemapINI->hasVariable( 'Settings', 'Archive' ) or $xrowsitemapINI->variable( 'Settings', 'Archive' ) != 'disabled' )
        {
            if( $xrowsitemapINI->hasVariable( 'Settings', 'ArchiveTimeShift' ) )
            {
                $offset = 3600 * 24 * (int)$xrowsitemapINI->variable( 'Settings', 'ArchiveTimeShift' );
            }
            else
            {
                $offset = 3600 * 24 * 30;
            }

            $time = eZSiteData::fetchByName( self::SITEDATA_ARCHIVE_KEY );
            if ( ! $time )
            {
                $row = array(
                    'name' => self::SITEDATA_ARCHIVE_KEY ,
                    'value' => time() - $offset
                );
                $time = new eZSiteData( $row );
                $time->store();
            }
            if ( $archive )
            {
                $time = eZSiteData::fetchByName( self::SITEDATA_ARCHIVE_KEY );
                $time->setAttribute( 'value', time() - $offset );
                $time->store();
            }
            $timestamp = $time->attribute( 'value' );
        }
        eZDebug::writeDebug( "Generating sitemap ...", __METHOD__ );
        if ( ! $cli->isQuiet() )
        {
            $cli->output( "Generating sitemap for siteaccess " . $GLOBALS['eZCurrentAccess']['name'] . " \n" );
        }

        if ( $xrowsitemapINI->hasVariable( 'SitemapSettings', 'ClassFilterType' ) and $xrowsitemapINI->hasVariable( 'SitemapSettings', 'ClassFilterArray' ) )
        {
            $params2 = array(
                'ClassFilterType' => $xrowsitemapINI->variable( 'SitemapSettings', 'ClassFilterType' ) ,
                'ClassFilterArray' => $xrowsitemapINI->variable( 'SitemapSettings', 'ClassFilterArray' )
            );
        }
        $max = self::MAX_PER_FILE;
        if ( $xrowsitemapINI->hasVariable( 'SitemapSettings', 'LimitPerLoop' ) )
        {
            $limit = (int) $xrowsitemapINI->variable( 'SitemapSettings', 'LimitPerLoop' );
        }
        else
        {
            $limit = self::DEFAULT_LIMIT;
        }
        if( $limit > $max )
        {
            $cli->output( "LimitPerLoop can`t be larger as MAX_PER_FILE. LimitPerLoop is set to MAX_PER_FILE\n" );
            $limit = $max;
        }
        // Fetch the content tree
        $params = array(
            'SortBy' => array(
                array(
                    'depth' ,
                    true
                ) ,
                array(
                    'published' ,
                    true
                )
            )
        );

        if ( isset( $timestamp ) and $archive === true )
        {
            $params['AttributeFilter'] = array( array( 'published', '<=', $timestamp ) );
        }
        elseif( isset( $timestamp ) and $archive === false )
        {
            $params['AttributeFilter'] = array( array( 'published', '>', $timestamp ) );
        }

        if ( $xrowsitemapINI->hasVariable( 'SitemapSettings', 'ExtraAttributeFilter' ) )
        {
            $extra_attribute_filter = array();
            $extra_attribute_filter = $xrowsitemapINI->variable( 'SitemapSettings', 'ExtraAttributeFilter' );
        }

        if ( isset( $extra_attribute_filter ) )
        {
            foreach ( $extra_attribute_filter as $key => $extra_attribute_filter_item )
            {
                if ( $xrowsitemapINI->hasGroup( $extra_attribute_filter_item ) )
                {
                    $value = $xrowsitemapINI->variable( $extra_attribute_filter_item, 'Value' );
                    array_push( $params['AttributeFilter'], $value );
                }
            }
        }

        if( $xrowsitemapINI->hasVariable( 'SitemapSettings', 'MainNodeOnly' ) && $xrowsitemapINI->Variable( 'SitemapSettings', 'MainNodeOnly' ) == "true" )
        {
            $params['MainNodeOnly'] = true;
        }

        if ( isset( $params2 ) )
        {
            $params = array_merge( $params, $params2 );
        }
        $rootNode = self::rootNode();
        $subtreeCount = eZContentObjectTreeNode::subTreeCountByNodeID( $params, $rootNode->NodeID );

        if ( $subtreeCount <= 1 )
        {
            # could be an old installation with no fresh content!
            //throw new Exception( "No Items found under RootNode $rootNode->NodeID." );
            return;
        }

        $sitemap = new xrowSitemap();
        // Generate Sitemap
        self::addNode( $sitemap, $rootNode );

        $max = min( $max, $subtreeCount );
        $max_all = $max;
        $params['Limit'] = $limit;
        $params['Offset'] = 0;
        $counter = 1;
        $runs = ceil( $subtreeCount / $max_all );
        if ( ! $cli->isQuiet() )
        {
            $amount = $subtreeCount + 1; // for root node
            $cli->output( "Adding $amount nodes to the sitemap for RootNode $rootNode->NodeID." );
            $output = new ezcConsoleOutput();
        }

        // write XML Sitemap to file
        if ( $archive )
        {
            $filetyp = self::FILETYP_ARCHIVE;
        }
        else
        {
            $filetyp = self::FILETYP_STANDARD;
        }
        $dir = eZSys::storageDirectory() . '/sitemap/' . self::domain() . '/' . $filetyp;
        $cachedir = eZSys::cacheDirectory() . '/sitemap/' . self::domain() . '/' . $filetyp;
        $sitemapfiles = array();
        $tmpsitemapfiles = array();
        while ( $counter <= $runs )
        {
            eZDebug::writeDebug( 'Run ' . $counter . ' of ' . $runs . ' runs' );
            if ( ! $cli->isQuiet() )
            {
                $cli->output( 'Run ' . $counter . ' of ' . $runs . ' runs' );
                if ( $counter == 1 )
                {
                    $bar = new ezcConsoleProgressbar( $output, $max + 1 ); // for root node
                }
                else
                {
                    $bar = new ezcConsoleProgressbar( $output, $max );
                }
            }
            while ( $params['Offset'] < $max_all )
            {
                $nodeArray = eZContentObjectTreeNode::subTreeByNodeID( $params, $rootNode->NodeID );
                foreach ( $nodeArray as $subTreeNode )
                {
                    self::addNode( $sitemap, $subTreeNode );
                    if ( isset( $bar ) )
                    {
                        $bar->advance();
                    }
                }
                eZContentObject::clearCache();
                $params['Offset'] += $params['Limit'];
            }

            if ( ! is_dir( $dir ) )
            {
                mkdir( $dir, 0777, true );
            }
            if ( ! is_dir( $cachedir ) )
            {
                mkdir( $cachedir, 0777, true );
            }

            $filename = xrowSitemap::BASENAME . '_' . $GLOBALS['eZCurrentAccess']['name'] . '.' . xrowSitemap::SUFFIX;
            if ( $counter > 1 )
            {
                $filename = xrowSitemap::BASENAME . '_' . $GLOBALS['eZCurrentAccess']['name'] . '_' . $counter . '.' . xrowSitemap::SUFFIX;
            }
            $sitemapfiles[] = $dir . "/" . $filename;
            $tmpsitemapfiles[] = $cachedir . "/" . $filename;

            if( $sitemap->saveLocal( $cachedir . "/" . $filename ) === false )
            {
                throw new ezcBaseFileIoException(
                    $filename,
                    ezcBaseFileException::WRITE, 'Could not write data to cache file.' );
            }
            if ( ! $cli->isQuiet() )
            {
                $cli->output( "\n" );
                $cli->output( "Time: " . date( 'd.m.Y H:i:s' ) . ". Action: Sitemap $filename for siteaccess " . $GLOBALS['eZCurrentAccess']['name'] . " has been generated in $cachedir.\n" );
            }

            /**
             * @TODO How will this work with cluster?
            if ( function_exists( 'gzencode' ) and $xrowsitemapINI->variable( 'SitemapSettings', 'Gzip' ) == 'enabled' )
            {
                $content = file_get_contents( $filename );
                $content = gzencode( $content );
                file_put_contents( $filename . '.gz', $content );
                unlink( $filename );
                $filename .= '.gz';
            }
            **/
            $counter ++;
            $max_all += $max;
            $sitemap = new xrowSitemap();
        }

        $handler = eZClusterFileHandler::instance();
        if ( $handler instanceof eZDFSFileHandler or $handler instanceof eZDBFileHandler )
        {
            self::cleanDir($cachedir);
        }
        //move all from cache to cluster filesystem
        foreach( $sitemapfiles as $key => $sitemapfile )
        {
            $file = eZClusterFileHandler::instance( $sitemapfile );
            $file->storeContents( file_get_contents( $tmpsitemapfiles[$key] ), 'sitemap', 'text/xml' );
            if ( ! $cli->isQuiet() )
            {
                $cli->output( "\n" );
                $cli->output( "Time: " . date( 'd.m.Y H:i:s' ) . ". Action: Sitemap $filename for siteaccess " . $GLOBALS['eZCurrentAccess']['name'] . " has been moved to $dir.\n" );
            }
            if ( $handler instanceof eZDFSFileHandler or $handler instanceof eZDBFileHandler )
            {
                unlink( $tmpsitemapfiles[$key] );
            }
        }
        if ( $handler instanceof eZFSFileHandler )
        {
            self::cleanDir($cachedir);
        }
    }

    public static function createNewsSitemap()
    {
        eZDebug::writeDebug( "Generating news sitemap ...", __METHOD__ );

        $cli = eZCLI::instance();
        if ( ! $cli->isQuiet() )
        {
            $cli->output( "Generating new sitemap for siteaccess " . $GLOBALS['eZCurrentAccess']['name'] . " \n" );
        }

        $rootNode = self::rootNode();
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        if ( $ini->hasVariable( 'NewsSitemapSettings', 'RootNode' ) )
        {
            $rootNodeID = (int)$ini->variable( 'NewsSitemapSettings', 'RootNode' );
            $rootNode = eZContentObjectTreeNode::fetch( $rootNodeID );
            if ( ! $rootNode instanceof eZContentObjectTreeNode )
            {
                throw new Exception( "Invalid RootNode ".$rootNodeID." for Siteaccess " . $GLOBALS['eZCurrentAccess']['name'] . " \n" );
            }
        }

        if ( $ini->hasVariable( 'NewsSitemapSettings', 'ClassFilterArray' ) )
        {
            $params2 = array();
            $params2['ClassFilterArray'] = $ini->variable( 'NewsSitemapSettings', 'ClassFilterArray' );
            $params2['ClassFilterType'] = 'include';
        }

        if ( $ini->hasVariable( 'NewsSitemapSettings', 'Limitation' ) )
        {
            $limitation = $ini->variable( 'NewsSitemapSettings', 'Limitation' );
        }

        if ( $ini->hasVariable( 'NewsSitemapSettings', 'ExtraAttributeFilter' ) )
        {
            $extra_attribute_filter = array();
            $extra_attribute_filter = $ini->variable( 'NewsSitemapSettings', 'ExtraAttributeFilter' );
        }

        // Your News Sitemap should contain only URLs for your articles published in the last two days.
        $from = time() - 172800; // minus 2 days
        $till = time();
        // A News Sitemap can contain no more than 1,000 URLs.
        $max = 1000;
        $limit = 50;

        // first check if it's necerssary to recreate an exisiting one
        $dir = eZSys::storageDirectory() . '/sitemap/' . self::domain();
        $filename = $dir . '/' . xrowSitemap::BASENAME . '_' . self::FILETYP_NEWS . '_' . $GLOBALS['eZCurrentAccess']['name'] . '.' . xrowSitemap::SUFFIX;
        $file = eZClusterFileHandler::instance( $filename );
        if ( $file->exists() )
        {
            #If older as 2 days
            $oldnews = $file->mtime() - 300;
            if ( $file->mtime() < $from )
            {
                $file->delete();
            }
            #reduce 5 min because article might be published during the runtime of the cron
            $mtime = $file->mtime() - 300;
            if ( $mtime > 0 )
            {
                $params = array(
                    'IgnoreVisibility' => false ,
                    'MainNodeOnly' => false ,

                    'SortBy' => array(
                        array(
                            'published' ,
                            false
                        )
                    ) ,
                    'AttributeFilter' => array(
                        'and' ,
                        array(
                            'published' ,
                            '>' ,
                            $mtime
                        ) ,
                        array(
                            'published' ,
                            '<=' ,
                            $till
                        )
                    )
                );
                if ( isset( $params2 ) )
                {
                    $params = array_merge( $params, $params2 );
                }
                if ( isset( $limitation ) && $limitation == 'disable' )
                {
                    $params['Limitation'] = array();
                }
                if ( isset( $extra_attribute_filter ) )
                {
                    foreach ( $extra_attribute_filter as $key => $extra_attribute_filter_item )
                    {
                        if ( $ini->hasGroup( $extra_attribute_filter_item ) )
                        {
                            $value = $ini->variable( $extra_attribute_filter_item, 'Value' );
                            array_push( $params['AttributeFilter'], $value );
                        }
                    }
                }
                $subtreeCount = eZContentObjectTreeNode::subTreeCountByNodeID( $params, $rootNode->NodeID );
                if ( $subtreeCount == 0 )
                {
                    eZDebug::writeDebug( "No new published news", __METHOD__ );
                    return;
                }
            }
        }
        $params = array(
            'IgnoreVisibility' => false ,
            'MainNodeOnly' => false ,

            'SortBy' => array(
                array(
                    'published' ,
                    false
                )
            ) ,
            'AttributeFilter' => array(
                'and' ,
                array(
                    'published' ,
                    '>' ,
                    $from
                ) ,
                array(
                    'published' ,
                    '<=' ,
                    $till
                )
            )
        );
        if ( isset( $params2 ) )
        {
            $params = array_merge( $params, $params2 );
        }
        if ( isset( $limitation ) && $limitation == 'disable' )
        {
            $params['Limitation'] = array();
        }
        if ( isset( $extra_attribute_filter ) )
        {
            foreach ( $extra_attribute_filter as $key => $extra_attribute_filter_item )
            {
                if ( $ini->hasGroup( $extra_attribute_filter_item ) )
                {
                    $value = $ini->variable( $extra_attribute_filter_item, 'Value' );
                    array_push( $params['AttributeFilter'], $value );
                }
            }
        }
        $subtreeCount = eZContentObjectTreeNode::subTreeCountByNodeID( $params, $rootNode->NodeID );

        $max = min( $max, $subtreeCount );
        $max_all = $max;
        $params['Limit'] = $limit;
        $params['Offset'] = 0;
        $counter = 1;
        $runs = ceil( $subtreeCount / $max_all );
        if ( ! $cli->isQuiet() )
        {
            $amount = $subtreeCount;
            $cli->output( "Adding $amount nodes to the news sitemap." );
            $output = new ezcConsoleOutput();
        }

        while ( $counter <= $runs )
        {
            eZDebug::writeDebug( 'Run ' . $counter . ' of ' . $runs . ' runs' );
            if ( ! $cli->isQuiet() )
            {
                $cli->output( 'Run ' . $counter . ' of ' . $runs . ' runs' );
                if ( $counter == 1 )
                {
                    $bar = new ezcConsoleProgressbar( $output, $max + 1 ); // for root node
                }
                else
                {
                    $bar = new ezcConsoleProgressbar( $output, $max );
                }
            }

            // Generate Sitemap
            $sitemap = new xrowSitemap();
            while ( $params['Offset'] < $max_all )
            {
                $nodeArray = eZContentObjectTreeNode::subTreeByNodeID( $params, $rootNode->NodeID );
                foreach ( $nodeArray as $node )
                {
                    $extensions = array();
                    $extensions[] = self::getNewsConverter()->addNews( $node );
                    $imageadd = self::getImageConverter()->addImage( $node );
                    if ( ! empty( $imageadd ) )
                    {
                        $extensions = array_merge( $extensions, $imageadd );
                    }

                    $url = $node->attribute( 'url_alias' );
                    eZURI::transformURI( $url, true );
                    $url = 'https://' . self::domain() . $url;
                    $sitemap->add( $url, $extensions );

                    if ( isset( $bar ) )
                    {
                        $bar->advance();
                    }
                }
                eZContentObject::clearCache();
                $params['Offset'] += $params['Limit'];
            }

            // write XML Sitemap to file
            if ( ! is_dir( $dir ) )
            {
                mkdir( $dir, 0777, true );
            }

            $filename = $dir . '/' . xrowSitemap::BASENAME . '_' . self::FILETYP_NEWS . '_' . $GLOBALS['eZCurrentAccess']['name'] . '.' . xrowSitemap::SUFFIX;
            if ( $counter > 1 )
            {
                $filename = $dir . '/' . xrowSitemap::BASENAME . '_' . self::FILETYP_NEWS . '_' . $GLOBALS['eZCurrentAccess']['name'] . '_' . $counter . '.' . xrowSitemap::SUFFIX;
            }
            $sitemap->save( $filename );
            if ( ! $cli->isQuiet() )
            {
                $cli->output( "\n" );
                $cli->output( "Time: " . date( 'd.m.Y H:i:s' ) . ". Action: Sitemap $filename for siteaccess " . $GLOBALS['eZCurrentAccess']['name'] . " has been generated.\n" );
            }

            $counter ++;
            $max_all += $max;
            $sitemap = new xrowSitemap();
        }
    }

    public static function createMobileSitemap()
    {
        eZDebug::writeDebug( "Generating mobile sitemap ...", __METHOD__ );

        $cli = eZCLI::instance();
        if ( ! $cli->isQuiet() )
        {
            $cli->output( "Generating mobile sitemap for siteaccess " . $GLOBALS['eZCurrentAccess']['name'] . " \n" );
        }
        $ini = eZINI::instance( 'site.ini' );
        $xrowsitemapINI = eZINI::instance( 'xrowsitemap.ini' );
        // Get the Sitemap's root node
        $rootNode = self::rootNode();

        // Settings variables
        if ( $xrowsitemapINI->hasVariable( 'MobileSitemapSettings', 'ClassFilterType' ) and $xrowsitemapINI->hasVariable( 'MobileSitemapSettings', 'ClassFilterArray' ) )
        {
            $params2 = array(
                'ClassFilterType' => $xrowsitemapINI->variable( 'MobileSitemapSettings', 'ClassFilterType' ) ,
                'ClassFilterArray' => $xrowsitemapINI->variable( 'MobileSitemapSettings', 'ClassFilterArray' )
            );
        }
        $max = self::MAX_PER_FILE;
        $limit = 50;

        // Fetch the content tree
        $params = array(
            'SortBy' => array(
                array(
                    'depth' ,
                    true
                ) ,
                array(
                    'published' ,
                    true
                )
            )
        );
        if ( isset( $params2 ) )
        {
            $params = array_merge( $params, $params2 );
        }

        $subtreeCount = eZContentObjectTreeNode::subTreeCountByNodeID( $params, $rootNode->NodeID );

        if ( $subtreeCount == 1 )
        {
            $cli->output( "No Items found under RootNode $rootNode->NodeID." );
        }

        if ( ! $cli->isQuiet() )
        {
            $amount = $subtreeCount + 1; // +1 is root node
            $cli->output( "Adding $amount nodes to the sitemap for RootNode $rootNode->NodeID." );
            $output = new ezcConsoleOutput();
            $bar = new ezcConsoleProgressbar( $output, $amount );
        }

        $addPrio = false;
        if ( $xrowsitemapINI->hasVariable( 'Settings', 'AddPriorityToSubtree' ) and $xrowsitemapINI->variable( 'Settings', 'AddPriorityToSubtree' ) == 'true' )
        {
            $addPrio = true;
        }

        $sitemap = new xrowMobileSitemap();
        // Generate Sitemap
        /** START Adding the root node **/
        $object = $rootNode->object();

        $meta = xrowMetaDataFunctions::fetchByObject( $object );
        $extensions = array();
        $extensions[] = new xrowSitemapItemModified( $rootNode->attribute( 'modified_subnode' ) );

        $url = $rootNode->attribute( 'url_alias' );
        eZURI::transformURI( $url );

        if ( $xrowsitemapINI->hasVariable( 'SitemapSettings', 'MobileDomainName' ) &&
             $xrowsitemapINI->hasVariable( 'SitemapSettings', 'MobileDomainName' ) != '' )
        {
            $mobileDomain = $xrowsitemapINI->variable( 'SitemapSettings', 'MobileDomainName' );
        }
        else
        {
            $mobileDomain = self::domain();
        }


        $url = 'https://' . $mobileDomain . $url;

        if ( $meta and $meta->sitemap_use != '0' )
        {
            $extensions[] = new xrowSitemapItemFrequency( $meta->change );
            $extensions[] = new xrowSitemapItemPriority( $meta->priority );
            $sitemap->add( $url, $extensions );
        }
        elseif ( $meta === false and $xrowsitemapINI->variable( 'Settings', 'AlwaysAdd' ) == 'enabled' )
        {
            if ( $addPrio )
            {
                $extensions[] = new xrowSitemapItemPriority( '1' );
            }

            $sitemap->add( $url, $extensions );
        }

        if ( isset( $bar ) )
        {
            $bar->advance();
        }
        /** END Adding the root node **/
        $max = min( $max, $subtreeCount );
        $params['Limit'] = min( $max, $limit );
        $params['Offset'] = 0;
        while ( $params['Offset'] < $max )
        {
            $nodeArray = eZContentObjectTreeNode::subTreeByNodeID( $params, $rootNode->NodeID );
            foreach ( $nodeArray as $subTreeNode )
            {
                eZContentLanguage::expireCache();
                $meta = xrowMetaDataFunctions::fetchByNode( $subTreeNode );
                $extensions = array();
                $extensions[] = new xrowSitemapItemModified( $subTreeNode->attribute( 'modified_subnode' ) );

                $url = $subTreeNode->attribute( 'url_alias' );
                eZURI::transformURI( $url );
                $url = 'https://' . $mobileDomain . $url;

                if ( $meta and $meta->sitemap_use != '0' )
                {
                    $extensions[] = new xrowSitemapItemFrequency( $meta->change );
                    $extensions[] = new xrowSitemapItemPriority( $meta->priority );
                    $sitemap->add( $url, $extensions );
                }
                elseif ( $meta === false and $xrowsitemapINI->variable( 'Settings', 'AlwaysAdd' ) == 'enabled' )
                {
                    if ( $addPrio )
                    {
                        $rootDepth = $rootNode->attribute( 'depth' );
                        $prio = 1 - ( ( $subTreeNode->attribute( 'depth' ) - $rootDepth ) / 10 );
                        if ( $prio > 0 )
                        {
                            $extensions[] = new xrowSitemapItemPriority( $prio );
                        }
                    }
                    $sitemap->add( $url, $extensions );
                }

                if ( isset( $bar ) )
                {
                    $bar->advance();
                }
            }
            eZContentObject::clearCache();
            $params['Offset'] += $params['Limit'];
        }
        // write XML Sitemap to file
        $dir = eZSys::storageDirectory() . '/sitemap/' . self::domain();
        if ( ! is_dir( $dir ) )
        {
            mkdir( $dir, 0777, true );
        }
        $filename = $dir . '/' . xrowSitemap::BASENAME . '_' . self::FILETYP_MOBILE . '_' . $GLOBALS['eZCurrentAccess']['name'] . '.' . xrowSitemap::SUFFIX;
        $sitemap->save( $filename );

        /**
         * @TODO How will this work with cluster?
        if ( function_exists( 'gzencode' ) and $xrowsitemapINI->variable( 'MobileSitemapSettings', 'Gzip' ) == 'enabled' )
        {
            $content = file_get_contents( $filename );
            $content = gzencode( $content );
            file_put_contents( $filename . '.gz', $content );
            unlink( $filename );
            $filename .= '.gz';
        }
         **/
        if ( ! $cli->isQuiet() )
        {
            $cli->output( "\n" );
            $cli->output( "Mobile sitemap $filename for siteaccess " . $GLOBALS['eZCurrentAccess']['name'] . " has been generated.\n" );
        }
    }

    /*!
     Check access for the current object

     \param function name ( edit, read, remove, etc. )
     \param original class ID ( used to check access for object creation ), default false
     \param parent class id ( used to check access for object creation ), default false
     \param return access list instead of access result (optional, default false )

     \return 1 if has access, 0 if not.
             If returnAccessList is set to true, access list is returned
    */
    public static function checkAccess( eZContentObject $contentobject, eZUser $user, $functionName, $originalClassID = false, $parentClassID = false, $returnAccessList = false, $language = false )
    {
        $classID = $originalClassID;

        $userID = $user->attribute( 'contentobject_id' );
        $origFunctionName = $functionName;

        // Fetch the ID of the language if we get a string with a language code
        // e.g. 'eng-GB'
        $originalLanguage = $language;
        if ( is_string( $language ) && strlen( $language ) > 0 )
        {
            $language = eZContentLanguage::idByLocale( $language );
        }
        else
        {
            $language = false;
        }

        // This will be filled in with the available languages of the object
        // if a Language check is performed.
        $languageList = false;

        // The 'move' function simply reuses 'edit' for generic access
        // but adds another top-level check below
        // The original function is still available in $origFunctionName
        if ( $functionName == 'move' )
            $functionName = 'edit';

        $accessResult = $user->hasAccessTo( 'content', $functionName );
        $accessWord = $accessResult['accessWord'];

        /*
        // Uncomment this part if 'create' permissions should become implied 'edit'.
        // Merges in 'create' policies with 'edit'
        if ( $functionName == 'edit' &&
             !in_array( $accessWord, array( 'yes', 'no' ) ) )
        {
            // Add in create policies.
            $accessExtraResult = $user->hasAccessTo( 'content', 'create' );
            if ( $accessExtraResult['accessWord'] != 'no' )
            {
                $accessWord = $accessExtraResult['accessWord'];
                if ( isset( $accessExtraResult['policies'] ) )
                {
                    $accessResult['policies'] = array_merge( $accessResult['policies'],
                                                             $accessExtraResult['policies'] );
                }
                if ( isset( $accessExtraResult['accessList'] ) )
                {
                    $accessResult['accessList'] = array_merge( $accessResult['accessList'],
                                                               $accessExtraResult['accessList'] );
                }
            }
        }
        */

        if ( $origFunctionName == 'remove' or $origFunctionName == 'move' )
        {
            $mainNode = $contentobject->attribute( 'main_node' );
            // We do not allow these actions on objects placed at top-level
            // - remove
            // - move
            if ( $mainNode and $mainNode->attribute( 'parent_node_id' ) <= 1 )
            {
                return 0;
            }
        }

        if ( $classID === false )
        {
            $classID = $contentobject->attribute( 'contentclass_id' );
        }
        if ( $accessWord == 'yes' )
        {
            return 1;
        }
        else
        {
            if ( $accessWord == 'no' )
            {
                if ( $functionName == 'edit' )
                {
                    // Check if we have 'create' access under the main parent
                    if ( $contentobject->attribute( 'current_version' ) == 1 && ! $contentobject->attribute( 'status' ) )
                    {
                        $mainNode = eZNodeAssignment::fetchForObject( $contentobject->attribute( 'id' ), $contentobject->attribute( 'current_version' ) );
                        $parentObj = $mainNode[0]->attribute( 'parent_contentobject' );
                        $result = $parentObj->checkAccess( 'create', $contentobject->attribute( 'contentclass_id' ), $parentObj->attribute( 'contentclass_id' ), false, $originalLanguage );
                        return $result;
                    }
                    else
                    {
                        return 0;
                    }
                }

                if ( $returnAccessList === false )
                {
                    return 0;
                }
                else
                {
                    return $accessResult['accessList'];
                }
            }
            else
            {
                $policies = & $accessResult['policies'];
                $access = 'denied';
                foreach ( array_keys( $policies ) as $pkey )
                {
                    $limitationArray = & $policies[$pkey];
                    if ( $access == 'allowed' )
                    {
                        break;
                    }

                    $limitationList = array();
                    if ( isset( $limitationArray['Subtree'] ) )
                    {
                        $checkedSubtree = false;
                    }
                    else
                    {
                        $checkedSubtree = true;
                        $accessSubtree = false;
                    }
                    if ( isset( $limitationArray['Node'] ) )
                    {
                        $checkedNode = false;
                    }
                    else
                    {
                        $checkedNode = true;
                        $accessNode = false;
                    }
                    foreach ( array_keys( $limitationArray ) as $key )
                    {
                        $access = 'denied';
                        switch ( $key )
                        {
                            case 'Class':
                            {
                                if ( $functionName == 'create' and ! $originalClassID )
                                {
                                    $access = 'allowed';
                                }
                                else
                                {
                                    if ( $functionName == 'create' and in_array( $classID, $limitationArray[$key] ) )
                                    {
                                        $access = 'allowed';
                                    }
                                    else
                                    {
                                        if ( $functionName != 'create' and in_array( $contentobject->attribute( 'contentclass_id' ), $limitationArray[$key] ) )
                                        {
                                            $access = 'allowed';
                                        }
                                        else
                                        {
                                            $access = 'denied';
                                            $limitationList = array(
                                                'Limitation' => $key ,
                                                'Required' => $limitationArray[$key]
                                            );
                                        }
                                    }
                                }
                            }
                            break;

                            case 'ParentClass':
                            {
                                if ( in_array( $contentobject->attribute( 'contentclass_id' ), $limitationArray[$key] ) )
                                {
                                    $access = 'allowed';
                                }
                                else
                                {
                                    $access = 'denied';
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                            }
                            break;

                            case 'ParentDepth':
                            {
                                $assignedNodes = $contentobject->attribute( 'assigned_nodes' );
                                if ( count( $assignedNodes ) > 0 )
                                {
                                    foreach ( $assignedNodes as $assignedNode )
                                    {
                                        $depth = $assignedNode->attribute( 'depth' );
                                        if ( in_array( $depth, $limitationArray[$key] ) )
                                        {
                                            $access = 'allowed';
                                            break;
                                        }
                                    }
                                }

                                if ( $access != 'allowed' )
                                {
                                    $access = 'denied';
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                            }
                            break;

                            case 'Section':
                            case 'User_Section':
                            {
                                if ( in_array( $contentobject->attribute( 'section_id' ), $limitationArray[$key] ) )
                                {
                                    $access = 'allowed';
                                }
                                else
                                {
                                    $access = 'denied';
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                            }
                            break;

                            case 'Language':
                            {
                                $languageMask = 0;
                                // If we don't have a language list yet we need to fetch it
                                // and optionally filter out based on $language.


                                if ( $functionName == 'create' )
                                {
                                    // If the function is 'create' we do not use the language_mask for matching.
                                    if ( $language !== false )
                                    {
                                        $languageMask = $language;
                                    }
                                    else
                                    {
                                        // If the create is used and no language specified then
                                        // we need to match against all possible languages (which
                                        // is all bits set, ie. -1).
                                        $languageMask = - 1;
                                    }
                                }
                                else
                                {
                                    if ( $language !== false )
                                    {
                                        if ( $languageList === false )
                                        {
                                            $languageMask = (int) $contentobject->attribute( 'language_mask' );
                                            // We are restricting language check to just one language
                                            $languageMask &= (int) $language;
                                            // If the resulting mask is 0 it means that the user is trying to
                                            // edit a language which does not exist, ie. translating.
                                            // The mask will then become the language trying to edit.
                                            if ( $languageMask == 0 )
                                            {
                                                $languageMask = $language;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $languageMask = - 1;
                                    }
                                }
                                // Fetch limit mask for limitation list
                                $limitMask = eZContentLanguage::maskByLocale( $limitationArray[$key] );
                                if ( ( $languageMask & $limitMask ) != 0 )
                                {
                                    $access = 'allowed';
                                }
                                else
                                {
                                    $access = 'denied';
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                            }
                            break;

                            case 'Owner':
                            case 'ParentOwner':
                            {
                                // if limitation value == 2, anonymous limited to current session.
                                if ( in_array( 2, $limitationArray[$key] ) && $user->isAnonymous() )
                                {
                                    $createdObjectIDList = eZPreferences::value( 'ObjectCreationIDList' );
                                    if ( $createdObjectIDList && in_array( $contentobject->ID, unserialize( $createdObjectIDList ) ) )
                                    {
                                        $access = 'allowed';
                                    }
                                }
                                else
                                    if ( $contentobject->attribute( 'owner_id' ) == $userID || $contentobject->ID == $userID )
                                    {
                                        $access = 'allowed';
                                    }
                                if ( $access != 'allowed' )
                                {
                                    $access = 'denied';
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                            }
                            break;

                            case 'Group':
                            case 'ParentGroup':
                            {
                                $access = $contentobject->checkGroupLimitationAccess( $limitationArray[$key], $userID );

                                if ( $access != 'allowed' )
                                {
                                    $access = 'denied';
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                            }
                            break;

                            case 'State':
                            {
                                if ( count( array_intersect( $limitationArray[$key], $contentobject->attribute( 'state_id_array' ) ) ) == 0 )
                                {
                                    $access = 'denied';
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                                else
                                {
                                    $access = 'allowed';
                                }
                            }
                            break;

                            case 'Node':
                            {
                                $accessNode = false;
                                $mainNodeID = $contentobject->attribute( 'main_node_id' );
                                foreach ( $limitationArray[$key] as $nodeID )
                                {
                                    $node = eZContentObjectTreeNode::fetch( $nodeID, false, false );
                                    $limitationNodeID = $node['main_node_id'];
                                    if ( $mainNodeID == $limitationNodeID )
                                    {
                                        $access = 'allowed';
                                        $accessNode = true;
                                        break;
                                    }
                                }
                                if ( $access != 'allowed' && $checkedSubtree && ! $accessSubtree )
                                {
                                    $access = 'denied';
                                    // ??? TODO: if there is a limitation on Subtree, return two limitations?
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                                else
                                {
                                    $access = 'allowed';
                                }
                                $checkedNode = true;
                            }
                            break;

                            case 'Subtree':
                            {
                                $accessSubtree = false;
                                $assignedNodes = $contentobject->attribute( 'assigned_nodes' );
                                if ( count( $assignedNodes ) != 0 )
                                {
                                    foreach ( $assignedNodes as $assignedNode )
                                    {
                                        $path = $assignedNode->attribute( 'path_string' );
                                        $subtreeArray = $limitationArray[$key];
                                        foreach ( $subtreeArray as $subtreeString )
                                        {
                                            if ( strstr( $path, $subtreeString ) )
                                            {
                                                $access = 'allowed';
                                                $accessSubtree = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $parentNodes = $contentobject->attribute( 'parent_nodes' );
                                    if ( count( $parentNodes ) == 0 )
                                    {
                                        if ( $contentobject->attribute( 'owner_id' ) == $userID || $contentobject->ID == $userID )
                                        {
                                            $access = 'allowed';
                                            $accessSubtree = true;
                                        }
                                    }
                                    else
                                    {
                                        foreach ( $parentNodes as $parentNode )
                                        {
                                            $parentNode = eZContentObjectTreeNode::fetch( $parentNode, false, false );
                                            $path = $parentNode['path_string'];

                                            $subtreeArray = $limitationArray[$key];
                                            foreach ( $subtreeArray as $subtreeString )
                                            {
                                                if ( strstr( $path, $subtreeString ) )
                                                {
                                                    $access = 'allowed';
                                                    $accessSubtree = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                if ( $access != 'allowed' && $checkedNode && ! $accessNode )
                                {
                                    $access = 'denied';
                                    // ??? TODO: if there is a limitation on Node, return two limitations?
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                                else
                                {
                                    $access = 'allowed';
                                }
                                $checkedSubtree = true;
                            }
                            break;

                            case 'User_Subtree':
                            {
                                $assignedNodes = $contentobject->attribute( 'assigned_nodes' );
                                if ( count( $assignedNodes ) != 0 )
                                {
                                    foreach ( $assignedNodes as $assignedNode )
                                    {
                                        $path = $assignedNode->attribute( 'path_string' );
                                        $subtreeArray = $limitationArray[$key];
                                        foreach ( $subtreeArray as $subtreeString )
                                        {
                                            if ( strstr( $path, $subtreeString ) )
                                            {
                                                $access = 'allowed';
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $parentNodes = $contentobject->attribute( 'parent_nodes' );
                                    if ( count( $parentNodes ) == 0 )
                                    {
                                        if ( $contentobject->attribute( 'owner_id' ) == $userID || $contentobject->ID == $userID )
                                        {
                                            $access = 'allowed';
                                        }
                                    }
                                    else
                                    {
                                        foreach ( $parentNodes as $parentNode )
                                        {
                                            $parentNode = eZContentObjectTreeNode::fetch( $parentNode, false, false );
                                            $path = $parentNode['path_string'];

                                            $subtreeArray = $limitationArray[$key];
                                            foreach ( $subtreeArray as $subtreeString )
                                            {
                                                if ( strstr( $path, $subtreeString ) )
                                                {
                                                    $access = 'allowed';
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                if ( $access != 'allowed' )
                                {
                                    $access = 'denied';
                                    $limitationList = array(
                                        'Limitation' => $key ,
                                        'Required' => $limitationArray[$key]
                                    );
                                }
                            }
                            break;

                            default:
                            {
                                if ( strncmp( $key, 'StateGroup_', 11 ) === 0 )
                                {
                                    if ( count( array_intersect( $limitationArray[$key], $contentobject->attribute( 'state_id_array' ) ) ) == 0 )
                                    {
                                        $access = 'denied';
                                        $limitationList = array(
                                            'Limitation' => $key ,
                                            'Required' => $limitationArray[$key]
                                        );
                                    }
                                    else
                                    {
                                        $access = 'allowed';
                                    }
                                }
                            }
                        }
                        if ( $access == 'denied' )
                        {
                            break;
                        }
                    }

                    $policyList[] = array(
                        'PolicyID' => $pkey ,
                        'LimitationList' => $limitationList
                    );
                }

                if ( $access == 'denied' )
                {
                    if ( $functionName == 'edit' )
                    {
                        // Check if we have 'create' access under the main parent
                        if ( $contentobject->attribute( 'current_version' ) == 1 && ! $contentobject->attribute( 'status' ) )
                        {
                            $mainNode = eZNodeAssignment::fetchForObject( $contentobject->attribute( 'id' ), $contentobject->attribute( 'current_version' ) );
                            $parentObj = $mainNode[0]->attribute( 'parent_contentobject' );
                            $result = $parentObj->checkAccess( 'create', $contentobject->attribute( 'contentclass_id' ), $parentObj->attribute( 'contentclass_id' ), false, $originalLanguage );
                            if ( $result )
                            {
                                $access = 'allowed';
                            }
                            return $result;
                        }
                    }
                }

                if ( $access == 'denied' )
                {
                    if ( $returnAccessList === false )
                    {
                        return 0;
                    }
                    else
                    {
                        return array(
                            'FunctionRequired' => array(
                                'Module' => 'content' ,
                                'Function' => $origFunctionName ,
                                'ClassID' => $classID ,
                                'MainNodeID' => $contentobject->attribute( 'main_node_id' )
                            ) ,
                            'PolicyList' => $policyList
                        );
                    }
                }
                else
                {
                    return 1;
                }
            }
        }
    }

    /*!
     get URI for the current object
     return the URI
     */
    public static function getUri( $urimatchMapitems )
    {
        foreach ( $urimatchMapitems as $hostUriMap )
        {
            $hostUri = explode( ';', $hostUriMap );
            if ( count( $hostUri ) < 3 )
            {
                $accessname = $hostUri[1];
                $uri = '';
            } else {
                $accessname = $hostUri[2];
                $uri = $hostUri[1];
            }
            if ( $accessname == $GLOBALS['eZCurrentAccess']['name'] )
            {
                break;
            }
        }
        return $uri;
    }
}
