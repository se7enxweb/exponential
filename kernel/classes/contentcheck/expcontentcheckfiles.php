<?php
/**
 * File containing the expContentCheckFiles class.
 *
 * Missing files and image aliases (doc/features/6.0/content-checks.md, "Missing files"): the file-backed fields of
 * the content below a node (images, files, media and every datatype named in contentcheck.ini [MissingFileSettings]
 * Handlers[]) whose file is not in the storage (var/<site>/storage, or the cluster's DFS when one is used), in the
 * published version and in drafts; and images whose aliases (image.ini) are missing or stale.
 *
 *   missing        the original file of the field version is not in the storage
 *   alias_missing  the field's XML names an alias whose file is not there (the original is)
 *   alias_stale    an alias made with other settings (its alias_key differs from image.ini's now), before the image
 *                  aliases were last expired, or older than the original
 *   alias_absent   an alias of image.ini (AliasList[] of [MissingFileSettings]; empty: all) that was never made;
 *                  normal, since aliases are made when first shown, so it is off unless asked for
 *
 * The objects are walked by object id in chunks (scan()); the list of the broken relations walks them all (findAll(),
 * kept a minute) for its total and pages. What the user may not read is left out in the SQL. A file is looked up through
 * eZClusterFileHandler, so the DFS of a cluster counts, not the local disk.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckFiles
{
    /** What a row can be, in the order they are offered */
    const PROBLEMS = array( 'missing', 'alias_missing', 'alias_stale', 'alias_absent' );

    /** What is checked when nothing is chosen */
    const DEFAULT_PROBLEMS = array( 'missing', 'alias_missing', 'alias_stale' );

    /** The versions checked: the published one, and drafts (draft, internal draft, pending) */
    const VERSIONS = array( 'published', 'draft' );

    /** The version statuses of each choice */
    const VERSION_STATUSES = array( 'published' => array( 1 ), 'draft' => array( 0, 2, 5 ) );

    /** The target of the broken relations (Kind[]) that asks for the missing files */
    const TARGET = 'missing_file';

    /** The session variable the fixfiles view leaves its outcome in */
    const FEEDBACK = 'ContentCheckFixFilesFeedback';

    /** The prefix of a review mark in ezsite_data: contentcheck.review.<attribute id>.<version> */
    const REVIEW_PREFIX = 'contentcheck.review.';

    /** @var array path => bool, this request */
    protected static $exists = array();

    /** @var array path => int|null, this request */
    protected static $mtimes = array();

    /** @var array|null datatype => handler */
    protected static $handlers = null;

    // ------------------------------------------------------------------ input

    /**
     * The parameters of the list from the request.
     *
     * @param array $input NodeID, Problem[], Version[], After, Limit
     * @param int $defaultNodeID
     * @return array node_id, problems, versions, after, limit
     */
    public static function parameters( array $input, $defaultNodeID = 1 )
    {
        $problems = isset( $input['Problem'] ) && is_array( $input['Problem'] ) ? array_values( array_intersect( self::PROBLEMS, $input['Problem'] ) ) : array();
        $versions = isset( $input['Version'] ) && is_array( $input['Version'] ) ? array_values( array_intersect( self::VERSIONS, $input['Version'] ) ) : array();
        $limit = isset( $input['Limit'] ) ? (int)$input['Limit'] : 25;
        return array( 'node_id' => expContentCheck::id( isset( $input['NodeID'] ) ? $input['NodeID'] : null, $defaultNodeID ),
                      'problems' => $problems ? $problems : self::DEFAULT_PROBLEMS,
                      'versions' => $versions ? $versions : self::VERSIONS,
                      'after' => isset( $input['After'] ) && is_scalar( $input['After'] ) && preg_match( '/^[0-9]{1,10}$/', (string)$input['After'] ) ? (int)$input['After'] : 0,
                      'limit' => max( 1, min( 200, $limit ) ) );
    }

    /**
     * The key of a field version: "<attribute id>-<version>".
     *
     * @param int $attributeID
     * @param int $version
     * @return string
     */
    public static function key( $attributeID, $version )
    {
        return (int)$attributeID . '-' . (int)$version;
    }

    /**
     * Field version keys from a request: "<attribute id>-<version>", unique, at most $max; anything else is left out.
     *
     * @param mixed $value
     * @param int $max
     * @return array list of array( attribute id, version ), keyed by the key
     */
    public static function keyList( $value, $max = 200 )
    {
        if ( is_scalar( $value ) )
            $value = preg_split( '/[\s,;]+/', (string)$value, -1, PREG_SPLIT_NO_EMPTY );
        $keys = array();
        foreach ( is_array( $value ) ? $value : array() as $item )
        {
            if ( !is_scalar( $item ) || !preg_match( '/^\s*([0-9]{1,10})-([0-9]{1,6})\s*$/', (string)$item, $m ) )
                continue;
            $attributeID = (int)$m[1];
            $version = (int)$m[2];
            if ( $attributeID < 1 || $attributeID > 2147483647 || $version < 1 )
                continue;
            $keys[self::key( $attributeID, $version )] = array( $attributeID, $version );
            if ( count( $keys ) >= $max )
                break;
        }
        return $keys;
    }

    // ------------------------------------------------------------------ handlers and settings

    /**
     * The handler of each datatype: contentcheck.ini [MissingFileSettings] Handlers[]. A class that does not exist
     * or does not implement expContentCheckFileHandler is left out (with a warning in the debug output).
     *
     * @return expContentCheckFileHandler[] datatype => handler
     */
    public static function handlers()
    {
        if ( self::$handlers !== null )
            return self::$handlers;
        $handlers = array();
        $classes = expContentCheck::setting( 'MissingFileSettings', 'Handlers', array( 'ezimage' => 'expContentCheckFileImage',
                                                                                      'ezbinaryfile' => 'expContentCheckFileTable',
                                                                                      'ezmedia' => 'expContentCheckFileTable' ) );
        foreach ( is_array( $classes ) ? $classes : array() as $datatype => $class )
        {
            if ( !is_string( $datatype ) || !preg_match( '/^[a-z][a-z0-9_]{0,49}$/', $datatype ) || !is_string( $class ) || $class === '' )
                continue;
            if ( !class_exists( $class ) || !in_array( 'expContentCheckFileHandler', class_implements( $class ), true ) )
            {
                eZDebug::writeWarning( "Content checks: the file handler $class of $datatype is not an expContentCheckFileHandler", __METHOD__ );
                continue;
            }
            $handlers[$datatype] = new $class();
        }
        return self::$handlers = $handlers;
    }

    /**
     * @param string $datatype
     * @return expContentCheckFileHandler|null
     */
    public static function handler( $datatype )
    {
        $handlers = self::handlers();
        return isset( $handlers[$datatype] ) ? $handlers[$datatype] : null;
    }

    /**
     * Forgets the handlers and the file lookups of this request (tests, a long command).
     */
    public static function reset()
    {
        self::$handlers = null;
        self::$exists = array();
        self::$mtimes = array();
    }

    /**
     * The aliases of image.ini now: name => alias_key, without the original.
     *
     * @return array
     */
    public static function aliasKeys()
    {
        $keys = array();
        $manager = eZImageManager::factory();
        foreach ( $manager->aliasList() as $name => $alias )
        {
            if ( $name !== 'original' && isset( $alias['alias_key'] ) )
                $keys[(string)$name] = (string)$alias['alias_key'];
        }
        return $keys;
    }

    /**
     * The aliases an image is expected to have for alias_absent: [MissingFileSettings] AliasList[], empty: all.
     *
     * @param array $aliasKeys from aliasKeys()
     * @return string[]
     */
    public static function expectedAliases( array $aliasKeys )
    {
        $list = expContentCheck::setting( 'MissingFileSettings', 'AliasList', array() );
        $list = is_array( $list ) ? array_values( array_filter( $list, 'strlen' ) ) : array();
        return $list ? array_values( array_intersect( $list, array_keys( $aliasKeys ) ) ) : array_keys( $aliasKeys );
    }

    /**
     * When the image aliases were last expired (all aliases older are stale); 0 when never.
     *
     * @return int
     */
    public static function aliasExpiry()
    {
        $handler = eZExpiryHandler::instance();
        return $handler->hasTimestamp( 'image-manager-alias' ) ? (int)$handler->timestamp( 'image-manager-alias' ) : 0;
    }

    // ------------------------------------------------------------------ the storage

    /**
     * Whether a file is in the storage (the cluster's, when there is one).
     *
     * @param string $path
     * @return bool
     */
    public static function exists( $path )
    {
        $path = (string)$path;
        if ( $path === '' )
            return false;
        if ( !isset( self::$exists[$path] ) )
            self::$exists[$path] = (bool)eZClusterFileHandler::instance( $path )->exists();
        return self::$exists[$path];
    }

    /**
     * The modification time of a file in the storage.
     *
     * @param string $path
     * @return int|null null when it is not there
     */
    public static function mtime( $path )
    {
        $path = (string)$path;
        if ( !array_key_exists( $path, self::$mtimes ) )
        {
            $mtime = null;
            if ( self::exists( $path ) )
            {
                $file = eZClusterFileHandler::instance( $path );
                $value = $file->mtime();
                if ( !$value )
                {
                    $stat = $file->stat();
                    $value = is_array( $stat ) && isset( $stat['mtime'] ) ? $stat['mtime'] : null;
                }
                $mtime = $value ? (int)$value : null;
            }
            self::$mtimes[$path] = $mtime;
        }
        return self::$mtimes[$path];
    }

    /**
     * Forgets what is known about one file (after a fix wrote it).
     *
     * @param string $path
     */
    public static function forget( $path )
    {
        unset( self::$exists[(string)$path], self::$mtimes[(string)$path] );
    }

    // ------------------------------------------------------------------ judging

    /**
     * What is wrong with the files of one field version. No database access: the lookups are passed in.
     *
     * @param array $files from a handler (the original first)
     * @param callable $exists path => bool
     * @param callable $mtime path => int|null
     * @param array $aliasKeys name => current alias_key (image.ini); empty for a datatype without aliases
     * @param string[] $expected the aliases looked for by alias_absent
     * @param int $expiry when the aliases were last expired (0: never)
     * @return array list of problems: array( 'problem' => missing, 'path', 'name' ) or array( 'problem' =>
     *               alias_missing|alias_stale|alias_absent (the worst of them), 'path' => the first alias's path,
     *               'aliases' => list of array( 'alias', 'state' => missing|stale|absent, 'why' => '' | settings |
     *               expired | older, 'path' ) )
     */
    public static function judge( array $files, $exists, $mtime, array $aliasKeys, array $expected, $expiry )
    {
        if ( !$files || $files[0]['role'] !== 'original' )
            return array();
        $original = $files[0];
        if ( !$exists( $original['path'] ) )
            return array( array( 'problem' => 'missing', 'path' => $original['path'], 'name' => $original['name'], 'aliases' => array() ) );
        if ( !$aliasKeys )
            return array();

        $aliases = array();
        $named = array();
        $originalTime = null;
        foreach ( array_slice( $files, 1 ) as $file )
        {
            $name = $file['alias'];
            $named[$name] = true;
            // an alias image.ini no longer defines is not looked at: it is never shown again
            if ( !isset( $aliasKeys[$name] ) )
                continue;
            if ( $file['path'] === '' || !$exists( $file['path'] ) )
            {
                $aliases[] = array( 'alias' => $name, 'state' => 'missing', 'why' => '', 'path' => $file['path'] );
                continue;
            }
            $why = '';
            if ( (string)$file['alias_key'] !== (string)$aliasKeys[$name] )
                $why = 'settings';
            elseif ( (int)$expiry > 0 && (int)$file['timestamp'] > 0 && (int)$file['timestamp'] <= (int)$expiry )
                $why = 'expired';
            else
            {
                if ( $originalTime === null )
                    $originalTime = (int)$mtime( $original['path'] );
                $aliasTime = (int)$mtime( $file['path'] );
                if ( $originalTime > 0 && $aliasTime > 0 && $aliasTime < $originalTime )
                    $why = 'older';
            }
            if ( $why !== '' )
                $aliases[] = array( 'alias' => $name, 'state' => 'stale', 'why' => $why, 'path' => $file['path'] );
        }
        foreach ( $expected as $name )
        {
            if ( !isset( $named[$name] ) && isset( $aliasKeys[$name] ) )
                $aliases[] = array( 'alias' => $name, 'state' => 'absent', 'why' => '', 'path' => '' );
        }
        if ( !$aliases )
            return array();
        $states = array_column( $aliases, 'state' );
        $problem = in_array( 'missing', $states, true ) ? 'alias_missing' : ( in_array( 'stale', $states, true ) ? 'alias_stale' : 'alias_absent' );
        $path = '';
        foreach ( $aliases as $alias )
        {
            if ( $alias['path'] !== '' )
            {
                $path = $alias['path'];
                break;
            }
        }
        return array( array( 'problem' => $problem, 'path' => $path !== '' ? $path : $original['path'], 'name' => $original['name'], 'aliases' => $aliases ) );
    }

    /**
     * Keeps the aliases of a problem that the chosen problems ask for; drops the problem when none is left.
     *
     * @param array $problem from judge()
     * @param string[] $problems the chosen problems
     * @return array|null
     */
    public static function filterProblem( array $problem, array $problems )
    {
        if ( $problem['problem'] === 'missing' )
            return in_array( 'missing', $problems, true ) ? $problem : null;
        $kept = array();
        foreach ( $problem['aliases'] as $alias )
        {
            if ( in_array( 'alias_' . $alias['state'], $problems, true ) )
                $kept[] = $alias;
        }
        if ( !$kept )
            return null;
        $states = array_column( $kept, 'state' );
        $problem['aliases'] = $kept;
        $problem['problem'] = in_array( 'missing', $states, true ) ? 'alias_missing' : ( in_array( 'stale', $states, true ) ? 'alias_stale' : 'alias_absent' );
        return $problem;
    }

    // ------------------------------------------------------------------ the queries

    /**
     * The objects a page walks: published, with their main location below the start node, after an object id.
     *
     * @param string $subtreePattern from expContentCheck::subtreePattern()
     * @param array $permission the content/read condition on ezcontentobject_tree
     * @param int $after
     * @return string
     */
    public static function sourceQuery( $subtreePattern, array $permission, $after )
    {
        if ( !preg_match( '#^/([0-9]+/)+%$#', (string)$subtreePattern ) )
            throw new InvalidArgumentException( 'not a subtree pattern' );
        return 'SELECT ezcontentobject.id AS object_id, ezcontentobject.current_version AS current_version,' .
               ' ezcontentobject.name AS object_name, ezcontentobject.contentclass_id AS class_id, ezcontentobject_tree.node_id AS node_id' .
               ' FROM ezcontentobject, ezcontentobject_tree' . ( isset( $permission['from'] ) ? (string)$permission['from'] : '' ) .
               ' WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id' .
               ' AND ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id' .
               " AND ezcontentobject_tree.path_string LIKE '" . $subtreePattern . "'" .
               ' AND ezcontentobject.status = ' . eZContentObject::STATUS_PUBLISHED .
               ' AND ezcontentobject.id > ' . max( 0, (int)$after ) .
               ( isset( $permission['where'] ) ? ' ' . (string)$permission['where'] : '' ) .
               ' ORDER BY ezcontentobject.id ASC';
    }

    /**
     * The file-backed field versions of some objects.
     *
     * @param int[] $objectIDs
     * @param string[] $datatypes
     * @param int[] $versionStatuses
     * @return string
     */
    public static function attributeQuery( array $objectIDs, array $datatypes, array $versionStatuses )
    {
        $quoted = array();
        foreach ( $datatypes as $datatype )
        {
            if ( preg_match( '/^[a-z][a-z0-9_]{0,49}$/', (string)$datatype ) )
                $quoted[] = "'" . $datatype . "'";
        }
        return 'SELECT a.id, a.version, a.contentobject_id, a.contentclassattribute_id, a.language_code, a.data_type_string,' .
               ' a.data_text, a.data_int, v.status AS version_status, v.modified AS version_modified' .
               ' FROM ezcontentobject_attribute a, ezcontentobject_version v' .
               ' WHERE a.contentobject_id IN ( ' . expContentCheck::inList( $objectIDs ) . ' )' .
               ' AND a.data_type_string IN ( ' . ( $quoted ? implode( ', ', $quoted ) : "''" ) . ' )' .
               ' AND v.contentobject_id = a.contentobject_id AND v.version = a.version' .
               ' AND v.status IN ( ' . expContentCheck::inList( $versionStatuses ) . ' )' .
               ' ORDER BY a.contentobject_id ASC, a.id ASC, a.version ASC';
    }

    /**
     * The version statuses of the chosen versions.
     *
     * @param string[] $versions
     * @return int[]
     */
    public static function versionStatuses( array $versions )
    {
        $statuses = array();
        foreach ( $versions as $version )
        {
            if ( isset( self::VERSION_STATUSES[$version] ) )
                $statuses = array_merge( $statuses, self::VERSION_STATUSES[$version] );
        }
        return $statuses;
    }

    // ------------------------------------------------------------------ the list

    /**
     * One page of the list.
     *
     * @param array $params from parameters()
     * @return array array( 'node', 'rows', 'checked', 'last', 'done', 'error' => '' | not_found | database )
     */
    public static function scan( array $params, $describe = true )
    {
        $result = array( 'node' => null, 'rows' => array(), 'checked' => 0, 'last' => (int)$params['after'], 'done' => true, 'error' => '' );
        if ( !expContentCheck::isSupportedDatabase() )
            return array_merge( $result, array( 'error' => 'database' ) );
        $node = expContentCheck::readableNode( $params['node_id'] );
        $pattern = $node ? expContentCheck::subtreePattern( $node->attribute( 'path_string' ) ) : null;
        if ( !$node || $pattern === null )
            return array_merge( $result, array( 'error' => 'not_found' ) );
        $result['node'] = $node;

        $chunk = expContentCheck::intSetting( 'MissingFileSettings', 'ChunkSize', 200, 10, 1000 );
        $scanLimit = expContentCheck::intSetting( 'MissingFileSettings', 'ScanLimit', 5000, $chunk, 100000 );
        $db = eZDB::instance();
        $permission = expContentCheckUnreferenced::permission( $node->attribute( 'path_string' ), (int)$node->attribute( 'node_id' ) );
        try
        {
            $after = (int)$params['after'];
            while ( true )
            {
                $sources = $db->arrayQuery( self::sourceQuery( $pattern, $permission, $after ), array( 'offset' => 0, 'limit' => $chunk ) );
                $sources = is_array( $sources ) ? array_values( $sources ) : array();
                if ( !$sources )
                {
                    $result['done'] = true;
                    break;
                }
                $byObject = array();
                foreach ( self::rowsOfObjects( $sources, $params ) as $row )
                    $byObject[$row['object_id']][] = $row;
                $stopped = false;
                $index = 0;
                foreach ( $sources as $index => $source )
                {
                    $after = (int)$source['object_id'];
                    $result['checked']++;
                    if ( isset( $byObject[$after] ) )
                        $result['rows'] = array_merge( $result['rows'], $byObject[$after] );
                    // a page ends after a whole object
                    if ( count( $result['rows'] ) >= $params['limit'] || $result['checked'] >= $scanLimit )
                    {
                        $stopped = true;
                        break;
                    }
                }
                $result['last'] = $after;
                $lastOfAll = count( $sources ) < $chunk && $index === count( $sources ) - 1;
                if ( $stopped || $lastOfAll )
                {
                    $result['done'] = $lastOfAll;
                    break;
                }
            }
        }
        finally
        {
            if ( !empty( $permission['temp_tables'] ) )
                $db->dropTempTableList( $permission['temp_tables'] );
        }
        if ( $describe )
            if ( $describe )
            $result['rows'] = self::describe( $result['rows'] );
        return $result;
    }

    /**
     * Every finding below the node (not described), for a list with a total and pages: the objects are walked to
     * the end, at most [MissingFileSettings] ListLimit objects. Kept for ListCacheTTL seconds per user and filters, so
     * that paging does not walk them again; a resolution clears it (clearListCache()).
     *
     * @param array $params from parameters() (after and limit are not used)
     * @return array array( 'node', 'rows', 'checked', 'error', 'incomplete' => bool (stopped at ListLimit) )
     */
    public static function findAll( array $params )
    {
        $user = eZUser::currentUser();
        $current = eZSiteAccess::current();
        $key = md5( json_encode( array( (int)$params['node_id'], $params['problems'], $params['versions'],
                                        $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0,
                                        is_array( $current ) && isset( $current['name'] ) ? (string)$current['name'] : '' ) ) );
        $ttl = expContentCheck::intSetting( 'MissingFileSettings', 'ListCacheTTL', 60, 0, 3600 );
        $file = self::listCacheDirectory() . '/files-' . $key . '.php';
        $node = expContentCheck::readableNode( $params['node_id'] );
        if ( !$node )
            return array( 'node' => null, 'rows' => array(), 'checked' => 0, 'error' => 'not_found', 'incomplete' => false );
        if ( $ttl > 0 && is_file( $file ) && filemtime( $file ) > time() - $ttl )
        {
            $cached = @unserialize( (string)file_get_contents( $file ), array( 'allowed_classes' => false ) );
            if ( is_array( $cached ) && isset( $cached['rows'] ) )
                return array_merge( $cached, array( 'node' => $node ) );
        }
        $limit = expContentCheck::intSetting( 'MissingFileSettings', 'ListLimit', 50000, 100, 1000000 );
        $params['after'] = 0;
        $params['limit'] = 200;
        $all = array( 'rows' => array(), 'checked' => 0, 'error' => '', 'incomplete' => false );
        do
        {
            $result = self::scan( $params, false );
            if ( $result['error'] !== '' )
                return array( 'node' => $node, 'rows' => array(), 'checked' => 0, 'error' => $result['error'], 'incomplete' => false );
            $all['rows'] = array_merge( $all['rows'], $result['rows'] );
            $all['checked'] += $result['checked'];
            $params['after'] = $result['last'];
            if ( !$result['done'] && $all['checked'] >= $limit )
            {
                $all['incomplete'] = true;
                break;
            }
        }
        while ( !$result['done'] );
        if ( $ttl > 0 )
        {
            eZDir::mkdir( dirname( $file ), false, true );
            eZFile::create( basename( $file ), dirname( $file ), serialize( $all ), true );
        }
        return array_merge( $all, array( 'node' => $node ) );
    }

    /**
     * @return string the directory of the list cache
     */
    protected static function listCacheDirectory()
    {
        return eZSys::cacheDirectory() . '/contentcheck';
    }

    /**
     * Forgets every kept list (after a resolution changed what is missing).
     */
    public static function clearListCache()
    {
        foreach ( glob( self::listCacheDirectory() . '/files-*.php' ) ?: array() as $file )
            @unlink( $file );
    }

    /**
     * The problem rows of some objects (sources of sourceQuery()).
     *
     * @param array $sources
     * @param array $params problems, versions
     * @return array rows, not yet described
     */
    public static function rowsOfObjects( array $sources, array $params )
    {
        $handlers = self::handlers();
        $statuses = self::versionStatuses( $params['versions'] );
        if ( !$handlers || !$statuses || !$sources )
            return array();
        $objects = array();
        foreach ( $sources as $source )
            $objects[(int)$source['object_id']] = $source;
        $attributes = eZDB::instance()->arrayQuery( self::attributeQuery( array_keys( $objects ), array_keys( $handlers ), $statuses ) );
        return self::rowsOfAttributes( is_array( $attributes ) ? $attributes : array(), $objects, $params['problems'] );
    }

    /**
     * The problem rows of field versions.
     *
     * @param array $attributes rows of attributeQuery()
     * @param array $objects object id => array( object_id, object_name, node_id, class_id, current_version )
     * @param string[] $problems
     * @return array
     */
    public static function rowsOfAttributes( array $attributes, array $objects, array $problems )
    {
        $byDatatype = array();
        foreach ( $attributes as $attribute )
            $byDatatype[(string)$attribute['data_type_string']][] = $attribute;
        $aliasKeys = null;
        $expected = array();
        $expiry = 0;
        $exists = array( __CLASS__, 'exists' );
        $mtime = array( __CLASS__, 'mtime' );
        $rows = array();
        foreach ( $byDatatype as $datatype => $list )
        {
            $handler = self::handler( $datatype );
            if ( !$handler )
                continue;
            if ( $handler->hasAliases() && $aliasKeys === null )
            {
                $aliasKeys = self::aliasKeys();
                $expected = in_array( 'alias_absent', $problems, true ) ? self::expectedAliases( $aliasKeys ) : array();
                $expiry = self::aliasExpiry();
            }
            $files = $handler->files( $list );
            foreach ( $list as $attribute )
            {
                $key = self::key( $attribute['id'], $attribute['version'] );
                if ( empty( $files[$key] ) )
                    continue;
                $objectID = (int)$attribute['contentobject_id'];
                $object = isset( $objects[$objectID] ) ? $objects[$objectID] : array();
                foreach ( self::judge( $files[$key], $exists, $mtime, $handler->hasAliases() ? $aliasKeys : array(), $expected, $expiry ) as $problem )
                {
                    $problem = self::filterProblem( $problem, $problems );
                    if ( $problem === null )
                        continue;
                    $rows[] = array( 'key' => $key, 'object_id' => $objectID,
                                     'object_name' => isset( $object['object_name'] ) ? (string)$object['object_name'] : '',
                                     'node_id' => isset( $object['node_id'] ) ? (int)$object['node_id'] : 0,
                                     'class_id' => isset( $object['class_id'] ) ? (int)$object['class_id'] : 0,
                                     'attribute_id' => (int)$attribute['id'], 'class_attribute_id' => (int)$attribute['contentclassattribute_id'],
                                     'version' => (int)$attribute['version'],
                                     'version_status' => (int)$attribute['version_status'] === 1 ? 'published' : 'draft',
                                     'language' => (string)$attribute['language_code'], 'datatype' => $datatype,
                                     'has_aliases' => $handler->hasAliases(),
                                     'problem' => $problem['problem'], 'path' => $problem['path'], 'file_name' => (string)$problem['name'],
                                     'aliases' => $problem['aliases'],
                                     'modified' => (int)$attribute['version_modified'],
                                     'file_modified' => $problem['problem'] === 'missing' ? 0 : (int)self::mtime( $problem['path'] ) );
                }
            }
        }
        return $rows;
    }

    /**
     * Adds what a page shows: the field's name, the review mark, whether the user may fix the object.
     *
     * @param array $rows
     * @return array
     */
    public static function describe( array $rows )
    {
        if ( !$rows )
            return array();
        $classAttributeIDs = array();
        $objectIDs = array();
        foreach ( $rows as $row )
        {
            $classAttributeIDs[$row['class_attribute_id']] = true;
            $objectIDs[$row['object_id']] = true;
        }
        $fieldNames = array();
        $list = eZContentClassAttribute::fetchFilteredList( array( 'id' => array( array_keys( $classAttributeIDs ) ),
                                                                   'version' => eZContentClass::VERSION_STATUS_DEFINED ) );
        foreach ( is_array( $list ) ? $list : array() as $classAttribute )
            $fieldNames[(int)$classAttribute->attribute( 'id' )] = (string)$classAttribute->attribute( 'name' );
        $canEdit = array();
        $objects = eZContentObject::fetchIDArray( array_keys( $objectIDs ) );
        foreach ( is_array( $objects ) ? $objects : array() as $object )
            $canEdit[(int)$object->attribute( 'id' )] = (bool)$object->canEdit();
        $marks = self::reviewMarks( array_column( $rows, 'key' ) );
        foreach ( $rows as $i => $row )
        {
            $rows[$i]['field_name'] = isset( $fieldNames[$row['class_attribute_id']] ) ? $fieldNames[$row['class_attribute_id']] : '';
            $rows[$i]['can_fix'] = !empty( $canEdit[$row['object_id']] );
            $rows[$i]['review'] = isset( $marks[$row['key']] ) ? $marks[$row['key']] : null;
        }
        return $rows;
    }

    /**
     * The current problem rows of some field versions, whatever the list's filters: what a fix plans with.
     * Only field versions of objects the user may read.
     *
     * @param array $keys from keyList()
     * @param string[]|null $problems null: every problem but alias_absent (an alias never made is only a problem
     *                                when asked for)
     * @return array key => list of rows (described)
     */
    public static function inspect( array $keys, ?array $problems = null )
    {
        $found = array();
        if ( !$keys )
            return $found;
        $db = eZDB::instance();
        $conditions = array();
        foreach ( $keys as $pair )
            $conditions[] = '( a.id = ' . (int)$pair[0] . ' AND a.version = ' . (int)$pair[1] . ' )';
        $attributes = array();
        foreach ( array_chunk( $conditions, 100 ) as $part )
        {
            $rows = $db->arrayQuery( 'SELECT a.id, a.version, a.contentobject_id, a.contentclassattribute_id, a.language_code, a.data_type_string,' .
                                     ' a.data_text, a.data_int, v.status AS version_status, v.modified AS version_modified' .
                                     ' FROM ezcontentobject_attribute a, ezcontentobject_version v' .
                                     ' WHERE ( ' . implode( ' OR ', $part ) . ' )' .
                                     ' AND v.contentobject_id = a.contentobject_id AND v.version = a.version' .
                                     ' AND v.status IN ( ' . expContentCheck::inList( self::versionStatuses( self::VERSIONS ) ) . ' )' );
            $attributes = array_merge( $attributes, is_array( $rows ) ? $rows : array() );
        }
        $objectIDs = array_values( array_unique( array_map( 'intval', array_column( $attributes, 'contentobject_id' ) ) ) );
        $objects = array();
        $fetched = $objectIDs ? eZContentObject::fetchIDArray( $objectIDs ) : array();
        foreach ( is_array( $fetched ) ? $fetched : array() as $object )
        {
            if ( (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED || !$object->canRead() )
                continue;
            $objects[(int)$object->attribute( 'id' )] = array( 'object_id' => (int)$object->attribute( 'id' ), 'object_name' => (string)$object->attribute( 'name' ),
                                                              'node_id' => (int)$object->attribute( 'main_node_id' ), 'class_id' => (int)$object->attribute( 'contentclass_id' ),
                                                              'current_version' => (int)$object->attribute( 'current_version' ) );
        }
        $readable = array();
        foreach ( $attributes as $attribute )
        {
            if ( isset( $objects[(int)$attribute['contentobject_id']] ) )
                $readable[] = $attribute;
        }
        foreach ( self::describe( self::rowsOfAttributes( $readable, $objects, $problems === null ? self::DEFAULT_PROBLEMS : $problems ) ) as $row )
            $found[$row['key']][] = $row;
        return $found;
    }

    // ------------------------------------------------------------------ the target of the broken relations

    /**
     * What the broken relations' target choice (Kind[]) asks of the missing files.
     *
     * @param mixed $kinds the request's Kind[]
     * @return array array( 'enabled' => bool (missing_file is ticked), 'only' => bool (and no target of the broken
     *               relations is) )
     */
    public static function target( $kinds )
    {
        $kinds = is_array( $kinds ) ? array_values( array_filter( $kinds, 'is_string' ) ) : array();
        $enabled = in_array( self::TARGET, $kinds, true );
        $others = array_intersect( expContentCheckBrokenRelations::KINDS, $kinds );
        return array( 'enabled' => $enabled, 'only' => $enabled && !$others );
    }

    /**
     * The missing files section of the broken relations page: the page of findings and what the template needs.
     *
     * @param array $get the request's GET fields: Problem[], Version[], ProblemChoice, VersionChoice, SelectAll
     * @param int $nodeID the page's start node
     * @param int $limit the page size
     * @param int $offset the first finding of the page
     * @return array array( 'enabled' => true, 'result' => scan(), 'params', 'problems', 'versions', 'storage_dir',
     *               'can_fix', 'actions', 'max_items', 'form_token', 'token_field', 'feedback' )
     */
    public static function section( array $get, $nodeID, $limit, $offset = 0 )
    {
        $input = array( 'NodeID' => (int)$nodeID, 'Limit' => (int)$limit );
        foreach ( array( 'Problem' => 'Problem', 'Version' => 'Version' ) as $field => $name )
        {
            if ( isset( $get[$field] ) )
                $input[$name] = $get[$field];
        }
        // "Select all" and "Select none" of the filter groups: submit buttons, so they work without JavaScript
        foreach ( array( 'Problem' => self::PROBLEMS, 'Version' => self::VERSIONS ) as $name => $all )
        {
            $choice = isset( $get[$name . 'Choice'] ) && is_string( $get[$name . 'Choice'] ) ? $get[$name . 'Choice'] : '';
            if ( $choice === 'all' )
                $input[$name] = $all;
        }
        $params = self::parameters( $input, (int)$nodeID );
        foreach ( array( 'Problem' => 'problems', 'Version' => 'versions' ) as $name => $key )
        {
            if ( isset( $get[$name . 'Choice'] ) && $get[$name . 'Choice'] === 'none' )
                $params[$key] = array();
        }
        $canFix = class_exists( 'expContentCheckFileFix' ) && expContentCheck::hasPolicy( 'contentcheck', 'fixfiles' );
        $feedback = null;
        $http = eZHTTPTool::instance();
        if ( $http->hasSessionVariable( self::FEEDBACK ) )
        {
            $feedback = $http->sessionVariable( self::FEEDBACK );
            $http->removeSessionVariable( self::FEEDBACK );
            if ( !is_array( $feedback ) )
                $feedback = null;
        }
        // nothing ticked in a filter group: nothing to look for
        if ( $params['problems'] && $params['versions'] )
            $result = self::findAll( $params );
        else
            $result = array( 'node' => expContentCheck::readableNode( (int)$nodeID ), 'rows' => array(), 'checked' => 0, 'error' => '',
                             'incomplete' => false, 'nothing_chosen' => true );
        // the page: offset (file_offset in the address) and size
        $result['count'] = count( $result['rows'] );
        $offset = max( 0, (int)$offset );
        if ( $offset >= $result['count'] )
            $offset = $result['count'] > 0 ? (int)( floor( ( $result['count'] - 1 ) / max( 1, (int)$limit ) ) * max( 1, (int)$limit ) ) : 0;
        $result['offset'] = $offset;
        $result['rows'] = self::describe( array_slice( $result['rows'], $offset, max( 1, (int)$limit ) ) );
        $params['offset'] = $offset;
        return array( 'enabled' => true, 'result' => $result, 'params' => $params,
                      'select_all' => isset( $get['SelectAll'] ) && $get['SelectAll'] === '1',
                      'problems' => self::PROBLEMS, 'versions' => self::VERSIONS, 'storage_dir' => eZSys::storageDirectory(),
                      'can_fix' => $canFix, 'actions' => $canFix ? expContentCheckFileFix::ACTIONS : array(),
                      'max_items' => $canFix ? expContentCheckFileFix::maxItems() : 0,
                      'form_token' => $canFix ? expContentCheck::formToken() : '', 'token_field' => expContentCheck::TOKEN_FIELD,
                      'feedback' => $feedback );
    }

    /**
     * The query string of the broken relations page with the missing files asked for: every target ticked (so that
     * missing_file alone does not read as "only missing files"), and the missing files' own filters.
     *
     * @param string $suffix the page's query string without them ('?...')
     * @param string[] $kinds the targets of the broken relations ticked (none: only the missing files)
     * @param array $params from parameters()
     * @return string starting with ?
     */
    public static function querySuffix( $suffix, array $kinds, array $params )
    {
        $parts = array();
        foreach ( explode( '&', ltrim( (string)$suffix, '?' ) ) as $part )
        {
            if ( $part !== '' && strpos( $part, 'Kind%5B%5D=' ) !== 0 )
                $parts[] = $part;
        }
        foreach ( $kinds as $kind )
            $parts[] = 'Kind%5B%5D=' . rawurlencode( $kind );
        $parts[] = 'Kind%5B%5D=' . self::TARGET;
        if ( $params['problems'] !== self::DEFAULT_PROBLEMS )
        {
            foreach ( $params['problems'] as $problem )
                $parts[] = 'Problem%5B%5D=' . rawurlencode( $problem );
        }
        if ( count( $params['versions'] ) < count( self::VERSIONS ) )
        {
            foreach ( $params['versions'] as $version )
                $parts[] = 'Version%5B%5D=' . rawurlencode( $version );
        }
        return '?' . implode( '&', $parts );
    }

    /**
     * The broken relations page's own result when only the missing files are asked for: no source checked.
     *
     * @param int $nodeID
     * @param int $after
     * @return array as expContentCheckBrokenRelations::scan()
     */
    public static function noBrokenRelations( $nodeID, $after )
    {
        $node = expContentCheck::readableNode( (int)$nodeID );
        return array( 'node' => $node, 'excluded' => array(), 'findings' => array(), 'layouts' => array(), 'checked' => 0,
                      'last' => (int)$after, 'done' => true, 'error' => $node ? '' : 'not_found' );
    }

    // ------------------------------------------------------------------ review marks

    /**
     * The review marks of some field versions.
     *
     * @param string[] $keys
     * @return array key => array( 'user_id', 'time', 'problem', 'path' )
     */
    public static function reviewMarks( array $keys )
    {
        $marks = array();
        $names = array();
        foreach ( $keys as $key )
        {
            if ( preg_match( '/^[0-9]{1,10}-[0-9]{1,6}$/', (string)$key ) )
                $names[] = "'" . self::reviewName( $key ) . "'";
        }
        if ( !$names )
            return $marks;
        $rows = eZDB::instance()->arrayQuery( 'SELECT name, value FROM ezsite_data WHERE name IN ( ' . implode( ', ', array_unique( $names ) ) . ' )' );
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $value = json_decode( (string)$row['value'], true );
            if ( is_array( $value ) )
                $marks[str_replace( '.', '-', substr( (string)$row['name'], strlen( self::REVIEW_PREFIX ) ) )] = $value;
        }
        return $marks;
    }

    /**
     * @param string $key "<attribute id>-<version>"
     * @return string the name of its review mark in ezsite_data
     */
    public static function reviewName( $key )
    {
        return self::REVIEW_PREFIX . str_replace( '-', '.', (string)$key );
    }
}

?>
