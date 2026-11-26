<?php
/**
 * The MongoDB DFS cluster backend (expDFSFileHandlerMongoDBBackend) on a real MongoDB server: the parity checks every
 * backend passes (expDFSBackendParityChecks), including four processes on one cache key, plus the MongoDB gateway of
 * index_cluster.php, the schema, the case sensitivity of the name searches, regular expression characters in names
 * and the use of the name index.
 *
 * Needs a server: EXP_TEST_DFS_MONGODB_URI=mongodb://[<user>:<password>@]<host>[:<port>][/?authSource=admin], an
 * account that may create and drop databases. The class makes the database dfsparity_<pid>_<random> with
 * kernel/sql/mongodb/cluster_dfs_schema.json and drops it after the last test. Skipped without the URI or without
 * the PHP extension mongodb.
 *   docker run -d --rm --name dfs-mongo -p 127.0.0.1:27711:27017 mongo:8.0
 *   EXP_TEST_DFS_MONGODB_URI=mongodb://127.0.0.1:27711 php vendor/bin/phpunit \
 *     tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSMongoDBBackendIntegrationTest.php
 *
 * The rawExec()/rawRows() of the parity checks are SQL; the few statements they run (DELETE FROM, UPDATE ... SET
 * mtime, INSERT INTO, SELECT name ... WHERE name_hash) are translated here, outside the backend.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group cluster-live
 */

require_once __DIR__ . '/expDFSBackendParityChecks.php';

#[\PHPUnit\Framework\Attributes\Group('cluster-live')]
class expDFSMongoDBBackendIntegrationTest extends ezpTestCase
{
    use expDFSBackendParityChecks;

    /** @var string|null the connection string of the server */
    private static $uri = null;

    /** @var string|null the test database */
    private static $database = null;

    /** @var MongoDB\Driver\Manager|null a connection outside the backend */
    private static $raw = null;

    protected function backendClass()
    {
        return 'expDFSFileHandlerMongoDBBackend';
    }

    protected function supportsTimesAfter2038()
    {
        return true;
    }

    protected static function engineSetUp()
    {
        if ( !extension_loaded( 'mongodb' ) )
            return 'the PHP extension mongodb is not loaded';
        $uri = trim( (string)getenv( 'EXP_TEST_DFS_MONGODB_URI' ) );
        if ( $uri === '' )
            return 'EXP_TEST_DFS_MONGODB_URI is not set (mongodb://[user:password@]host[:port])';
        if ( !preg_match( '#^mongodb(\+srv)?://#', $uri ) )
            return 'EXP_TEST_DFS_MONGODB_URI is not a mongodb:// connection string';
        self::$uri = $uri;
        self::$database = 'dfsparity_' . getmypid() . '_' . bin2hex( random_bytes( 3 ) );
        try
        {
            self::$raw = new MongoDB\Driver\Manager( $uri, array( 'serverSelectionTimeoutMS' => 5000 ) );
            self::$raw->executeCommand( 'admin', new MongoDB\Driver\Command( array( 'ping' => 1 ) ) );
            expDFSFileHandlerMongoDBBackend::createSchema( self::$raw, self::$database, dirname( __DIR__, 6 ) . '/kernel/sql/mongodb/cluster_dfs_schema.json' );
        }
        catch ( Exception $e )
        {
            return 'Cannot prepare a MongoDB test database: ' . $e->getMessage();
        }
        return null;
    }

    protected static function engineTearDown()
    {
        if ( self::$raw && self::$database !== null && strpos( self::$database, 'dfsparity_' ) === 0 )
        {
            try
            {
                self::$raw->executeCommand( self::$database, new MongoDB\Driver\Command( array( 'dropDatabase' => 1 ) ) );
            }
            catch ( Exception $e )
            {
            }
        }
        self::$raw = null;
        self::$database = null;
    }

    protected function engineSettings()
    {
        return array(
            'DBHost' => self::$uri,
            'DBPort' => '',
            'DBSocket' => '',
            'DBName' => self::$database,
            'DBUser' => '',
            'DBPassword' => '',
        );
    }

    /** A literal of the parity checks' SQL: a quoted string or an integer */
    private static function literal( $text )
    {
        $text = trim( $text );
        if ( preg_match( "/^'(.*)'$/s", $text, $m ) )
            return str_replace( "''", "'", $m[1] );
        return (int)$text;
    }

    /** @return array( collection, filter ) of "<table> WHERE name_hash = '<hash>'" or "<table>" */
    private static function target( $sql )
    {
        if ( !preg_match( "/(?:FROM|UPDATE|INTO)\s+(ezdfsfile(?:_cache)?)\b/i", $sql, $m ) )
            throw new RuntimeException( "Unknown table in: $sql" );
        $filter = array();
        if ( preg_match( "/WHERE\s+name_hash\s*=\s*'([0-9a-f]{32})'/i", $sql, $h ) )
            $filter = array( '_id' => $h[1] );
        return array( $m[1], $filter );
    }

