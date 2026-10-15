<?php
/**
 * File containing the expContentCheckReferences class.
 *
 * The reference index of the unreferenced list (doc/features/6.0/content-checks.md, "What counts as a reference"):
 * every object id, node id, remote id and path that something in the installation uses, collected from all the
 * places content can be reached from besides ezcontentobject_link, which expContentCheck::referenceExistsSQL()
 * covers in SQL:
 *
 *   attributes  the fields of every counting version (expContentCheck::REFERENCE_*_STATUSES) of a published or
 *               draft object: ezobjectrelation, ezobjectrelationlist, ngenhancedlink (no relation rows), and in
 *               any text: object_id/node_id of links and embeds, ezobject:// eznode:// ezcontent:// ezlocation://,
 *               exp-remote-location://, content/view/<mode>/<node>, content/download/<object>, storage file paths
 *               and internal paths of links;
 *   urls        the ezurl table (links of rich text and URL fields), custom URL aliases and URL wildcards;
 *   layouts     explayouts collection items (value_id of ez_location is a NODE id), dynamic collection queries
 *               (their parent node, list or tree, class filter; a query on the current location applies anywhere),
 *               block parameters, rule targets, in every status;
 *   ini         every settings file: values of settings whose block or name speaks of nodes, objects or roots, and
 *               content/view paths anywhere;
 *   templates   literal node and object ids in the templates of every design (a hint: an id a template computes
 *               cannot be known);
 *   roles       role assignment and policy limitations by subtree or node;
 *   tables      the columns of [UnreferencedSettings] ObjectColumns[], NodeColumns[], NodeListColumns[] (shop,
 *               information collection, notifications, bookmarks, pending actions, draft placements, the newsletter
 *               ...), each skipped when its table does not exist;
 *   newsletter  the newsletter's article pools (children of their parent nodes, by class).
 *
 * Building the index reads all of these once; it is kept in the cache for IndexTTL seconds, or built by the CLI
 * script bin/php/contentcheck.php (also a cronjob part) on installations where BuildOnRequest is false.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckReferences
{
    /** The cache file of the index, below the cache directory */
    const CACHE_FILE = 'contentcheck/references.php';

    /** The format of the index; an index of another format is built again */
    const FORMAT = 2;

    /** Settings and blocks whose names speak of content ids (INI files) */
    const INI_KEY_PATTERN = '/(node|location|object|root|parent|subtree|indexpage|defaultpage|startpage|toppage|container|folder|nexus|remote)/i';

    /** At most this many references are kept per id (the first ones; more only say the same) */
    const MAX_PER_ID = 2;

    /** @var array the index being built */
    protected $index;

    /** @var array path => array( from, label ) collected while building, resolved at the end */
    protected $paths = array();

    /** @var array storage file path => array( from, label ) */
    protected $files = array();

    /** @var array|null an index set by tests */
    protected static $testIndex = null;

    public function __construct()
    {
        $this->index = self::emptyIndex();
    }

    /**
     * @return array
     */
    public static function emptyIndex()
    {
        return array( 'format' => self::FORMAT, 'built' => 0, 'seconds' => 0.0,
                      'objects' => array(), 'nodes' => array(), 'remote' => array(), 'aliases' => array(),
                      'queries' => array(), 'counts' => array() );
    }

    // ------------------------------------------------------------------ the index and its cache

    /**
     * The reference index: from the cache while it is younger than IndexTTL, else built now (BuildOnRequest) or
     * the older one with 'stale' set (built by the CLI script or the cronjob on large installations).
     *
     * @param bool $fresh build it now whatever the cache holds (the trash action)
     * @return array the index, with 'stale' => bool; null when there is none and none may be built here
     */
    public static function index( $fresh = false )
    {
        if ( self::$testIndex !== null )
            return array_merge( self::$testIndex, array( 'stale' => false ) );
        $ttl = expContentCheck::intSetting( 'UnreferencedSettings', 'IndexTTL', 600, 0, 604800 );
        $buildHere = expContentCheck::setting( 'UnreferencedSettings', 'BuildOnRequest', 'true' ) === 'true';
        $cached = self::readCache();
        $young = is_array( $cached ) && ( time() - (int)$cached['built'] ) < $ttl;
        if ( $cached && $young && !$fresh )
            return array_merge( $cached, array( 'stale' => false ) );
        if ( !$buildHere )
        {
            // a fresh index is required to move anything; the list may show an older one, marked stale
            if ( $fresh )
                return $young ? array_merge( $cached, array( 'stale' => false ) ) : null;
            return $cached ? array_merge( $cached, array( 'stale' => true ) ) : null;
        }
        $index = self::build();
        self::writeCache( $index );
        return array_merge( $index, array( 'stale' => false ) );
    }

    /**
     * @param array|null $index an index for tests; null goes back to the real one
     */
    public static function setTestIndex( $index )
    {
        self::$testIndex = $index === null ? null : array_merge( self::emptyIndex(), $index );
    }

    /**
     * @return string
     */
    public static function cachePath()
    {
        return eZSys::cacheDirectory() . '/' . self::CACHE_FILE;
    }

    /**
     * @return array|null
     */
    public static function readCache()
    {
        $handler = eZClusterFileHandler::instance( self::cachePath() );
        if ( !$handler->exists() )
            return null;
        $data = @unserialize( (string)$handler->fetchContents(), array( 'allowed_classes' => false ) );
        return is_array( $data ) && isset( $data['format'] ) && (int)$data['format'] === self::FORMAT ? $data : null;
    }

    /**
     * @param array $index
     */
    public static function writeCache( array $index )
    {
        eZClusterFileHandler::instance( self::cachePath() )->storeContents( serialize( $index ), 'contentcheck', 'php' );
    }

    /**
     * Removes the cached index (after a move to the trash, which changes what refers to what).
     */
    public static function clearCache()
    {
        $handler = eZClusterFileHandler::instance( self::cachePath() );
        if ( $handler->exists() )
            $handler->delete();
    }

    /**
     * Builds the index from every source.
     *
     * @return array
     */
    public static function build()
    {
        $start = microtime( true );
        $builder = new self();
        $sources = array( 'attributes', 'urls', 'layouts', 'ini', 'templates', 'roles', 'tables', 'newsletter' );
        foreach ( $sources as $source )
        {
            $before = $builder->total();
            try
            {
                $builder->{'from' . ucfirst( $source )}();
            }
            catch ( Throwable $e )
            {
                // a source that fails leaves the index incomplete: the list must then not offer anything
                eZDebug::writeError( 'Content checks: reference source ' . $source . ' failed: ' . $e->getMessage(), __METHOD__ );
                $builder->index['failed'][] = $source;
            }
            $builder->index['counts'][$source] = $builder->total() - $before;
        }
        $builder->resolvePaths();
        $builder->index['built'] = time();
        $builder->index['seconds'] = round( microtime( true ) - $start, 2 );
        return $builder->index;
    }

    /**
     * @return int
     */
    protected function total()
    {
        return count( $this->index['objects'] ) + count( $this->index['nodes'] ) + count( $this->index['remote'] ) +
               count( $this->index['queries'] ) + count( $this->paths ) + count( $this->files );
    }

    // ------------------------------------------------------------------ adding references

    /**
     * Adds what an extractor found.
     *
     * @param array $references list of array( 'type' => object|node|remote|path|file, 'value' => int|string )
     * @param int $from the object that refers (0: not an object), so an object referring to itself does not count
     * @param string $label where the reference is, for the CLI's --why
     */
    public function addAll( array $references, $from, $label )
    {
        foreach ( $references as $reference )
            $this->add( $reference['type'], $reference['value'], $from, $label );
    }

    /**
     * @param string $type object|node|remote|path|file
     * @param int|string $value
     * @param int $from
     * @param string $label
     */
    public function add( $type, $value, $from, $label )
    {
        $entry = array( (int)$from, (string)$label );
        switch ( $type )
        {
            case 'object':
            case 'node':
                $value = (int)$value;
                if ( $value <= 0 )
                    return;
                $list =& $this->index[$type === 'object' ? 'objects' : 'nodes'];
                break;
            case 'remote':
                $value = (string)$value;
                if ( $value === '' )
                    return;
                $list =& $this->index['remote'];
                break;
            case 'path':
                $value = self::normalisePath( $value );
                if ( $value === '' )
                    return;
                $list =& $this->paths;
                break;
            case 'file':
                $value = ltrim( (string)$value, '/' );
                if ( $value === '' )
                    return;
                $list =& $this->files;
                break;
            default:
                return;
        }
        if ( !isset( $list[$value] ) )
            $list[$value] = array( $entry );
        else if ( count( $list[$value] ) < self::MAX_PER_ID && $list[$value][0][0] !== $entry[0] )
            $list[$value][] = $entry;
        unset( $list );
    }

    /**
     * Adds a dynamic collection: the children (list) or the whole subtree (tree) of a node, or of any node (0), of
     * the classes it lets through.
     *
     * @param array $query array( 'node' => int, 'depth' => list|tree, 'include' => string[], 'exclude' => string[] )
     * @param string $label
     */
    public function addQuery( array $query, $label )
    {
        $query['label'] = (string)$label;
        $this->index['queries'][] = $query;
    }

    /**
     * The internal paths found are kept as every tail of the path, so that a link to "/site/media/images/logo"
     * finds the node whose alias is "media/images/logo" whatever site access prefix it was written with. Storage
     * file paths are looked up in the file tables.
     */
    protected function resolvePaths()
    {
        foreach ( $this->paths as $path => $entries )
        {
            $parts = explode( '/', $path );
            for ( $i = 0; $i < count( $parts ); $i++ )
            {
                $tail = implode( '/', array_slice( $parts, $i ) );
                if ( $tail !== '' && !isset( $this->index['aliases'][$tail] ) )
                    $this->index['aliases'][$tail] = $entries[0];
            }
        }
        $this->paths = array();
        if ( !$this->files )
            return;
        $db = eZDB::instance();
        $images = array();
        $names = array();
        foreach ( $this->files as $path => $entries )
        {
            $images[] = $path;
            $names[basename( $path )] = $entries[0];
        }
        foreach ( array_chunk( $images, 200 ) as $chunk )
        {
            $in = array();
            foreach ( $chunk as $path )
                $in[] = "'" . $db->escapeString( $path ) . "'";
            foreach ( (array)$db->arrayQuery( 'SELECT DISTINCT ezimagefile.filepath AS filepath, ezcontentobject_attribute.contentobject_id AS object_id' .
                                              ' FROM ezimagefile, ezcontentobject_attribute' .
                                              ' WHERE ezimagefile.filepath IN ( ' . implode( ', ', $in ) . ' )' .
                                              ' AND ezcontentobject_attribute.id = ezimagefile.contentobject_attribute_id' ) as $row )
            {
                $entry = $this->files[$row['filepath']][0];
                $this->add( 'object', (int)$row['object_id'], $entry[0], $entry[1] );
            }
        }
        foreach ( array( 'ezbinaryfile', 'ezmedia' ) as $table )
        {
            foreach ( array_chunk( array_keys( $names ), 200 ) as $chunk )
            {
                $in = array();
                foreach ( $chunk as $name )
                    $in[] = "'" . $db->escapeString( $name ) . "'";
                foreach ( (array)$db->arrayQuery( 'SELECT DISTINCT ' . $table . '.filename AS filename, ezcontentobject_attribute.contentobject_id AS object_id' .
                                                  ' FROM ' . $table . ', ezcontentobject_attribute' .
                                                  ' WHERE ' . $table . '.filename IN ( ' . implode( ', ', $in ) . ' )' .
                                                  ' AND ezcontentobject_attribute.id = ' . $table . '.contentobject_attribute_id' ) as $row )
                {
                    $entry = $names[$row['filename']];
                    $this->add( 'object', (int)$row['object_id'], $entry[0], $entry[1] );
                }
            }
        }
        $this->files = array();
    }

    // ------------------------------------------------------------------ extractors (no database access)

    /**
     * The references in a text: rich text, a URL, an INI value, a block parameter, a template.
     *
     * @param string $text
     * @return array list of array( 'type' => object|node|remote|path|file, 'value' => int|string )
     */
    public static function textReferences( $text )
    {
        $text = (string)$text;
        $found = array();
        if ( $text === '' )
            return array();
        $add = function ( $type, $value ) use ( &$found ) {
            $found[$type . ':' . $value] = array( 'type' => $type, 'value' => $type === 'object' || $type === 'node' ? (int)$value : $value );
        };
        if ( preg_match_all( '/(?<![\w-])(object_id|objectid|contentobject_id|contentobject-id|content_id|contentid)\s*=\s*["\']([0-9]{1,10})["\']/i', $text, $m ) )
            foreach ( $m[2] as $id ) $add( 'object', $id );
        if ( preg_match_all( '/(?<![\w-])(node_id|nodeid|node-id|location_id|locationid)\s*=\s*["\']([0-9]{1,10})["\']/i', $text, $m ) )
            foreach ( $m[2] as $id ) $add( 'node', $id );
        if ( preg_match_all( '#\b(?:ezobject|ezcontent|ibexa-?object|ibexa-?content)://([0-9]{1,10})#i', $text, $m ) )
            foreach ( $m[1] as $id ) $add( 'object', $id );
        if ( preg_match_all( '#\b(eznode|ezlocation|ibexa-?location)://([0-9]{1,10})#i', $text, $m, PREG_SET_ORDER ) )
        {
            foreach ( $m as $match )
            {
                $add( 'node', $match[2] );
                if ( stripos( $match[1], 'ibexa' ) === 0 )
                {
                    $mapped = self::remapNode( (int)$match[2] );
                    if ( $mapped )
                        $add( 'node', $mapped );
                }
            }
        }
        if ( preg_match_all( '#exp-remote-(?:location|content|object)://([A-Za-z0-9_.\-]{1,100})#i', $text, $m ) )
            foreach ( $m[1] as $remote ) $add( 'remote', $remote );
        if ( preg_match_all( '#content/view/[a-z_]+/([0-9]{1,10})#i', $text, $m ) )
            foreach ( $m[1] as $id ) $add( 'node', $id );
        if ( preg_match_all( '#content/download/([0-9]{1,10})#i', $text, $m ) )
            foreach ( $m[1] as $id ) $add( 'object', $id );
        if ( preg_match_all( '#(var/[A-Za-z0-9_\-/.]*?storage/[^"\'\s<>()?\#\\\\]+)#', $text, $m ) )
            foreach ( $m[1] as $path ) $add( 'file', rawurldecode( $path ) );
        if ( preg_match_all( '#(?:href|src|url|link)\s*[=:]\s*["\']((?:https?://[^/"\'\s]+)?/[^"\'\s<>?\#]*)#i', $text, $m ) )
        {
            foreach ( $m[1] as $path )
            {
                if ( strpos( $path, 'storage/' ) === false )
                    $add( 'path', $path );
            }
        }
        return array_values( $found );
    }

    /**
     * The references of a field, by its datatype.
     *
     * @param string $datatype
     * @param int $dataInt
     * @param string $dataText
     * @return array as textReferences()
     */
    public static function attributeReferences( $datatype, $dataInt, $dataText )
    {
        $references = array();
        if ( $datatype === 'ezobjectrelation' && (int)$dataInt > 0 )
            $references[] = array( 'type' => 'object', 'value' => (int)$dataInt );
        if ( $datatype === 'ngenhancedlink' )
        {
            // an internal link stores an id without saying object or node, and writes no relation row: both count
            $data = json_decode( (string)$dataText, true );
            $id = is_array( $data ) && ( !isset( $data['type'] ) || $data['type'] === 'internal' ) && isset( $data['id'] ) ? (int)$data['id'] : 0;
            if ( $id <= 0 && is_array( $data ) && ( !isset( $data['type'] ) || $data['type'] === 'internal' ) )
                $id = (int)$dataInt;
            if ( $id > 0 )
            {
                $references[] = array( 'type' => 'object', 'value' => $id );
                $references[] = array( 'type' => 'node', 'value' => $id );
            }
            if ( is_array( $data ) && isset( $data['url'] ) && is_string( $data['url'] ) )
                $references = array_merge( $references, self::textReferences( 'href="' . $data['url'] . '"' ) );
            return $references;
        }
        if ( $datatype === 'ezurl' )
            return array_merge( $references, self::textReferences( 'href="' . (string)$dataText . '"' ) );
        return array_merge( $references, self::textReferences( $dataText ) );
    }

    /**
     * The references of a block parameter of a layout: a link (exp-remote-location://, ezlocation://, an internal
     * path), an id in a parameter whose name speaks of content, ids in JSON, HTML with links or storage paths.
     *
     * @param string $name
     * @param string $value
     * @return array as textReferences()
     */
    public static function blockParameterReferences( $name, $value )
    {
        $value = (string)$value;
        $references = array();
        $decoded = strlen( $value ) > 1 && ( $value[0] === '{' || $value[0] === '[' ) ? json_decode( $value, true ) : null;
        if ( is_array( $decoded ) )
        {
            array_walk_recursive( $decoded, function ( $item, $key ) use ( &$references ) {
                $references = array_merge( $references, self::blockParameterReferences( (string)$key, is_scalar( $item ) ? (string)$item : '' ) );
            } );
            return $references;
        }
        $isIdName = (bool)preg_match( '/(image|node|location|content|object|item|parent|target|link|id)$/i', (string)$name )
                    || (bool)preg_match( '/(image|node|location|content|object)/i', (string)$name );
        if ( $isIdName && preg_match( '/^[0-9]{1,10}$/', trim( $value ) ) )
        {
            $references[] = array( 'type' => 'object', 'value' => (int)$value );
            $references[] = array( 'type' => 'node', 'value' => (int)$value );
            $mapped = preg_match( '/(node|location)/i', (string)$name ) ? self::remapNode( (int)$value ) : 0;
            if ( $mapped && $mapped !== (int)$value )
                $references[] = array( 'type' => 'node', 'value' => $mapped );
        }
        if ( preg_match( '#^(exp-remote-[a-z]+|ezlocation|ezcontent|ezobject|eznode|ibexa-?[a-z]+)://#i', trim( $value ) ) )
            return array_merge( $references, self::textReferences( trim( $value ) ) );
        if ( preg_match( '/link/i', (string)$name ) && preg_match( '#^/[^\s<>"]*$#', trim( $value ) ) )
            $references[] = array( 'type' => 'path', 'value' => trim( $value ) );
        return array_merge( $references, self::textReferences( $value ) );
    }

    /**
     * What a dynamic collection query of a layout reaches.
     *
     * @param string $parameters the query's parameters (JSON)
     * @return array array( 'references' => as textReferences(), 'query' => array for addQuery() or null )
     */
    public static function collectionQueryReferences( $parameters )
    {
        $params = json_decode( (string)$parameters, true );
        if ( !is_array( $params ) )
            return array( 'references' => array(), 'query' => null );
        $references = array();
        array_walk_recursive( $params, function ( $item, $key ) use ( &$references ) {
            if ( !is_scalar( $item ) || !preg_match( '/^[0-9]{1,10}$/', (string)$item ) || (int)$item <= 0 )
                return;
            if ( preg_match( '/(location|node)(_id)?s?$/i', (string)$key ) )
            {
                $references[] = array( 'type' => 'node', 'value' => (int)$item );
                $mapped = self::remapNode( (int)$item );
                if ( $mapped && $mapped !== (int)$item )
                    $references[] = array( 'type' => 'node', 'value' => $mapped );
            }
            else if ( preg_match( '/(content|object)(_id)?s?$/i', (string)$key ) )
                $references[] = array( 'type' => 'object', 'value' => (int)$item );
        } );

        $include = array();
        $exclude = array();
        if ( !empty( $params['filter_by_content_type'] ) && isset( $params['content_types'] ) && is_array( $params['content_types'] ) )
        {
            $types = array_values( array_filter( array_map( 'strval', $params['content_types'] ), 'strlen' ) );
            if ( isset( $params['content_types_filter'] ) && $params['content_types_filter'] === 'exclude' )
                $exclude = $types;
            else
                $include = $types;
        }
        $current = !empty( $params['use_current_location'] );
        $parent = isset( $params['parent_location_id'] ) ? (int)$params['parent_location_id'] : 0;
        if ( !$current && $parent <= 0 )
            return array( 'references' => $references, 'query' => null );
        $nodes = $current ? array( 0 ) : array_values( array_unique( array_filter( array( $parent, self::remapNode( $parent ) ) ) ) );
        $depth = isset( $params['query_type'] ) && $params['query_type'] === 'list' ? 'list' : 'tree';
        return array( 'references' => $references,
                      'query' => array( 'nodes' => $nodes, 'depth' => $depth, 'include' => $include, 'exclude' => $exclude ) );
    }

    /**
     * The references of a layout's rule target.
     *
     * @param string $type
     * @param string $value
     * @return array as textReferences()
     */
    public static function ruleTargetReferences( $type, $value )
    {
        $value = trim( (string)$value );
        switch ( $type )
        {
            case 'node':
            case 'location':
            case 'subtree':
                if ( !preg_match( '/^[0-9]{1,10}$/', $value ) )
                    return self::textReferences( $value );
                $references = array( array( 'type' => 'node', 'value' => (int)$value ) );
                $mapped = self::remapNode( (int)$value );
                if ( $mapped && $mapped !== (int)$value )
                    $references[] = array( 'type' => 'node', 'value' => $mapped );
                return $references;
            case 'content':
            case 'object':
                return preg_match( '/^[0-9]{1,10}$/', $value ) ? array( array( 'type' => 'object', 'value' => (int)$value ) ) : array();
            case 'path':
            case 'path_prefix':
            case 'path_info_prefix':
                return trim( $value, '/' ) !== '' ? array( array( 'type' => 'path', 'value' => $value ) ) : array();
            default:
                return array();
        }
    }

    /**
     * The references in the text of a settings file: every whole number in the value of a setting whose block or
     * name speaks of nodes, objects or roots (and a remote id there), and content paths in any value.
     *
     * @param string $text the file's text
     * @return array list of array( 'type', 'value', 'setting' => "[Block] Name" )
     */
    public static function iniReferences( $text )
    {
        $found = array();
        $block = '';
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$text ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' || strpos( $line, '<?php' ) === 0 || strpos( $line, '*/' ) === 0 || strpos( $line, '/*' ) === 0 )
                continue;
            if ( preg_match( '/^\[([^\]]+)\]/', $line, $m ) )
            {
                $block = $m[1];
                continue;
            }
            $equals = strpos( $line, '=' );
            if ( $equals === false )
                continue;
            $name = trim( substr( $line, 0, $equals ) );
            $value = trim( substr( $line, $equals + 1 ) );
            if ( $value === '' )
                continue;
            $setting = '[' . $block . '] ' . $name;
            foreach ( self::textReferences( $value ) as $reference )
                $found[] = array_merge( $reference, array( 'setting' => $setting ) );
            if ( !preg_match( self::INI_KEY_PATTERN, $block . ' ' . $name ) )
                continue;
            if ( preg_match_all( '/(?<![\w.\/-])([0-9]{1,10})(?![\w.-])/', $value, $m ) )
            {
                foreach ( $m[1] as $id )
                {
                    $found[] = array( 'type' => 'node', 'value' => (int)$id, 'setting' => $setting );
                    $found[] = array( 'type' => 'object', 'value' => (int)$id, 'setting' => $setting );
                }
            }
            if ( preg_match( '/^[A-Za-z][A-Za-z0-9_.\-]{5,99}$/', $value ) && preg_match( '/[0-9]/', $value ) )
                $found[] = array( 'type' => 'remote', 'value' => $value, 'setting' => $setting );
        }
        return $found;
    }

    /**
     * Literal ids in a template: fetch( 'content', 'node', hash( 'node_id', 123 ) ), parent_node_id, object_id,
     * content/view/full/123 and the like. An id a template computes is not found.
     *
     * @param string $text
     * @return array as textReferences()
     */
    public static function templateReferences( $text )
    {
        $text = (string)$text;
        $found = array();
        if ( preg_match_all( '/\b(node_id|parent_node_id|main_node_id|nodeid|location_id|parent_location_id|subtree|root_node_id)[\'"]?\s*[,=]\s*[\'"]?([0-9]{1,10})\b/i', $text, $m ) )
            foreach ( $m[2] as $id ) $found['node:' . $id] = array( 'type' => 'node', 'value' => (int)$id );
        if ( preg_match_all( '/\b(object_id|contentobject_id|content_id|objectid)[\'"]?\s*[,=]\s*[\'"]?([0-9]{1,10})\b/i', $text, $m ) )
            foreach ( $m[2] as $id ) $found['object:' . $id] = array( 'type' => 'object', 'value' => (int)$id );
        foreach ( self::textReferences( $text ) as $reference )
        {
            if ( $reference['type'] !== 'path' )
                $found[$reference['type'] . ':' . $reference['value']] = $reference;
        }
        return array_values( $found );
    }

    /**
     * The node ids of a path string (/1/2/58/ => 1, 2, 58) or of a list of ids (1;2,58).
     *
     * @param string $value
     * @return int[]
     */
    public static function idsOf( $value )
    {
        preg_match_all( '/[0-9]{1,10}/', (string)$value, $m );
        return array_values( array_unique( array_filter( array_map( 'intval', $m[0] ) ) ) );
    }

    /**
     * A path as kept in the index: no scheme and host, no query, no leading or trailing slash, lower case.
     *
     * @param string $path
     * @return string
     */
    public static function normalisePath( $path )
    {
        $path = (string)$path;
        $path = preg_replace( '#^[a-z][a-z0-9+.\-]*://[^/]*#i', '', $path );
        $path = preg_replace( '/[?#].*$/', '', $path );
        $path = trim( rawurldecode( $path ), "/ \t" );
        if ( $path === '' || strpos( $path, '..' ) !== false || strlen( $path ) > 500 )
            return '';
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $path, 'UTF-8' ) : strtolower( $path );
    }

    /**
     * A node id of the reference installation mapped through explayouts.ini [NexusNodeMap], as the layouts do.
     *
     * @param int $nodeID
     * @return int the mapped id, 0 when there is no mapping or no layouts
     */
    public static function remapNode( $nodeID )
    {
        if ( (int)$nodeID <= 0 || !class_exists( 'eZINI' ) )
            return 0;
        static $map = null;
        if ( $map === null )
        {
            $map = array();
            $ini = eZINI::instance( 'explayouts.ini' );
            if ( $ini->hasGroup( 'NexusNodeMap' ) )
                $map = (array)$ini->group( 'NexusNodeMap' );
        }
        if ( !isset( $map[(string)(int)$nodeID] ) )
            return 0;
        $mapped = trim( (string)$map[(string)(int)$nodeID] );
        if ( ctype_digit( $mapped ) )
            return (int)$mapped;
        $node = class_exists( 'eZContentObjectTreeNode' ) ? eZContentObjectTreeNode::fetchByRemoteID( $mapped, false ) : null;
        return is_array( $node ) && isset( $node['node_id'] ) ? (int)$node['node_id'] : 0;
    }

    // ------------------------------------------------------------------ the sources (database and files)

    /**
     * Whether a table exists, on every SQL engine.
     *
     * @param string $table
     * @return bool
     */
    public static function tableExists( $table )
    {
        $table = (string)$table;
        if ( !preg_match( '/^[a-z0-9_]{1,64}$/i', $table ) )
            return false;
        static $known = array();
        if ( isset( $known[$table] ) )
            return $known[$table];
        $db = eZDB::instance();
        switch ( $db->databaseName() )
        {
            case 'sqlite':
                $rows = $db->arrayQuery( "SELECT name FROM sqlite_master WHERE type = 'table' AND name = '" . $table . "'" );
                break;
            case 'postgresql':
                $rows = $db->arrayQuery( "SELECT tablename FROM pg_tables WHERE tablename = '" . strtolower( $table ) . "'" );
                break;
            case 'oracle':
                $rows = $db->arrayQuery( "SELECT table_name FROM user_tables WHERE table_name = '" . strtoupper( $table ) . "'" );
                break;
            default:
                $rows = $db->arrayQuery( "SHOW TABLES LIKE '" . str_replace( '_', '\\_', $table ) . "'" );
        }
        return $known[$table] = is_array( $rows ) && count( $rows ) > 0;
    }

    /**
     * The fields of every counting version, in chunks by attribute id: only those whose datatype refers by its
     * data or whose text holds something that looks like a reference.
     */
    protected function fromAttributes()
    {
        $db = eZDB::instance();
        $chunk = expContentCheck::intSetting( 'UnreferencedSettings', 'ScanChunkSize', 2000, 100, 20000 );
        $condition = '( ezcontentobject_attribute.data_type_string IN ( \'ezobjectrelation\', \'ngenhancedlink\' )' .
                     " OR ezcontentobject_attribute.data_text LIKE '%id=%'" .
                     " OR ezcontentobject_attribute.data_text LIKE '%://%'" .
                     " OR ezcontentobject_attribute.data_text LIKE '%content/view/%'" .
                     " OR ezcontentobject_attribute.data_text LIKE '%content/download/%'" .
                     " OR ezcontentobject_attribute.data_text LIKE '%storage/%'" .
                     " OR ezcontentobject_attribute.data_text LIKE '%href=%' )";
        $after = 0;
        while ( true )
        {
            $rows = $db->arrayQuery( 'SELECT ezcontentobject_attribute.id AS id, ezcontentobject_attribute.contentobject_id AS object_id,' .
                                     ' ezcontentobject_attribute.data_type_string AS datatype, ezcontentobject_attribute.data_int AS data_int,' .
                                     ' ezcontentobject_attribute.data_text AS data_text, ezcontentobject_attribute.version AS version' .
                                     ' FROM ezcontentobject_attribute, ezcontentobject, ezcontentobject_version' .
                                     ' WHERE ezcontentobject_attribute.id > ' . (int)$after .
                                     ' AND ezcontentobject.id = ezcontentobject_attribute.contentobject_id' .
                                     ' AND ezcontentobject.status IN ( ' . implode( ', ', expContentCheck::REFERENCE_OBJECT_STATUSES ) . ' )' .
                                     ' AND ezcontentobject_version.contentobject_id = ezcontentobject_attribute.contentobject_id' .
                                     ' AND ezcontentobject_version.version = ezcontentobject_attribute.version' .
                                     ' AND ezcontentobject_version.status IN ( ' . implode( ', ', expContentCheck::REFERENCE_VERSION_STATUSES ) . ' )' .
                                     " AND ezcontentobject_attribute.data_type_string NOT IN ( 'ezimage', 'ezbinaryfile', 'ezmedia' )" .
                                     ' AND ' . $condition .
                                     ' ORDER BY ezcontentobject_attribute.id ASC', array( 'offset' => 0, 'limit' => $chunk ) );
            if ( !is_array( $rows ) || !$rows )
                break;
            foreach ( $rows as $row )
            {
                $after = (int)$row['id'];
                $this->addAll( self::attributeReferences( (string)$row['datatype'], (int)$row['data_int'], (string)$row['data_text'] ),
                               (int)$row['object_id'], 'field of object ' . (int)$row['object_id'] . ' version ' . (int)$row['version'] . ' (' . $row['datatype'] . ')' );
            }
            if ( count( $rows ) < $chunk )
                break;
        }
    }

    /**
     * The ezurl table (links in rich text and URL fields), custom URL aliases and URL wildcards.
     */
    protected function fromUrls()
    {
        $db = eZDB::instance();
        $after = 0;
        while ( true )
        {
            $rows = $db->arrayQuery( 'SELECT id, url FROM ezurl WHERE id > ' . (int)$after . ' ORDER BY id ASC', array( 'offset' => 0, 'limit' => 5000 ) );
            if ( !is_array( $rows ) || !$rows )
                break;
            foreach ( $rows as $row )
            {
                $after = (int)$row['id'];
                $this->addAll( self::textReferences( 'href="' . $row['url'] . '"' ), 0, 'link (ezurl ' . (int)$row['id'] . ')' );
            }
            if ( count( $rows ) < 5000 )
                break;
        }
        foreach ( (array)$db->arrayQuery( "SELECT action, text FROM ezurlalias_ml WHERE is_alias = 1 AND action LIKE 'eznode:%'" ) as $row )
            $this->add( 'node', (int)substr( (string)$row['action'], 7 ), 0, 'custom URL alias "' . $row['text'] . '"' );
        if ( self::tableExists( 'ezurlwildcard' ) )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT source_url, destination_url FROM ezurlwildcard' ) as $row )
            {
                $this->addAll( self::textReferences( 'href="/' . ltrim( (string)$row['destination_url'], '/' ) . '"' ), 0, 'URL wildcard ' . $row['source_url'] );
                $this->add( 'path', (string)$row['destination_url'], 0, 'URL wildcard ' . $row['source_url'] );
            }
        }
    }

    /**
     * The layouts: collection items, dynamic collections, block parameters and rule targets, in every status. A
     * dynamic collection on the current location applies wherever its layout is used, so anywhere.
     */
    protected function fromLayouts()
    {
        if ( !self::tableExists( 'explayouts_block' ) )
            return;
        $db = eZDB::instance();
        if ( self::tableExists( 'explayouts_collection_item' ) )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT ezi.id AS id, ezi.value_id AS value_id, ezi.value_type AS value_type, c.block_id AS block_id' .
                                              ' FROM explayouts_collection_item ezi LEFT JOIN explayouts_collection c ON c.id = ezi.collection_id' ) as $row )
            {
                $label = 'layout block ' . (int)$row['block_id'] . ', collection item ' . (int)$row['id'];
                $type = strtolower( (string)$row['value_type'] );
                if ( !preg_match( '/^[0-9]{1,10}$/', trim( (string)$row['value_id'] ) ) )
                {
                    $this->addAll( self::textReferences( (string)$row['value_id'] ), 0, $label );
                    $this->add( 'remote', trim( (string)$row['value_id'] ), 0, $label );
                    continue;
                }
                // value_id of a location item is a NODE id
                if ( strpos( $type, 'location' ) !== false || strpos( $type, 'node' ) !== false )
                    $this->add( 'node', (int)$row['value_id'], 0, $label );
                else if ( strpos( $type, 'content' ) !== false || strpos( $type, 'object' ) !== false )
                    $this->add( 'object', (int)$row['value_id'], 0, $label );
                else
                {
                    $this->add( 'node', (int)$row['value_id'], 0, $label );
                    $this->add( 'object', (int)$row['value_id'], 0, $label );
                }
            }
        }
        if ( self::tableExists( 'explayouts_collection_query' ) )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT q.id AS id, q.parameters AS parameters, c.block_id AS block_id' .
                                              ' FROM explayouts_collection_query q LEFT JOIN explayouts_collection c ON c.id = q.collection_id' ) as $row )
            {
                $label = 'layout block ' . (int)$row['block_id'] . ', dynamic collection ' . (int)$row['id'];
                $found = self::collectionQueryReferences( (string)$row['parameters'] );
                $this->addAll( $found['references'], 0, $label );
                if ( $found['query'] )
                    $this->addQuery( $found['query'], $label );
            }
        }
        if ( self::tableExists( 'explayouts_block_parameter' ) )
        {
            $after = 0;
            while ( true )
            {
                $rows = $db->arrayQuery( 'SELECT id, block_id, name, value FROM explayouts_block_parameter WHERE id > ' . (int)$after .
                                         " AND value IS NOT NULL AND value <> '' ORDER BY id ASC", array( 'offset' => 0, 'limit' => 5000 ) );
                if ( !is_array( $rows ) || !$rows )
                    break;
                foreach ( $rows as $row )
                {
                    $after = (int)$row['id'];
                    $this->addAll( self::blockParameterReferences( (string)$row['name'], (string)$row['value'] ), 0,
                                   'layout block ' . (int)$row['block_id'] . ', parameter ' . $row['name'] );
                }
                if ( count( $rows ) < 5000 )
                    break;
            }
        }
        if ( self::tableExists( 'explayouts_rule_target' ) )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT id, rule_id, target_type, target_value FROM explayouts_rule_target' ) as $row )
                $this->addAll( self::ruleTargetReferences( (string)$row['target_type'], (string)$row['target_value'] ), 0,
                               'layout rule ' . (int)$row['rule_id'] . ', target ' . $row['target_type'] );
        }
    }

    /**
     * The settings files: settings/, its site access and override directories, and every extension's settings.
     */
    protected function fromIni()
    {
        $root = eZSys::rootDir();
        $files = array();
        foreach ( array( 'settings', 'extension' ) as $top )
        {
            $dir = $root . '/' . $top;
            if ( !is_dir( $dir ) )
                continue;
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS ) );
            foreach ( $iterator as $file )
            {
                $path = $file->getPathname();
                if ( preg_match( '#\.ini(\.append(\.php)?)?$#', $path ) && ( $top === 'settings' || strpos( $path, '/settings/' ) !== false )
                     && strpos( $path, '/var/reference/' ) === false && strpos( $path, '/tests/' ) === false )
                    $files[] = $path;
            }
        }
        sort( $files );
        foreach ( $files as $path )
        {
            $short = substr( $path, strlen( $root ) + 1 );
            foreach ( self::iniReferences( (string)@file_get_contents( $path ) ) as $reference )
                $this->add( $reference['type'], $reference['value'], 0, 'setting ' . $reference['setting'] . ' in ' . $short );
        }
    }

    /**
     * Literal ids in the templates of every design.
     */
    protected function fromTemplates()
    {
        $root = eZSys::rootDir();
        $dirs = array_merge( glob( $root . '/design/*', GLOB_ONLYDIR ) ?: array(), glob( $root . '/extension/*/design/*', GLOB_ONLYDIR ) ?: array() );
        foreach ( $dirs as $dir )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $iterator as $file )
            {
                $path = $file->getPathname();
                if ( substr( $path, -4 ) !== '.tpl' )
                    continue;
                $text = (string)@file_get_contents( $path );
                if ( !preg_match( '/[0-9]/', $text ) )
                    continue;
                $this->addAll( self::templateReferences( $text ), 0, 'template ' . substr( $path, strlen( $root ) + 1 ) );
            }
        }
    }

    /**
     * Role assignments limited to a subtree, and policies limited by node or subtree.
     */
    protected function fromRoles()
    {
        $db = eZDB::instance();
        foreach ( (array)$db->arrayQuery( "SELECT role_id, limit_identifier, limit_value FROM ezuser_role WHERE limit_value IS NOT NULL AND limit_value <> ''" ) as $row )
        {
            if ( stripos( (string)$row['limit_identifier'], 'subtree' ) === false )
                continue;
            $ids = self::idsOf( $row['limit_value'] );
            if ( $ids )
                $this->add( 'node', end( $ids ), 0, 'role ' . (int)$row['role_id'] . ' assigned with a subtree limitation' );
        }
        foreach ( (array)$db->arrayQuery( 'SELECT ezpolicy_limitation.identifier AS identifier, ezpolicy_limitation_value.value AS value, ezpolicy_limitation.policy_id AS policy_id' .
                                          ' FROM ezpolicy_limitation, ezpolicy_limitation_value' .
                                          ' WHERE ezpolicy_limitation_value.limitation_id = ezpolicy_limitation.id' ) as $row )
        {
            $identifier = strtolower( (string)$row['identifier'] );
            if ( $identifier === 'node' || $identifier === 'parentnode' )
            {
                foreach ( self::idsOf( $row['value'] ) as $id )
                    $this->add( 'node', $id, 0, 'policy ' . (int)$row['policy_id'] . ' limited to a node' );
            }
            else if ( strpos( $identifier, 'subtree' ) !== false )
            {
                $ids = self::idsOf( $row['value'] );
                if ( $ids )
                    $this->add( 'node', end( $ids ), 0, 'policy ' . (int)$row['policy_id'] . ' limited to a subtree' );
            }
        }
    }

    /**
     * The columns named in [UnreferencedSettings]: ObjectColumns[] and NodeColumns[] hold one id, NodeListColumns[]
     * a list of ids or a path string. A column whose table or column does not exist is skipped.
     */
    protected function fromTables()
    {
        $db = eZDB::instance();
        foreach ( array( 'ObjectColumns' => 'object', 'NodeColumns' => 'node', 'NodeListColumns' => 'nodes' ) as $setting => $type )
        {
            foreach ( (array)expContentCheck::setting( 'UnreferencedSettings', $setting, array() ) as $spec )
            {
                if ( !is_string( $spec ) || !preg_match( '/^([a-z0-9_]{1,64})\.([a-z0-9_]{1,64})$/i', trim( $spec ), $m ) || !self::tableExists( $m[1] ) )
                    continue;
                $rows = $db->arrayQuery( 'SELECT DISTINCT ' . $m[2] . ' AS value FROM ' . $m[1] . ' WHERE ' . $m[2] . ' IS NOT NULL' );
                foreach ( is_array( $rows ) ? $rows : array() as $row )
                {
                    $label = $m[1] . '.' . $m[2];
                    if ( $type === 'nodes' )
                    {
                        foreach ( self::idsOf( $row['value'] ) as $id )
                            $this->add( 'node', $id, 0, $label );
                    }
                    else if ( preg_match( '/^\s*[0-9]{1,10}\s*$/', (string)$row['value'] ) )
                        $this->add( $type, (int)$row['value'], 0, $label );
                }
            }
        }
    }

    /**
     * The newsletter's article pools: the children of their parent nodes, of their classes, go into editions.
     */
    protected function fromNewsletter()
    {
        if ( !self::tableExists( 'cjwnl_article_pool' ) )
            return;
        foreach ( (array)eZDB::instance()->arrayQuery( 'SELECT id, parent_node_id_array_string, class_identifier_array_string FROM cjwnl_article_pool' ) as $row )
        {
            $nodes = self::idsOf( $row['parent_node_id_array_string'] );
            if ( !$nodes )
                continue;
            $classes = array_values( array_filter( preg_split( '/[\s,;]+/', (string)$row['class_identifier_array_string'] ), 'strlen' ) );
            $label = 'newsletter article pool ' . (int)$row['id'];
            foreach ( $nodes as $node )
                $this->add( 'node', $node, 0, $label );
            $this->addQuery( array( 'nodes' => $nodes, 'depth' => 'tree', 'include' => $classes, 'exclude' => array() ), $label );
        }
    }

    // ------------------------------------------------------------------ judging

    /**
     * Why an object is in use, or null when nothing in the index refers to it.
     *
     * @param array $index from index()
     * @param array $object array( 'object_id', 'remote_id', 'class_identifier', 'locations' => list of array( 'node_id',
     *              'parent_node_id', 'path_string', 'remote_id', 'path' (alias path), 'parent_class' ) )
     * @param string[] $storageParents classes of a parent that only stores its children
     * @return string|null
     */
    public static function reason( array $index, array $object, array $storageParents )
    {
        $objectID = (int)$object['object_id'];
        $first = function ( $entries ) use ( $objectID ) {
            foreach ( (array)$entries as $entry )
            {
                if ( (int)$entry[0] !== $objectID )
                    return (string)$entry[1];
            }
            return null;
        };
        if ( isset( $index['objects'][$objectID] ) && ( $label = $first( $index['objects'][$objectID] ) ) !== null )
            return $label;
        if ( !empty( $object['remote_id'] ) && isset( $index['remote'][$object['remote_id']] ) && ( $label = $first( $index['remote'][$object['remote_id']] ) ) !== null )
            return $label;
        foreach ( (array)$object['locations'] as $location )
        {
            $nodeID = (int)$location['node_id'];
            if ( isset( $index['nodes'][$nodeID] ) && ( $label = $first( $index['nodes'][$nodeID] ) ) !== null )
                return $label;
            if ( !empty( $location['remote_id'] ) && isset( $index['remote'][$location['remote_id']] ) && ( $label = $first( $index['remote'][$location['remote_id']] ) ) !== null )
                return $label;
            $alias = self::normalisePath( isset( $location['path'] ) ? $location['path'] : '' );
            if ( $alias !== '' && isset( $index['aliases'][$alias] ) && (int)$index['aliases'][$alias][0] !== $objectID )
                return (string)$index['aliases'][$alias][1];
            if ( !in_array( (string)$location['parent_class'], $storageParents, true ) )
                return 'shown by its parent (' . $location['parent_class'] . ', node ' . (int)$location['parent_node_id'] . ')';
            $ancestors = self::idsOf( $location['path_string'] );
            array_pop( $ancestors );
            foreach ( (array)$index['queries'] as $query )
            {
                if ( !self::queryReaches( $query, (string)$object['class_identifier'], (int)$location['parent_node_id'], $ancestors ) )
                    continue;
                return (string)$query['label'];
            }
        }
        return null;
    }

    /**
     * Whether a dynamic collection lists an object of a class at a location.
     *
     * @param array $query array( 'nodes', 'depth', 'include', 'exclude' )
     * @param string $class
     * @param int $parentNodeID
     * @param int[] $ancestors the node ids above the location
     * @return bool
     */
    public static function queryReaches( array $query, $class, $parentNodeID, array $ancestors )
    {
        if ( !empty( $query['include'] ) && !in_array( $class, $query['include'], true ) )
            return false;
        if ( !empty( $query['exclude'] ) && in_array( $class, $query['exclude'], true ) )
            return false;
        foreach ( (array)$query['nodes'] as $node )
        {
            $node = (int)$node;
            if ( $node === 0 )
                return true;
            if ( $query['depth'] === 'list' ? $parentNodeID === $node : in_array( $node, $ancestors, true ) )
                return true;
        }
        return false;
    }
}

?>
