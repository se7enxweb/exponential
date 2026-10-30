<?php
/**
 * Reads help maintained as content out of the database into plain arrays (the shapes expHelpContentValidator
 * describes), without any permission filter: the provider (expHelpContentProvider) filters by the current user, the
 * validator and the command see everything.
 *
 * Everything is found by remote_id (help-root ...) and class identifier, never by a node, section or state id from a
 * settings file: those differ between installations.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentRepository
{
    /** @var expHelpContentRepository|null */
    protected static $instance = null;

    /** The format of the cache file; a new one is written when it changes */
    const CACHE_FORMAT = 'exphelp-content-repository-1';

    /** Objects loaded at once when the rows are read from the database */
    const LOAD_CHUNK = 2000;

    /** Up to this many objects read again stay loaded for object() */
    const KEEP_OBJECTS = 50;

    /** A web request reads at most this many changed objects itself; more are read by the background command */
    const WEB_BUILD_LIMIT = 300;

    /** Seconds the marker of a background build keeps another from starting */
    const BUILD_MARKER_SECONDS = 900;

    /** @var bool the cache is filled in the background and this request has no help content */
    protected $building = false;

    /** @var array|null the loaded set with the topics' text fields: topics, figures, maps, glossary */
    protected $loaded = null;

    /** @var array|null the cache's data without the text fields */
    protected $index = null;

    /** @var array|null lookups() */
    protected $lookups = null;

    /** @var array|null object id => the text fields of a topic */
    protected $fields = null;

    /** @var array object id => eZContentObject|null, loaded when asked for */
    protected $objects = array();

    /** @var array|false|null state id => identifier of the help_status group (false: no such group) */
    protected $helpStates = null;

    /** @var array|null searchWords() of this request */
    protected $searchWords = null;

    /** @var array section id => identifier */
    protected $sectionIdentifiers = array();

    /**
     * The repository of this request
     *
     * @return expHelpContentRepository
     */
    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * Forgets what was loaded (after a change, between requests of a persistent worker)
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    /**
     * Whether the content model is installed: the classes exist and the help root is there
     *
     * @return bool
     */
    public static function isInstalled()
    {
        return self::rootNode() !== null && eZContentClass::fetchByIdentifier( 'help_task', false ) !== null;
    }

    /**
     * The node of the help root (remote_id help-root), null when the tree is not created
     *
     * @return eZContentObjectTreeNode|null
     */
    public static function rootNode()
    {
        $object = eZContentObject::fetchByRemoteID( expHelpContentSchema::ROOT );
        if ( !$object instanceof eZContentObject )
            return null;
        $node = $object->attribute( 'main_node' );
        return $node instanceof eZContentObjectTreeNode ? $node : null;
    }

    /**
     * Every help object below the help root, as arrays, with the text fields of the topics
     *
     * The rows come from the persistent cache (cacheFile()) while the help subtree is unchanged (stamp()); after a
     * change only the objects that changed are read again. The content objects are loaded only when asked for
     * (object()).
     *
     * @return array array( 'topics' => [], 'figures' => [], 'maps' => [], 'glossary' => [], 'objects' => [id => eZContentObject] )
     */
    public function load()
    {
        if ( $this->loaded !== null )
            return $this->loaded + array( 'objects' => $this->objects );
        $this->ensure( true );
        $set = $this->index;
        $fields = $this->fields;
        foreach ( $set['topics'] as $i => $topic )
            $set['topics'][$i]['fields'] = isset( $fields[$topic['object_id']] ) ? $fields[$topic['object_id']] : array();
        $this->loaded = array( 'topics' => $set['topics'], 'figures' => $set['figures'], 'maps' => $set['maps'], 'glossary' => $set['glossary'] );
        return $this->loaded + array( 'objects' => $this->objects );
    }

    /**
     * Every help object below the help root, as arrays, without the text fields of the topics ('fields'): what lists,
     * counts and permission checks need, read without the texts
     *
     * @return array array( 'topics' => [], 'figures' => [], 'maps' => [], 'glossary' => [] )
     */
    public function index()
    {
        if ( $this->loaded !== null )
            return $this->loaded;
        $this->ensure( false );
        return array( 'topics' => $this->index['topics'], 'figures' => $this->index['figures'], 'maps' => $this->index['maps'],
                      'glossary' => $this->index['glossary'] );
    }

    /**
     * The data the permissions are decided with (expHelpContentAccess): id, class_id, section_id, owner_id, status,
     * main_node_id, state_ids, locations (path, depth); null for an object that is not loaded
     *
     * @param int $objectId
     * @return array|null
     */
    public function accessData( $objectId )
    {
        // a set given from outside (a subclass that sets $loaded) has no such data: the objects decide
        if ( $this->index === null && $this->loaded !== null )
            return null;
        $this->ensure( false );
        return isset( $this->index['access'][(int)$objectId] ) ? $this->index['access'][(int)$objectId] : null;
    }

    /**
     * Where each object and help_id is in the lists of index() and load() (the same order)
     *
     * @return array array( 'by_object_id' => id => array( kind, position ), 'by_help_id' => help_id => position )
     */
    protected function lookups()
    {
        if ( $this->lookups === null )
        {
            $set = $this->index();
            $this->lookups = array( 'by_object_id' => array(), 'by_help_id' => array() );
            foreach ( array( 'topics', 'figures', 'maps', 'glossary' ) as $kind )
            {
                foreach ( isset( $set[$kind] ) ? $set[$kind] : array() as $i => $row )
                {
                    if ( !isset( $this->lookups['by_object_id'][(int)$row['object_id']] ) )
                        $this->lookups['by_object_id'][(int)$row['object_id']] = array( $kind, $i );
                }
            }
            foreach ( isset( $set['topics'] ) ? $set['topics'] : array() as $i => $row )
            {
                // the first topic with a help_id, as the list was searched before
                if ( !isset( $this->lookups['by_help_id'][(string)$row['help_id']] ) )
                    $this->lookups['by_help_id'][(string)$row['help_id']] = $i;
            }
        }
        return $this->lookups;
    }

    /**
     * The topic with this help_id (whatever its state), null when there is none
     *
     * @param string $helpId
     * @return array|null
     */
    public function topic( $helpId )
    {
        $lookups = $this->lookups();
        if ( !isset( $lookups['by_help_id'][(string)$helpId] ) )
            return null;
        $set = $this->load();
        return $set['topics'][$lookups['by_help_id'][(string)$helpId]];
    }

    /**
     * The manual or chapter with this remote_id or object id, null when there is none
     *
     * @param string|int $key
     * @return array|null
     */
    public function map( $key )
    {
        foreach ( $this->index()['maps'] as $map )
        {
            if ( $map['remote_id'] === (string)$key || ( is_int( $key ) && $map['object_id'] === $key ) )
                return $map;
        }
        return null;
    }

    /**
     * Any loaded help object by object id: array( kind => topics|figures|maps|glossary, item => array ) or null. A
     * topic comes with its text fields.
     *
     * @param int $objectId
     * @return array|null
     */
    public function byObjectId( $objectId )
    {
        $lookups = $this->lookups();
        if ( !isset( $lookups['by_object_id'][(int)$objectId] ) )
            return null;
        list( $kind, $i ) = $lookups['by_object_id'][(int)$objectId];
        $set = $kind === 'topics' ? $this->load() : $this->index();
        return array( 'kind' => $kind, 'item' => $set[$kind][$i] );
    }

    /**
     * The content object of a loaded help object
     *
     * @param int $objectId
     * @return eZContentObject|null
     */
    public function object( $objectId )
    {
        $objectId = (int)$objectId;
        // a set given from outside with its objects
        if ( $this->index === null && isset( $this->loaded['objects'] ) )
            return isset( $this->loaded['objects'][$objectId] ) ? $this->loaded['objects'][$objectId] : null;
        if ( !array_key_exists( $objectId, $this->objects ) )
        {
            $lookups = $this->lookups();
            $object = isset( $lookups['by_object_id'][$objectId] ) && $this->index !== null ? eZContentObject::fetch( $objectId ) : null;
            $this->objects[$objectId] = $object instanceof eZContentObject ? $object : null;
        }
        return $this->objects[$objectId];
    }

    /**
     * The words of every topic for the search (expHelpContentProvider::searchWordSets()), kept in a cache file of
     * their own next to the rows: computed again only for the topics whose texts changed
     *
     * @return array object id => searchWordSets()
     */
    public function searchWords()
    {
        if ( $this->searchWords !== null )
            return $this->searchWords;
        $set = $this->load();
        // a set given from outside: computed, not kept
        $root = $this->index !== null ? self::rootNode() : null;
        $file = $root !== null ? self::cacheFile( $root ) : null;
        $file = $file !== null ? dirname( $file ) . '/search-' . substr( basename( $file ), strlen( 'repository-' ) ) : null;
        $stamp = $this->index !== null ? $this->index['stamp'] : '';
        $cached = null;
        if ( $file !== null && $stamp !== '' )
        {
            clearstatcache( true, $file );
            $content = @file_get_contents( $file );
            $cached = $content !== false ? @unserialize( $content, array( 'allowed_classes' => false ) ) : false;
            if ( !is_array( $cached ) || !isset( $cached['format'], $cached['stamp'], $cached['keys'], $cached['words'] ) || $cached['format'] !== self::CACHE_FORMAT )
                $cached = null;
            elseif ( $cached['stamp'] === $stamp )
                return $this->searchWords = $cached['words'];
        }
        $words = array();
        $keys = array();
        $missing = array();
        foreach ( $set['topics'] as $topic )
        {
            $id = $topic['object_id'];
            $keys[$id] = md5( serialize( array( $topic['title'], $topic['help_id'], $topic['shortdesc'], $topic['fields'] ) ) );
            if ( $cached !== null && isset( $cached['keys'][$id], $cached['words'][$id] ) && $cached['keys'][$id] === $keys[$id] )
                $words[$id] = $cached['words'][$id];
            else
                $missing[] = $topic;
        }
        // a web request computes the words of a few changed topics, never of all of them (25 seconds): the background
        // command does, and until then this request finds no help topics (isBuilding())
        if ( $file !== null && $stamp !== '' && count( $missing ) > self::WEB_BUILD_LIMIT && self::isWebRequest()
             && self::backgroundCommand() !== null )
        {
            self::startBackgroundBuild( dirname( $file ) . '/repository-' . substr( basename( $file ), strlen( 'search-' ) ) );
            $this->building = true;
            return $this->searchWords = array();
        }
        foreach ( $missing as $topic )
            $words[$topic['object_id']] = expHelpContentProvider::searchWordSets( $topic );
        if ( $file !== null && $stamp !== '' )
        {
            $tmp = $file . '.' . getmypid() . '.' . mt_rand() . '.tmp';
            if ( @file_put_contents( $tmp, serialize( array( 'format' => self::CACHE_FORMAT, 'stamp' => $stamp, 'keys' => $keys, 'words' => $words ) ) ) !== false )
            {
                self::sharedMode( $tmp );
                if ( !@rename( $tmp, $file ) )
                    @unlink( $tmp );
            }
        }
        return $this->searchWords = $words;
    }

    // ---------------------------------------------------------------- the persistent cache

    /**
     * Makes sure the index (and with $withFields the text fields) of this request is there: from the memory of this
     * request, from the cache file while the stamp is the same, else read from the database (only what changed when
     * the cache file is there) and written to the cache file
     *
     * @param bool $withFields
     */
    protected function ensure( $withFields )
    {
        if ( $this->index !== null && ( !$withFields || $this->fields !== null ) )
            return;
        $root = self::rootNode();
        if ( $root === null )
        {
            $this->setData( self::emptyData( '' ) );
            $this->fields = array();
            return;
        }
        $file = self::cacheFile( $root );
        // the stamp first: a change while the rows are read makes the next request read them again
        $stamp = self::stamp( $root );
        if ( $this->index !== null && $this->index['stamp'] === $stamp && $stamp !== '' )
        {
            $fields = self::readCacheFile( $file, true );
            if ( $fields !== null && $fields['stamp'] === $stamp )
            {
                $this->fields = $fields['fields'];
                return;
            }
        }
        elseif ( $stamp !== '' && $file !== null )
        {
            $cached = self::readCacheFile( $file, $withFields );
            if ( $cached !== null && $cached['stamp'] === $stamp )
            {
                $this->setData( $cached );
                $this->fields = $withFields ? $cached['fields'] : null;
                return;
            }
        }
        // A web request never reads every help object (a cleared cache, a changed help class or section, or more
        // changed objects than WEB_BUILD_LIMIT): that took longer than Velocity's 30 seconds. It starts the command
        // "exp:help:content cache" in the background and answers as if there were no help content until the file is
        // there (isBuilding()). Where no background command can be started (no proc_open, setsid or command line
        // PHP) it reads them itself, as before.
        $web = $stamp !== '' && $file !== null && self::isWebRequest() && self::backgroundCommand() !== null;
        if ( $web && !self::canUpdate( self::readCacheFile( $file, false ) ) )
        {
            $this->startBuilding( $file );
            return;
        }
        $lock = $file !== null ? self::lock( $file ) : null;
        try
        {
            // another process may have written it while this one waited
            $cached = $file !== null ? self::readCacheFile( $file, true ) : null;
            if ( $cached !== null && $cached['stamp'] === $stamp && $stamp !== '' )
                $data = $cached;
            else
            {
                $data = $this->build( $root, $stamp, $cached, $web ? self::WEB_BUILD_LIMIT : 0 );
                if ( $data !== null && $file !== null && $stamp !== '' )
                {
                    self::writeCacheFile( $file, $data );
                    self::recordSiteAccess( $file );
                }
            }
        }
        finally
        {
            if ( $lock !== null )
            {
                flock( $lock, LOCK_UN );
                fclose( $lock );
            }
        }
        if ( $data === null )
        {
            $this->startBuilding( $file );
            return;
        }
        $this->setData( $data );
        $this->fields = $data['fields'];
    }

    /**
     * Whether the cache's data can be brought up to date by reading only the objects that changed: its format and
     * globalStamp() are the current ones
     *
     * @param array|null $data readCacheFile()
     * @return bool
     */
    protected static function canUpdate( $data )
    {
        return is_array( $data ) && isset( $data['format'], $data['global'] ) && $data['format'] === self::CACHE_FORMAT
            && $data['global'] === self::globalStamp();
    }

    /**
     * Whether this process answers a web request (Apache, PHP-FPM, Velocity's workers), not a command
     *
     * @return bool
     */
    protected static function isWebRequest()
    {
        return class_exists( 'eZExecution' ) && method_exists( 'eZExecution', 'isWebRequest' ) ? eZExecution::isWebRequest()
            : PHP_SAPI !== 'cli';
    }

    /**
     * This request goes on without help content while the background command reads it (isBuilding())
     *
     * @param string|null $file
     */
    protected function startBuilding( $file )
    {
        if ( $file !== null )
            self::startBackgroundBuild( $file );
        $this->building = true;
        $this->setData( self::emptyData( '' ) );
        $this->fields = array();
    }

    /**
     * Whether this request has no help content because the cache is being filled in the background
     *
     * @return bool
     */
    public function isBuilding()
    {
        return $this->building;
    }

    /**
     * Starts "php bin/php/exphelpcontent.php cache -s <this siteaccess>" detached (setsid), once: a marker file next to
     * the cache file (<file>.building) says it runs, and only one older than BUILD_MARKER_SECONDS lets another start
     *
     * @param string $file the cache file
     * @return bool whether a build runs now (started here or before)
     */
    public static function startBackgroundBuild( $file )
    {
        $marker = $file . '.building';
        clearstatcache( true, $marker );
        if ( is_file( $marker ) && filemtime( $marker ) > time() - self::BUILD_MARKER_SECONDS )
            return true;
        if ( !is_dir( dirname( $file ) ) )
            eZDir::mkdir( dirname( $file ), false, true );
        @unlink( $marker );
        $handle = @fopen( $marker, 'x' );
        if ( $handle === false )
            return true; // another request was faster
        fwrite( $handle, (string)time() );
        fclose( $handle );
        self::sharedMode( $marker );
        $command = self::backgroundCommand();
        if ( $command === null )
        {
            eZDebug::writeWarning( 'Help content: no PHP command line binary or setsid to fill the cache in the background; run ./console exp:help:content cache', __METHOD__ );
            return false;
        }
        $process = @proc_open( $command, array( 0 => array( 'file', '/dev/null', 'r' ), 1 => array( 'file', '/dev/null', 'w' ),
                                                2 => array( 'file', '/dev/null', 'w' ) ), $pipes, rtrim( eZSys::rootDir(), '/' ) ?: getcwd() );
        $code = is_resource( $process ) ? proc_close( $process ) : 127;
        if ( $code !== 0 )
        {
            @unlink( $marker );
            eZDebug::writeWarning( 'Help content: the cache could not be filled in the background (exit ' . $code . ')', __METHOD__ );
            return false;
        }
        eZDebug::writeNotice( 'Help content: the cache is filled in the background: ' . implode( ' ', $command ), __METHOD__ );
        return true;
    }

    /**
     * Ends the marker of startBackgroundBuild() (the command "cache" when it is done)
     *
     * @param string $file
     */
    public static function finishBackgroundBuild( $file )
    {
        @unlink( $file . '.building' );
    }

    /**
     * The background command: setsid -f <php> bin/php/exphelpcontent.php cache -s <siteaccess> --allow-root-user;
     * null when there is no setsid, no command line PHP or no siteaccess
     *
     * @return string[]|null
     */
    public static function backgroundCommand()
    {
        $setsid = is_executable( '/usr/bin/setsid' ) ? '/usr/bin/setsid' : ( is_executable( '/bin/setsid' ) ? '/bin/setsid' : '' );
        $php = self::commandLinePhp();
        $access = class_exists( 'eZSiteAccess' ) ? eZSiteAccess::current() : null;
        $siteaccess = is_array( $access ) && isset( $access['name'] ) ? (string)$access['name'] : '';
        if ( $setsid === '' || $php === '' || !preg_match( '/^[A-Za-z0-9_-]{1,100}$/', $siteaccess ) || !function_exists( 'proc_open' ) )
            return null;
        return array( $setsid, '-f', $php, 'bin/php/exphelpcontent.php', 'cache', '-s', $siteaccess, '--allow-root-user', '--no-colors' );
    }

    /**
     * The command line PHP: exphelp.ini [ContentSettings] CachePhpBinary, else this process's binary when it is the
     * command line one (Velocity), else the php next to PHP-FPM's (<prefix>/sbin/php-fpm: <prefix>/bin/php), else
     * <PHP_BINDIR>/php
     *
     * @return string '' when none is found
     */
    public static function commandLinePhp()
    {
        $configured = class_exists( 'expHelpContentSchema' ) ? trim( (string)expHelpContentSchema::setting( 'CachePhpBinary', '' ) ) : '';
        $candidates = array();
        if ( $configured !== '' )
            $candidates[] = $configured;
        $binary = (string)PHP_BINARY;
        if ( $binary !== '' && preg_match( '/^php[0-9.]*$/', basename( $binary ) ) )
            $candidates[] = $binary;
        if ( $binary !== '' )
            $candidates[] = dirname( dirname( $binary ) ) . '/bin/php';
        $candidates[] = PHP_BINDIR . '/php';
        foreach ( $candidates as $candidate )
        {
            if ( is_file( $candidate ) && is_executable( $candidate ) )
                return $candidate;
        }
        return '';
    }

    /**
     * Remembers which siteaccess uses which cache file (<cache>/siteaccesses.json): exp:velocity deploy fills the
     * cache of each (siteAccessesToFill())
     *
     * @param string $file
     */
    protected static function recordSiteAccess( $file )
    {
        $access = class_exists( 'eZSiteAccess' ) ? eZSiteAccess::current() : null;
        $siteaccess = is_array( $access ) && isset( $access['name'] ) ? (string)$access['name'] : '';
        if ( !preg_match( '/^[A-Za-z0-9_-]{1,100}$/', $siteaccess ) )
            return;
        $record = dirname( $file ) . '/siteaccesses.json';
        $list = self::recordedSiteAccesses( dirname( $file ) );
        if ( isset( $list[basename( $file )] ) && $list[basename( $file )] === $siteaccess )
            return;
        $list[basename( $file )] = $siteaccess;
        $tmp = $record . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $tmp, json_encode( $list, JSON_PRETTY_PRINT ) ) !== false )
        {
            self::sharedMode( $tmp );
            if ( !@rename( $tmp, $record ) )
                @unlink( $tmp );
        }
    }

    /**
     * @param string|null $dir the cache directory (default cacheDirectory())
     * @return array cache file name => siteaccess
     */
    public static function recordedSiteAccesses( $dir = null )
    {
        $dir = $dir !== null ? $dir : self::cacheDirectory();
        if ( $dir === null )
            return array();
        $list = @json_decode( (string)@file_get_contents( $dir . '/siteaccesses.json' ), true );
        $out = array();
        foreach ( is_array( $list ) ? $list : array() as $name => $siteaccess )
        {
            if ( is_string( $siteaccess ) && preg_match( '/^[A-Za-z0-9_-]{1,100}$/', $siteaccess ) )
                $out[(string)$name] = $siteaccess;
        }
        return $out;
    }

    /**
     * The siteaccesses whose help cache exp:velocity deploy fills: exphelp.ini [ContentSettings]
     * CacheSiteAccessList[], and those that have used the cache (recordedSiteAccesses()); only siteaccesses of
     * site.ini [SiteAccessSettings] AvailableSiteAccessList[]
     *
     * @return string[]
     */
    public static function siteAccessesToFill()
    {
        $list = class_exists( 'expHelpContentSchema' ) ? (array)expHelpContentSchema::setting( 'CacheSiteAccessList', array() ) : array();
        $list = array_merge( $list, array_values( self::recordedSiteAccesses() ) );
        $ini = eZINI::instance();
        $available = $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ? (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) : array();
        $out = array();
        foreach ( $list as $siteaccess )
        {
            $siteaccess = trim( (string)$siteaccess );
            if ( $siteaccess !== '' && ( !$available || in_array( $siteaccess, $available, true ) ) && !in_array( $siteaccess, $out, true ) )
                $out[] = $siteaccess;
        }
        return $out;
    }

    /**
     * Reads every help object from the database, without the persistent cache (the validator's view after a change
     * made in this process, and the measurements)
     *
     * @return array as load()
     */
    public function loadFromDatabase()
    {
        $root = self::rootNode();
        $data = $root !== null ? $this->build( $root, '', null ) : self::emptyData( '' );
        $this->setData( $data );
        $this->fields = $data['fields'];
        $this->loaded = null;
        return $this->load();
    }

    protected function setData( array $data )
    {
        $this->index = $data;
        unset( $this->index['fields'] );
        $this->lookups = null;
        $this->loaded = null;
    }

    /**
     * @param string $stamp
     * @return array the shape of the cache: stamp, topics, figures, maps, glossary (rows without the topics' text
     *               fields), access (object id => accessData()), signatures (object id => string), fields (object id =>
     *               the text fields of a topic)
     */
    protected static function emptyData( $stamp )
    {
        return array( 'format' => self::CACHE_FORMAT, 'stamp' => (string)$stamp, 'topics' => array(), 'figures' => array(),
                      'maps' => array(), 'glossary' => array(), 'access' => array(), 'signatures' => array(), 'fields' => array() );
    }

    /**
     * Reads the help objects from the database: the objects whose signature is the same as in $previous are taken
     * from it, the others are loaded (all of them without $previous)
     *
     * @param eZContentObjectTreeNode $root
     * @param string $stamp
     * @param array|null $previous the cache's data, also of an older stamp
     * @param int $limit read at most this many objects (0: no limit)
     * @return array|null the shape of emptyData(); null when more than $limit objects would have to be read
     */
    protected function build( eZContentObjectTreeNode $root, $stamp, $previous, $limit = 0 )
    {
        $data = self::emptyData( $stamp );
        $rows = eZContentObjectTreeNode::subTreeByNodeID(
            array( 'ClassFilterType' => 'include', 'ClassFilterArray' => expHelpContentSchema::classIdentifiers(), 'Limitation' => array(),
                   'IgnoreVisibility' => true, 'MainNodeOnly' => true, 'AsObject' => false, 'SortBy' => array( 'path', true ) ),
            (int)$root->attribute( 'node_id' ) );
        $rows = is_array( $rows ) ? $rows : array();
        $ids = array();
        foreach ( $rows as $row )
            $ids[] = (int)$row['id'];
        $states = self::fetchGrouped( 'SELECT contentobject_id, contentobject_state_id FROM ezcobj_state_link WHERE ', $ids );
        $locations = self::fetchGrouped( 'SELECT contentobject_id, node_id, path_string, depth FROM ezcontentobject_tree WHERE ', $ids );
        $usable = is_array( $previous ) && isset( $previous['format'], $previous['global'] ) && $previous['format'] === self::CACHE_FORMAT
            && $previous['global'] === self::globalStamp();
        $previousRows = array();
        if ( $usable )
        {
            foreach ( array( 'topics', 'figures', 'maps', 'glossary' ) as $kind )
            {
                foreach ( $previous[$kind] as $row )
                    $previousRows[$row['object_id']] = $row;
            }
        }
        // the objects to load: new, or changed since the cache was written
        $load = array();
        $access = array();
        foreach ( $rows as $row )
        {
            $id = (int)$row['id'];
            $stateIds = array();
            foreach ( isset( $states[$id] ) ? $states[$id] : array() as $state )
                $stateIds[] = (int)$state['contentobject_state_id'];
            sort( $stateIds );
            $paths = array();
            foreach ( isset( $locations[$id] ) ? $locations[$id] : array() as $location )
                $paths[(int)$location['node_id']] = array( 'path' => (string)$location['path_string'], 'depth' => (int)$location['depth'] );
            ksort( $paths );
            $access[$id] = array( 'id' => $id, 'class_id' => (int)$row['contentclass_id'], 'section_id' => (int)$row['section_id'],
                                  'owner_id' => (int)$row['owner_id'], 'status' => (int)$row['status'], 'main_node_id' => (int)$row['main_node_id'],
                                  'state_ids' => $stateIds, 'locations' => array_values( $paths ) );
            $signature = md5( serialize( array( $access[$id], (int)$row['modified'], (int)$row['published'], (int)$row['current_version'],
                                                (int)$row['language_mask'], (string)$row['object_remote_id'], (int)$row['node_id'] ) ) );
            $data['signatures'][$id] = $signature;
            if ( !$usable || !isset( $previousRows[$id], $previous['signatures'][$id] ) || $previous['signatures'][$id] !== $signature )
                $load[] = $id;
        }
        if ( $limit > 0 && count( $load ) > $limit )
            return null;
        $stateIds = array();
        foreach ( $access as $id => $item )
            $stateIds[$id] = $item['state_ids'];
        $loaded = $this->loadRows( $load, $stateIds );
        $load = array_flip( $load );
        foreach ( $rows as $row )
        {
            $id = (int)$row['id'];
            if ( isset( $loaded[$id] ) )
            {
                $item = $loaded[$id];
                $item['node_id'] = (int)$row['node_id'];
            }
            elseif ( isset( $previousRows[$id] ) && !isset( $load[$id] ) )
            {
                $item = $previousRows[$id];
                if ( isset( $previous['fields'][$id] ) )
                    $item['fields'] = $previous['fields'][$id];
            }
            else
                continue; // gone between the two reads; the next request finds the new stamp
            if ( array_key_exists( 'fields', $item ) )
            {
                $data['fields'][$id] = $item['fields'];
                unset( $item['fields'] );
            }
            $data['access'][$id] = $access[$id];
            switch ( $item['class'] )
            {
                case 'help_screenshot':
                    $data['figures'][] = $item;
                    break;
                case 'help_glossary_entry':
                    $data['glossary'][] = $item;
                    break;
                case 'help_manual':
                case 'help_section':
                    $data['maps'][] = $item;
                    break;
                default:
                    $data['topics'][] = $item;
            }
        }
        $data['global'] = self::globalStamp();
        return $data;
    }

    /**
     * The rows (toArray()) of objects, loaded with their data maps (fillAttributes()) a chunk at a time. A few
     * objects (a change) stay loaded for object(); after a large chunk the objects are let go.
     *
     * @param int[] $ids
     * @param array $stateIds object id => the object's state ids (read with the others; null: each object asks)
     * @return array id => row
     */
    protected function loadRows( array $ids, ?array $stateIds = null )
    {
        $rows = array();
        $keep = count( $ids ) <= self::KEEP_OBJECTS;
        foreach ( array_chunk( $ids, self::LOAD_CHUNK ) as $chunk )
        {
            $list = eZContentObject::fetchIDArray( $chunk );
            $list = is_array( $list ) ? array_values( array_filter( $list, function ( $o ) { return $o instanceof eZContentObject; } ) ) : array();
            if ( $list )
                self::fillAttributes( $list );
            foreach ( $list as $object )
            {
                $id = (int)$object->attribute( 'id' );
                $rows[$id] = $this->toArray( $object, null, $stateIds !== null ? ( isset( $stateIds[$id] ) ? $stateIds[$id] : array() ) : null );
                if ( $keep )
                    $this->objects[$id] = $object;
            }
            unset( $list );
            if ( !$keep )
                eZContentObject::clearCache( $chunk );
        }
        return $rows;
    }

    /**
     * eZContentObject::fillNodeListAttributes() for many objects: the same attributes, asked one version and language
     * at a time with the attribute table first, so that the database finds them by its index on (contentobject_id,
     * version, language_code). The kernel's query, all versions in one OR, made SQLite go through every attribute of
     * the help classes for each chunk (30 s for the help's 5700 objects; this way about 1 s). MongoDB keeps the
     * kernel's own path.
     *
     * @param eZContentObject[] $objects
     */
    protected static function fillAttributes( array $objects )
    {
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            eZContentObject::fillNodeListAttributes( $objects );
            return;
        }
        $keys = array();
        $languages = array();
        foreach ( $objects as $object )
        {
            $id = (int)$object->attribute( 'id' );
            $languages[$id] = $object->currentLanguage();
            $keys[] = array( 'id' => $id, 'version' => (int)$object->attribute( 'current_version' ), 'language' => $languages[$id] );
        }
        $attributes = array();
        foreach ( eZContentObject::attributeListConditions( $keys, $db, 1, 500 ) as $where )
        {
            $rows = $db->arrayQuery(
                'SELECT ezcontentobject_attribute.*, ezcontentclass_attribute.identifier AS identifier, ezcontentclass_attribute.placement AS hd_placement ' .
                'FROM ezcontentobject_attribute CROSS JOIN ezcontentclass_attribute ' .
                "WHERE ezcontentclass_attribute.version = 0 AND ezcontentclass_attribute.id = ezcontentobject_attribute.contentclassattribute_id AND $where" );
            if ( !is_array( $rows ) )
            {
                // a database that does not take the query: the kernel's
                eZContentObject::fillNodeListAttributes( $objects );
                return;
            }
            foreach ( $rows as $row )
                $attributes[(int)$row['contentobject_id']][] = $row;
        }
        foreach ( $objects as $object )
        {
            $id = (int)$object->attribute( 'id' );
            $rows = isset( $attributes[$id] ) ? $attributes[$id] : array();
            // the kernel's order: by the class attribute's placement
            usort( $rows, function ( $a, $b ) { return (int)$a['hd_placement'] <=> (int)$b['hd_placement']; } );
            $list = array();
            foreach ( $rows as $row )
            {
                unset( $row['hd_placement'] );
                $attribute = new eZContentObjectAttribute( $row );
                $attribute->setContentClassAttributeIdentifier( $row['identifier'] );
                $list[] = $attribute;
            }
            $object->setContentObjectAttributes( $list, $object->attribute( 'current_version' ), $languages[$id] );
        }
    }

    /**
     * Rows of a query per object id, asked in chunks
     *
     * @param string $select "SELECT ... WHERE "
     * @param int[] $ids
     * @return array object id => list of rows
     */
    protected static function fetchGrouped( $select, array $ids )
    {
        $db = eZDB::instance();
        $grouped = array();
        foreach ( array_chunk( $ids, 500 ) as $chunk )
        {
            $rows = $db->arrayQuery( $select . $db->generateSQLINStatement( $chunk, 'contentobject_id', false, true, 'int' ) );
            foreach ( is_array( $rows ) ? $rows : array() as $row )
                $grouped[(int)$row['contentobject_id']][] = $row;
        }
        return $grouped;
    }

    /**
     * What the rows of all help objects depend on, in a few cheap queries: every location of the objects below the
     * help root with its object (count, modification and publication times, versions, sections, owners, classes,
     * statuses, language masks, parents, depths, main nodes), their states, and globalStamp(). It changes with a
     * publish, a translation (added or removed), a move, a new or removed location, a removal, a state or section
     * change. '' when it cannot be read (the cache is then not used).
     *
     * @param eZContentObjectTreeNode $root
     * @return string
     */
    public static function stamp( eZContentObjectTreeNode $root )
    {
        $db = eZDB::instance();
        $path = $db->escapeString( (string)$root->attribute( 'path_string' ) );
        $below = "SELECT contentobject_id FROM ezcontentobject_tree WHERE path_string LIKE '$path%'";
        $objects = $db->arrayQuery(
            'SELECT COUNT(*) AS c, SUM(o.modified) AS m, SUM(o.published) AS pu, SUM(o.current_version) AS v, SUM(o.section_id) AS s, ' .
            'SUM(o.owner_id) AS ow, SUM(o.contentclass_id) AS cl, SUM(o.status) AS st, SUM(o.language_mask) AS lm, ' .
            'SUM(t.parent_node_id) AS p, SUM(t.depth) AS d, SUM(t.main_node_id) AS mn, SUM(t.node_id) AS n, MAX(o.modified) AS mx ' .
            'FROM ezcontentobject_tree t INNER JOIN ezcontentobject o ON o.id = t.contentobject_id ' .
            "WHERE t.contentobject_id IN ( $below )" );
        $states = $db->arrayQuery(
            'SELECT COUNT(*) AS c, SUM(contentobject_state_id * contentobject_id) AS s, SUM(contentobject_state_id) AS t ' .
            "FROM ezcobj_state_link WHERE contentobject_id IN ( $below )" );
        if ( !is_array( $objects ) || !isset( $objects[0] ) || !is_array( $states ) || !isset( $states[0] ) )
            return '';
        return md5( json_encode( array( $root->attribute( 'node_id' ), $path, array_values( $objects[0] ), array_values( $states[0] ), self::globalStamp() ) ) );
    }

    /**
     * What every row depends on beyond its object: the help classes (their options, for selections), the sections'
     * identifiers. A change here reads every object again.
     *
     * @return string
     */
    protected static function globalStamp()
    {
        $db = eZDB::instance();
        $identifiers = array();
        foreach ( expHelpContentSchema::classIdentifiers() as $identifier )
            $identifiers[] = "'" . $db->escapeString( $identifier ) . "'";
        $classes = $db->arrayQuery( 'SELECT id, modified FROM ezcontentclass WHERE version = 0 AND identifier IN ( ' . implode( ', ', $identifiers ) . ' ) ORDER BY id' );
        $sections = $db->arrayQuery( 'SELECT id, identifier FROM ezsection ORDER BY id' );
        return md5( json_encode( array( is_array( $classes ) ? $classes : null, is_array( $sections ) ? $sections : null ) ) );
    }

    /**
     * The cache file of this help root, languages and database ('' setting HelpSettings CacheDir: none)
     *
     * @param eZContentObjectTreeNode $root
     * @return string|null
     */
    public static function cacheFile( eZContentObjectTreeNode $root )
    {
        $dir = self::cacheDirectory();
        if ( $dir === null )
            return null;
        $ini = eZINI::instance();
        $key = array( self::CACHE_FORMAT, (int)$root->attribute( 'node_id' ), (string)$root->attribute( 'path_string' ),
                      eZContentLanguage::prioritizedLanguageCodes(),
                      $ini->variable( 'RegionalSettings', 'ShowUntranslatedObjects' ),
                      $ini->variable( 'DatabaseSettings', 'DatabaseImplementation' ),
                      $ini->variable( 'DatabaseSettings', 'Server' ), $ini->variable( 'DatabaseSettings', 'Database' ) );
        return $dir . '/repository-' . md5( json_encode( $key ) ) . '.cache';
    }

    /**
     * The directory of the cache files: <help cache>/content
     *
     * @return string|null
     */
    public static function cacheDirectory()
    {
        if ( !class_exists( 'expHelp' ) )
            return null;
        $dir = expHelp::cacheDirectory();
        return is_string( $dir ) && $dir !== '' ? rtrim( $dir, '/' ) . '/content' : null;
    }

    /**
     * Removes the cache files (ezcache --clear-id=exphelpcontent, site.ini [Cache_exphelpcontent]); the next request
     * reads every help object again
     */
    public static function clearCache()
    {
        $dir = self::cacheDirectory();
        if ( $dir !== null && is_dir( $dir ) )
        {
            foreach ( array_merge( (array)glob( $dir . '/repository-*.cache' ), (array)glob( $dir . '/search-*.cache' ) ) as $file )
                @unlink( $file );
        }
        self::resetInstance();
    }

    /**
     * ezcache --purge: the same as clearCache()
     */
    public static function purgeCache()
    {
        self::clearCache();
    }

    /**
     * The cache file's data: the index part, and with $withFields the topics' text fields; null when it is missing,
     * of another format or damaged
     *
     * The file is "<format>\n<length of the index>\n<serialized index><serialized fields>", so that the index can
     * be read without the texts.
     *
     * @param string $file
     * @param bool $withFields
     * @return array|null
     */
    public static function readCacheFile( $file, $withFields )
    {
        clearstatcache( true, $file );
        $handle = @fopen( $file, 'rb' );
        if ( $handle === false )
            return null;
        try
        {
            if ( rtrim( (string)fgets( $handle ), "\n" ) !== self::CACHE_FORMAT )
                return null;
            $length = (int)fgets( $handle );
            if ( $length <= 0 )
                return null;
            $data = @unserialize( (string)stream_get_contents( $handle, $length ), array( 'allowed_classes' => false ) );
            if ( !is_array( $data ) || !isset( $data['stamp'] ) || !is_string( $data['stamp'] ) )
                return null;
            foreach ( array( 'topics', 'figures', 'maps', 'glossary', 'access', 'signatures' ) as $key )
            {
                if ( !isset( $data[$key] ) || !is_array( $data[$key] ) )
                    return null;
            }
            if ( $withFields )
            {
                $fields = @unserialize( (string)stream_get_contents( $handle ), array( 'allowed_classes' => false ) );
                if ( !is_array( $fields ) || !isset( $fields['stamp'] ) || $fields['stamp'] !== $data['stamp'] || !is_array( $fields['fields'] ) )
                    return null;
                $data['fields'] = $fields['fields'];
            }
            return $data;
        }
        finally
        {
            fclose( $handle );
        }
    }

    /**
     * Writes the cache file (a temporary file renamed over it: a reader sees the old or the new one, never a part)
     *
     * @param string $file
     * @param array $data the shape of emptyData()
     * @return bool
     */
    public static function writeCacheFile( $file, array $data )
    {
        $index = $data;
        unset( $index['fields'] );
        $index = serialize( $index );
        $content = self::CACHE_FORMAT . "\n" . strlen( $index ) . "\n" . $index
            . serialize( array( 'stamp' => $data['stamp'], 'fields' => $data['fields'] ) );
        if ( !is_dir( dirname( $file ) ) )
            eZDir::mkdir( dirname( $file ), false, true );
        $tmp = $file . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( !is_dir( dirname( $file ) ) || @file_put_contents( $tmp, $content ) === false )
            return false;
        self::sharedMode( $tmp );
        if ( !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            return false;
        }
        return true;
    }

    /**
     * An exclusive lock next to the cache file, so that one process reads the database while the others wait for
     * its file; null when it cannot be had (then each reads for itself)
     *
     * @param string $file
     * @return resource|null
     */
    protected static function lock( $file )
    {
        if ( !is_dir( dirname( $file ) ) )
            eZDir::mkdir( dirname( $file ), false, true );
        $existed = is_file( $file . '.lock' );
        $handle = @fopen( $file . '.lock', 'c' );
        if ( $handle === false )
            return null;
        if ( !$existed )
            self::sharedMode( $file . '.lock' );
        if ( !flock( $handle, LOCK_EX ) )
        {
            fclose( $handle );
            return null;
        }
        return $handle;
    }

    /**
     * The file permissions of the storage, so the web server's user and the command line's can both replace the file
     *
     * @param string $file
     */
    protected static function sharedMode( $file )
    {
        $mode = eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' );
        @chmod( $file, eZFile::fileMode( octdec( $mode ) ) );
    }

    /**
     * One object as an array (the shapes of expHelpContentValidator)
     *
     * @param eZContentObject $object
     * @param eZContentObjectTreeNode|null $node
     * @return array
     */
    public function toArray( eZContentObject $object, $node = null, ?array $stateIds = null )
    {
        $class = (string)$object->attribute( 'class_identifier' );
        $dataMap = $object->attribute( 'data_map' );
        $value = function ( $identifier ) use ( $dataMap ) {
            return isset( $dataMap[$identifier] ) ? self::attributeValue( $dataMap[$identifier] ) : null;
        };
        $state = '';
        if ( $stateIds !== null && ( $helpStates = $this->helpStateIdentifiers() ) !== null )
        {
            // the state ids read with the other objects' (build()): the identifier of the help_status state
            foreach ( $stateIds as $stateId )
            {
                if ( isset( $helpStates[(int)$stateId] ) )
                    $state = $helpStates[(int)$stateId];
            }
        }
        else
        {
            foreach ( $object->stateIdentifierArray() as $pair )
            {
                if ( strpos( $pair, expHelpContentSchema::STATE_GROUP . '/' ) === 0 )
                    $state = substr( $pair, strlen( expHelpContentSchema::STATE_GROUP ) + 1 );
            }
        }
        $row = array(
            'object_id' => (int)$object->attribute( 'id' ),
            'node_id' => $node instanceof eZContentObjectTreeNode ? (int)$node->attribute( 'node_id' ) : (int)$object->attribute( 'main_node_id' ),
            'class' => $class,
            'remote_id' => (string)$object->attribute( 'remote_id' ),
            'name' => (string)$object->attribute( 'name' ),
            'section' => $this->sectionIdentifier( (int)$object->attribute( 'section_id' ) ),
            'section_id' => (int)$object->attribute( 'section_id' ),
            'state' => $state,
            'modified' => (int)$object->attribute( 'modified' ),
            'published' => (int)$object->attribute( 'published' ),
            'language' => (string)$object->attribute( 'current_language' ),
        );
        if ( isset( expHelpContentSchema::TOPIC_TYPES[$class] ) )
        {
            $fields = array();
            foreach ( array_keys( expHelpContentSchema::textFields( $class ) ) as $field )
                $fields[$field] = (string)$value( $field );
            $row += array(
                'type' => expHelpContentSchema::TOPIC_TYPES[$class],
                'help_id' => trim( (string)$value( 'help_id' ) ),
                'title' => trim( (string)$value( 'title' ) ),
                'shortdesc' => trim( (string)$value( 'shortdesc' ) ),
                'audience' => (array)$value( 'audience' ),
                'platform' => (array)$value( 'platform' ),
                'version_from' => trim( (string)$value( 'version_from' ) ),
                'version_to' => trim( (string)$value( 'version_to' ) ),
                'review_date' => (int)$value( 'review_date' ),
                'owner' => (int)$value( 'owner' ),
                'related' => (array)$value( 'related' ),
                'figures' => (array)$value( 'figures' ),
                'fields' => $fields,
            );
        }
        elseif ( $class === 'help_screenshot' )
        {
            $image = isset( $dataMap['image'] ) ? $dataMap['image'] : null;
            $row += array(
                'alt_text' => trim( (string)$value( 'alt_text' ) ),
                'caption' => trim( (string)$value( 'caption' ) ),
                'platform' => (array)$value( 'platform' ),
                'version' => trim( (string)$value( 'version' ) ),
                'figure_language' => trim( (string)$value( 'language' ) ),
                'topic_field' => trim( (string)$value( 'section' ) ),
                'figure_id' => trim( (string)$value( 'figure_id' ) ),
                'has_image' => $image instanceof eZContentObjectAttribute && $image->attribute( 'has_content' ),
                'image_url' => self::imageUrl( $image ),
            );
        }
        elseif ( $class === 'help_glossary_entry' )
        {
            $row += array(
                'term' => trim( (string)$value( 'term' ) ),
                'definition' => (string)$value( 'definition' ),
                'avoid' => self::splitList( (string)$value( 'avoid' ) ),
                'audience' => (array)$value( 'audience' ),
                'note' => (string)$value( 'note' ),
            );
        }
        else
        {
            $row += array(
                'title' => trim( (string)$value( 'title' ) ),
                'subtitle' => trim( (string)$value( 'subtitle' ) ),
                'shortdesc' => trim( (string)$value( 'shortdesc' ) ),
                'audience' => $class === 'help_manual' ? (array)$value( 'audience' ) : array(),
                'edition' => trim( (string)$value( 'edition' ) ),
                'date' => (int)$value( 'date' ),
                'license' => trim( (string)$value( 'license' ) ),
                'authors' => (array)$value( 'authors' ),
                'cover' => (int)$value( 'cover' ),
                'topicrefs' => (array)$value( 'topicrefs' ),
            );
        }
        return $row;
    }

    /**
     * The value of an attribute as plain PHP: text, list of option names, list of object ids, an object id, a timestamp
     *
     * @param eZContentObjectAttribute $attribute
     * @return mixed
     */
    public static function attributeValue( eZContentObjectAttribute $attribute )
    {
        switch ( $attribute->attribute( 'data_type_string' ) )
        {
            case 'ezstring':
            case 'eztext':
                return (string)$attribute->attribute( 'data_text' );
            case 'ezselection':
                $ids = explode( '-', (string)$attribute->attribute( 'data_text' ) );
                $content = $attribute->attribute( 'class_content' );
                $names = array();
                foreach ( isset( $content['options'] ) ? $content['options'] : array() as $option )
                {
                    if ( (string)$attribute->attribute( 'data_text' ) !== '' && in_array( (string)$option['id'], $ids, true ) )
                        $names[] = (string)$option['name'];
                }
                return $names;
            case 'ezobjectrelationlist':
                $content = $attribute->content();
                $ids = array();
                foreach ( isset( $content['relation_list'] ) ? $content['relation_list'] : array() as $item )
                    $ids[] = (int)$item['contentobject_id'];
                return $ids;
            case 'ezobjectrelation':
            case 'ezdate':
                return (int)$attribute->attribute( 'data_int' );
            case 'ezauthor':
                $content = $attribute->content();
                $names = array();
                if ( is_object( $content ) )
                {
                    foreach ( (array)$content->attribute( 'author_list' ) as $author )
                        $names[] = (string)$author['name'];
                }
                return $names;
        }
        return $attribute->attribute( 'has_content' ) ? (string)$attribute->toString() : '';
    }

    /**
     * The URL of the image of a figure (its "original" alias), '' when there is none
     *
     * @param eZContentObjectAttribute|null $attribute
     * @return string
     */
    public static function imageUrl( $attribute )
    {
        if ( !$attribute instanceof eZContentObjectAttribute || !$attribute->attribute( 'has_content' ) )
            return '';
        $content = $attribute->content();
        if ( !is_object( $content ) )
            return '';
        $alias = $content->attribute( 'original' );
        return is_array( $alias ) && !empty( $alias['url'] ) ? '/' . ltrim( (string)$alias['url'], '/' ) : '';
    }

    /**
     * A list field: one value per line or separated by commas
     *
     * @param string $text
     * @return string[]
     */
    public static function splitList( $text )
    {
        $parts = preg_split( '/[\r\n,;]+/', (string)$text );
        return array_values( array_filter( array_map( 'trim', $parts ), 'strlen' ) );
    }

    /**
     * The states of the group help_status: state id => identifier; null when the group does not exist
     *
     * @return array|null
     */
    protected function helpStateIdentifiers()
    {
        if ( $this->helpStates === null )
        {
            $this->helpStates = false;
            $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
            if ( $group instanceof eZContentObjectStateGroup )
            {
                $this->helpStates = array();
                foreach ( $group->states() as $state )
                    $this->helpStates[(int)$state->attribute( 'id' )] = (string)$state->attribute( 'identifier' );
            }
        }
        return $this->helpStates !== false ? $this->helpStates : null;
    }

    protected function sectionIdentifier( $sectionId )
    {
        if ( !isset( $this->sectionIdentifiers[$sectionId] ) )
        {
            $section = eZSection::fetch( $sectionId );
            $this->sectionIdentifiers[$sectionId] = $section instanceof eZSection ? (string)$section->attribute( 'identifier' ) : '';
        }
        return $this->sectionIdentifiers[$sectionId];
    }
}

?>
