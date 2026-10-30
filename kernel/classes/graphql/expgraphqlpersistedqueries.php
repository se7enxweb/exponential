<?php
/**
 * The persisted queries of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Persisted queries"): queries
 * stored on the server and called by an id instead of their text.
 *
 * An id is the SHA-256 of the query text (64 hexadecimal digits: Apollo's automatic persisted queries,
 * extensions.persistedQuery.sha256Hash) or a name given when the query was stored (the id of a request,
 * ?id=articles). A query is stored by exp:graphql:persist, or by a client sending the text with its hash
 * (AutomaticPersistedQueries), or shipped in a directory of PersistedQueryDirectoryList[] (*.graphql, the file
 * name is its name). The store is var/<site>/graphql/persisted/: <sha256>.graphql and names.json (name => hash).
 *
 * Files, not a database table: they work on every database engine, need no connection and go with a deployment.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLPersistedQueries
{
    /** A name: letters, digits, _ - . and /, 1 to 100 characters, not a hash. */
    const NAME_PATTERN = '#^[A-Za-z0-9_][A-Za-z0-9_./-]{0,99}$#';

    /** @var string the store directory */
    protected $dir;

    /** @var string[] directories of shipped queries (read only) */
    protected $shipped;

    /** @var array|null name => hash, read when first needed */
    protected $names = null;

    /**
     * @param string $dir the store directory
     * @param string[] $shipped directories of shipped *.graphql files
     */
    public function __construct( $dir, array $shipped = array() )
    {
        $this->dir = rtrim( (string)$dir, '/' );
        $this->shipped = $shipped;
    }

    /**
     * The store of the installation: var/<site>/graphql/persisted and PersistedQueryDirectoryList[] (relative to
     * the installation root, or absolute).
     *
     * @return self
     */
    public static function fromSettings()
    {
        $shipped = array();
        foreach ( expGraphQL::listSetting( 'PersistedQueryDirectoryList' ) as $dir )
            $shipped[] = $dir[0] === '/' ? $dir : expGraphQL::rootDir() . '/' . $dir;
        return new self( eZSys::varDirectory() . '/graphql/persisted', $shipped );
    }

    /**
     * @param string $query
     * @return string the id of a query text: its SHA-256
     */
    public static function hash( $query )
    {
        return hash( 'sha256', (string)$query );
    }

    /**
     * @param string $id
     * @return bool the id is a SHA-256 (64 hexadecimal digits)
     */
    public static function isHash( $id )
    {
        return (bool)preg_match( '/^[0-9a-f]{64}$/', (string)$id );
    }

    /**
     * @param string $name
     * @return bool the name can name a stored query
     */
    public static function isName( $name )
    {
        return !self::isHash( $name ) && (bool)preg_match( self::NAME_PATTERN, (string)$name ) && strpos( (string)$name, '..' ) === false;
    }

    /** @return string */
    public function directory()
    {
        return $this->dir;
    }

    /**
     * @param string $id a hash or a name
     * @return string|null the query text, null when there is none
     */
    public function find( $id )
    {
        $id = (string)$id;
        if ( self::isHash( $id ) )
        {
            $file = $this->dir . '/' . $id . '.graphql';
            if ( is_file( $file ) )
                return (string)file_get_contents( $file );
            foreach ( $this->shippedQueries() as $query )
            {
                if ( self::hash( $query['query'] ) === $id )
                    return $query['query'];
            }
            return null;
        }
        if ( !self::isName( $id ) )
            return null;
        $names = $this->names();
        if ( isset( $names[$id] ) )
            return $this->find( $names[$id] );
        $shipped = $this->shippedQueries();
        return isset( $shipped[$id] ) ? $shipped[$id]['query'] : null;
    }

    /**
     * Stores a query (and its name). The same text is stored once; a name points at the text it was last given.
     *
     * @param string $query
     * @param string|null $name
     * @return string the hash
     */
    public function store( $query, $name = null )
    {
        $query = (string)$query;
        $hash = self::hash( $query );
        $file = $this->dir . '/' . $hash . '.graphql';
        if ( !is_file( $file ) )
            self::writeAtomic( $file, $query );
        if ( $name !== null && $name !== '' )
        {
            if ( !self::isName( $name ) )
                throw new InvalidArgumentException( "Not a name for a persisted query: $name" );
            $names = $this->names();
            if ( !isset( $names[$name] ) || $names[$name] !== $hash )
            {
                $names[$name] = $hash;
                ksort( $names );
                $this->writeNames( $names );
            }
        }
        return $hash;
    }

    /**
     * Removes a stored query (by hash or name) and every name that points at it.
     *
     * @param string $id
     * @return bool something was removed
     */
    public function remove( $id )
    {
        $names = $this->names();
        $hash = self::isHash( $id ) ? (string)$id : ( isset( $names[$id] ) ? $names[$id] : null );
        if ( $hash === null )
            return false;
        $removed = false;
        $file = $this->dir . '/' . $hash . '.graphql';
        if ( is_file( $file ) )
            $removed = unlink( $file );
        $kept = array_filter( $names, function ( $value ) use ( $hash ) { return $value !== $hash; } );
        if ( count( $kept ) !== count( $names ) )
        {
            $this->writeNames( $kept );
            $removed = true;
        }
        return $removed;
    }

    /**
     * @return array[] every query: hash => array( hash, names, source (store or a directory), bytes, modified )
     */
    public function all()
    {
        $list = array();
        $byHash = array();
        foreach ( $this->names() as $name => $hash )
            $byHash[$hash][] = $name;
        foreach ( is_dir( $this->dir ) ? (array)glob( $this->dir . '/*.graphql' ) : array() as $file )
        {
            $hash = basename( $file, '.graphql' );
            if ( !self::isHash( $hash ) )
                continue;
            $list[$hash] = array( 'hash' => $hash, 'names' => isset( $byHash[$hash] ) ? $byHash[$hash] : array(), 'source' => 'store',
                                  'bytes' => (int)filesize( $file ), 'modified' => (int)filemtime( $file ) );
        }
        foreach ( $this->shippedQueries() as $name => $query )
        {
            $hash = self::hash( $query['query'] );
            if ( isset( $list[$hash] ) )
            {
                $list[$hash]['names'][] = $name;
                continue;
            }
            $list[$hash] = array( 'hash' => $hash, 'names' => array( $name ), 'source' => $query['dir'], 'bytes' => strlen( $query['query'] ),
                                  'modified' => (int)filemtime( $query['file'] ) );
        }
        ksort( $list );
        return $list;
    }

    /**
     * @return int how many queries the store holds (the shipped ones not counted)
     */
    public function count()
    {
        return is_dir( $this->dir ) ? count( (array)glob( $this->dir . '/*.graphql' ) ) : 0;
    }

    /**
     * @return array name => hash
     */
    protected function names()
    {
        if ( $this->names === null )
        {
            $file = $this->dir . '/names.json';
            $names = is_file( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : array();
            $this->names = is_array( $names ) ? $names : array();
        }
        return $this->names;
    }

    /**
     * @param array $names
     */
    protected function writeNames( array $names )
    {
        $this->names = $names;
        self::writeAtomic( $this->dir . '/names.json', json_encode( $names, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
    }

    /**
     * @return array name => array( query, file, dir ): the *.graphql files of the shipped directories
     */
    protected function shippedQueries()
    {
        $queries = array();
        foreach ( $this->shipped as $dir )
        {
            foreach ( is_dir( $dir ) ? (array)glob( rtrim( $dir, '/' ) . '/*.graphql' ) : array() as $file )
            {
                $name = basename( $file, '.graphql' );
                if ( self::isName( $name ) && !isset( $queries[$name] ) )
                    $queries[$name] = array( 'query' => (string)file_get_contents( $file ), 'file' => $file, 'dir' => $dir );
            }
        }
        return $queries;
    }

    /**
     * Writes a file under a temporary name and renames it, so a reader never sees half a file.
     *
     * @param string $file
     * @param string $content
     */
    public static function writeAtomic( $file, $content )
    {
        $dir = dirname( $file );
        if ( !expGraphQLResponseCache::makeDirectory( $dir ) )
            throw new RuntimeException( "Cannot create $dir" );
        $tmp = $dir . '/.' . basename( $file ) . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( file_put_contents( $tmp, $content ) === false || !chmod( $tmp, 0666 ) || !rename( $tmp, $file ) )
        {
            if ( is_file( $tmp ) )
                unlink( $tmp );
            throw new RuntimeException( "Cannot write $file" );
        }
    }
}

?>
