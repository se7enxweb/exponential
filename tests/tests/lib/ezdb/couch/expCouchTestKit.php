<?php
/**
 * Shared pieces of the Couchbase and CouchDB driver tests: the drivers' files, quiet debug output, stand-ins for the
 * servers (expCouchbaseFakeService answers SQL++ requests from a script; expCouchDBFake is a small CouchDB in
 * memory: documents with revisions, _find with = selectors and $and, _bulk_docs with conflicts, _all_docs, indexes)
 * and the live targets from the environment:
 *
 *   EXP_TEST_COUCHBASE_HOST, _PORT (8093), _USER, _PASSWORD, _BUCKET   the Query service of a Couchbase cluster
 *   EXP_TEST_COUCHDB_HOST, _PORT (5984), _USER, _PASSWORD              a CouchDB node
 *
 * The live tests skip without them.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'lib/ezdb/classes/expdbunsupportedexception.php', 'lib/ezdb/classes/expdbsqlhelpers.php', 'lib/ezdb/classes/expcouchsqllexer.php',
                 'lib/ezdb/classes/expcouchbasedialect.php', 'lib/ezdb/classes/expcouchbasequeryservice.php', 'lib/ezdb/classes/expcouchbasedb.php',
                 'lib/ezdb/classes/expcouchdbclient.php', 'lib/ezdb/classes/expcouchdbdb.php', 'lib/ezdbschema/classes/ezdbschemainterface.php',
                 'lib/ezdbschema/classes/expcouchbaseschema.php', 'lib/ezdbschema/classes/expcouchdbschema.php' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/' . $file;

class expCouchTestKit
{
    /**
     * eZDB parameters with every key the drivers read.
     */
    public static function parameters( array $over = array() )
    {
        return $over + array( 'server' => '127.0.0.1', 'port' => false, 'user' => 'u', 'password' => 'p', 'database' => '',
                              'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                              'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                              'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                              'use_persistent_connection' => false, 'show_errors' => false );
    }

    /**
     * The live target of a product from the environment, or null.
     *
     * @param string $product couchbase or couchdb
     * @return array|null host, port, user, password (and bucket for couchbase)
     */
    public static function live( $product )
    {
        $prefix = 'EXP_TEST_' . strtoupper( $product ) . '_';
        $host = getenv( $prefix . 'HOST' );
        if ( !is_string( $host ) || $host === '' )
            return null;
        $port = getenv( $prefix . 'PORT' );
        return array( 'host' => $host, 'port' => $port ? (int)$port : ( $product === 'couchbase' ? 8093 : 5984 ),
                      'user' => (string)getenv( $prefix . 'USER' ), 'password' => (string)getenv( $prefix . 'PASSWORD' ),
                      'bucket' => (string)( getenv( $prefix . 'BUCKET' ) ?: 'exponential' ) );
    }

    /**
     * An open driver to the live server of a product, or the reason there is none.
     *
     * @param string $product couchbase or couchdb
     * @param array $settings [CouchbaseSettings] or [CouchDBSettings]
     * @param string|null $database couchbase: the scope in the bucket (exp_live); couchdb: the database (exp_live)
     * @return expCouchbaseDB|expCouchDBDB|string
     */
    public static function liveDriver( $product, array $settings = array(), $database = null )
    {
        $live = self::live( $product );
        if ( $live === null )
            return "No $product for the live tests: set EXP_TEST_" . strtoupper( $product ) . '_HOST (and _PORT, _USER, _PASSWORD' . ( $product === 'couchbase' ? ', _BUCKET)' : ')' );
        try
        {
            if ( $product === 'couchbase' )
                return new expCouchbaseDB( self::parameters( array( 'server' => $live['host'], 'port' => $live['port'], 'user' => $live['user'], 'password' => $live['password'],
                                                                    'database' => $live['bucket'] . '.' . ( $database ?: 'exp_live' ),
                                                                    'couchbase' => $settings + array( 'AutoCreateScope' => 'enabled', 'DurabilityLevel' => 'none' ) ) ) );
            return new expCouchDBDB( self::parameters( array( 'server' => $live['host'], 'port' => $live['port'], 'user' => $live['user'], 'password' => $live['password'],
                                                              'database' => $database ?: 'exp_live', 'couchdb' => $settings + array( 'AutoCreateDatabase' => 'enabled' ) ) ) );
        }
        catch ( Exception $e )
        {
            return "The $product server for the live tests does not answer: " . $e->getMessage();
        }
    }

    public static function quietDebug()
    {
        $saved = array();
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
        return $saved;
    }

    public static function restoreDebug( array $saved )
    {
        foreach ( $saved as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
    }

    /**
     * A catalogue entry of the Couchbase dialect from a short form: column => kind, the key, the counted-up column.
     */
    public static function entry( array $fields, array $primary = array(), $auto = null, array $defaults = array() )
    {
        return array( 'fields' => $fields, 'order' => array_keys( $fields ), 'primary' => $primary, 'auto' => $auto, 'defaults' => $defaults, 'not_null' => array() );
    }

    /**
     * The catalogue the dialect tests use: a few of the kernel's tables.
     */
    public static function catalogue()
    {
        return array(
            'ezcontentobject' => self::entry( array( 'id' => 'int', 'name' => 'text', 'remote_id' => 'text', 'section_id' => 'int', 'language_mask' => 'int',
                                                     'current_version' => 'int' ), array( 'id' ), 'id' ),
            'ezcontentobject_tree' => self::entry( array( 'node_id' => 'int', 'contentobject_id' => 'int', 'parent_node_id' => 'int', 'path_string' => 'text',
                                                          'depth' => 'int' ), array( 'node_id' ), 'node_id' ),
            'ezurlalias_ml' => self::entry( array( 'parent' => 'int', 'text_md5' => 'text', 'id' => 'int', 'link' => 'int', 'lang_mask' => 'int', 'text' => 'text' ),
                                            array( 'parent', 'text_md5' ) ),
            'ezpreferences' => self::entry( array( 'id' => 'int', 'user_id' => 'int', 'name' => 'text', 'value' => 'text' ), array( 'id' ), 'id', array( 'value' => '' ) ),
        );
    }
}

/**
 * The Couchbase Query service in a test: records every request and answers from a callable or a queue.
 */
class expCouchbaseFakeService
{
    /** @var array[] the requests, in order */
    public $requests = array();

    /** @var callable|null function( array $request ): array|null (null: the queue's next answer) */
    public $answer = null;

    /** @var array[] answers given in turn */
    public $queue = array();

    public function __invoke( array $request )
    {
        $this->requests[] = $request;
        if ( $this->answer )
        {
            $response = call_user_func( $this->answer, $request );
            if ( $response !== null )
                return $response;
        }
        if ( $this->queue )
            return array_shift( $this->queue );
        if ( strpos( $request['statement'], 'ds_version' ) !== false )
            return self::ok( array( '7.6.2-3721-community' ) );
        return self::ok( array() );
    }

    public static function ok( array $results )
    {
        return array( 'status' => 'success', 'results' => $results, 'metrics' => array( 'elapsedTime' => '1ms' ) );
    }

    public static function error( $code, $msg, $reasonCode = null )
    {
        $error = array( 'code' => $code, 'msg' => $msg );
        if ( $reasonCode !== null )
            $error['reason'] = array( 'code' => $reasonCode );
        return array( 'status' => 'errors', 'results' => array(), 'errors' => array( $error ) );
    }

    /** The statements sent, in order */
    public function statements()
    {
        return array_map( function ( $r ) { return $r['statement']; }, $this->requests );
    }
}

/**
 * A small CouchDB in memory, for the driver and the import without a server.
 */
class expCouchDBFake
{
    /** @var array db => id => doc (with _rev; a deleted one has _deleted) */
    public $dbs = array();

    /** @var array db => index name => fields */
    public $indexes = array();

    /** @var array[] the requests: method, path */
    public $log = array();

    public function __invoke( $method, $path, $body )
    {
        $this->log[] = array( $method, $path );
        $url = parse_url( $path );
        $parts = explode( '/', ltrim( $url['path'], '/' ) );
        parse_str( isset( $url['query'] ) ? $url['query'] : '', $query );
        if ( $parts[0] === '' )
            return array( 200, array( 'couchdb' => 'Welcome', 'version' => '3.4.3' ) );
        if ( $parts[0] === '_all_dbs' )
            return array( 200, array_keys( $this->dbs ) );
        $db = rawurldecode( $parts[0] );
        if ( count( $parts ) === 1 )
        {
            if ( $method === 'PUT' )
            {
                if ( isset( $this->dbs[$db] ) )
                    return array( 412, array( 'error' => 'file_exists' ) );
                $this->dbs[$db] = array();
                return array( 201, array( 'ok' => true ) );
            }
            if ( $method === 'DELETE' )
            {
                unset( $this->dbs[$db] );
                return array( 200, array( 'ok' => true ) );
            }
            return isset( $this->dbs[$db] ) ? array( 200, array( 'db_name' => $db ) ) : array( 404, array( 'error' => 'not_found', 'reason' => 'Database does not exist.' ) );
        }
        if ( !isset( $this->dbs[$db] ) )
            return array( 404, array( 'error' => 'not_found', 'reason' => 'Database does not exist.' ) );
        $what = rawurldecode( implode( '/', array_slice( $parts, 1 ) ) );
        switch ( $what )
        {
            case '_find':
                $docs = array();
                foreach ( $this->dbs[$db] as $doc )
                    if ( empty( $doc['_deleted'] ) && self::matches( $doc, $body['selector'] ) )
                        $docs[] = $doc;
                $skip = isset( $body['bookmark'] ) ? (int)$body['bookmark'] : 0;
                $page = array_slice( $docs, $skip, $body['limit'] );
                return array( 200, array( 'docs' => $page, 'bookmark' => (string)( $skip + count( $page ) ) ) );
            case '_bulk_docs':
                $results = array();
                foreach ( $body['docs'] as $doc )
                    $results[] = $this->put( $db, $doc );
                return array( 201, $results );
            case '_index':
                if ( $method === 'POST' )
                {
                    $this->indexes[$db][$body['name']] = $body['index']['fields'];
                    return array( 200, array( 'result' => 'created' ) );
                }
                $list = array();
                foreach ( isset( $this->indexes[$db] ) ? $this->indexes[$db] : array() as $name => $fields )
                    $list[] = array( 'ddoc' => '_design/exp-couchdb', 'name' => $name, 'def' => array( 'fields' => $fields ) );
                return array( 200, array( 'indexes' => $list ) );
            case '_all_docs':
                $rows = array();
                if ( $method === 'POST' )
                {
                    foreach ( $body['keys'] as $key )
                    {
                        if ( !isset( $this->dbs[$db][$key] ) )
                        {
                            $rows[] = array( 'key' => $key, 'error' => 'not_found' );
                            continue;
                        }
                        $deleted = !empty( $this->dbs[$db][$key]['_deleted'] );
                        $row = array( 'id' => $key, 'key' => $key, 'value' => array( 'rev' => $this->dbs[$db][$key]['_rev'] ) + ( $deleted ? array( 'deleted' => true ) : array() ) );
                        if ( !empty( $query['include_docs'] ) )
                            $row['doc'] = $deleted ? null : $this->dbs[$db][$key];
                        $rows[] = $row;
                    }
                    return array( 200, array( 'rows' => $rows ) );
                }
                $ids = array_keys( $this->dbs[$db] );
                sort( $ids, SORT_STRING );
                $start = isset( $query['startkey'] ) ? json_decode( $query['startkey'], true ) : null;
                $end = isset( $query['endkey'] ) ? json_decode( $query['endkey'], true ) : null;
                foreach ( $ids as $id )
                {
                    $doc = $this->dbs[$db][$id];
                    if ( !empty( $doc['_deleted'] ) || ( $start !== null && strcmp( $id, $start ) < 0 ) || ( $end !== null && strcmp( $id, $end ) > 0 ) )
                        continue;
                    $rows[] = array( 'id' => $id, 'key' => $id, 'value' => array( 'rev' => $doc['_rev'] ), 'doc' => $doc );
                }
                $limit = isset( $query['limit'] ) ? (int)$query['limit'] : count( $rows );
                $skip = isset( $query['skip'] ) ? (int)$query['skip'] : 0;
                return array( 200, array( 'rows' => array_slice( $rows, $skip, $limit ) ) );
        }
        if ( strncmp( $what, '_index/', 7 ) === 0 && $method === 'DELETE' )
        {
            $name = rawurldecode( basename( $what ) );
            unset( $this->indexes[$db][$name] );
            return array( 200, array( 'ok' => true ) );
        }
        // a document
        $id = $what;
        if ( $method === 'GET' )
            return isset( $this->dbs[$db][$id] ) && empty( $this->dbs[$db][$id]['_deleted'] ) ? array( 200, $this->dbs[$db][$id] ) : array( 404, array( 'error' => 'not_found', 'reason' => 'missing' ) );
        if ( $method === 'PUT' )
        {
            $r = $this->put( $db, array( '_id' => $id ) + $body );
            return isset( $r['error'] ) ? array( 409, array( 'error' => 'conflict', 'reason' => 'Document update conflict.' ) ) : array( 201, array( 'ok' => true, 'id' => $id, 'rev' => $r['rev'] ) );
        }
        return array( 405, array( 'error' => 'method_not_allowed' ) );
    }

    /** A write of one document with CouchDB's revision rule */
    protected function put( $db, array $doc )
    {
        $id = isset( $doc['_id'] ) ? (string)$doc['_id'] : bin2hex( random_bytes( 8 ) );
        $current = isset( $this->dbs[$db][$id] ) ? $this->dbs[$db][$id] : null;
        $live = $current !== null && empty( $current['_deleted'] );
        $given = isset( $doc['_rev'] ) ? $doc['_rev'] : null;
        if ( ( $live && $given !== $current['_rev'] ) || ( !$live && $given !== null && ( $current === null || $given !== $current['_rev'] ) ) )
            return array( 'id' => $id, 'error' => 'conflict', 'reason' => 'Document update conflict.' );
        $n = $current ? (int)$current['_rev'] + 1 : 1;
        $doc['_id'] = $id;
        $doc['_rev'] = $n . '-' . substr( md5( $id . $n ), 0, 8 );
        if ( !empty( $doc['_deleted'] ) )
            $doc = array( '_id' => $id, '_rev' => $doc['_rev'], '_deleted' => true );
        $this->dbs[$db][$id] = $doc;
        return array( 'ok' => true, 'id' => $id, 'rev' => $doc['_rev'] );
    }

    /** Whether a document matches a selector of = fields and $and */
    public static function matches( array $doc, $selector )
    {
        foreach ( (array)$selector as $field => $value )
        {
            if ( $field === '$and' )
            {
                foreach ( $value as $part )
                    if ( !self::matches( $doc, $part ) )
                        return false;
                continue;
            }
            if ( !array_key_exists( $field, $doc ) || $doc[$field] !== $value )
                return false;
        }
        return true;
    }
}
?>