    protected function rawExec( $sql )
    {
        list( $collection, $filter ) = self::target( $sql );
        $bulk = new MongoDB\Driver\BulkWrite();
        if ( preg_match( '/^\s*DELETE\s+FROM/i', $sql ) )
        {
            $bulk->delete( $filter, array( 'limit' => 0 ) );
        }
        elseif ( preg_match( '/^\s*UPDATE\s+\w+\s+SET\s+(\w+)\s*=\s*(-?\d+)/i', $sql, $m ) )
        {
            $bulk->update( $filter, array( '$set' => array( $m[1] => (int)$m[2] ) ), array( 'multi' => true ) );
        }
        elseif ( preg_match( '/^\s*INSERT\s+INTO\s+\w+\s*\(([^)]*)\)\s*VALUES\s*\((.*)\)\s*$/is', $sql, $m ) )
        {
            $columns = array_map( 'trim', explode( ',', $m[1] ) );
            preg_match_all( "/'(?:[^']|'')*'|-?\d+/", $m[2], $values );
            $document = array_combine( $columns, array_map( array( __CLASS__, 'literal' ), $values[0] ) );
            $document['_id'] = $document['name_hash'];
            $bulk->insert( $document );
        }
        else
        {
            throw new RuntimeException( "Statement not translated: $sql" );
        }
        self::$raw->executeBulkWrite( self::$database . '.' . $collection, $bulk );
    }

    protected function rawRows( $sql )
    {
        list( $collection, $filter ) = self::target( $sql );
        $cursor = self::$raw->executeQuery( self::$database . '.' . $collection, new MongoDB\Driver\Query( $filter ) );
        $cursor->setTypeMap( array( 'root' => 'array', 'document' => 'array', 'array' => 'array' ) );
        return $cursor->toArray();
    }

