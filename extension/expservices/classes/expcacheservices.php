<?php
/**
 * expcache: the caches (setup/managecache): what exists, their state, and clearing them.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expCacheServices extends expServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'Every cache with its id, name, tags and how it is cleared',
            'access' => array( 'setup', 'managecache' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of id, name, tags, enabled, how' ),
        'tags' => array( 'summary' => 'The cache tags with the cache ids each clears',
            'access' => array( 'setup', 'managecache' ), 'write' => false, 'args' => array(), 'returns' => 'list of tag, ids' ),
        'describe' => array( 'summary' => 'One cache by id, with its path and the size of its files',
            'access' => array( 'setup', 'managecache' ), 'write' => false, 'args' => array( 'id' => 'string' ), 'returns' => 'id, name, tags, how, path, files, bytes' ),
        'php' => array( 'summary' => 'The PHP level caches: OPcache and APCu state',
            'access' => array( 'setup', 'managecache' ), 'write' => false, 'args' => array(), 'returns' => 'opcache, apcu' ),
        'velocity' => array( 'summary' => 'Velocity response cache: where it is, how many files, when cleared',
            'access' => array( 'setup', 'managecache' ), 'write' => false, 'args' => array(), 'returns' => 'result with data' ),
        'http' => array( 'summary' => 'The HTTP cache: whether it is enabled and its status',
            'access' => array( 'setup', 'managecache' ), 'write' => false, 'args' => array(), 'returns' => 'enabled, status' ),
        'staticcache' => array( 'summary' => 'The static cache: whether it is enabled and its status',
            'access' => array( 'setup', 'managecache' ), 'write' => false, 'args' => array(), 'returns' => 'enabled, status' ),
        'query' => array( 'summary' => 'The database query cache state',
            'access' => array( 'setup', 'managecache' ), 'write' => false, 'args' => array(), 'returns' => 'available, status' ),
        'clear' => array( 'summary' => 'Clears caches: POST by=all|tag|id, names=comma list, dry_run=1 to only report',
            'access' => array( 'setup', 'managecache' ), 'write' => true, 'args' => array( 'by' => 'string (POST)', 'names' => 'list (POST)', 'dry_run' => 'bool (POST)' ), 'returns' => 'result: ok, message, items, dry_run' ),
        'cleartag' => array( 'summary' => 'Clears the caches of one tag (POST tag, dry_run)',
            'access' => array( 'setup', 'managecache' ), 'write' => true, 'args' => array( 'tag' => 'string (POST)', 'dry_run' => 'bool (POST)' ), 'returns' => 'result' ),
        'clearid' => array( 'summary' => 'Clears one cache by id (POST id, dry_run)',
            'access' => array( 'setup', 'managecache' ), 'write' => true, 'args' => array( 'id' => 'string (POST)', 'dry_run' => 'bool (POST)' ), 'returns' => 'result' ),
        'clearnode' => array( 'summary' => 'Clears the view cache of a node (POST node_id)',
            'access' => array( 'setup', 'managecache' ), 'write' => true, 'args' => array( 'node_id' => 'int (POST)' ), 'returns' => 'result' ),
        'clearvelocity' => array( 'summary' => 'Clears the Velocity response cache (POST dry_run)',
            'access' => array( 'setup', 'managecache' ), 'write' => true, 'args' => array( 'dry_run' => 'bool (POST)' ), 'returns' => 'result' ),
        'clearopcache' => array( 'summary' => 'Resets OPcache of this PHP process pool (POST dry_run)',
            'access' => array( 'setup', 'managecache' ), 'write' => true, 'args' => array( 'dry_run' => 'bool (POST)' ), 'returns' => 'result' ),
    );

    /** @var expCacheManager|null replaceable in tests */
    public static $manager = null;

    protected static function manager()
    {
        return self::$manager !== null ? self::$manager : new expCacheManager();
    }


    public static function list( $args )
    {
        self::guard( __FUNCTION__ );
        $manager = self::manager();
        $items = array();
        foreach ( $manager->cacheList() as $item )
        {
            $d = $manager->describeItem( $item );
            $items[] = array( 'id' => $d['id'], 'name' => $d['name'], 'tags' => $d['tags'], 'enabled' => $d['enabled'], 'how' => $d['how'] );
        }
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function tags( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( self::manager()->tagMap() as $tag => $ids )
            $list[] = array( 'tag' => $tag, 'ids' => $ids );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function describe( $args )
    {
        self::guard( __FUNCTION__ );
        $id = self::arg( $args, 0, 'string' );
        $manager = self::manager();
        foreach ( $manager->cacheList() as $item )
        {
            if ( isset( $item['id'] ) && $item['id'] === $id )
                return self::ok( $manager->describeItem( $item, true ) );
        }
        throw new expServiceException( "No cache '$id'", 404 );
    }

    public static function php( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( expCacheManager::phpCacheState() );
    }

    public static function velocity( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( expCacheManager::velocityCacheStatus() );
    }

    public static function http( $args )
    {
        self::guard( __FUNCTION__ );
        $enabled = expCacheManager::httpCacheEnabled();
        return self::ok( array( 'enabled' => (bool)$enabled, 'status' => $enabled ? expCacheManager::httpCacheStatus() : null ) );
    }

    public static function staticcache( $args )
    {
        self::guard( __FUNCTION__ );
        $enabled = expCacheManager::staticCacheEnabled();
        return self::ok( array( 'enabled' => (bool)$enabled, 'status' => $enabled ? expCacheManager::staticCacheStatus() : null ) );
    }

    public static function query( $args )
    {
        self::guard( __FUNCTION__ );
        $available = expCacheManager::queryCacheAvailable();
        return self::ok( array( 'available' => (bool)$available, 'status' => $available ? expCacheManager::queryCacheStatus() : null ) );
    }

    /** Runs a clear of the manager and answers with its result; an unsuccessful result is a 422. */
    protected static function cleared( array $result )
    {
        if ( empty( $result['ok'] ) )
            throw new expServiceException( isset( $result['message'] ) ? $result['message'] : 'The cache could not be cleared', 422 );
        return self::ok( $result );
    }

    public static function clear( $args )
    {
        self::guard( __FUNCTION__ );
        $by = self::post( 'by', 'string' );
        if ( !in_array( $by, array( 'all', 'tag', 'id' ), true ) )
            throw new expServiceException( "by is all, tag or id", 400 );
        $names = self::post( 'names', 'list', array() );
        if ( $by !== 'all' && !$names )
            throw new expServiceException( 'names is required for by=' . $by, 400 );
        return self::cleared( self::manager()->clear( $by, $names, self::post( 'dry_run', 'bool', false ) ) );
    }

    public static function cleartag( $args )
    {
        self::guard( __FUNCTION__ );
        return self::cleared( self::manager()->clear( 'tag', array( self::post( 'tag', 'string' ) ), self::post( 'dry_run', 'bool', false ) ) );
    }

    public static function clearid( $args )
    {
        self::guard( __FUNCTION__ );
        return self::cleared( self::manager()->clear( 'id', array( self::post( 'id', 'string' ) ), self::post( 'dry_run', 'bool', false ) ) );
    }

    public static function clearnode( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::node( self::post( 'node_id', 'int' ), 'read' );
        eZContentCacheManager::clearNodeViewCacheArray( array( (int)$node->attribute( 'node_id' ) ), array( (int)$node->attribute( 'contentobject_id' ) ) );
        return self::ok( array( 'ok' => true, 'message' => 'cleared the view cache of node ' . (int)$node->attribute( 'node_id' ) ) );
    }

    public static function clearvelocity( $args )
    {
        self::guard( __FUNCTION__ );
        return self::cleared( expCacheManager::clearVelocityCache( self::post( 'dry_run', 'bool', false ) ) );
    }

    public static function clearopcache( $args )
    {
        self::guard( __FUNCTION__ );
        return self::cleared( expCacheManager::resetOPcache( self::post( 'dry_run', 'bool', false ) ) );
    }
}
