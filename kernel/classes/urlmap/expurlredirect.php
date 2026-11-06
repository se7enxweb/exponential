<?php
/**
 * File containing the expUrlRedirect class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A redirect of one old address, checked on the full request before the URL aliases and wildcards: a row of the table
 * exp_url_redirect. It holds what a global URL alias cannot: an old address with a query (/index.php?id=12, the
 * addresses of an earlier CMS), a temporary redirect (302, 303, 307) or 308, and a target on another site.
 *
 * Matching (predictable on purpose):
 * - the path is compared after the normalization of expUrlMapRow::normalizePath() (one slash between segments, no
 *   trailing slash, dot segments resolved, percent-encoded UTF-8 decoded) and without regard to case, as aliases
 *   and wildcards are; it is tried as the request's whole path (/index.php, /eng/old) and as the path inside the
 *   siteaccess (/old when /eng is the siteaccess's URI part)
 * - the query is compared exactly, but the order of its parameters is ignored: the parameters are decoded ("+" is a
 *   space), "a" counts as "a=", and they are sorted by name, then value (canonicalQuery()). A redirect without a
 *   query only answers a request without a query. The request's query is not carried over to the target.
 * - a redirect of a siteaccess answers only in it; one without a siteaccess (empty) in every siteaccess, after those
 *   of the siteaccess
 *
 * The target is node:<id> (the node's address, found at the time of the request, so it follows the node when it
 * moves), a path of this site, or a full URL. The redirects are read from a cache file (var/<site>/cache/wildcard/,
 * cleared with the URL alias cache) that is built again after expireCache(); without the table nothing is read.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlRedirect extends eZPersistentObject
{
    const TABLE = 'exp_url_redirect';

    /** The expiry key of the cache */
    const CACHE_SIGNATURE = 'url-redirect';

    /** The statuses a redirect can answer with */
    const STATUSES = array( 301, 302, 303, 307, 308 );

    /** @var array|null source key => list of entries, for this request */
    protected static $index = null;

    /** @var bool|null whether the table exists, for this request */
    protected static $tableExists = null;

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $int = array( 'datatype' => 'integer', 'default' => 0, 'required' => true );
            $string = array( 'datatype' => 'string', 'default' => '', 'required' => true );
            $definition = array(
                'fields' => array(
                    'id' => array( 'name' => 'ID' ) + $int,
                    'siteaccess' => array( 'name' => 'Siteaccess' ) + $string,
                    'language' => array( 'name' => 'Language' ) + $string,
                    'source_key' => array( 'name' => 'SourceKey' ) + $string,
                    'source_path' => array( 'name' => 'SourcePath' ) + $string,
                    'source_query' => array( 'name' => 'SourceQuery' ) + $string,
                    'target' => array( 'name' => 'Target' ) + $string,
                    'status' => array( 'name' => 'Status', 'datatype' => 'integer', 'default' => 301, 'required' => true ),
                    'batch_id' => array( 'name' => 'BatchID' ) + $int,
                    'created' => array( 'name' => 'Created' ) + $int,
                    'modified' => array( 'name' => 'Modified' ) + $int,
                ),
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array( 'source' => 'source' ),
                'sort' => array( 'id' => 'asc' ),
                'class_name' => 'expUrlRedirect',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }

    /**
     * A new redirect (not stored).
     *
     * @param string $path
     * @param string $query
     * @param string $target node:<id>, a path or a full URL
     * @param int $status
     * @param string $siteaccess
     * @param string $language
     * @param int $batchId
     * @return expUrlRedirect
     */
    public static function create( $path, $query, $target, $status = 301, $siteaccess = '', $language = '', $batchId = 0 )
    {
        $path = self::normalizePath( $path );
        $query = self::canonicalQuery( $query );
        $now = time();
        return new self( array( 'siteaccess' => (string)$siteaccess, 'language' => (string)$language,
                                'source_key' => self::sourceKey( $path, $query ), 'source_path' => $path, 'source_query' => $query,
                                'target' => (string)$target, 'status' => in_array( (int)$status, self::STATUSES, true ) ? (int)$status : 301,
                                'batch_id' => (int)$batchId, 'created' => $now, 'modified' => $now ) );
    }

    /** @return string the old address: path and query */
    public function source()
    {
        $query = (string)$this->attribute( 'source_query' );
        return $this->attribute( 'source_path' ) . ( $query !== '' ? '?' . $query : '' );
    }

    /** @return array the columns, as stored (for the record of an import) */
    public function asArray()
    {
        $row = array();
        foreach ( array_keys( self::definition()['fields'] ) as $name )
            $row[$name] = $this->attribute( $name );
        $row['id'] = (int)$row['id'];
        $row['status'] = (int)$row['status'];
        $row['batch_id'] = (int)$row['batch_id'];
        $row['created'] = (int)$row['created'];
        $row['modified'] = (int)$row['modified'];
        return $row;
    }

    /**
     * A path as it is compared: normalized as expUrlMapRow::normalizePath(). Pure.
     *
     * @param string $path
     * @return string
     */
    public static function normalizePath( $path )
    {
        return expUrlMapRow::normalizePath( (string)$path );
    }

    /**
     * A query as it is compared: each parameter decoded ("+" is a space; "a" is "a="), sorted by name, then value,
     * and encoded again (RFC 3986). Pure.
     *
     * @param string $query with or without "?"
     * @return string
     */
    public static function canonicalQuery( $query )
    {
        $query = ltrim( (string)$query, '?' );
        $pairs = array();
        foreach ( explode( '&', $query ) as $part )
        {
            if ( $part === '' )
                continue;
            $equals = strpos( $part, '=' );
            $name = urldecode( $equals === false ? $part : substr( $part, 0, $equals ) );
            $value = $equals === false ? '' : urldecode( substr( $part, $equals + 1 ) );
            if ( $name === '' )
                continue;
            $pairs[] = array( $name, $value );
        }
        usort( $pairs, function ( $a, $b ) {
            return strcmp( $a[0], $b[0] ) ?: strcmp( $a[1], $b[1] );
        } );
        $out = array();
        foreach ( $pairs as $pair )
            $out[] = rawurlencode( $pair[0] ) . '=' . rawurlencode( $pair[1] );
        return implode( '&', $out );
    }

    /**
     * The key a path and a query are looked up by: the path in lower case and the canonical query. Pure.
     *
     * @param string $path
     * @param string $query
     * @return string 32 hex digits
     */
    public static function sourceKey( $path, $query )
    {
        $path = self::normalizePath( $path );
        $lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $path, 'UTF-8' ) : strtolower( $path );
        return md5( $lower . '?' . self::canonicalQuery( $query ) );
    }

    /**
     * Whether the table exists (false also when it cannot be told).
     *
     * @param eZDBInterface|null $db
     * @return bool
     */
    public static function tableExists( $db = null )
    {
        if ( $db !== null )
            return expKernelTableSchema::exists( self::TABLE, $db ) === true;
        if ( self::$tableExists === null )
            self::$tableExists = expKernelTableSchema::exists( self::TABLE ) === true;
        return self::$tableExists;
    }

    /**
     * The redirect of a siteaccess for an old address, or null.
     *
     * @param string $siteaccess
     * @param string $path
     * @param string $query
     * @return expUrlRedirect|null
     */
    public static function fetchBySource( $siteaccess, $path, $query )
    {
        if ( !self::tableExists() )
            return null;
        $list = eZPersistentObject::fetchObjectList( self::definition(), null,
                                                     array( 'source_key' => self::sourceKey( $path, $query ), 'siteaccess' => (string)$siteaccess ),
                                                     array( 'id' => 'asc' ), null, true );
        return is_array( $list ) && $list ? $list[0] : null;
    }

    /**
     * @param int $id
     * @return expUrlRedirect|null
     */
    public static function fetch( $id )
    {
        if ( !self::tableExists() )
            return null;
        $object = eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ), true );
        return $object instanceof self ? $object : null;
    }

    /**
     * The redirects, in the order of their ids.
     *
     * @param array $conditions column => value (siteaccess, language, batch_id)
     * @param int $offset
     * @param int $limit 0 for all
     * @return expUrlRedirect[]
     */
    public static function fetchList( array $conditions = array(), $offset = 0, $limit = 0 )
    {
        if ( !self::tableExists() )
            return array();
        $list = eZPersistentObject::fetchObjectList( self::definition(), null, $conditions ?: null, array( 'id' => 'asc' ),
                                                     $limit > 0 ? array( 'offset' => (int)$offset, 'length' => (int)$limit ) : null, true );
        return is_array( $list ) ? $list : array();
    }

    /**
     * The redirect that answers a request, or null.
     *
     * @param string $siteaccess the current siteaccess
     * @param string[] $paths the request's whole path and its path inside the siteaccess (as typed, decoded or not)
     * @param string $query the request's query, with or without "?"
     * @param array|null $index the index to use (tests); null for the cached one
     * @return array|null id, target, status, language, siteaccess
     */
    public static function match( $siteaccess, array $paths, $query, ?array $index = null )
    {
        if ( $index === null )
            $index = self::index();
        if ( !$index )
            return null;
        foreach ( array_unique( $paths ) as $path )
        {
            $key = self::sourceKey( $path, $query );
            if ( !isset( $index[$key] ) )
                continue;
            $any = null;
            foreach ( $index[$key] as $entry )
            {
                if ( $entry['siteaccess'] === (string)$siteaccess )
                    return $entry;
                if ( $entry['siteaccess'] === '' && $any === null )
                    $any = $entry;
            }
            if ( $any !== null )
                return $any;
        }
        return null;
    }

    /**
     * Where a redirect sends the visitor: content/view/full/<id> for node:<id> (the kernel writes the node's address
     * for it), a path without its leading slash, or a full URL. Pure.
     *
     * @param string $target
     * @return string
     */
    public static function location( $target )
    {
        $target = (string)$target;
        if ( preg_match( '#^node:(\d+)$#', $target, $m ) )
            return 'content/view/full/' . (int)$m[1];
        if ( preg_match( '#^(?:[a-z][a-z0-9+.\-]*:|//)#i', $target ) )
            return $target;
        return ltrim( $target, '/' );
    }

    /**
     * The redirect of the current web request, or null: what ezpKernelWeb answers before the URL aliases.
     *
     * @param string $siteaccess
     * @param string $requestUri the request's REQUEST_URI (path and query)
     * @param string $uriString the path inside the siteaccess (eZURI::uriString())
     * @param string $queryString the request's query
     * @return array|null location, status, id
     */
    public static function forRequest( $siteaccess, $requestUri, $uriString, $queryString )
    {
        $index = self::index();
        if ( !$index )
            return null;
        $requestPath = (string)parse_url( '/' . ltrim( (string)$requestUri, '/' ), PHP_URL_PATH );
        $entry = self::match( $siteaccess, array( $requestPath, '/' . ltrim( (string)$uriString, '/' ) ), (string)$queryString, $index );
        if ( $entry === null )
            return null;
        return array( 'location' => self::location( $entry['target'] ), 'status' => (int)$entry['status'], 'id' => (int)$entry['id'] );
    }

    /**
     * The redirects by source key, from the cache file (built from the table when it is older than the last
     * expireCache()); empty without the table.
     *
     * @return array source key => list of array( id, siteaccess, language, target, status )
     */
    public static function index()
    {
        if ( self::$index !== null )
            return self::$index;
        self::$index = array();
        try
        {
            $file = eZClusterFileHandler::instance( self::cachePath() );
            $index = $file->processFile( array( __CLASS__, 'fetchCacheFile' ), self::expiryTimestamp() );
            if ( !is_array( $index ) )
            {
                $index = self::buildIndex();
                $file->storeContents( "<?php\nreturn " . var_export( $index, true ) . ";\n", 'url-redirect', 'php', true );
            }
            self::$index = $index;
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'The redirects of exp_url_redirect cannot be read: ' . $e->getMessage(), __METHOD__ );
        }
        return self::$index;
    }

    /**
     * The index read from the table. Pure but for the database.
     *
     * @return array
     */
    public static function buildIndex()
    {
        $index = array();
        if ( !self::tableExists() )
            return $index;
        foreach ( self::fetchList() as $redirect )
        {
            $index[(string)$redirect->attribute( 'source_key' )][] = array(
                'id' => (int)$redirect->attribute( 'id' ), 'siteaccess' => (string)$redirect->attribute( 'siteaccess' ),
                'language' => (string)$redirect->attribute( 'language' ), 'target' => (string)$redirect->attribute( 'target' ),
                'status' => (int)$redirect->attribute( 'status' ) );
        }
        return $index;
    }

    /**
     * Includes a cache file (callback of eZClusterFileHandler::processFile()).
     *
     * @param string $path
     * @return mixed
     */
    public static function fetchCacheFile( $path )
    {
        return include $path;
    }

    /** The redirects are read again from the table on the next request. */
    public static function expireCache()
    {
        $handler = eZExpiryHandler::instance();
        $handler->setTimestamp( self::CACHE_SIGNATURE, time() );
        $handler->store();
        // the file goes too: one built in the same second as the expiry would otherwise count as current
        try
        {
            eZClusterFileHandler::instance( self::cachePath() )->delete();
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'The cache of exp_url_redirect was not removed: ' . $e->getMessage(), __METHOD__ );
        }
        self::resetRequest();
    }

    /** Forgets what this request read (a persistent worker calls it for each request). */
    public static function resetRequest()
    {
        self::$index = null;
        self::$tableExists = null;
    }

    /** @return int|false */
    protected static function expiryTimestamp()
    {
        $handler = eZExpiryHandler::instance();
        return $handler->hasTimestamp( self::CACHE_SIGNATURE ) ? $handler->timestamp( self::CACHE_SIGNATURE ) : false;
    }

    /** @return string the cache file, next to the wildcard cache (cleared with the URL alias cache) */
    protected static function cachePath()
    {
        $ini = eZINI::instance();
        $key = md5( implode( "\n", array( $ini->variable( 'DatabaseSettings', 'DatabaseImplementation' ),
                                          $ini->variable( 'DatabaseSettings', 'Server' ), $ini->variable( 'DatabaseSettings', 'Database' ) ) ) );
        return eZSys::cacheDirectory() . '/wildcard/urlredirect_' . $key . '.php';
    }
}