    public function testSchemaHasTheIndexesAndIsIdempotent()
    {
        foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $collection )
        {
            $cursor = self::$raw->executeCommand( self::$database, new MongoDB\Driver\Command( array( 'listIndexes' => $collection ) ) );
            $names = array();
            foreach ( $cursor as $index )
                $names[] = $index->name;
            foreach ( array( '_id_', "{$collection}_name", "{$collection}_name_trunk", "{$collection}_expired_scope", "{$collection}_scope", "{$collection}_mtime" ) as $name )
                $this->assertContains( $name, $names );
        }
        $again = expDFSFileHandlerMongoDBBackend::createSchema( self::$raw, self::$database, dirname( __DIR__, 6 ) . '/kernel/sql/mongodb/cluster_dfs_schema.json' );
        $this->assertNotContains( 'ezdfsfile', $again, 'an existing collection is not created again' );
    }

    public function testDocumentsUse64BitIntegersAndTheHashAsId()
    {
        $path = $this->path( 'files/big.bin' );
        $this->backend->_storeContents( $path, 'x', 'binaryfile', 'application/octet-stream', 4102444800 );
        $rows = $this->rawRows( "SELECT name FROM ezdfsfile WHERE name_hash = '" . md5( $path ) . "'" );
        $this->assertCount( 1, $rows );
        $this->assertSame( md5( $path ), $rows[0]['_id'] );
        $this->assertSame( 4102444800, $rows[0]['mtime'] );
    }

    /**
     * The prefix search the purges and deletes run uses the name index, so a purge of var/<site>/cache/* does not
     * read the whole collection.
     */
    public function testPrefixSearchUsesTheIndex()
    {
        for ( $i = 0; $i < 20; ++$i )
            $this->backend->_storeContents( $this->path( "index/f$i.txt" ), "$i", 'binaryfile', 'text/plain', 1700000000 );
        $regex = expDFSFileHandlerMongoDBBackend::likeToRegex( $this->path( 'index/f1%' ) );
        $cursor = self::$raw->executeCommand( self::$database, new MongoDB\Driver\Command( array(
            'explain' => array( 'find' => 'ezdfsfile', 'filter' => array( 'name' => new MongoDB\BSON\Regex( $regex, '' ) ) ),
            'verbosity' => 'queryPlanner' ) ) );
        $plan = json_encode( $cursor->toArray()[0]->queryPlanner->winningPlan );
        $this->assertStringContainsString( 'IXSCAN', $plan );
        $this->assertStringContainsString( 'ezdfsfile_name', $plan );
    }

    /** Names are compared case sensitively, as MySQL's binary LIKE on name compares them */
    public function testNameSearchesAreCaseSensitive()
    {
        $this->backend->_storeContents( $this->path( 'case/Upper.txt' ), 'U', 'binaryfile', 'text/plain', 1700000000 );
        $this->backend->_storeContents( $this->path( 'case/upper.txt' ), 'u', 'binaryfile', 'text/plain', 1700000000 );
        $this->assertTrue( $this->backend->_deleteByLike( $this->path( 'case/up%' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 'case/Upper.txt' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 'case/upper.txt' ) ) );
        $this->assertSame( array( $this->path( 'case/Upper.txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 'case/U' ) ) );
    }

    /** Characters of regular expressions in a name are literals of a LIKE pattern, a wildcard and a prefix */
    public function testRegularExpressionCharactersInNamesAreLiterals()
    {
        foreach ( array( 'a[1].txt', 'a1.txt', 'b*.txt', 'bX.txt', 'c(1)+.txt', 'c1.txt' ) as $name )
            $this->backend->_storeContents( $this->path( "re/$name" ), $name, 'binaryfile', 'text/plain', 1700000000 );
        $this->assertTrue( $this->backend->_deleteByLike( $this->path( 're/a[1]%' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 're/a[1].txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 're/a1.txt' ) ) );
        $this->assertSame( array( $this->path( 're/b*.txt' ) ), $this->backend->_getFileList( false, false, false, $this->path( 're/b*' ) ) );
        $this->assertTrue( $this->backend->_deleteByWildcard( $this->path( 're/c(1)+.t?t' ) ) );
        $this->assertFalse( $this->backend->_exists( $this->path( 're/c(1)+.txt' ) ) );
        $this->assertTrue( $this->backend->_exists( $this->path( 're/c1.txt' ) ) );
    }

    /** A second insert of one .generating document is refused by the unique _id: the duplicate key, code 11000 */
    public function testTheGeneratingDocumentIsTheLock()
    {
        $path = $this->path( 'cache/gen/lock.cache' );
        $this->assertSame( 'ok', $this->backend->_startCacheGeneration( $path, "$path.generating" )['result'] );
        $this->assertSame( 'ko', $this->backend->_startCacheGeneration( $path, "$path.generating" )['result'] );
        $this->assertCount( 1, $this->rawRows( "SELECT name FROM ezdfsfile_cache WHERE name_hash = '" . md5( "$path.generating" ) . "'" ) );
    }

    /** A server that does not answer is a connection error after DBLockTimeout, not a hang */
    public function testAServerThatDoesNotAnswerIsAConnectionError()
    {
        ezpINIHelper::setINISetting( 'file.ini', 'eZDFSClusteringSettings', 'DBHost', '127.0.0.1' );
        ezpINIHelper::setINISetting( 'file.ini', 'eZDFSClusteringSettings', 'DBPort', '1' );
        $time = microtime( true );
        try
        {
            $this->newBackend();
            $this->fail( 'connected to a server that does not exist' );
        }
        catch ( eZClusterHandlerDBNoConnectionException $e )
        {
            $this->assertLessThan( 5.0, microtime( true ) - $time );
        }
    }

    public function testGatewayServesMetadataAndRanges()
    {
        $path = $this->path( 'storage/original/application/file.bin' );
        $data = random_bytes( 150000 );
        $this->backend->_storeContents( $path, $data, 'binaryfile', 'application/octet-stream', 1700000000 );

        if ( !class_exists( 'ezpClusterGateway', false ) )
            require_once dirname( __DIR__, 6 ) . '/kernel/clustering/gateway.php';
        require_once dirname( __DIR__, 6 ) . '/kernel/clustering/dfsmongodb.php';
        if ( !defined( 'CLUSTER_MOUNT_POINT_PATH' ) )
            define( 'CLUSTER_MOUNT_POINT_PATH', realpath( $this->mount ) );
        if ( CLUSTER_MOUNT_POINT_PATH !== realpath( $this->mount ) )
            $this->markTestSkipped( 'CLUSTER_MOUNT_POINT_PATH is already defined for another mount' );

        $gateway = new expDfsMongoDBClusterGateway( array( 'host' => self::$uri, 'name' => self::$database, 'charset' => 'utf-8' ) );
        $gateway->connect();
        $meta = $gateway->fetchFileMetadata( $path );
        $this->assertSame( array( 'datatype' => 'application/octet-stream', 'size' => '150000', 'mtime' => '1700000000' ), $meta );
        $this->assertFalse( $gateway->fetchFileMetadata( $this->path( 'storage/none.bin' ) ) );

        ob_start();
        $gateway->passthrough( $path, 150000, 100, 70000 );
        $this->assertSame( substr( $data, 100, 70000 ), ob_get_clean() );
        ob_start();
        $gateway->passthrough( $path, 150000 );
        $this->assertSame( $data, ob_get_clean() );
        $gateway->close();

        $gateway = new expDfsMongoDBClusterGateway( array( 'host' => self::$uri, 'name' => 'no/database' ) );
        $this->expectException( RuntimeException::class );
        $gateway->connect();
    }
}
