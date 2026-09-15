<?php
/**
 * File containing the eZRSSExport class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZRSSExport ezrssexport.php
  \brief Handles RSS Export in eZ Publish

  RSSExport is used to create RSS feeds from published content. See kernel/rss for more files.
*/

class eZRSSExport extends eZPersistentObject
{
    public $ImageID;
    public $ModifierID;
    public $MainNodeOnly;
    public $NumberOfObjects;
    public $Title;
    public $URL;
    public $SiteAccess;
    public $CreatorID;
    public $Description;
    public $RSSVersion;
    public $AccessURL;
    public $Active;
    public $OPMLHead;

    /// The podcast channel fields, as json. See podcastHead().
    public $PodcastHead;

    const STATUS_VALID = 1;
    const STATUS_DRAFT = 0;

    static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'node_id' => array( 'name' => 'NodeID',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => false ),
                                         'title' => array( 'name' => 'Title',
                                                           'datatype' => 'string',
                                                           'default' => ezpI18n::tr( 'kernel/rss', 'New RSS Export' ),
                                                           'required' => true ),
                                         'url' => array( 'name' => 'URL',
                                                         'datatype' => 'string',
                                                         'default' => '',
                                                         'required' => true ),
                                         'site_access' => array( 'name' => 'SiteAccess',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         'modified' => array( 'name' => 'Modified',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ),
                                         'modifier_id' => array( 'name' => 'ModifierID',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true,
                                                                 'foreign_class' => 'eZUser',
                                                                 'foreign_attribute' => 'contentobject_id',
                                                                 'multiplicity' => '1..*' ),
                                         'created' => array( 'name' => 'Created',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ),
                                         'creator_id' => array( 'name' => 'CreatorID',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true,
                                                                'foreign_class' => 'eZUser',
                                                                'foreign_attribute' => 'contentobject_id',
                                                                'multiplicity' => '1..*' ),
                                         'description' => array( 'name' => 'Description',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ),
                                         'image_id' => array( 'name' => 'ImageID',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => false ),
                                         'rss_version' => array( 'name' => 'RSSVersion',
                                                                 'datatype' => 'string',
                                                                 'default' => 0,
                                                                 'required' => true ),
                                         'active' => array( 'name' => 'Active',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'status' => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'access_url' => array( 'name' => 'AccessURL',
                                                                'datatype' => 'string',
                                                                'default' => 'rss_feed',
                                                                'required' => false ),
                                         'number_of_objects' => array( 'name' => 'NumberOfObjects',
                                                                       'datatype' => 'integer',
                                                                       'default' => 0,
                                                                       'required' => true ),
                                         'main_node_only' => array( 'name' => 'MainNodeOnly',
                                                                    'datatype' => 'integer',
                                                                    'default' => 0,
                                                                    'required' => true ),
                                         // The OPML head fields - owner, docs,
                                         // expansion and window state - as json.
                                         // Only an OPML export ever fills it in.
                                         'opml_head' => array( 'name' => 'OPMLHead',
                                                               'datatype' => 'string',
                                                               'default' => '',
                                                               'required' => false ),
                                         // The channel fields an Apple Podcasts
                                         // feed needs and no other format has
                                         // anywhere to put, as json. Only a
                                         // podcast export ever fills it in.
                                         'podcast_head' => array( 'name' => 'PodcastHead',
                                                                  'datatype' => 'string',
                                                                  'default' => '',
                                                                  'required' => false ) ),
                      'keys' => array( 'id', 'status' ),
                      'function_attributes' => array( 'item_list' => 'itemList',
                                                      'is_opml' => 'isOPML',
                                                      'is_podcast' => 'isPodcast',
                                                      'podcast_head_data' => 'podcastHead',
                                                      'opml_item_list' => 'opmlItemList',
                                                      'opml_head_data' => 'opmlHead',
                                                      'modifier' => 'modifier',
                                                      'rss-xml-content' => 'rssXmlContent', // new attribute which uses the Feed component
                                                      'image_path' => 'imagePath',
                                                      'image_node' => 'imageNode' ),
                      'increment_key' => 'id',
                      'sort' => array( 'title' => 'asc' ),
                      'class_name' => 'eZRSSExport',
                      'name' => 'ezrss_export' );

    }

    /*!
     \static
     Creates a new RSS Export
     \param User ID

     \return the URL alias object
    */
    /**
     * The address a feed should advertise itself and its links under.
     *
     * A feed is published for a public siteaccess, but it is written and
     * generated from the administration siteaccess, so the siteaccess that
     * happens to be executing is the wrong thing to ask. Asking it gave every
     * export here 'http://localhost' - the admin siteaccess's own SiteURL -
     * and, where the stored url was empty, the edit host instead, which is
     * never somewhere a reader can follow.
     *
     * So: the siteaccess the export is for, if it names one; otherwise the
     * default siteaccess, which is what a reader reaching the site plainly
     * gets. Only if neither can be read does it fall back to the running
     * configuration.
     *
     * @param string|false $siteAccess the export's site_access, if it has one.
     * @return string scheme and host, no trailing slash, e.g. https://example.com
     */
    static function publicSiteURL( $siteAccess = false )
    {
        $ini = eZINI::instance();

        $candidates = array();
        if ( is_string( $siteAccess ) && $siteAccess !== '' )
            $candidates[] = $siteAccess;
        $candidates[] = $ini->variable( 'SiteSettings', 'DefaultAccess' );

        $host = '';
        foreach ( $candidates as $name )
        {
            if ( !is_string( $name ) || $name === '' )
                continue;

            $dir = 'settings/siteaccess/' . $name;
            if ( !is_dir( $dir ) )
                continue;

            $saINI = eZINI::instance( 'site.ini', $dir, null, false, null, true );
            if ( !$saINI || !$saINI->hasVariable( 'SiteSettings', 'SiteURL' ) )
                continue;

            $candidate = trim( $saINI->variable( 'SiteSettings', 'SiteURL' ) );
            // localhost is what an unconfigured siteaccess says; it is never
            // an address a feed reader can use, so keep looking.
            if ( $candidate === '' || $candidate === 'localhost' )
                continue;

            $host = $candidate;
            break;
        }

        if ( $host === '' )
            $host = trim( (string) $ini->variable( 'SiteSettings', 'SiteURL' ) );

        $host = rtrim( $host, '/' );

        // A host already carrying a scheme is taken as given.
        if ( preg_match( '#^https?://#i', $host ) )
            return $host;

        // Inside a request, follow the request. Outside one - a cron
        // regeneration, a repair script - there is no request to follow, and a
        // feed address is a public, long lived thing that gets written into
        // other people's readers: https is the right default to commit to
        // there. A site genuinely served over plain http can say so by putting
        // the scheme into SiteURL, which is honoured just above.
        $scheme = 'https';

        if ( eZSys::serverVariable( 'REQUEST_URI', true ) !== null )
            $scheme = eZSys::isSSLNow() ? 'https' : 'http';

        return $scheme . '://' . $host;
    }

    static function create( $user_id )
    {
        $config = eZINI::instance( 'site.ini' );
        $dateTime = time();
        $row = array( 'id' => null,
                      'node_id', '',
                      'title' => ezpI18n::tr( 'kernel/classes', 'New RSS Export' ),
                      'site_access' => '',
                      'modifier_id' => $user_id,
                      'modified' => $dateTime,
                      'creator_id' => $user_id,
                      'created' => $dateTime,
                      'status' => self::STATUS_DRAFT,
                      // The public site, not whichever siteaccess is creating
                      // this - that is the admin one, and its SiteURL is no use
                      // to a reader. See publicSiteURL().
                      'url' => self::publicSiteURL(),
                      'description' => '',
                      'image_id' => 0,
                      'active' => 1,
                      'access_url' => '',
                      'opml_head' => '' );
        return new eZRSSExport( $row );
    }

    /*!
     Store Object to database
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    function store( $storeAsValid = false )
    {
        $dateTime = time();
        $user = eZUser::currentUser();
        if (  $this->ID == null )
        {
            parent::store();
            return;
        }

        $db = eZDB::instance();
        $db->begin();
        if ( $storeAsValid )
        {
            $oldStatus = $this->attribute( 'status' );
            $this->setAttribute( 'status', eZRSSExport::STATUS_VALID );
        }
        $this->setAttribute( 'modified', $dateTime );
        $this->setAttribute( 'modifier_id', $user->attribute( "contentobject_id" ) );
        parent::store();
        $db->commit();
        if ( $storeAsValid )
        {
            $this->setAttribute( 'status', $oldStatus );
        }
    }

    /*!
     Remove the RSS Export.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    function removeThis()
    {
        $exportItems = $this->fetchItems();

        $db = eZDB::instance();
        $db->begin();
        foreach ( $exportItems as $item )
        {
            $item->remove();
        }
        // The outlines of this status go with it. Only this status: removing a
        // draft must leave the published document standing, exactly as the
        // export items above do.
        eZRSSExportOPMLItem::removeByExport( $this->ID, $this->Status );
        $this->remove();
        $db->commit();
    }

    /*!
     \static
      Fetches the RSS Export by ID.

     \param RSS Export ID
    */
    static function fetch( $id, $asObject = true, $status = eZRSSExport::STATUS_VALID )
    {
        return eZPersistentObject::fetchObject( eZRSSExport::definition(),
                                                null,
                                                array( "id" => $id,
                                                       'status' => $status ),
                                                $asObject );
    }

    /*!
     \static
      Fetches the RSS Export by feed access url and is active.

     \param RSS Export access url
    */
    static function fetchByName( $access_url, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZRSSExport::definition(),
                                                null,
                                                array( 'access_url' => $access_url,
                                                       'active' => 1,
                                                       'status' => self::STATUS_VALID ),
                                                $asObject );
    }

    /*!
     \static
      Fetches complete list of RSS Exports.
    */
    /**
     * Fetches the valid RSS exports, a page at a time when asked.
     *
     * A list view with thousands of feeds in it must not pull them all into
     * memory to show twenty five, so an offset and a length can be given. With
     * neither, the whole list comes back as it always did.
     *
     * @param bool $asObject
     * @param int|false $offset first row to return.
     * @param int|false $limit  how many rows to return, false for all of them.
     * @param array|null $sorts  field => 'asc'|'desc', or null for the default order.
     * @return array
     */
    static function fetchList( $asObject = true, $offset = false, $limit = false, $sorts = null )
    {
        $limitArray = null;
        if ( $limit !== false && $limit !== null )
            $limitArray = array( 'offset' => (int) $offset, 'length' => (int) $limit );

        return eZPersistentObject::fetchObjectList( eZRSSExport::definition(),
                                                    null, array( 'status' => self::STATUS_VALID ), $sorts, $limitArray,
                                                    $asObject );
    }

    /**
     * One page of feeds for the browser on an OPML export's edit page.
     *
     * eZPersistentObject can filter, but it joins its conditions with AND, and
     * a search box has to look in more than one column at once. The where
     * clause is therefore built here - from a term that is escaped and has its
     * own wildcards defused, so a name containing a percent sign searches for
     * that sign rather than for everything.
     *
     * @param string $search     what to look for in the name, address or description.
     * @param int    $offset
     * @param int    $limit
     * @param array|null $sorts  field => 'asc'|'desc'.
     * @param int|false $excludeID an export to leave out, normally the one being edited.
     * @return array of eZRSSExport
     */
    static function fetchBrowserList( $search = '', $offset = 0, $limit = 25, $sorts = null, $excludeID = false )
    {
        $db    = eZDB::instance();
        $where = self::browserCondition( $db, $search, $excludeID );
        $order = self::browserOrder( $sorts );

        $rows = $db->arrayQuery( 'SELECT * FROM ezrss_export WHERE ' . $where . $order,
                                 array( 'offset' => (int) $offset, 'limit' => (int) $limit ) );

        $exports = array();
        foreach ( $rows as $row )
            $exports[] = new eZRSSExport( $row );

        return $exports;
    }

    /**
     * How many feeds that browser has to page through.
     *
     * @param string $search
     * @param int|false $excludeID
     * @return int
     */
    static function fetchBrowserListCount( $search = '', $excludeID = false )
    {
        $db   = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezrss_export WHERE '
                                 . self::browserCondition( $db, $search, $excludeID ) );

        return count( $rows ) ? (int) $rows[0]['c'] : 0;
    }

    /**
     * The where clause both of the above share.
     *
     * @param eZDBInterface $db
     * @param string $search
     * @param int|false $excludeID
     * @return string
     */
    protected static function browserCondition( $db, $search, $excludeID )
    {
        $where = 'status=' . (int) self::STATUS_VALID;

        if ( $excludeID !== false && is_numeric( $excludeID ) )
            $where .= ' AND id<>' . (int) $excludeID;

        // Longer than any name in the table, so nothing beyond this could match
        // anything; the cap is there to keep the query itself a sensible size.
        $search = eZRSSExportOPMLItem::safeText( $search, 255 );
        if ( $search !== '' )
        {
            // The wildcards belong to the query, not to what was typed.
            $term = str_replace( array( '\\', '%', '_' ), array( '\\\\', '\\%', '\\_' ), $search );
            $term = '%' . $db->escapeString( $term ) . '%';
            $where .= " AND ( title LIKE '$term' OR access_url LIKE '$term' OR description LIKE '$term' )";
        }

        return $where;
    }

    /**
     * The order clause, from a sort the caller has already had checked.
     *
     * @param array|null $sorts
     * @return string
     */
    protected static function browserOrder( $sorts )
    {
        if ( !is_array( $sorts ) || !count( $sorts ) )
            return ' ORDER BY title ASC';

        $fields = self::sortableFields();
        $parts  = array();

        foreach ( $sorts as $field => $direction )
        {
            // Belt and braces: the caller settles on a column from the list
            // above, and nothing else is allowed to reach the clause here.
            if ( !in_array( $field, $fields, true ) )
                continue;
            $parts[] = $field . ( strtolower( $direction ) === 'desc' ? ' DESC' : ' ASC' );
        }

        return count( $parts ) ? ' ORDER BY ' . implode( ', ', $parts ) : ' ORDER BY title ASC';
    }

    /**
     * The columns the list can be sorted by.
     *
     * A column not named here is refused, so what reaches the order clause is
     * always a real field of this table.
     *
     * @return array of field name.
     */
    static function sortableFields()
    {
        return array( 'id', 'title', 'access_url', 'rss_version', 'active', 'modifier_id', 'modified' );
    }

    /**
     * How many valid RSS exports there are, without fetching any of them.
     *
     * @return int
     */
    static function fetchListCount()
    {
        return (int) eZPersistentObject::count( eZRSSExport::definition(),
                                                array( 'status' => self::STATUS_VALID ) );
    }

    function itemList()
    {
        return $this->fetchItems();
    }

    function imageNode()
    {
        if ( isset( $this->ImageID ) and $this->ImageID )
        {
            return eZContentObjectTreeNode::fetch( $this->ImageID );
        }
        return null;
    }

    function imagePath()
    {
        if ( isset( $this->ImageID ) and $this->ImageID )
        {
            $objectNode = eZContentObjectTreeNode::fetch( $this->ImageID );
            if ( isset( $objectNode ) )
            {
                $retValue = '';
                $path_array = $objectNode->attribute( 'path_array' );
                for ( $i = 0; $i < count( $path_array ); $i++ )
                {
                    $treenode = eZContentObjectTreeNode::fetch( $path_array[$i], false, false );

                    if( $i != 0 )
                    {
                        $retValue .= '/';
                    }

                    $retValue .= array_key_exists( 'name', $treenode ) ? $treenode['name'] : '';
                }
                return $retValue;
            }
        }
        return null;

    }

    function modifier()
    {
        if ( isset( $this->ModifierID ) and $this->ModifierID )
        {
            return eZUser::fetch( $this->ModifierID );
        }
        return null;
    }

    /**
     * Generates an RSS feed document based on the rss_version attribute.
     *
     * It uses the Feed component from eZ Components.
     *
     * Supported types: 'rss1', 'rss2', 'atom'.
     *
     * @since 4.2
     * @return string XML document as a string
     */
    function rssXmlContent()
    {
        try
        {
            switch ( $this->attribute( 'rss_version' ) )
            {
                case '1.0':
                {
                    return $this->generateFeed( 'rss1' );
                } break;

                case '2.0':
                {
                    return $this->generateFeed( 'rss2' );
                } break;

                case 'ATOM':
                {
                    return $this->generateFeed( 'atom' );
                } break;

                case 'ITUNES':
                {
                    // Same reasoning as OPML below: this is a public address,
                    // and whatever goes wrong behind it something well formed
                    // has to come back rather than a stack trace.
                    try
                    {
                        return $this->generateITunes();
                    }
                    catch ( Exception $e )
                    {
                        eZDebug::writeError( $e->getMessage(), __METHOD__ );
                        return self::emptyPodcast( $this->attribute( 'title' ) );
                    }
                    catch ( Throwable $e )
                    {
                        eZDebug::writeError( $e->getMessage(), __METHOD__ );
                        return self::emptyPodcast( $this->attribute( 'title' ) );
                    }
                } break;

                case 'OPML':
                {
                    // OPML is a list of feeds rather than a list of articles,
                    // and the Feed component does not write it, so it is
                    // written here. This is a public address: whatever goes
                    // wrong behind it, something valid has to come back, or a
                    // reader is handed a stack trace.
                    try
                    {
                        return $this->generateOPML();
                    }
                    catch ( Exception $e )
                    {
                        eZDebug::writeError( $e->getMessage(), __METHOD__ );
                        return self::emptyOPML( $this->attribute( 'title' ) );
                    }
                    catch ( Throwable $e )
                    {
                        eZDebug::writeError( $e->getMessage(), __METHOD__ );
                        return self::emptyOPML( $this->attribute( 'title' ) );
                    }
                } break;

                default:
                {
                    return null;
                } break;
            }
        }
        catch ( ezcFeedException $e )
        {
            return '<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom" xml:lang=""><title>The RSS feed you were trying to access contains some errors and cannot be generated: ' . $e->getMessage() . ' Please contact the webmaster.</title></feed>';
        }

        return null;
    }

    /**
     * What each stored format value is called in the interface.
     *
     * The values themselves - '1.0', '2.0', 'ATOM', 'OPML' - are what is stored,
     * posted and read by everything that already exists, and none of them
     * change. This only says how to write them where a person reads them: "2.0"
     * on its own does not say which format it is the version of.
     *
     * @return array stored value => the words for it.
     */
    static function formatLabels()
    {
        return array( '1.0'    => 'RSS 1.0 (RDF)',
                      '2.0'    => 'RSS 2.0',
                      'ATOM'   => 'Atom 1.0',
                      'OPML'   => 'OPML 2.0 (list of feeds)',
                      'ITUNES' => 'Apple Podcasts (RSS 2.0 + iTunes)' );
    }

    /**
     * The words for one stored format value.
     *
     * A value nobody has a name for - one an extension has added to
     * AvailableVersionList - is shown as it stands rather than hidden.
     *
     * @param string $version
     * @return string
     */
    static function formatLabel( $version )
    {
        $labels = self::formatLabels();

        return isset( $labels[$version] ) ? $labels[$version] : (string) $version;
    }

    /**
     * Whether this export is written as OPML rather than as a feed of articles.
     *
     * @return bool
     */
    function isOPML()
    {
        return $this->attribute( 'rss_version' ) === 'OPML';
    }



    /**
     * Writes this export as an Apple Podcasts feed.
     *
     * RSS 2.0 with the iTunes namespace. Apple ingests plain RSS and reads the
     * itunes: elements for everything RSS has no room for - who the show is by,
     * its artwork, its category, whether it is explicit - and rejects the feed
     * outright if any of the required ones are missing, so this writes them
     * from podcastHead() and leaves out only what is genuinely optional.
     *
     * Written with DOM rather than the Feed component for the same reason OPML
     * is: the component does not know this vocabulary, and the requirements are
     * exact enough that it is better to be able to see every element being
     * written than to hope a general writer emits them the way Apple wants.
     *
     * @return string XML document.
     */
    function generateITunes()
    {
        $head    = $this->podcastHead();
        $baseURL = trim( (string) $this->attribute( 'url' ) );

        if ( $baseURL === '' )
            $baseURL = self::publicSiteURL( $this->attribute( 'site_access' ) );

        $baseURL = rtrim( $baseURL, '/' );

        $doc = new DOMDocument( '1.0', 'utf-8' );
        $doc->formatOutput = true;

        $rss = $doc->createElement( 'rss' );
        $rss->setAttribute( 'version', '2.0' );
        $rss->setAttribute( 'xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd' );
        $rss->setAttribute( 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/' );
        $rss->setAttribute( 'xmlns:atom', 'http://www.w3.org/2005/Atom' );
        $doc->appendChild( $rss );

        $channel = $doc->createElement( 'channel' );
        $rss->appendChild( $channel );

        $feedURL = $baseURL . '/rss/feed/' . $this->attribute( 'access_url' );

        // ── required by Apple ────────────────────────────────────────────────
        self::element( $doc, $channel, 'title', $this->attribute( 'title' ) );
        self::element( $doc, $channel, 'link', $baseURL );
        self::element( $doc, $channel, 'description', $this->attribute( 'description' ) );

        $language = $head['language'] !== ''
                    ? $head['language']
                    : eZLocale::instance()->httpLocaleCode();
        self::element( $doc, $channel, 'language', $language );

        // itunes:image carries its address in an href attribute and has no text
        // content. Written as text it is silently ignored, which is the single
        // most common reason a feed is rejected for having no artwork.
        if ( $head['imageUrl'] !== '' )
        {
            $image = $doc->createElement( 'itunes:image' );
            $image->setAttribute( 'href', $head['imageUrl'] );
            $channel->appendChild( $image );
        }

        if ( $head['category'] !== '' )
        {
            $category = $doc->createElement( 'itunes:category' );
            $category->setAttribute( 'text', $head['category'] );

            if ( $head['subcategory'] !== '' )
            {
                $sub = $doc->createElement( 'itunes:category' );
                $sub->setAttribute( 'text', $head['subcategory'] );
                $category->appendChild( $sub );
            }

            $channel->appendChild( $category );
        }

        self::element( $doc, $channel, 'itunes:explicit',
                       $head['explicit'] === 'true' ? 'true' : 'false' );

        // ── recommended ──────────────────────────────────────────────────────
        self::element( $doc, $channel, 'itunes:author', $head['author'] );
        self::element( $doc, $channel, 'itunes:title', $this->attribute( 'title' ) );
        self::element( $doc, $channel, 'itunes:subtitle', $head['subtitle'] );
        self::element( $doc, $channel, 'itunes:summary', $head['summary'] );
        self::element( $doc, $channel, 'copyright', $head['copyright'] );
        self::element( $doc, $channel, 'itunes:type',
                       $head['type'] === 'serial' ? 'serial' : 'episodic' );

        if ( $head['ownerName'] !== '' || $head['ownerEmail'] !== '' )
        {
            $owner = $doc->createElement( 'itunes:owner' );
            self::element( $doc, $owner, 'itunes:name', $head['ownerName'] );
            self::element( $doc, $owner, 'itunes:email', $head['ownerEmail'] );
            $channel->appendChild( $owner );
        }

        // ── lifecycle, written only when set ─────────────────────────────────
        if ( $head['block'] === 'true' )
            self::element( $doc, $channel, 'itunes:block', 'Yes' );
        if ( $head['complete'] === 'true' )
            self::element( $doc, $channel, 'itunes:complete', 'Yes' );
        if ( $head['newFeedUrl'] !== '' )
            self::element( $doc, $channel, 'itunes:new-feed-url', $head['newFeedUrl'] );

        // Where this document lives, so a reader that has been handed a copy
        // can find the original.
        $self = $doc->createElement( 'atom:link' );
        $self->setAttribute( 'href', $feedURL );
        $self->setAttribute( 'rel', 'self' );
        $self->setAttribute( 'type', 'application/rss+xml' );
        $channel->appendChild( $self );

        self::element( $doc, $channel, 'lastBuildDate', date( DATE_RFC2822 ) );
        self::element( $doc, $channel, 'generator', eZPublishSDK::EDITION );

        // ── episodes ─────────────────────────────────────────────────────────
        foreach ( $this->podcastItemList( $baseURL ) as $episode )
        {
            // An episode with nothing to play is not an episode. Apple rejects
            // the feed rather than the item, so it is left out here.
            if ( $episode['enclosureUrl'] === '' )
                continue;

            $item = $doc->createElement( 'item' );
            $channel->appendChild( $item );

            self::element( $doc, $item, 'title', $episode['title'] );
            self::element( $doc, $item, 'itunes:title', $episode['title'] );
            self::element( $doc, $item, 'link', $episode['link'] );
            self::element( $doc, $item, 'description', $episode['description'] );
            self::element( $doc, $item, 'pubDate', $episode['pubDate'] );

            $enclosure = $doc->createElement( 'enclosure' );
            $enclosure->setAttribute( 'url', $episode['enclosureUrl'] );
            // The byte length has to be the real one; a wrong length is the
            // second most common reason a feed is rejected.
            $enclosure->setAttribute( 'length', (string) $episode['enclosureLength'] );
            $enclosure->setAttribute( 'type', $episode['enclosureType'] );
            $item->appendChild( $enclosure );

            // A guid must never change for an episode, or every reader treats
            // it as new. The object's remote id is the one thing about it that
            // survives being moved, renamed and republished.
            $guid = $doc->createElement( 'guid', htmlspecialchars( $episode['guid'], ENT_XML1, 'UTF-8' ) );
            $guid->setAttribute( 'isPermaLink', 'false' );
            $item->appendChild( $guid );

            if ( $episode['duration'] !== '' )
                self::element( $doc, $item, 'itunes:duration', $episode['duration'] );

            self::element( $doc, $item, 'itunes:explicit',
                           $head['explicit'] === 'true' ? 'true' : 'false' );

            if ( $episode['author'] !== '' )
                self::element( $doc, $item, 'itunes:author', $episode['author'] );

            if ( $episode['category'] !== '' )
                self::element( $doc, $item, 'category', $episode['category'] );
        }

        return $doc->saveXML();
    }

    /**
     * Appends a simple element carrying text, and nothing when there is none.
     *
     * @param DOMDocument $doc
     * @param DOMNode $parent
     * @param string $name
     * @param string $value
     */
    static function element( DOMDocument $doc, DOMNode $parent, $name, $value )
    {
        $value = (string) $value;

        if ( trim( $value ) === '' )
            return;

        $element = $doc->createElement( $name );
        $element->appendChild( $doc->createTextNode( $value ) );
        $parent->appendChild( $element );
    }

    /**
     * The episodes of this export, as plain values the writer can use.
     *
     * The sources and the class attribute mapping are the ones every other
     * format uses, read the same way; what is different is that a podcast item
     * stands or falls on its enclosure, so the file is resolved here rather
     * than left to the writer.
     *
     * @param string $baseURL the site addresses are built against.
     * @return array of episode.
     */
    function podcastItemList( $baseURL )
    {
        $episodes = array();

        $sources = eZRSSExportItem::fetchFilteredList(
            array( 'rssexport_id' => $this->ID, 'status' => $this->Status ) );

        $nodes = eZRSSExportItem::fetchNodeList( $sources, $this->getObjectListFilter() );

        if ( !is_array( $nodes ) || !count( $nodes ) )
            return $episodes;

        $mappings   = eZRSSExportItem::getAttributeMappings( $sources );
        $useAlias   = eZINI::instance()->variable( 'URLTranslator', 'Translation' ) == 'enabled';

        foreach ( $nodes as $node )
        {
            if ( $node->attribute( 'is_hidden' ) && !eZContentObjectTreeNode::showInvisibleNodes() )
                continue;

            $object  = $node->attribute( 'object' );
            $dataMap = $object->dataMap();

            $mapping = false;
            foreach ( $mappings as $candidate )
            {
                if ( $candidate[0]->attribute( 'class_id' ) == $object->attribute( 'contentclass_id' ) &&
                     in_array( $candidate[0]->attribute( 'source_node_id' ), $node->attribute( 'path_array' ) ) )
                {
                    $mapping = $candidate[0];
                    break;
                }
            }

            if ( !$mapping )
                continue;

            $link = $useAlias
                    ? $this->urlEncodePath( $baseURL . '/' . $node->urlAlias() )
                    : $baseURL . '/content/view/full/' . $node->attribute( 'node_id' );

            $episode = array(
                'title'           => self::podcastText( $dataMap, $mapping->attribute( 'title' ) ),
                'description'     => self::podcastText( $dataMap, $mapping->attribute( 'description' ) ),
                'category'        => self::podcastText( $dataMap, $mapping->attribute( 'category' ) ),
                'link'            => $link,
                'guid'            => $object->attribute( 'remote_id' ),
                'pubDate'         => date( DATE_RFC2822, $node->attribute( 'object' )->attribute( 'published' ) ),
                'author'          => '',
                'duration'        => '',
                'enclosureUrl'    => '',
                'enclosureLength' => 0,
                'enclosureType'   => '' );

            if ( $episode['title'] === '' )
                $episode['title'] = $object->attribute( 'name' );

            $creator = $node->attribute( 'creator' );
            if ( $creator instanceof eZContentObject )
                $episode['author'] = $creator->attribute( 'name' );

            $identifier = $mapping->attribute( 'enclosure' );
            $attribute  = $identifier && isset( $dataMap[$identifier] ) ? $dataMap[$identifier] : false;
            $content    = $attribute ? $attribute->attribute( 'content' ) : false;

            if ( $content instanceof eZMedia || $content instanceof eZBinaryFile )
            {
                $episode['enclosureLength'] = (int) $content->attribute( 'filesize' );
                $episode['enclosureType']   = (string) $content->attribute( 'mime_type' );
                $episode['enclosureUrl']    = $baseURL . '/content/download/'
                                            . $attribute->attribute( 'contentobject_id' ) . '/'
                                            . $content->attribute( 'contentobject_attribute_id' ) . '/'
                                            . urlencode( $content->attribute( 'original_filename' ) );

                $duration = self::audioDuration( $content );
                if ( $duration !== false )
                    $episode['duration'] = $duration;
            }

            $episodes[] = $episode;
        }

        return $episodes;
    }

    /**
     * One mapped class attribute as plain text, or an empty string.
     *
     * @param array $dataMap
     * @param string|false $identifier
     * @return string
     */
    static function podcastText( $dataMap, $identifier )
    {
        if ( !$identifier || !isset( $dataMap[$identifier] ) )
            return '';

        $content = $dataMap[$identifier]->attribute( 'content' );

        if ( $content instanceof eZXMLText )
            return (string) $content->attribute( 'output' )->attribute( 'output_text' );

        if ( $content instanceof eZKeyword )
            return (string) $content->keywordString();

        return is_scalar( $content ) ? (string) $content : '';
    }

    /**
     * How long an audio enclosure runs, as itunes:duration wants it.
     *
     * Read from the file rather than from a field, because there is no field:
     * the media datatypes record a size and a type and not a length. Only
     * constant bitrate MPEG audio is worked out, which is what an exported mp3
     * is; anything else returns false and the tag is left out, which is allowed
     * - a wrong duration is worse than none.
     *
     * @param eZMedia|eZBinaryFile $content
     * @return string|false HH:MM:SS.
     */
    static function audioDuration( $content )
    {
        $path = false;

        if ( method_exists( $content, 'filePath' ) )
            $path = $content->filePath();
        else if ( $content->hasAttribute( 'filepath' ) )
            $path = $content->attribute( 'filepath' );

        if ( !$path )
            return false;

        $file = eZClusterFileHandler::instance( $path );
        if ( !$file->exists() )
            return false;

        $handle = @fopen( $path, 'rb' );
        if ( !$handle )
            return false;

        $seconds = false;
        $bytes   = (int) $content->attribute( 'filesize' );

        // Walk far enough in to clear an id3 tag, looking for the first frame
        // header: eleven set bits, then version, layer and bitrate.
        $buffer = fread( $handle, 65536 );
        fclose( $handle );

        if ( !is_string( $buffer ) )
            return false;

        $rates = array( 0, 32, 40, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 0 );

        for ( $i = 0; $i < strlen( $buffer ) - 4; $i++ )
        {
            if ( ord( $buffer[$i] ) !== 0xFF || ( ord( $buffer[$i + 1] ) & 0xE0 ) !== 0xE0 )
                continue;

            $version = ( ord( $buffer[$i + 1] ) >> 3 ) & 0x03;   // 3 is MPEG 1
            $layer   = ( ord( $buffer[$i + 1] ) >> 1 ) & 0x03;   // 1 is layer III
            $rate    = ( ord( $buffer[$i + 2] ) >> 4 ) & 0x0F;

            if ( $version !== 3 || $layer !== 1 || $rate === 0 || $rate === 0x0F )
                continue;

            $bitrate = $rates[$rate] * 1000;
            if ( $bitrate < 1 )
                continue;

            // Everything from the first frame to the end of the file, at a
            // constant rate.
            $seconds = (int) floor( ( ( $bytes - $i ) * 8 ) / $bitrate );
            break;
        }

        if ( $seconds === false || $seconds < 1 )
            return false;

        return sprintf( '%02d:%02d:%02d',
                        floor( $seconds / 3600 ),
                        floor( ( $seconds % 3600 ) / 60 ),
                        $seconds % 60 );
    }
    /**
     * The categories Apple Podcasts accepts, and their subcategories.
     *
     * Apple rejects a feed whose itunes:category is not one of these, spelled
     * exactly as it appears here, so the list is data rather than something
     * typed into a box. It is the list Apple published with the 2019 revision.
     *
     * If Apple revises it, this is the one place to change, and the edit page
     * and the writer both follow.
     *
     * @return array category => array of subcategory.
     */
    static function podcastCategories()
    {
        return array(
            'Arts' => array( 'Books', 'Design', 'Fashion & Beauty', 'Food',
                             'Performing Arts', 'Visual Arts' ),
            'Business' => array( 'Careers', 'Entrepreneurship', 'Investing',
                                 'Management', 'Marketing', 'Non-Profit' ),
            'Comedy' => array( 'Comedy Interviews', 'Improv', 'Stand-Up' ),
            'Education' => array( 'Courses', 'How To', 'Language Learning',
                                  'Self-Improvement' ),
            'Fiction' => array( 'Comedy Fiction', 'Drama', 'Science Fiction' ),
            'Government' => array(),
            'History' => array(),
            'Health & Fitness' => array( 'Alternative Health', 'Fitness', 'Medicine',
                                         'Mental Health', 'Nutrition', 'Sexuality' ),
            'Kids & Family' => array( 'Education for Kids', 'Parenting',
                                      'Pets & Animals', 'Stories for Kids' ),
            'Leisure' => array( 'Animation & Manga', 'Automotive', 'Aviation', 'Crafts',
                                'Games', 'Hobbies', 'Home & Garden', 'Video Games' ),
            'Music' => array( 'Music Commentary', 'Music History', 'Music Interviews' ),
            'News' => array( 'Business News', 'Daily News', 'Entertainment News',
                             'News Commentary', 'Politics', 'Sports News', 'Tech News' ),
            'Religion & Spirituality' => array( 'Buddhism', 'Christianity', 'Hinduism',
                                                'Islam', 'Judaism', 'Religion', 'Spirituality' ),
            'Science' => array( 'Astronomy', 'Chemistry', 'Earth Sciences', 'Life Sciences',
                                'Mathematics', 'Natural Sciences', 'Nature', 'Physics',
                                'Social Sciences' ),
            'Society & Culture' => array( 'Documentary', 'Personal Journals', 'Philosophy',
                                          'Places & Travel', 'Relationships' ),
            'Sports' => array( 'Baseball', 'Basketball', 'Cricket', 'Fantasy Sports',
                               'Football', 'Golf', 'Hockey', 'Rugby', 'Running', 'Soccer',
                               'Swimming', 'Tennis', 'Volleyball', 'Wilderness', 'Wrestling' ),
            'Technology' => array(),
            'True Crime' => array(),
            'TV & Film' => array( 'After Shows', 'Film History', 'Film Interviews',
                                  'Film Reviews', 'TV Reviews' ) );
    }

    /**
     * A category name if Apple lists it, otherwise an empty string.
     *
     * @param string $value the name to check.
     * @param string|false $parent when checking a subcategory, the category it
     *        has to belong to. False checks a top level category.
     * @return string
     */
    static function knownPodcastCategory( $value, $parent = false )
    {
        $value = trim( (string) $value );

        if ( $value === '' )
            return '';

        $categories = self::podcastCategories();

        if ( $parent === false )
            return isset( $categories[$value] ) ? $value : '';

        $parent = trim( (string) $parent );

        return isset( $categories[$parent] ) && in_array( $value, $categories[$parent], true )
               ? $value : '';
    }
    /**
     * Whether this export is written as an Apple Podcasts feed.
     *
     * @return bool
     */
    function isPodcast()
    {
        return $this->attribute( 'rss_version' ) === 'ITUNES';
    }

    /**
     * The podcast channel fields, as a hash the edit page and the writer read.
     *
     * Apple requires a handful of things RSS 2.0 has nowhere to put - who the
     * show is by, who owns it, its artwork, its category, whether it is
     * explicit - and rejects a feed missing any of them. They are kept as json
     * in one column for the same reason the OPML head is: written once, read
     * once, never searched on.
     *
     * Anything unreadable in the column is treated as nothing having been
     * filled in, rather than breaking the page trying to show it.
     *
     * @return array
     */
    function podcastHead()
    {
        $defaults = array( 'author'      => '',
                           'ownerName'   => '',
                           'ownerEmail'  => '',
                           'imageUrl'    => '',
                           'category'    => '',
                           'subcategory' => '',
                           'explicit'    => 'false',
                           'type'        => 'episodic',
                           'summary'     => '',
                           'subtitle'    => '',
                           'copyright'   => '',
                           'language'    => '',
                           'block'       => '',
                           'complete'    => '',
                           'newFeedUrl'  => '' );

        $stored = is_string( $this->PodcastHead ) && $this->PodcastHead !== ''
                  ? json_decode( $this->PodcastHead, true )
                  : null;

        if ( !is_array( $stored ) )
            return $defaults;

        foreach ( $defaults as $key => $value )
            if ( isset( $stored[$key] ) && is_scalar( $stored[$key] ) )
                $defaults[$key] = (string) $stored[$key];

        return $defaults;
    }

    /**
     * Records the podcast channel fields, keeping only the ones Apple defines.
     *
     * Values are held to what the format allows rather than stored as typed
     * and rejected later by Apple, where the reason would not be visible.
     *
     * @param array $head
     */
    function setPodcastHead( array $head )
    {
        $keep     = array();
        $defaults = $this->podcastHead();

        foreach ( array_keys( $defaults ) as $key )
        {
            if ( !isset( $head[$key] ) || !is_scalar( $head[$key] ) )
            {
                $keep[$key] = $defaults[$key];
                continue;
            }

            $value = trim( (string) $head[$key] );

            switch ( $key )
            {
                case 'imageUrl':
                case 'newFeedUrl':
                    $value = eZRSSExportOPMLItem::safeURL( $value );
                    break;

                // itunes:explicit is a boolean in the spec, and Apple reads
                // anything else as missing.
                case 'explicit':
                case 'block':
                case 'complete':
                    $value = in_array( strtolower( $value ), array( 'true', 'yes', '1' ), true )
                             ? 'true' : ( $value === '' ? '' : 'false' );
                    if ( $key === 'explicit' && $value === '' )
                        $value = 'false';
                    break;

                case 'type':
                    $value = $value === 'serial' ? 'serial' : 'episodic';
                    break;

                case 'category':
                case 'subcategory':
                    // Only a category Apple actually lists; anything else is
                    // dropped rather than written out to be rejected.
                    $value = self::knownPodcastCategory( $value,
                                 $key === 'subcategory' ? $head['category'] : false );
                    break;

                default:
                    $value = eZRSSExportOPMLItem::safeText( $value, 1024 );
            }

            $keep[$key] = $value;
        }

        $this->setAttribute( 'podcast_head', json_encode( $keep ) );
    }

    /**
     * The lines of this OPML export.
     *
     * @return array of eZRSSExportOPMLItem
     */
    function opmlItemList()
    {
        return eZRSSExportOPMLItem::fetchList( $this->ID, $this->Status );
    }

    /**
     * The OPML head fields, as a hash the edit page and the writer can read.
     *
     * Stored as json in one column. Anything unreadable there is treated as
     * nothing having been filled in, rather than breaking the page that is
     * trying to show it.
     *
     * @return array
     */
    function opmlHead()
    {
        $defaults = array( 'ownerName' => '', 'ownerEmail' => '', 'ownerId' => '',
                           'docs' => 'http://opml.org/spec2.opml',
                           'expansionState' => '', 'vertScrollState' => '',
                           'windowTop' => '', 'windowLeft' => '',
                           'windowBottom' => '', 'windowRight' => '' );

        $stored = is_string( $this->OPMLHead ) && $this->OPMLHead !== ''
                  ? json_decode( $this->OPMLHead, true )
                  : null;

        if ( !is_array( $stored ) )
            return $defaults;

        foreach ( $defaults as $key => $value )
            if ( isset( $stored[$key] ) && is_scalar( $stored[$key] ) )
                $defaults[$key] = (string) $stored[$key];

        return $defaults;
    }

    /**
     * Records the OPML head fields, keeping only the ones OPML defines.
     *
     * @param array $head
     */
    function setOPMLHead( array $head )
    {
        $keep = array();
        foreach ( array_keys( $this->opmlHead() ) as $key )
        {
            if ( !isset( $head[$key] ) || !is_scalar( $head[$key] ) )
                continue;

            $value = eZRSSExportOPMLItem::safeText( $head[$key], 1024 );

            // The window and scroll fields are numbers, or lists of them, and
            // docs is an address. Anything else in them is somebody trying
            // their luck, and is dropped rather than stored to be written out.
            if ( $key === 'docs' )
                $value = eZRSSExportOPMLItem::safeURL( $value );
            else if ( in_array( $key, array( 'expansionState', 'vertScrollState',
                                             'windowTop', 'windowLeft',
                                             'windowBottom', 'windowRight' ), true ) )
                $value = preg_match( '/^-?\d+(\s*,\s*-?\d+)*$/', $value ) ? $value : '';
            else if ( $key === 'ownerEmail' )
                $value = filter_var( $value, FILTER_VALIDATE_EMAIL ) ? $value : '';

            if ( $value !== '' )
                $keep[$key] = $value;
        }

        $encoded = json_encode( $keep );

        $this->setAttribute( 'opml_head', is_string( $encoded ) ? $encoded : '' );
    }

    /**
     * Writes this export as an OPML 2.0 document.
     *
     * Built with DOM rather than by pasting strings together, so a title with
     * an ampersand in it cannot produce a document nothing will parse. What is
     * written follows the OPML 2.0 specification: a head of optional elements,
     * a body of at least one outline, every outline carrying text, and every
     * outline of type rss carrying xmlUrl.
     *
     * @return string
     */
    function generateOPML()
    {
        $ini = eZINI::instance();

        // Empty means nobody has said where this feed lives, and the answer is
        // the public site it is published for - not transformURI(), which
        // answers with the host of the request doing the generating. That is
        // the administration host whenever a feed is looked at or regenerated
        // from the admin, and it is not somewhere a reader can follow.
        $baseURL = trim( (string) $this->attribute( 'url' ) );

        if ( $baseURL === '' )
            $baseURL = self::publicSiteURL( $this->attribute( 'site_access' ) );

        $doc = new DOMDocument( '1.0', 'utf-8' );
        $doc->formatOutput = true;

        $opml = $doc->createElement( 'opml' );
        $opml->setAttribute( 'version', '2.0' );
        $doc->appendChild( $opml );

        $head = $doc->createElement( 'head' );
        $opml->appendChild( $head );

        $stored = $this->opmlHead();

        // The owner's name is worth filling in from the export's creator when
        // nobody has typed one, because a reader shows it beside the list.
        if ( $stored['ownerName'] === '' )
        {
            $creator = eZContentObject::fetch( $this->attribute( 'creator_id' ) );
            if ( $creator instanceof eZContentObject )
                $stored['ownerName'] = $creator->attribute( 'name' );
        }

        $elements = array(
            'title'           => $this->attribute( 'title' ),
            'dateCreated'     => self::opmlDate( $this->attribute( 'created' ) ),
            'dateModified'    => self::opmlDate( $this->attribute( 'modified' ) ),
            'ownerName'       => $stored['ownerName'],
            'ownerEmail'      => $stored['ownerEmail'] !== '' ? $stored['ownerEmail']
                                 : $ini->variable( 'MailSettings', 'AdminEmail' ),
            'ownerId'         => $stored['ownerId'],
            'docs'            => eZRSSExportOPMLItem::safeURL( $stored['docs'] ),
            'expansionState'  => $stored['expansionState'],
            'vertScrollState' => $stored['vertScrollState'],
            'windowTop'       => $stored['windowTop'],
            'windowLeft'      => $stored['windowLeft'],
            'windowBottom'    => $stored['windowBottom'],
            'windowRight'     => $stored['windowRight'] );

        foreach ( $elements as $name => $value )
        {
            $value = eZRSSExportOPMLItem::safeText( $value, 1024 );
            if ( $value === '' )
                continue;

            // A text node rather than createElement's second argument, which
            // takes its value as markup: an ampersand in a title would
            // otherwise have to be escaped by hand, and escaping it twice is
            // just as wrong as not escaping it at all.
            $element = $doc->createElement( $name );
            $element->appendChild( $doc->createTextNode( $value ) );
            $head->appendChild( $element );
        }

        $body = $doc->createElement( 'body' );
        $opml->appendChild( $body );

        $written = $this->appendOPMLOutlines(
            $doc, $body,
            eZRSSExportOPMLItem::fetchTree( $this->ID, $this->Status, self::opmlMaxOutlines() ),
            $baseURL );

        // OPML says the body holds one or more outlines. An export with nothing
        // in it yet would otherwise produce a document a validator rejects.
        if ( !$written )
        {
            $outline = $doc->createElement( 'outline' );
            $emptyText = eZRSSExportOPMLItem::safeText( $this->attribute( 'title' ) );
            $outline->setAttribute( 'text', $emptyText !== '' ? $emptyText : 'Empty' );
            $body->appendChild( $outline );
        }

        return $doc->saveXML();
    }

    /**
     * Writes one level of outlines, and whatever hangs below them.
     *
     * @param DOMDocument $doc
     * @param DOMElement $parent
     * @param array $branch from eZRSSExportOPMLItem::fetchTree().
     * @param string $baseURL
     * @return int how many outlines were written.
     */
    protected function appendOPMLOutlines( DOMDocument $doc, DOMElement $parent, array $branch, $baseURL, $depth = 0 )
    {
        $written = 0;

        // Outlines nest, and a chain longer than this is not a document anyone
        // meant to write. Stopping is better than following it down.
        if ( $depth >= eZRSSExportOPMLItem::MAX_DEPTH )
            return 0;

        foreach ( $branch as $node )
        {
            $item     = $node['item'];
            $children = $node['children'];
            $outline  = $item->outline( $baseURL );

            // A line pointing at a feed that has since been deleted says
            // nothing useful; it is left out rather than written as a dead
            // entry. A group with something under it is kept either way.
            if ( $outline === null && !count( $children ) )
                continue;

            $element = $doc->createElement( 'outline' );

            if ( $outline === null )
            {
                $groupText = eZRSSExportOPMLItem::safeText( $item->attribute( 'title' ) );
                $element->setAttribute( 'text', $groupText !== '' ? $groupText : 'Group' );
            }
            else
            {
                $element->setAttribute( 'text', $outline['text'] );

                // type is left off a plain group: OPML gives no type for one,
                // and a made up value is worse than none.
                if ( $outline['type'] !== '' && $outline['type'] !== 'group' )
                    $element->setAttribute( 'type', $outline['type'] );

                foreach ( array( 'title'       => 'title',
                                 'description' => 'description',
                                 'category'    => 'category',
                                 'language'    => 'language',
                                 'xmlUrl'      => 'xmlUrl',
                                 'htmlUrl'     => 'htmlUrl',
                                 'url'         => 'url',
                                 'version'     => 'version' ) as $key => $attribute )
                {
                    if ( isset( $outline[$key] ) && $outline[$key] !== '' )
                        $element->setAttribute( $attribute, $outline[$key] );
                }

                if ( $outline['isComment'] )
                    $element->setAttribute( 'isComment', 'true' );
                if ( $outline['isBreakpoint'] )
                    $element->setAttribute( 'isBreakpoint', 'true' );
                if ( $outline['created'] )
                    $element->setAttribute( 'created', self::opmlDate( $outline['created'] ) );
            }

            $parent->appendChild( $element );
            $written++;

            if ( count( $children ) )
                $written += $this->appendOPMLOutlines( $doc, $element, $children, $baseURL, $depth + 1 );
        }

        return $written;
    }

    /**
     * The most outlines one document will carry.
     *
     * Settable, because what is too many depends on the installation, but
     * bounded whatever the setting says: a document large enough to exhaust
     * memory is served from a public address, so it would take the site down
     * rather than just itself.
     *
     * @return int
     */
    static function opmlMaxOutlines()
    {
        $ini = eZINI::instance( 'site.ini' );
        $limit = $ini->hasVariable( 'RSSSettings', 'OPMLMaxOutlines' )
                 ? (int) $ini->variable( 'RSSSettings', 'OPMLMaxOutlines' )
                 : 5000;

        return max( 1, min( $limit, 50000 ) );
    }


    /**
     * A well formed podcast feed with no episodes.
     *
     * What a reader gets when generation failed. It carries the channel
     * elements Apple requires so that the answer is a feed with nothing in it
     * rather than something no client can parse.
     *
     * @param string $title
     * @return string XML document.
     */
    static function emptyPodcast( $title = '' )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $doc->formatOutput = true;

        $rss = $doc->createElement( 'rss' );
        $rss->setAttribute( 'version', '2.0' );
        $rss->setAttribute( 'xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd' );
        $doc->appendChild( $rss );

        $channel = $doc->createElement( 'channel' );
        $rss->appendChild( $channel );

        $title = eZRSSExportOPMLItem::safeText( $title );
        if ( $title === '' )
            $title = 'Podcast';

        self::element( $doc, $channel, 'title', $title );
        self::element( $doc, $channel, 'description', $title );
        self::element( $doc, $channel, 'language', eZLocale::instance()->httpLocaleCode() );
        self::element( $doc, $channel, 'itunes:explicit', 'false' );

        return $doc->saveXML();
    }
    /**
     * A document to fall back on when the real one cannot be produced.
     *
     * Valid OPML, because whatever is reading it should be told the list is
     * empty rather than handed something it cannot parse.
     *
     * @param string $title
     * @return string
     */
    static function emptyOPML( $title = '' )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $doc->formatOutput = true;

        $opml = $doc->createElement( 'opml' );
        $opml->setAttribute( 'version', '2.0' );
        $doc->appendChild( $opml );

        $title = eZRSSExportOPMLItem::safeText( $title );
        if ( $title === '' )
            $title = 'OPML';

        $head = $doc->createElement( 'head' );
        $titleElement = $doc->createElement( 'title' );
        $titleElement->appendChild( $doc->createTextNode( $title ) );
        $head->appendChild( $titleElement );
        $opml->appendChild( $head );

        $body = $doc->createElement( 'body' );
        $outline = $doc->createElement( 'outline' );
        $outline->setAttribute( 'text', $title );
        $body->appendChild( $outline );
        $opml->appendChild( $body );

        return $doc->saveXML();
    }

    /**
     * A timestamp as OPML writes dates, which is the RFC 822 form.
     *
     * @param int $timestamp
     * @return string empty when there is no date to write.
     */
    static function opmlDate( $timestamp )
    {
        $timestamp = (int) $timestamp;

        return $timestamp > 0 ? gmdate( 'D, d M Y H:i:s', $timestamp ) . ' GMT' : '';
    }

    /*!
      Fetches RSS Items related to this RSS Export. The RSS Export Items contain information about which nodes to export information from

      \param RSSExport ID (optional). Uses current RSSExport's ID as default

      \return RSSExportItem list. null if no RSS Export items found
    */
    function fetchItems( $id = false, $status = eZRSSExport::STATUS_VALID )
    {
        if ( $id === false )
        {
            if ( isset( $this ) )
            {
                $id = $this->ID;
                $status = $this->Status;
            }
            else
            {
                $itemList = null;
                return $itemList;
            }
        }
        if ( $id !== null )
            $itemList = eZRSSExportItem::fetchFilteredList( array( 'rssexport_id' => $id, 'status' => $status ) );
        else
            $itemList = null;
        return $itemList;
    }

    function getObjectListFilter()
    {
        if ( $this->MainNodeOnly == 1 )
        {
            $this->MainNodeOnly = true;
        }
        else
        {
            $this->MainNodeOnly = false;
        }

        return array( 'number_of_objects' => intval($this->NumberOfObjects),
                      'main_node_only'    => $this->MainNodeOnly
                     );
    }

    /**
     * Generates an RSS feed document with type $type and returns it as a string.
     *
     * It uses the Feed component from eZ Components.
     *
     * Supported types: 'rss1', 'rss2', 'atom'.
     *
     * @since 4.2
     * @param string $type One of 'rss1', 'rss2' and 'atom'
     * @return string XML document as a string
     */
    function generateFeed( $type )
    {
        $locale = eZLocale::instance();

        // Get URL Translation settings.
        $config = eZINI::instance();
        if ( $config->variable( 'URLTranslator', 'Translation' ) == 'enabled' )
        {
            $useURLAlias = true;
        }
        else
        {
            $useURLAlias = false;
        }

        if ( $this->attribute( 'url' ) == '' )
        {
            $baseItemURL = '';
            eZURI::transformURI( $baseItemURL, false, 'full' );
            $baseItemURL .= '/';
        }
        else
        {
            $baseItemURL = $this->attribute( 'url' ) . '/'; //.$this->attribute( 'site_access' ).'/';
        }

        $feed = new ezcFeed();

        $feed->title = htmlspecialchars(
            $this->attribute( 'title' ), ENT_NOQUOTES, 'UTF-8'
        );

        $link = $feed->add( 'link' );
        $link->href = htmlspecialchars( $baseItemURL, ENT_NOQUOTES, 'UTF-8' );

        $feed->description = htmlspecialchars(
            $this->attribute( 'description' ), ENT_NOQUOTES, 'UTF-8'
        );
        $feed->language = $locale->httpLocaleCode();

        // to add the <atom:link> element needed for RSS2
        $feed->id = htmlspecialchars(
            $baseItemURL . 'rss/feed/' . $this->attribute( 'access_url' ),
            ENT_NOQUOTES, 'UTF-8'
        );

        // required for ATOM
        $feed->updated = time();
        $author        = $feed->add( 'author' );
        $author->email = htmlspecialchars(
            $config->variable( 'MailSettings', 'AdminEmail' ),
            ENT_NOQUOTES, 'UTF-8'
        );
        $creatorObject = eZContentObject::fetch( $this->attribute( 'creator_id' ) );
        if ( $creatorObject instanceof eZContentObject )
        {
            $author->name = htmlspecialchars(
                $creatorObject->attribute('name'), ENT_NOQUOTES, 'UTF-8'
            );
        }

        $imageURL = $this->fetchImageURL();
        if ( $imageURL !== false )
        {
            $imageURL = htmlspecialchars( $imageURL, ENT_NOQUOTES, 'UTF-8' );
            $image = $feed->add( 'image' );

            // Required for RSS1
            $image->about = $imageURL;

            $image->url = $imageURL;
            $image->title = htmlspecialchars(
                $this->attribute( 'title' ), ENT_NOQUOTES, 'UTF-8'
            );
            $image->link = $link->href;
        }

        $cond = array(
                    'rssexport_id'  => $this->ID,
                    'status'        => $this->Status
                    );
        $rssSources = eZRSSExportItem::fetchFilteredList( $cond );

        $nodeArray = eZRSSExportItem::fetchNodeList( $rssSources, $this->getObjectListFilter() );

        if ( is_array( $nodeArray ) && count( $nodeArray ) )
        {
            $attributeMappings = eZRSSExportItem::getAttributeMappings( $rssSources );

            foreach ( $nodeArray as $node )
            {
                if ( $node->attribute('is_hidden') && !eZContentObjectTreeNode::showInvisibleNodes() )
                {
                    // if the node is hidden skip past it and don't add it to the RSS export
                    continue;
                }
                $object = $node->attribute( 'object' );
                $dataMap = $object->dataMap();
                if ( $useURLAlias === true )
                {
                    $nodeURL = $this->urlEncodePath( $baseItemURL . $node->urlAlias() );
                }
                else
                {
                    $nodeURL = $baseItemURL . 'content/view/full/' . $node->attribute( 'node_id' );
                }

                // keep track if there's any match
                $doesMatch = false;
                // start mapping the class attribute to the respective RSS field
                foreach ( $attributeMappings as $attributeMapping )
                {
                    // search for correct mapping by path
                    if ( $attributeMapping[0]->attribute( 'class_id' ) == $object->attribute( 'contentclass_id' ) and
                         in_array( $attributeMapping[0]->attribute( 'source_node_id' ), $node->attribute( 'path_array' ) ) )
                    {
                        // found it
                        $doesMatch = true;
                        // now fetch the attributes
                        $title =  $dataMap[$attributeMapping[0]->attribute( 'title' )];
                        // description is optional
                        $descAttributeIdentifier = $attributeMapping[0]->attribute( 'description' );
                        $description = $descAttributeIdentifier ? $dataMap[$descAttributeIdentifier] : false;
                        // category is optional
                        $catAttributeIdentifier = $attributeMapping[0]->attribute( 'category' );
                        $category = $catAttributeIdentifier ? $dataMap[$catAttributeIdentifier] : false;
                        // enclosure is optional
                        $enclosureAttributeIdentifier = $attributeMapping[0]->attribute( 'enclosure' );
                        $enclosure = $enclosureAttributeIdentifier ? $dataMap[$enclosureAttributeIdentifier] : false;
                        break;
                    }
                }

                if( !$doesMatch )
                {
                    // no match
                    eZDebug::writeError( 'Cannot find matching RSS attributes for datamap on node: ' . $node->attribute( 'node_id' ), __METHOD__ );
                    return null;
                }

                // title RSS element with respective class attribute content
                $titleContent =  $title->attribute( 'content' );
                if ( $titleContent instanceof eZXMLText )
                {
                    $outputHandler = $titleContent->attribute( 'output' );
                    $itemTitleText = $outputHandler->attribute( 'output_text' );
                }
                else
                {
                    $itemTitleText = $titleContent;
                }

                $item = $feed->add( 'item' );

                $item->title = htmlspecialchars( $itemTitleText, ENT_NOQUOTES, 'UTF-8' );

                $link = $item->add( 'link' );
                $link->href = htmlspecialchars( $nodeURL, ENT_NOQUOTES, 'UTF-8' );

                switch ( $type )
                {
                    case 'rss2':
                        $item->id = $object->attribute( 'remote_id' );
                        $item->id->isPermaLink = false;
                        break;
                    default:
                        $item->id = $nodeURL;
                }

                $itemCreatorObject = $node->attribute('creator');
                if ( $itemCreatorObject instanceof eZContentObject )
                {
                    $author = $item->add( 'author' );
                    $author->name = htmlspecialchars(
                        $itemCreatorObject->attribute('name'), ENT_NOQUOTES, 'UTF-8'
                    );
                    $author->email = $config->variable( 'MailSettings', 'AdminEmail' );
                }

                // description RSS element with respective class attribute content
                if ( $description )
                {
                    $descContent = $description->attribute( 'content' );
                    if ( $descContent instanceof eZXMLText )
                    {
                        $outputHandler =  $descContent->attribute( 'output' );
                        $itemDescriptionText = htmlspecialchars(
                            $outputHandler->attribute( 'output_text' ), ENT_NOQUOTES, 'UTF-8'
                        );
                    }
                    else if ( $descContent instanceof eZImageAliasHandler )
                    {
                        $itemImage   = $descContent->hasAttribute( 'rssitem' ) ? $descContent->attribute( 'rssitem' ) : $descContent->attribute( 'rss' );
                        $origImage   = $descContent->attribute( 'original' );
                        eZURI::transformURI( $itemImage['full_path'], true, 'full' );
                        eZURI::transformURI( $origImage['full_path'], true, 'full' );
                        $itemDescriptionText = '&lt;a href="' . htmlspecialchars( $origImage['full_path'] )
                                             . '"&gt;&lt;img alt="' . htmlspecialchars( $descContent->attribute( 'alternative_text' ) )
                                             . '" src="' . htmlspecialchars( $itemImage['full_path'] )
                                             . '" width="' . $itemImage['width']
                                             . '" height="' . $itemImage['height']
                                             . '" /&gt;&lt;/a&gt;';
                    }
                    else
                    {
                        $itemDescriptionText = htmlspecialchars(
                            $descContent, ENT_NOQUOTES, 'UTF-8'
                        );
                    }
                    $item->description = $itemDescriptionText;
                }

                // category RSS element with respective class attribute content
                if ( $category )
                {
                    $categoryContent =  $category->attribute( 'content' );
                    if ( $categoryContent instanceof eZXMLText )
                    {
                        $outputHandler = $categoryContent->attribute( 'output' );
                        $itemCategoryText = $outputHandler->attribute( 'output_text' );
                    }
                    elseif ( $categoryContent instanceof eZKeyword )
                    {
                        $itemCategoryText = $categoryContent->keywordString();
                    }
                    else
                    {
                        $itemCategoryText = $categoryContent;
                    }

                    if ( $itemCategoryText )
                    {
                        $cat = $item->add( 'category' );
                        $cat->term = htmlspecialchars(
                            $itemCategoryText, ENT_NOQUOTES, 'UTF-8'
                        );
                    }
                }

                // enclosure RSS element with respective class attribute content
                if ( $enclosure )
                {
                    $encItemURL       = false;
                    $enclosureContent = $enclosure->attribute( 'content' );
                    if ( $enclosureContent instanceof eZMedia )
                    {
                        $enc         = $item->add( 'enclosure' );
                        $enc->length = $enclosureContent->attribute('filesize');
                        $enc->type   = $enclosureContent->attribute('mime_type');
                        $encItemURL = 'content/download/' . $enclosure->attribute('contentobject_id')
                                    . '/' . $enclosureContent->attribute( 'contentobject_attribute_id' )
                                    . '/' . urlencode( $enclosureContent->attribute( 'original_filename' ) );
                        eZURI::transformURI( $encItemURL, false, 'full' );
                    }
                    else if ( $enclosureContent instanceof eZBinaryFile )
                    {
                        $enc         = $item->add( 'enclosure' );
                        $enc->length = $enclosureContent->attribute('filesize');
                        $enc->type   = $enclosureContent->attribute('mime_type');
                        $encItemURL = 'content/download/' . $enclosure->attribute('contentobject_id')
                                    . '/' . $enclosureContent->attribute( 'contentobject_attribute_id' )
                                    . '/version/' . $enclosureContent->attribute( 'version' )
                                    . '/file/' . urlencode( $enclosureContent->attribute( 'original_filename' ) );
                        eZURI::transformURI( $encItemURL, false, 'full' );
                    }
                    else if ( $enclosureContent instanceof eZImageAliasHandler )
                    {
                        $enc         = $item->add( 'enclosure' );
                        $origImage   = $enclosureContent->attribute( 'original' );
                        $enc->length = $origImage['filesize'];
                        $enc->type   = $origImage['mime_type'];
                        $encItemURL  = $origImage['full_path'];
                        eZURI::transformURI( $encItemURL, true, 'full' );
                    }

                    if ( $encItemURL )
                    {
                        $enc->url = htmlspecialchars( $encItemURL, ENT_NOQUOTES, 'UTF-8' );
                    }
                }

                $item->published = $object->attribute( 'published' );
                $item->updated = $object->attribute( 'published' );
            }
        }
        return $feed->generate( $type );
    }

    /*!
     \private

     Fetch Image from current ezrss export object. If non exist, or invalid, return false

     \return valid image url
    */
    function fetchImageURL()
    {

        $imageNode =  $this->attribute( 'image_node' );
        if ( !$imageNode )
            return false;

        $imageObject =  $imageNode->attribute( 'object' );
        if ( !$imageObject )
            return false;

        $dataMap =  $imageObject->attribute( 'data_map' );
        if ( !$dataMap )
            return false;

        $imageAttribute =  $dataMap['image'];
        if ( !$imageAttribute )
            return false;

        $imageHandler =  $imageAttribute->attribute( 'content' );
        if ( !$imageHandler )
            return false;

        $imageAlias =  $imageHandler->imageAlias( 'rss' );
        if( !$imageAlias )
            return false;

        $url = eZSys::hostname() . eZSys::wwwDir() .'/'. $imageAlias['url'];
        $url = preg_replace( "#^(//)#", "/", $url );

        return 'http://'.$url;
    }

    /*!
     \private

     Performs rawurlencode() on the path part of the URL. The rest is not touched.

     \return partially encoded url
    */
    function urlEncodePath( $url )
    {
        // Raw encode the path part of the URL
        $urlComponents = parse_url( $url );
        $pathParts = explode( '/', $urlComponents['path'] );
        foreach ( $pathParts as $key => $pathPart )
        {
            $pathParts[$key] = rawurlencode( $pathPart );
        }
        $encodedPath = implode( '/', $pathParts );

        // Rebuild the URL again, like this: scheme://user:pass@host/path?query#fragment
        $encodedUrl = $urlComponents['scheme'] . '://';

        if ( isset( $urlComponents['user'] ) )
        {
            $encodedUrl .= $urlComponents['user'];
            if ( isset( $urlComponents['pass'] ) )
            {
                $encodedUrl .= ':' . $urlComponents['pass'];
            }
            $encodedUrl .= '@';
        }

        $encodedUrl .= $urlComponents['host'];
        if ( isset( $urlComponents['port'] ) )
        {
            $encodedUrl .= ':' . $urlComponents['port'];
        }
        $encodedUrl .= $encodedPath;

        if ( isset( $urlComponents['query'] ) )
        {
            $encodedUrl .= '?' . $urlComponents['query'];
        }

        if ( isset( $urlComponents['fragment'] ) )
        {
            $encodedUrl .= '#' . $urlComponents['fragment'];
        }

        return $encodedUrl;
    }
}
?>
